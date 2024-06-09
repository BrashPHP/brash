<?php
namespace App\Presentation;

use Core\Http\Interfaces\ActionInterface;
use Spatie\StructureDiscoverer\Discover;

final class RoutingColletor
{
    public static function getActions()
    {
        return Discover::in(__DIR__)->classes()->implementing(ActionInterface::class)->get() ?? [];
    }
}
