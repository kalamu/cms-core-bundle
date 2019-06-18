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

use Kalamu\CmsCoreBundle\Model\PublishStatusInterface;

trait PublishStatusTrait
{

    protected $publish_status;

    /**
     * Set publish_status
     *
     * @param \Kalamu\CmsCoreBundle\Model\PublishStatusInterface $publish_status
     *
     */
    public function setPublishStatus(PublishStatusInterface $publish_status = null)
    {
        $this->publish_status = $publish_status;

        return $this;
    }

    /**
     * Get publish_status
     *
     * @return \Kalamu\CmsCoreBundle\Model\PublishStatusInterface
     */
    public function getPublishStatus()
    {
        return $this->publish_status;
    }

}
