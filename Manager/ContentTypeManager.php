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
use Kalamu\CmsCoreBundle\Model\ContentTypeInterface;

/**
 * Manager to register content types
 */
class ContentTypeManager
{

    /**
     * @var array
     */
    protected $managers = array();

    /**
     * @var array
     */
    protected $labels = array();

    /**
     * Register a new content type
     * @param string $name
     * @param string $label
     * @param \Kalamu\CmsCoreBundle\Manager\Interfaces\ContentManagerInterface $service
     */
    public function registerContentType($name, $label, ContentManagerInterface $service){
        $this->managers[$name] = $service;
        $this->labels[$name] = $label;
    }

    /**
     * Get the list of types
     * @return array
     */
    public function getTypes(){
        return array_keys($this->labels);
    }

    /**
     * Get the manager of a type
     * @param string $name
     * @return \Kalamu\CmsCoreBundle\Manager\Interfaces\ContentManagerInterface
     */
    public function getType($name){
        if(!isset($this->managers[$name])){
            throw new \InvalidArgumentException(sprintf("No manager for the type '%s'", $name));
        }
        return $this->managers[$name];
    }

    /**
     * Get the label of a type
     * @param string $name
     * @return string
     */
    public function getLabel($name){
        return $this->labels[$name];
    }

    /**
     * Get the list of labels
     * @return array
     */
    public function getLabels(){
        return $this->labels;
    }

    /**
     * Get the list of managers
     * @return array
     */
    public function getTypesManagers(){
        return $this->managers;
    }

    /**
     * Check if the type exists
     * @param string $name
     * @return string
     */
    public function hasType($name){
        return isset($this->managers[$name]);
    }

    /**
     * Get the manager for the given content
     * @param ContentTypeInterface $content
     * @return \Kalamu\CmsCoreBundle\Manager\Interfaces\ContentManagerInterface
     */
    public function getManagerForContent(ContentTypeInterface $content){
        foreach($this->managers as $manager){
            $class_name = $manager->getReflectionClass()->getName();
            if($content instanceof $class_name){
                return $manager;
            }
        }
        throw new \InvalidArgumentException(sprintf('No manager for class %s', get_class($content)));
    }

    /**
     * Get the manager based on content class name
     * @param string $class_name
     * @return \Kalamu\CmsCoreBundle\Manager\Interfaces\ContentManagerInterface
     */
    public function getManagerForClass($class_name){
        foreach($this->managers as $manager){
            if($class_name === $manager->getReflectionClass()->getName()){
                return $manager;
            }
        }
        throw new \InvalidArgumentException(sprintf('No manager for class %s', $class_name));
    }
}