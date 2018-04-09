<?php
/**
 * finc Method Interception Factory
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
 * @package  finc\MethodInterception
 * @author   Sebastian Kehr <kehr@ub.uni-leipzig.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU GPLv2
 * @link     https://finc.info
 */

namespace finc\MethodInterception;

use ProxyManager\Factory\AccessInterceptorValueHolderFactory as ProxyFactory;
use ProxyManager\Proxy\AccessInterceptorInterface as Proxy;

/**
 * Factory for creating service proxy objects whose methods get intercepted.
 *
 * @category finc
 * @package  finc\MethodInterception
 * @author   Sebastian Kehr <kehr@ub.uni-leipzig.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU GPLv2
 * @link     https://finc.info
 */
class Factory
{
    /**
     * Creates a proxy object whose methods get intercepted.
     *
     * @param object $target       The proxy target object.
     * @param array  $interceptors A mapping from method names to lists of
     *                             callables intercepting the targets respective
     *                             method in the given order.
     *
     * @return Proxy
     */
    public static function createProxy($target, array $interceptors): Proxy
    {
        $factory = new ProxyFactory;
        $proxy = $factory->createProxy($target);
        foreach ($interceptors as $method => $callables) {
            $callables[] = [$target, $method];
            static::intercept($method, $proxy, $factory, ...$callables);
        }
        return $proxy;
    }

    /**
     * @param string       $method
     * @param Proxy        $proxy
     * @param ProxyFactory $factory
     * @param callable     $callable
     * @param callable[]   ...$callables
     */
    protected static function intercept(
        string $method,
        Proxy $proxy,
        ProxyFactory $factory,
        callable $callable,
        callable ...$callables
    ) {

        $args = [];
        if ($callables) {
            $args[] = $argProxy = $factory->createProxy($proxy);
            static::intercept($method, $argProxy, $factory, ...$callables);
        }

        $proxy->setMethodPrefixInterceptor($method, function (
            $proxy,
            $instance,
            string $method,
            array $params,
            & $returnEarly
        ) use ($callable, $args) {
            $returnEarly = true;
            $args = array_merge($args, array_values($params));
            return call_user_func_array($callable, $args);
        });
    }
}
