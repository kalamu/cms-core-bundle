<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\Manager\Interfaces;

use Symfony\Component\Routing\RouteCollection;


/**
 * Interface for manager that register the routes themself
 */
interface RouteRegisterInterface
{

    /**
     * This method must add the routes in the collection
     * @param RouteCollection $routes
     */
    public function loadRoutes(RouteCollection $routes);
}