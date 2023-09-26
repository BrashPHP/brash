<?php
namespace App\Domain\Models\RBAC;

use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

/**
 * AccessControl is a Facade to interact with Roles available in the system.
 * It will handle most authorization functions and 
 */
class AccessControl implements \JsonSerializable
{
    /** @var array<string, Role> */
    public array $roles = [];

    /** @var array<string, Resource> */
    public array $resources = [];

    public function addPermissionToRole(
        Role|string $role,
        Resource|string $resource,
        ContextIntent $intent,
        string $permissionName = ""
    ): self {
        $permission = $permissionName === "" ?
            Permission::makeWithPreferableName($intent, $resource) :
            new Permission($permissionName, $intent);

        $roleRef = $this->extractName($role);
        $resourceRef = $this->extractName($resource);
        $this->roles[$roleRef]->addPermissionToResource(
            $permission,
            $this->resources[$resourceRef]
        );

        return $this;
    }

    /**
     * @param Role|string $role
     * @param Resource $permission
     * @param Permission[] $permission
     */
    public function grantAccessOn(
        Role|string $role,
        Resource $resource,
        array $permissions
    ): self {
        $roleRef = $this->extractName($role);
        foreach ($permissions as $permission) {
            $this->roles[$roleRef]->addPermissionToResource(
                $permission,
                $resource
            );
        }

        return $this;
    }

    public function tryAccess(
        Role|string $role,
        Resource|string $resource,
        ContextIntent|Permission $permission,
        ?callable $fallback = null
    ): bool {
        $result = $this
            ->getResource($resource)
            ->map(
                fn(Resource $resource): bool => $this
                    ->getRole($role)
                    ->map(
                        static fn(Role $role): bool => $role->canAcess(
                            $resource,
                            $permission
                        )
                    )->get()
            )
            ->getOrElse(false);

        if (!$result) {
            return is_null($fallback)
                ? false
                : $fallback($role, $resource, $permission);
        }

        return true;
    }

    public function appendRole(Role $role): self
    {
        if (!in_array($role->name, $this->roles, true)) {
            $this->roles[$role->name] = $role;

            foreach ($role->keyMap as $resource => $permissions) {
                $this->appendResource($resource);
                $this->grantAccessOn($role, $resource, $permissions);
            }
        }

        return $this;
    }
    public function forgeRole(
        string $roleName,
        string $description = ''
    ): self {
        if (!in_array($roleName, $this->roles, true)) {
            $role = new Role(
                $roleName,
                $description
            );

            $this->roles[$roleName] = $role;
        }

        return $this;
    }

    /**
     * @return Option<Role>
     */
    public function getRole(Role|string $role): Option
    {
        $nameUtility = $this->extractName($role);

        return key_exists($nameUtility, $this->roles)
            ? new Some($this->roles[$nameUtility])
            : None::create();
    }

    public function getRoles()
    {
        return array_values($this->roles);
    }

    public function revokeRole(Role|string $role): void
    {
        $this->roles[$this->extractName($role)]->inactivate();
    }

    public function extendRole(Role|string $targetRole, Role|string ...$roles)
    {
        $this->getRole($targetRole)->map(function (Role $role) use ($roles) {
            foreach ($roles as $includer) {
                $ref = $this->extractName($includer);
                if (!key_exists($ref, $this->roles)) {
                    continue;
                }
                $role->extendRole($this->roles[$ref]);
            }
        });
    }

    /**
     * @return Resource[]
     */
    public function getResources(): array
    {
        return array_values($this->resources);
    }

    /** @return Option<Resource> */
    public function getResource(Resource|string $resource): Option
    {
        $nameUtility = $this->extractName($resource);

        $exists = key_exists($nameUtility, $this->resources);

        return $exists ? new Some($this->resources[$nameUtility]) : None::create();
    }

    public function createResource(string $name, string $description): Resource
    {
        $resource = new Resource($name, $description);

        if (!key_exists($name, $this->resources)) {
            $this->resources[$name] = $resource;
        }

        return $resource;
    }

    public function appendResource(Resource $resource): self
    {
        if (!key_exists($resource->name, $this->resources)) {
            $this->resources[$resource->name] = $resource;
        }

        return $this;
    }

    public function toJson(): string
    {
        return json_encode($this);
    }

    public function jsonSerialize(): mixed
    {
        return [
            "roles" => $this->roles,
        ];
    }

    private function extractName(Resource|Role|Permission|string $subject): string
    {
        return is_string($subject) ? $subject : $subject->name;
    }
}