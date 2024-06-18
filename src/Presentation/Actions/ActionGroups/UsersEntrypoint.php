<?php

namespace App\Presentation\Actions\ActionGroups;

use Core\Http\Attributes\RouteGroup;

#[RouteGroup("/users", parent: ApiEntrypoint::class)]
final class UsersEntrypoint
{
}


