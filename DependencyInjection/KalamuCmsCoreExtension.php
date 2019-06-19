<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KalamuCmsCoreExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if(isset($config['home_route'])){
            $container->setParameter('kalamu_cms_core.home_route', $config['home_route']);
        }

        $container->setParameter('kalamu_cms_core.menus', $config['menus']);
        $container->setParameter('kalamu_cms_core.template_search', $config['template_search']);
        $container->setParameter('kalamu_cms_core.results_per_page', $config['results_per_page']);
        $container->setParameter('kalamu_cms_core.activated_types', $config['types']);
        $container->setParameter('kalamu_cms_core.enable_contexts', $config['enable_contexts']);
        $container->setParameter('kalamu_cms_core.default_context', isset($config['default_context']) ? $config['default_context'] : null);
        $container->setParameter('kalamu_cms_core.default_context_inclusive', $config['default_context_inclusive']);
        if(isset($config['contexts'])){
            $container->setParameter('kalamu_cms_core.contexts', $config['contexts']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('services/managers.yml');
    }



    public function prepend(ContainerBuilder $container) {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
        $bundles = $container->getParameter('kernel.bundles');

        // Configuration of StofDoctrineExtension
        if(isset($bundles['StofDoctrineExtensionsBundle'])){
            $this->prependConfig($container, 'doctrine', $this->getDoctrineConfig());
            $this->prependConfig($container, 'stof_doctrine_extensions', $this->getStofDoctrineExtensionsConfig());
        }

        // Configuration of LiipImagineBundle
        if(isset($bundles['LiipImagineBundle'])){
            $this->prependConfig($container, 'liip_imagine', $this->getLiipImagineConfig());
        }

        // Configuration of FOSElasticaBundle
        if(isset($bundles['FOSElasticaBundle'])){
            $this->prependConfig($container, 'fos_elastica', $this->getFOSElasticaConfig($config));
        }
    }



    /**
     * @return array
     */
    protected function getStofDoctrineExtensionsConfig(){
        return array(
            'orm'   => array(
                'default' => array(
                    'sluggable'     => true,
                    'timestampable' => true,
                    'blameable'     => true,
                    'loggable'      => true,
                )
            )
        );
    }

    /**
     * @return array
     */
    protected function getDoctrineConfig(){
        return array(
            'orm'   => array(
                'entity_managers' => array(
                    'default'   => array(
                        'mappings' => array(
                            'gedmo_loggable'    => array(
                                'type'      => 'annotation',
                                'prefix'    => 'Gedmo\Loggable\Entity',
                                'dir'       => '%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Loggable/Entity',
                                'alias'     => 'GedmoLoggable',
                                'is_bundle' => false
                            )
                        )
                    )
                )
            )
        );
    }

    /**
     * @return array
     */
    protected function getLiipImagineConfig(){
        return array(
            'filter_sets'   => array(
                'admin_thumb' => array(
                    'quality' => 75,
                    'filters' => array(
                        'thumbnail' => array(
                            'size' => array(180, 130),
                            'mode' => 'outbound'
                        )
                    )
                ),
        ));
    }

    /**
     * @return array
     */
    protected function getFOSElasticaConfig($config){
        $fos_elastica_config = array(
            'indexes'   => array(
                'kalamu'    => array(
                    'finder'    => '~',
                    'types'     => array()
                )
            )
        );

        foreach($config['types'] as $type){
            if(!$type['searchable']){
                continue;
            }

            $fos_elastica_config['indexes']['kalamu']['types'][$type['name']] = array(
                'mappings' => ['id' => '~'],
                'persistence' => [
                    'driver'    => 'orm',
                    'model'     => $type['entity'],
                    'provider'  => null,
                    'listener'  => null,
                    'finder'    => null
                ]
            );
        }
        return $fos_elastica_config;
    }

    /**
     * Update the configuration of a bundle
     *
     * @param ContainerBuilder $container
     * @param string $config_root
     * @param array $config
     */
    protected function prependConfig(ContainerBuilder $container, $config_root, $config){
        foreach ($container->getExtensions() as $name => $extension) {
            if ($name == $config_root) {
                $container->prependExtensionConfig($config_root, $config);
            }
        }
    }

}
