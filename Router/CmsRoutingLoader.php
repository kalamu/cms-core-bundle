<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\Router;

use Kalamu\CmsCoreBundle\Manager\Interfaces\RouteRegisterInterface;
use Kalamu\CmsCoreBundle\Manager\Interfaces\ContentManagerInterface;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\Loader\LoaderResolverInterface;



/**
 * Router for CMS contents
 */
class CmsRoutingLoader implements LoaderInterface {

    /**
     * Is route loaded ?
     * @var boolean
     */
    protected $loaded = false;

    /**
     * @var array
     */
    protected $contentTypeManager;

    /**
     * @var array
     */
    protected $contexts = array();

    /**
     * @var array
     */
    protected $routes = array();

    /**
     * @param \Kalamu\CmsCoreBundle\Manager\ContentTypeManager $contentTypeManager
     */
    function __construct($contentTypeManager) {
        $this->contentTypeManager = $contentTypeManager;
    }

    /**
     * @param array $contexts
     */
    public function setContexts(array $contexts){
        $this->contexts = $contexts;
    }

    public function load($resource, $type = null){
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $this->routes = new RouteCollection();

        foreach($this->contentTypeManager->getTypesManagers() as $type => $manager){
            if($manager instanceof RouteRegisterInterface){
                $manager->loadRoutes($this->routes);
            }else{
                $this->registerManagerRoutes($type, $manager);

                foreach($this->contexts as $name => $context){
                    $this->registerManagerRoutes($type, $manager, $name, $context);
                }

            }
        }

        $sitemap = new Route('sitemap.xml', array('_controller' => 'KalamuCmsCoreBundle:Content:sitemap'));
        $this->routes->add('sitemap_xml', $sitemap);

        $robots = new Route('robots.txt', array('_controller' => 'KalamuCmsCoreBundle:Content:robots'));
        $this->routes->add('robots_txt', $robots);

        $search = new Route('search', array('_controller' => 'KalamuCmsCoreBundle:Content:search'));
        $this->routes->add('search', $search);

        $homepage = new Route('/', array('_controller' => 'KalamuCmsCoreBundle:Content:homepage'));
        $this->routes->add('cms_homepage', $homepage);

        $this->loaded = true;
        return $this->routes;
    }

    protected function registerManagerRoutes($type, ContentManagerInterface $manager, $context_name = null, array $context = null){

        $readPath = $context_name ? $context['prefix'].'/'.$type.'/{identifier}' : $type.'/{identifier}';
        $defaults = array(
            '_controller'   => $manager->getControllerRead($context_name) ? $manager->getControllerRead($context_name) : "KalamuCmsCoreBundle:Content:read",
            'type'          => $type,
            'context'       => $context_name
        );

        $read = new Route($readPath, $defaults);
        $this->routes->add(($context_name ? $context_name.'_' : '').'read_'.$type, $read);

        if($manager->hasIndex($context_name)){
            $indexPath = $context_name ? $context['prefix'].'/'.$type : $type;
            $defaults = array(
                '_controller'   => $manager->getControllerIndex($context_name) ? $manager->getControllerIndex($context_name) : "KalamuCmsCoreBundle:Content:index",
                'type'          => $type,
                'context'       => $context_name
            );

            $index = new Route($indexPath, $defaults);
            $this->routes->add(($context_name ? $context_name.'_' : '').'index_'.$type, $index);
        }

        if($manager->hasRss($context_name)){
            $rssPath = $context_name ? $context['prefix'].'/rss/'.$type : 'rss/'.$type;
            $defaults = array(
                '_controller'   => $manager->getControllerRss($context_name) ? $manager->getControllerRss($context_name) : "KalamuCmsCoreBundle:Content:rss",
                'type'          => $type,
                'context'       => $context_name
            );

            $rss = new Route($rssPath, $defaults);
            $this->routes->add(($context_name ? $context_name.'_' : '').'rss_'.$type, $rss);
        }

    }


    public function supports($resource, $type = null){
        return 'kalamu_cms_core' === $type;
    }

    public function getResolver() { }

    public function setResolver(LoaderResolverInterface $resolver) { }
}
