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

use Kalamu\CmsCoreBundle\ContentType\Traits\PublishStatusTrait;

trait PublishTimestampTrait
{

    use PublishStatusTrait;

    protected $published_at;

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     */
    public function setPublishedAt($publishedAt)
    {
        $this->published_at = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }


    public function updatePublishTimestamp(){
        if($this->published_at){
            if(!$this->getPublishStatus() || !$this->getPublishStatus()->getVisible()){
                $this->published_at = null;
            }
        }else{
            if($this->getPublishStatus() && $this->getPublishStatus()->getVisible()){
                $this->published_at = new \DateTime('now');
            }
        }
    }
}