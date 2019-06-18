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

use Kalamu\CmsCoreBundle\Model\PublishStatusInterface as ModelPublishStatus;

interface PublishStatusInterface
{

    public function getPublishStatus();

    public function setPublishStatus(ModelPublishStatus $PublishStatus = null);

}
