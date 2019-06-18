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

interface PublishUntilTimestampInterface
{

    /**
     * Set publishedUntil
     *
     * @param \DateTime $publishedUntil
     */
    public function setPublishedUntil($publishedUntil);

    /**
     * Get publishedUntil
     *
     * @return \DateTime
     */
    public function getPublishedUntil();

}