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

trait GeoLocalizableTrait
{

    /**
     * @var decimal
     */
    protected $latitude;

    /**
     * @var decimal
     */
    protected $longitude;

    /**
     * @var int
     */
    protected $srid;

    /**
     * Gel latitude
     *
     * @return decimal
     */
    public function getLatitude(){
        return $this->latitude;
    }

    /**
     * Set latitude
     *
     * @param decimal $latitude
     */
    public function setLatitude($latitude){
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return decimal
     */
    public function getLongitude(){
        return $this->longitude;
    }

    /**
     * Set longitude
     *
     * @param decimal $longitude
     */
    public function setLongitude($longitude){
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get SRID
     *
     * @return int
     */
    public function getSrid(){
        return $this->srid;
    }

    /**
     * Set SRID
     *
     * @param int $srid
     */
    public function setSrid($srid){
        $this->srid = $srid;

        return $this;
    }

}