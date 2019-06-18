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

trait CaracterizableTrait
{

    /**
     * @var array
     */
    protected $metas;

    /**
     * Set metas
     *
     * @param array $metas
     */
    public function setMetas($metas){
        $this->metas = $metas;

        return $this;
    }

    /**
     * Get metas
     *
     * @return array
     */
    public function getMetas(){
        return $this->metas;
    }

    /**
     * Set meta
     *
     * @param string $key
     * @param mixed $value
     */
    public function setMeta($key, $value){
        $this->metas[$key] = $value;

        return $this;
    }

    /**
     * get meta
     *
     * @param string $key
     */
    public function getMeta($key){
        return $this->metas[$key];
    }

    /**
     * detect meta existance
     *
     * @param string $key
     */
    public function hasMeta($key){
        return array_key_exists($key, $this->metas);
    }

    /**
     * remove meta
     *
     * @param string $key
     */
    public function removeMeta($key){
        unset($this->metas[$key]);
    }

}
