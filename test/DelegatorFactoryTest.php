<?php
/**
 * finc Method Interception Delegator Factory Test
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

use Psr\Container\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Delegator factory test.
 *
 * @category finc
 * @package  MethodInterception
 * @author   Sebastian Kehr <kehr@ub.uni-leipzig.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU GPLv2
 * @link     https://finc.info
 */
class DelegatorFactoryTest extends TestCase
{
    protected $config
        = [
            'finc' => [
                'method_interception' => [
                    'finc\MethodInterception\Target' => [
                        'concat' => [
                            'disabled' => false,
                            'key' => '100::Container::finc\MethodInterception\Interceptor::embrace',
                            '050::Container::finc\MethodInterception\Interceptor::lower'
                        ],
                        'square' => [
                            '0::Container::finc\MethodInterception\Interceptor::floor'
                        ]
                    ]
                ]
            ]
        ];

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var ContainerInterface|MockObject
     */
    protected $container;

    /**
     * @var Interceptor
     */
    protected $interceptor;

    /**
     * @var Target
     */
    protected $target;

    /**
     * @var Target
     */
    protected $targetProxy;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setUp()
    {
        $this->factory = new DelegatorFactory;
        $this->interceptor = new Interceptor;
        $this->target = new Target;
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('get')
            ->willReturnMap([
                ['Config', $this->config],
                ['Container', $this->container],
                [Target::class, $this->target],
                [Interceptor::class, $this->interceptor]
            ]);
        $this->targetProxy = $this->factory->__invoke($this->container,
            Target::class, function () {
                return $this->target;
            });
    }

    public function test()
    {
        $string = $this->targetProxy->concat("ALPHA", "beta");
        $this->assertEquals("(alpha ~ beta)", $string);

        $gamma = $this->targetProxy->square(4.75);
        $this->assertEquals(16, $gamma);
    }
}