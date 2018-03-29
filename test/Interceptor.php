<?php

namespace finc\MethodInterception;

class Interceptor
{
    public function embrace(Target $target, string $alpha, string $beta): string
    {
        $gamma = $target->concat($alpha, $beta);
        return "($gamma)";
    }

    public function lower(
        Target $target,
        string $alpha,
        string $beta
    ): string {
        return $target->concat(strtolower($alpha), $beta);
    }

    public function floor(Target $target, float $gamma): int
    {
        return $target->square(floor($gamma));
    }
}