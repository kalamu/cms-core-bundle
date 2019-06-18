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

trait NestableTrait {

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $children;

    protected $parent;

    /**
     * Add child
     *
     * @param object $child
     *
     * @return Term
     */
    public function addChild($child)
    {
        $this->checkEntityType($child, __METHOD__);
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param object $child
     */
    public function removeChild($child)
    {
        $this->checkEntityType($child, __METHOD__);
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param object $parent
     *
     * @return Term
     */
    public function setParent($parent = null)
    {
        if($parent){
            $this->checkEntityType($parent, __METHOD__);
        }
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return object
     */
    public function getParent()
    {
        return $this->parent;
    }

    private function checkEntityType($entity, $methodName){
        if(!$entity instanceof $this){
            throw new \InvalidArgumentException(sprintf("%s attend un paramètre de type %s : %s fourni", $methodName, get_class($this), get_class($entity)));
        }
    }
}
