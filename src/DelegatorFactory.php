<?php
/**
 * finc Method Interception Delegator Factory
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

use ProxyManager\Proxy\AccessInterceptorInterface;
use Psr\Container\ContainerInterface;

/**
 * Zend Service Manager 3 compatible delegator factory for creating service
 * proxy objects whose methods get intercepted.
 *
 * @category finc
 * @package  finc\MethodInterception
 * @author   Sebastian Kehr <kehr@ub.uni-leipzig.de>
 * @license  https://opensource.org/licenses/gpl-2.0.php GNU GPLv2
 * @link     https://finc.info
 */
class DelegatorFactory
{
    /**
     * Zend Service Manager 3 compatible delegator factory method creating
     * service proxy objects whose methods get intercepted.
     *
     * @param ContainerInterface $container
     * @param string             $name
     * @param callable           $callback
     * @param array|null         $options
     *
     * @return AccessInterceptorInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(
        ContainerInterface $container,
        string $name,
        callable $callback,
        array $options = null
    ) {
        $interceptors = $options ?? $this->getInterceptors($container, $name);
        return Factory::createProxy(call_user_func($callback), $interceptors);
    }

    /**
     * Get the mapping from method names to lists of intercepting callables.
     *
     * @param ContainerInterface $container The container to use for getting the
     *                                      configuration service.
     *
     * @param string             $name      The key of the mapping to look up.
     *
     * @return array Mapping from method names to lists
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getInterceptors(
        ContainerInterface $container,
        string $name
    ): array {
        $config = $container->get('Config');
        return array_map(function ($item) use ($container) {
            return $this->getInterceptingCallables($container, $item);
        }, $config['finc']['method_interception'][$name] ?? []);
    }

    protected function getInterceptingCallables(
        ContainerInterface $container,
        array $chain
    ): array {
        $chain = array_filter($chain, function ($item) {
            return !!$item;
        });

        $chain = array_map(function (string $item) {
            return explode('::', $item);
        }, array_values($chain));

        uksort($chain, function ($first, $second) use ($chain) {
            $firstPrio = intval($chain[$first][0]);
            $secondPrio = intval($chain[$second][0]);
            return $secondPrio - $firstPrio ?: $second - $first;
        });

        return array_map(function ($item) use ($container) {
            list(, $managerName, $serviceName, $methodName) = $item;
            $service = $container->get($managerName)->get($serviceName);
            return [$service, $methodName];
        }, $chain);
    }
}


