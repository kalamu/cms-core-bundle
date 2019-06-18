<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\Manager;


/**
 * Manager used to generate links with CmsLink datas
 */
class LinkManager
{

    /**
     * @var ContentTypeManager
     */
    protected $contentTypeManager;

    public function __construct(ContentTypeManager $contentTypeManager) {
        $this->contentTypeManager = $contentTypeManager;
    }

    /**
     *
     * @param array $infos Links datas link from CmsLink
     * @param array $parameters additionnal parameters
     * @param type $referenceType
     * @return string
     */
    public function generateUrl(array $infos, $parameters = array(), $referenceType = null){
        if(!$infos['type'] && !$infos['identifier']){
            return isset($infos['url']) ? $infos['url'] : null;
        }
        $typeManager = $this->contentTypeManager->getType($infos['type']);

        $parameters['_context'] = isset($infos['context']) ? $infos['context'] : null;
        if(!isset($infos['identifier']) || !$infos['identifier']){
            return $typeManager->getPublicIndexLink($parameters);
        }

        $content = $typeManager->getPublicContent($infos['identifier'], $parameters['_context']);
        if(!$content){
            return null;
        }

        return $typeManager->getPublicReadLink($content, $parameters, $referenceType);
    }
}