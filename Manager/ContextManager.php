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

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Manager for the contexts
 */
class ContextManager
{

    /**
     * @var array
     */
    protected $contexts = array();

    /**
     * @var string
     */
    protected $current_context = null;

    protected $default_context = null;

    protected $default_context_inclusive;

    /**
     * Register content for the context
     * @param string $name
     * @param array $config
     */
    public function registerContext($name, array $config){
        $this->contexts[$name] = $config;

        if($this->default_context === null && '/' === $config['prefix']){
            $this->default_context = $name;
        }
    }

    /**
     * Set the default context
     * @param string $context
     */
    public function setDefaultContext($context){
        $this->default_context = $context;
    }

    /**
     * Define if the default context include all other contexts or not
     * @param bool $inclusive
     */
    public function setDefaultContextInclusive($inclusive){
        $this->default_context_inclusive = $inclusive;
    }

    /**
     * Fetch the current context form Request
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event){
        $request = $event->getRequest();
        $this->current_context = $request->attributes->get('context', null);
    }

    /**
     * Set the current context
     * @param string $context
     */
    public function setCurrentContext($context){
        $this->current_context = $context;
    }

    /**
     * Get the current context
     * @return string
     */
    public function getCurrentContext(){
        return $this->current_context ? $this->current_context : $this->default_context;
    }

    /**
     * Get list of contexts
     * @return array
     */
    public function getContexts(){
        return array_keys($this->contexts);
    }

    /**
     * Get default context
     * @return string
     */
    public function getDefaultContext(){
        return $this->default_context;
    }

    /**
     * Get the context title
     * @param string $name
     */
    public function getContextTitle($name){
        return $this->contexts[$name]['title'];
    }

    /**
     * Get routing prefix for the context
     * @param string $name
     */
    public function getContextPrefix($name){
        return $this->contexts[$name]['prefix'];
    }

    /**
     * Check if the default context include the others
     * @return bool
     */
    public function isDefaultContextInclusive(){
        return $this->default_context_inclusive;
    }

}