<?php
namespace App\Presentation\Handlers;

final class UserDoesNotExistException extends \Exception
{
    public function __construct()
    {
        parent::__construct('User Not Existent in Database');
    }
}
