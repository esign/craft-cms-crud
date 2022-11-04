<?php

namespace modules\module\contracts;

interface ResponseInterface
{
    public static function fromResponse(string $response): array;
}
