<?php
namespace Core\Attributes;

use Attribute;
use ReflectionClass;

#[Attribute(Attribute::TARGET_CLASS)]
class ActionAttribute
{
    private function resolveListeners(string $subscriberClass): array
    {
        $reflectionClass = new ReflectionClass($subscriberClass);

        $listeners = [];

        foreach ($reflectionClass->getMethods() as $method) {
            $attributes = $method->getAttributes(ListensTo::class);

            foreach ($attributes as $attribute) {
                $listener = $attribute->newInstance();

                $listeners[] = [
                    // The event that's configured on the attribute
                    $listener->event,

                    // The listener for this event 
                    [$subscriberClass, $method->getName()],
                ];
            }
        }

        return $listeners;
    }
}