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

class RedirectCmsLinkException extends \Exception
{

    protected $cms_link;

    protected $parameters;

    protected $referenceType;

    public function __construct(array $cms_link, $parameters = [], $referenceType = null) {
        $this->cms_link = $cms_link;
        $this->parameters = $parameters;
        $this->referenceType = $referenceType;
    }

    public function getCmsLink() {
        return $this->cms_link;
    }

    public function getParameters(){
        return $this->parameters;
    }

    public function getReferenceType(){
        return $this->referenceType;
    }
}
