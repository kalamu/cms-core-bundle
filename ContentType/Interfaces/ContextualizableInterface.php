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

use Doctrine\Common\Collections\Collection;
use Kalamu\CmsCoreBundle\Model\ContextPublicationInterface;

interface ContextualizableInterface
{
    /**
     * Add contextPublication
     *
     * @param \Kalamu\CmsCoreBundle\Model\ContextPublicationInterface $contextPublication
     */
    public function addContextPublication(ContextPublicationInterface $contextPublication);

    /**
     * Remove contextPublication
     *
     * @param \Kalamu\CmsCoreBundle\Model\ContextPublicationInterface $contextPublication
     */
    public function removeContextPublication(ContextPublicationInterface $contextPublication);

    /**
     * Get contextPublication
     *
     * @return Collection
     */
    public function getContextPublication();
}