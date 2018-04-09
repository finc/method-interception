<?php
/**
 * finc Method Interception Test Interceptor
 *
 * Copyright (C) 2018 Leipzig University Library <info@ub.uni-leipzig.de>
 *
 * PHP version 7
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc. 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category finc
 * @package  MethodInterception
 * @author   Sebastian Kehr <kehr@ub.uni-leipzig.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU GPLv2
 * @link     https://finc.info
 */
namespace finc\MethodInterception;

/**
 * Test interceptor class.
 *
 * @category finc
 * @package  finc\MethodInterception
 * @author   Sebastian Kehr <kehr@ub.uni-leipzig.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU GPLv2
 * @link     https://finc.info
 */
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