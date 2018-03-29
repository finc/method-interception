<?php

namespace finc\MethodInterception;

class Target
{
    public function concat(string $alpha, string $beta): string
    {
        return "$alpha ~ $beta";
    }

    public function square(float $gamma): float
    {
        return $gamma * $gamma;
    }
}
