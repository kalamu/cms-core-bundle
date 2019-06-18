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

interface CaracterizableInterface
{

    /**
     * Set metas
     *
     * @param array $metas
     */
    public function setMetas($metas);

    /**
     * Get metas
     *
     * @return array
     */
    public function getMetas();

    /**
     * Set meta
     *
     * @param string $key
     * @param mixed $value
     */
    public function setMeta($key, $value);

    /**
     * get meta
     *
     * @param string $key
     */
    public function getMeta($key);

    /**
     * detect meta existance
     *
     * @param string $key
     */
    public function hasMeta($key);

    /**
     * remove meta
     *
     * @param string $key
     */
    public function removeMeta($key);

}