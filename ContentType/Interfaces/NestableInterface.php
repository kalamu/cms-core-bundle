<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\ContentType\Interfaces;

interface NestableInterface {

    public function addChild($child);

    public function removeChild($child);

    public function getChildren();

    public function setParent($parent = null);

    public function getParent();
}
