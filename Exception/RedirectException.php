<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\Exception;

class RedirectException extends \Exception
{

    protected $route;
    protected $parameters;
    protected $referenceType;

    public function __construct($route, $parameters = array(), $referenceType = null) {
        $this->route = $route;
        $this->parameters = $parameters;
        $this->referenceType = $referenceType;
    }

    public function getRoute() {
        return $this->route;
    }

    public function getParameters() {
        return $this->parameters ?: [];
    }

    public function getReferenceType(){
        return $this->referenceType;
    }

}
