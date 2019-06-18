<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\ContentType\Traits;

use Kalamu\CmsCoreBundle\Model\ContextPublicationInterface;

trait ContextualizableTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $context_publication;

    /**
     * Add contextPublication
     *
     * @param \Kalamu\CmsCoreBundle\Model\ContextPublicationInterface $contextPublication
     *
     * @return Page
     */
    public function addContextPublication(ContextPublicationInterface $contextPublication)
    {
        $this->context_publication[] = $contextPublication;

        return $this;
    }

    /**
     * Remove contextPublication
     *
     * @param \Kalamu\CmsCoreBundle\Model\ContextPublicationInterface $contextPublication
     */
    public function removeContextPublication(ContextPublicationInterface $contextPublication)
    {
        $this->context_publication->removeElement($contextPublication);
    }

    /**
     * Get contextPublication
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContextPublication()
    {
        return $this->context_publication;
    }
}