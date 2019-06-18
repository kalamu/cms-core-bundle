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

interface GeoLocalizableInterface
{

    /**
     * Gel latitude
     *
     * @return decimal
     */
    public function getLatitude();

    /**
     * Set latitude
     *
     * @param decimal $latitude
     */
    public function setLatitude($latitude);

    /**
     * Get longitude
     *
     * @return decimal
     */
    public function getLongitude();

    /**
     * Set longitude
     *
     * @param decimal $longitude
     */
    public function setLongitude($longitude);

    /**
     * Get SRID
     *
     * @return int
     */
    public function getSrid();

    /**
     * Set SRID
     *
     * @param int $srid
     */
    public function setSrid($srid);

}