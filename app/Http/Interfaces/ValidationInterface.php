<?php

namespace Core\Http\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

interface ValidationInterface{
    function rules(ServerRequestInterface $request);
    public function messages(): ?array;
}
