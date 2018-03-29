<?php
/**
 * finc Method Interception Factory Test
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

use PHPUnit\Framework\TestCase;
use ProxyManager\Proxy\AccessInterceptorInterface;

/**
 * Factory test.
 *
 * @category finc
 * @package  MethodInterception
 * @author   Sebastian Kehr <kehr@ub.uni-leipzig.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU GPLv2
 * @link     https://finc.info
 */
class FactoryTest extends TestCase
{
    /**
     * @var AccessInterceptorInterface|Target
     */
    protected $target;

    public function setUp()
    {
        $interceptor = new Interceptor;
        $this->target = Factory::createProxy(new Target, [
            'concat' => [
                [$interceptor, 'embrace'],
                [$interceptor, 'lower']
            ],
            'square' => [
                [$interceptor, 'floor']
            ]
        ]);

    }

    public function test()
    {
        $string = $this->target->concat("ALPHA", "beta");
        $this->assertEquals("(alpha ~ beta)", $string);

        $gamma = $this->target->square(4.75);
        $this->assertEquals(16, $gamma);
    }
}
