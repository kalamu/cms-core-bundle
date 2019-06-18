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

use Kalamu\DashboardBundle\Manager\DashboardManagerInterface;
use Kalamu\CmsCoreBundle\Manager\ContentTypeManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * Dashboard used for content editing
 */
class DashboardCmsManager implements DashboardManagerInterface
{

    /**
     * @var \Symfony\Bridge\Doctrine\ManagerRegistry
     */
    protected $em;

    /**
     *
     * @var \Kalamu\CmsCoreBundle\Manager\ContentTypeManager
     */
    protected $ContentTypeManager;

    public function __construct(ManagerRegistry $doctrine, ContentTypeManager $ContentTypeManager){
        $this->em = $doctrine->getManager();
        $this->ContentTypeManager = $ContentTypeManager;
    }

    /**
     * @param string $context
     * @return array
     */
    public function getList($context = 'default'){
        return array('default' => "default");
    }

    /**
     * @param string $context
     * @param string $name
     * @return array
     */
    public function getDashboardConfig($context, $name){
        return array();
    }

    /**
     * @param string $context
     * @param string $name
     * @param array $config
     */
    public function setDashboard($context, $name, $libelle = null, $config = array()){
    }

    /**
     * Supprime un dashboard
     * @param string $context
     * @param type $name
     */
    public function remove($context, $name){
    }

}