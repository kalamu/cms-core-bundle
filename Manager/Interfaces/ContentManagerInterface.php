<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\Manager\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Interface of a content manager
 *
 * All methods starting with "getPublic..." should be the ones used for frontend.
 * If not, this could lead to UNWANTED PUBLICATION.
 */
interface ContentManagerInterface
{

    /****************************************
     * CONFIGURATION OF THE MANAGER *
     ****************************************/

    /**
     * Set the main configuration of the manager
     * @param array $options
     */
    public function setMainConfiguration(array $options);

    /**
     * Define configuration options for the manager
     * @param OptionsResolver $resolver
     */
    public function configureBaseOptions(OptionsResolver $resolver);

    /**
     * Set the configuration for a context
     * @param array $options
     * @param string $context
     */
    public function setContextConfiguration(array $options, $context = null);

    /**
     * Define configuration option for the contexts
     * @param OptionsResolver $resolver
     */
    public function configureContextOptions(OptionsResolver $resolver);

    /********************************************
     * Getters for non contextualisable options *
     ********************************************/

    public function getName();

    public function getLabel();

    public function getEntityName();

    public function getIdentifier();

    public function getStringifier();

    public function getContexts();


    /*************************************
     * Getters for contextalised options *
     *************************************/

    /**
     * Check if has an index page
     * @param string $context Contexte
     * @return boolean
     */
    public function hasIndex($context = null);

    /**
     * Check if has an RSS flux
     * @param string $context Contexte
     * @return boolean
     */
    public function hasRss($context = null);

    /**
     * Get the number of element per page
     * @param string $context Contexte
     * @return int
     */
    public function maxPerPage($context = null);

    /**
     * Get the template for index page
     * @param string $context Contexte
     * @return string
     */
    public function getTemplateIndex($context = null);

    /**
     * Get the controller for index page
     * @param string $context Contexte
     * @return string
     */
    public function getControllerIndex($context = null);

    /**
     * Get the template for individual page
     * @param string $context Contexte
     * @return string
     */
    public function getTemplate($context = null);

    /**
     * Get list of available template for individual page
     * @param string $context Contexte
     * @return array
     */
    public function getTemplates($context = null);

    /**
     * Get controller for individual page
     * @param string $context Contexte
     * @return string
     */
    public function getControllerRead($context = null);

    /**
     * Get controller for RSS flux
     * @param string $context Contexte
     * @return string
     */
    public function getControllerRss($context = null);

    /**
     * Get the template for the given content
     * @param string $context Contexte
     * @return string des options contextualisées
     */
    public function getTemplateFor($object, $context = null);


    /***********************************
     *      Routing managment          *
     ***********************************/

    /**
     * Get the public link for the given content
     * @param object $object a content from the manager
     * @param array $parameters
     * @param int $referenceType
     * @return string
     */
    public function getPublicReadLink($object, $parameters = array(), $referenceType = null);

    /**
     * Get the public index link
     * @param array $parameters
     * @param int $referenceType
     * @return string
     */
    public function getPublicIndexLink($parameters = array(), $referenceType = null);

    /**
     * Get the public link for RSS flux
     * @param array $parameters
     * @param int $referenceType
     * @return string
     */
    public function getPublicRssLink($parameters = array(), $referenceType = null);



    /*******************************
     *     Content selection       *
     *******************************/


    /**
     * Get the content object for the given identifier
     * @param string $identifier
     * @param string $context
     * @return object
     */
    public function getPublicContent($identifier, $context = null);

    /**
     * Get base query for index page
     * @param Request $Request
     * @param string $context
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForIndex(Request $Request, $context = null);

    /**
     * Get base query for public content
     * @param string $context
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getBasePublicQuery($context = null);

    /**
     * Get base query for every contents
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getListQuery();

    /**
     * Get the entity attribut used as identifier
     * @param object $object
     * @return string
     */
    public function getObjectIdentifier($object);

    /**
     * Get the Reflection class for the entity
     * @return \ReflectionClass
     */
    public function getReflectionClass();
}