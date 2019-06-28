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

use Kalamu\CmsCoreBundle\Manager\Interfaces\ContentManagerInterface;
use Kalamu\CmsCoreBundle\ContentType\Interfaces\TemplateableInterface;
use Kalamu\CmsCoreBundle\ContentType\Interfaces\ContextualizableInterface;
use Kalamu\CmsCoreBundle\Manager\ContextManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Kalamu\CmsCoreBundle\Entity\Term;

/**
 * Default manager for content managment
 */
class ContentManager implements ContentManagerInterface
{

    /**
     * Entity Manager
     * @var \Doctrine\ORM\EntityManager
     */
    protected $doctrine;

    /**
     * Router service
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * Context manager
     * @var Kalamu\CmsCoreBundle\Manager\ContextManager
     */
    protected $contextManager;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * Attribut used for __toString()
     * @var string
     */
    protected $stringifier;

    /**
     * Main configuration options
     * @var array
     */
    protected $mainConfiguration;

    /**
     * Configuration of the contexts
     * @var array
     */
    protected $contextConfiguration = [];

    public function setDoctrine(Registry $doctrine){
        $this->doctrine = $doctrine->getManager();
    }

    public function setRouter(Router $router){
        $this->router = $router;
    }

    public function setContextManager(ContextManager $ContextManager){
        $this->contextManager = $ContextManager;
    }

    /****************************************
     *    Configuration of the manager      *
     ****************************************/

    /**
     * Define the main configuration for the manager
     * @param array $options
     */
    public function setMainConfiguration(array $options){

        $resolver = new OptionsResolver();
        $this->configureBaseOptions($resolver);
        $config = $resolver->resolve($options);

        foreach($config as $key => $val){
            $this->$key = $config[$key];
        }

    }

    /**
     * Configuraton of the main options
     * @param OptionsResolver $resolver
     */
    public function configureBaseOptions(OptionsResolver $resolver){
        $resolver->setRequired(array(
            'name', 'label', 'entity', 'identifier', 'stringifier',
        ));
    }


    /**
     * Define the configuration for the given context
     * @param array $options
     * @param string $context
     */
    public function setContextConfiguration(array $options, $context = null){
        $resolver = new OptionsResolver();
        $this->configureContextOptions($resolver);

        if($context){
            if(!$this->getReflectionClass()->implementsInterface('Kalamu\CmsCoreBundle\ContentType\Interfaces\ContextualizableInterface')){
                throw new \InvalidArgumentException(sprintf("You cannot set context configuration on '%s' as the underlying entity (%s) doesn't implements %s.",
                        $this->name, $this->entity, 'Kalamu\CmsCoreBundle\ContentType\Interfaces\ContextualizableInterface'));
            }

            $this->contextConfiguration[$context] = $resolver->resolve($options);
        }else{
            $this->mainConfiguration = $resolver->resolve($options);
        }
    }

    /**
     * Configure the options for context configuration
     * @param OptionsResolver $resolver
     */
    public function configureContextOptions(OptionsResolver $resolver){
        $resolver->setDefined(array(
            'has_index', 'has_rss', 'max_per_page', 'template_index', 'controller_index',
            'controller_read', 'controller_rss', 'template', 'templates', 'title',
            'searchable', 'results_per_page', 'template_search'
        ));

        if($this->mainConfiguration){
           $resolver->setDefaults($this->mainConfiguration);
        }
    }


    /****************************************
     * Getter of non contextualised options *
     ****************************************/


    /**
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel(){
        return $this->label;
    }

    /**
     * @return string
     */
    public function getEntityName(){
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getIdentifier(){
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getStringifier(){
        return $this->stringifier;
    }

    /**
     * @return array
     */
    public function getContexts(){
        return is_array($this->contextConfiguration) ? array_keys($this->contextConfiguration) : array();
    }

    /**
     * @return \Kalamu\CmsCoreBundle\Manager\ContextManager
     */
    public function getContextManager(){
        return $this->contextManager;
    }



    /**************************************
     * Getters for contextalised options *
     **************************************/

    /**
     * Check if this content is searchable
     * @param string $context
     * @return boolean
     */
    public function isSearchable($context = null){
        return $this->getContextConfiguration($context, 'searchable');
    }

    /**
     * Check if has an index page
     * @return boolean
     */
    public function hasIndex($context = null){
        return $this->getContextConfiguration($context, 'has_index');
    }

    /**
     * Check if has an RSS flux
     * @return boolean
     */
    public function hasRss($context = null){
        return $this->getContextConfiguration($context, 'has_rss');
    }

    /**
     * Get the number of element per page
     * @return int
     */
    public function maxPerPage($context = null){
        return $this->getContextConfiguration($context, 'max_per_page');
    }

    /**
     * Get the number of element per search result page
     * @return int
     */
    public function resultsPerPage($context = null){
        return $this->getContextConfiguration($context, 'results_per_page');
    }

    /**
     * Get the template for search page
     * @param type $context
     */
    public function getTemplateSearch($context = null){
        return $this->getContextConfiguration($context, 'template_search');
    }

    /**
     * Get the template for index page
     * @param type $context
     */
    public function getTemplateIndex($context = null){
        return $this->getContextConfiguration($context, 'template_index');
    }

    /**
     * Get the controller for index page
     * @param type $context
     */
    public function getControllerIndex($context = null){
        return $this->getContextConfiguration($context, 'controller_index');
    }

    /**
     * Get the template for individual page
     * @param type $context
     */
    public function getTemplate($context = null){
        return $this->getContextConfiguration($context, 'template');
    }

    /**
     * Get the list of template available for individual page
     * @param type $context
     */
    public function getTemplates($context = null){
        return $this->getContextConfiguration($context, 'templates');
    }

    /**
     * Get the controller for individual page
     * @param type $context
     */
    public function getControllerRead($context = null){
        return $this->getContextConfiguration($context, 'controller_read');
    }

    /**
     * Get the controller for RSS flux
     * @param type $context
     */
    public function getControllerRss($context = null){
        return $this->getContextConfiguration($context, 'controller_rss');
    }

    public function getContextTitle($context){
        return $this->getContextConfiguration($context, 'title');
    }

    protected function getContextConfiguration($context, $option){
        if($context && isset($this->contextConfiguration[$context])){
            return $this->contextConfiguration[$context][$option];
        }
        return $this->mainConfiguration[$option];
    }

    /**
     * Get the template for the given content
     * @param type $object
     */
    public function getTemplateFor($object, $context = null) {
        if($object instanceof TemplateableInterface && $object->getTemplate()){
            $templates = $this->getContextConfiguration($context, 'templates');
            if(isset($templates[$object->getTemplate()])){
                return $templates[$object->getTemplate()]['template'];
            }
        }

        return $this->getContextConfiguration($context, 'template');
    }




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
    public function getPublicReadLink($object, $parameters = array(), $referenceType = null) {

        if($this->contextManager){
            // If the contexts are active and the content is contextisable and that no specific context is requested
            if($object instanceof ContextualizableInterface && !isset($parameters['_context'])){
                $parameters['_context'] = $this->getBestContentContext($object);
            }elseif($object instanceof Term){
                $parameters['_context'] = $this->contextManager->getCurrentContext();
            }
        }

        if(isset($parameters['_context']) && !$parameters['_context']){
            unset($parameters['_context']);
        }

        return $this->contextualiseRoute('read_'.$this->name, array_merge(array(
            'identifier' => $this->getObjectIdentifier($object)
        ), $parameters), $referenceType);
    }

    /**
     * Get the public index link
     * @param array $parameters
     * @param int $referenceType
     * @return string
     */
    public function getPublicIndexLink($parameters = array(), $referenceType = null){
        return $this->contextualiseRoute('index_'.$this->name, $parameters, $referenceType);
    }

    /**
     * Get the public link for RSS flux
     * @param array $parameters
     * @param int $referenceType
     * @return string
     */
    public function getPublicRssLink($parameters = array(), $referenceType = null){
        return $this->contextualiseRoute('rss_'.$this->name, $parameters, $referenceType);
    }

    protected function contextualiseRoute($base_route, $parameters = array(), $referenceType = null){
        if(isset($parameters['_context']) && $parameters['_context']){
            $base_route = sprintf('%s_%s', $parameters['_context'], $base_route);
            unset($parameters['_context']);
        }
        return $this->router->generate($base_route, $parameters, $referenceType);
    }

    /**
     * Get the defaut context if not specified for an object
     */
    protected function getBestContentContext($object){
        $nb_context = $object->getContextPublication()->count();
        $default_inclusive = $this->contextManager->isDefaultContextInclusive();
        $default_context = $this->contextManager->getDefaultContext();
        $current_context = $this->contextManager->getCurrentContext();

        // No context defined on the content
        if(!$nb_context){
            return $default_inclusive ? $default_context : null;
        }

        // Only one context defined
        if(1 == $nb_context){
            if($current_context != $object->getContextPublication()->current()->getName() && $default_inclusive){
                return $default_context;
            }
            return $object->getContextPublication()->current()->getName();
        }

        // we take the current context if possible
        $has_current = $object->getContextPublication()->filter(function($context) use ($current_context){
            return $context->getName() == $current_context;
        })->count();
        if($has_current){
            return $current_context;
        }

        $has_default = $object->getContextPublication()->filter(function($context) use ($default_context){
            return $context->getName() == $default_context;
        })->count();
        if($default_inclusive || $has_default){
            return $default_context;
        }
        return null;
    }






    /*******************************
     *     Content selection       *
     *******************************/


    /**
     * Get the content object for the given identifier
     * @param type $identifier
     * @return type
     */
    public function getPublicContent($identifier, $context = null) {
        $baseQuery = $this->getBasePublicQuery($context);

        return $baseQuery->andWhere('c.'.$this->identifier.' = :identifier')
                ->setParameter(':identifier', $identifier)
                ->getQuery()
                ->getOneOrNullResult();
    }

    /**
     * Get base query for index page
     * @param Request $Request
     * @param type $context
     * @return type
     */
    public function getQueryBuilderForIndex(Request $Request, $context = null){
        $query = $this->getBasePublicQuery($context);

        $reflection = $this->getReflectionClass();
        if($reflection->implementsInterface('Kalamu\CmsCoreBundle\ContentType\Interfaces\PublishTimestampInterface')){
            $query->orderBy('c.published_at', 'DESC');
        }

        return $query;
    }

    /**
     * Get base query for public content
     * @param string $context
     * @param boolean $apply_context
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getBasePublicQuery($context = null, $apply_context = true){
        $baseQuery = $this->getBaseQuery();

        $reflection = $this->getReflectionClass();
        if($reflection->implementsInterface('Kalamu\CmsCoreBundle\ContentType\Interfaces\PublishStatusInterface')){
            $baseQuery->leftJoin('c.publish_status', 's')
                    ->andWhere('s.visible = TRUE');
        }
        if($reflection->implementsInterface('Kalamu\CmsCoreBundle\ContentType\Interfaces\PublishTimestampInterface')){
            $baseQuery->andWhere('c.published_at <= :date_publish')
                    ->setParameter('date_publish', new \DateTime('now'));
        }
        if($reflection->implementsInterface('Kalamu\CmsCoreBundle\ContentType\Interfaces\PublishUntilTimestampInterface')){
            $baseQuery->andWhere('c.published_until IS NULL OR c.published_until > :date_until')
                    ->setParameter('date_until', new \DateTime('now'));
        }
        // As long as it's not defined whe apply contexts
        if(false !== $apply_context && $reflection->implementsInterface('Kalamu\CmsCoreBundle\ContentType\Interfaces\ContextualizableInterface')){
            // If the context is not applied and that the content has context, whe take the default one
            if(!$context && count($this->contextConfiguration)){
                $context = $this->contextManager->getDefaultContext();
            }

            if($context){
                $this->appendContextClause($baseQuery, $context);
            }
        }

        return $baseQuery;
    }

    /**
     * Append the context restriction on the query
     * @param QueryBuilder $baseQuery
     * @param string $context
     */
    public function appendContextClause(QueryBuilder $baseQuery, $context = null){
        if(!$this->contextManager || !$this->getReflectionClass()->implementsInterface('Kalamu\CmsCoreBundle\ContentType\Interfaces\ContextualizableInterface')){
            return;
        }

        $default_inclusive = $this->contextManager->isDefaultContextInclusive();
        $default_context = $this->contextManager->getDefaultContext();
        $current_context = $this->contextManager->getCurrentContext();

        $context = $context ? $context : ($current_context ? $current_context : $default_context);

        // If there is a specified context and : it's not the default one OR the default one is not inclusive
        if($context && ($context != $default_context || !$default_inclusive)){
            $baseQuery->leftJoin('c.context_publication', 'context')
                    ->andWhere('context.name = :context')
                    ->setParameter('context', $context);
        }elseif(!$context && !$default_inclusive){
            // We take thoses without context
            $baseQuery
                    ->leftJoin('c.context_publication', 'context')
                    ->andWhere('context.id IS NULL');
        }
    }

    /**
     * Get the defaut context for an object
     * @param type $object
     */
    public function getObjectDefaultContext($object){
        if(!$this->contextManager || !$object instanceof \Kalamu\CmsCoreBundle\ContentType\Interfaces\ContextualizableInterface){
            return;
        }

        $default_context = $this->contextManager->getDefaultContext();
        $current_context = $this->contextManager->getCurrentContext();

        foreach([$current_context, $default_context] as $context_name){
            foreach($object->getContextPublication() as $context){
                if($context->getName() === $context_name){
                    return $context_name;
                }
            }
        }

        return $object->getContextPublication()->count() ? $object->getContextPublication()->first()->getName() : null;
    }

    /**
     * Get base query for every contents
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getListQuery(){
        return $this->getBaseQuery();
    }

    /**
     * Get base query for every contents
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getBaseQuery() {
        return $this->doctrine->getRepository($this->entity)
                ->createQueryBuilder('c')
                ->orderBy('c.'.$this->stringifier, 'asc');
    }

    /**
     * Get the entity attribut used as identifier
     * @param object $object
     * @return string
     */
    public function getObjectIdentifier($object){
        $method = 'get'.str_replace(' ', '', ucwords(str_replace('_', ' ', $this->identifier)));

        return $object->$method();
    }

    public function getObjectByIdentifier($identifier){
        return $this->getBaseQuery()->andWhere('c.'.$this->identifier.' = :identifier')
                ->setParameter(':identifier', $identifier)
                ->getQuery()->getOneOrNullResult();
    }

    /**
     * Get the Reflection class for the entity
     * @return \ReflectionClass
     */
    public function getReflectionClass(){
        return $this->doctrine->getMetadataFactory()->getMetadataFor($this->entity)->getReflectionClass();
    }
}