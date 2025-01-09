<?php

declare(strict_types=1);

namespace Core\Http\Routing;

use App\Presentation\RoutingColletor;
use Core\Http\Attributes\Route as RouteAttribute;
use Core\Http\Domain\RouteModel;
use Core\Http\Interfaces\RouterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use ReflectionClass;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;

class RouterCollector implements RouterInterface
{
    public function __construct(
        private RouteFactory $routeFactory,
        private RouteIncluder $routeIncluder,

    ) {}

    public function run(): void
    {
        $controllers = new ArrayCollection(RoutingColletor::getActions());

        $controllers
            ->map(fn (DiscoveredStructure|string $controller) => new ReflectionClass($controller))
            ->map(fn (ReflectionClass $reflectionClass): ?RouteModel => $this->createRouteModel($reflectionClass))
            ->filter(fn (?RouteModel $route) => $route instanceof \Core\Http\Domain\RouteModel)
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
