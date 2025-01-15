<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Routing;

use Brash\Framework\Http\Action;
use Brash\Framework\Http\Attributes\Route as RouteAttribute;
use Brash\Framework\Http\Domain\RouteModel;
use Brash\Framework\Http\Interfaces\ActionInterface;
use Brash\Framework\Http\Interfaces\RouterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use ReflectionClass;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Discover;

class RouterCollector implements RouterInterface
{
    public function __construct(
        private RouteFactory $routeFactory,
        private RouteIncluder $routeIncluder,
        private string $paths
    ) {}

    public function run(): void
    {
        $el = Discover::in(directories: $this->paths)->classes();
        $extendsAction = $el->extending(Action::class)->get();
        $implementsAction = $el->implementing(ActionInterface::class)->get();

        $controllers = new ArrayCollection(array_merge($extendsAction, $implementsAction));

        $controllers
            ->map(fn (DiscoveredStructure|string $controller) => new ReflectionClass($controller))
            ->map(fn (ReflectionClass $reflectionClass): ?RouteModel => $this->createRouteModel($reflectionClass))
            ->filter(fn (?RouteModel $route) => $route instanceof RouteModel)
            ->map(fn (RouteModel $route) => $this->routeIncluder->include($route));
    }

    private function createRouteModel(ReflectionClass $reflectionClass): ?RouteModel
    {
        $attributes = $reflectionClass->getAttributes(RouteAttribute::class);
        $attribute = array_pop($attributes);

        if ($attribute !== null) {
            return $this->routeFactory->make(
                $attribute->newInstance(),
                $reflectionClass->getName()
            );
        }

        return null;
    }
}
