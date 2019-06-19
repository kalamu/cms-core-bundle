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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kalamu_cms_core');

        $rootNode->children()
                    ->arrayNode('menus')
                        ->prototype('scalar')->end()
                    ->end()
                    ->booleanNode('enable_contexts')
                        ->defaultValue(false)
                        ->info('Enable or not the contexts')
                    ->end()
                    ->scalarNode('default_context')
                        ->info("Define the default context")
                    ->end()
                    ->booleanNode('default_context_inclusive')
                        ->defaultValue(true)
                        ->info("Define if the default context include or exclude the other contexts")
                    ->end()
                    ->arrayNode('contexts')
                        ->fixXmlConfig('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('title')
                                    ->isRequired()
                                    ->info("Title of the context")
                                ->end()
                                ->scalarNode('prefix')
                                    ->isRequired()
                                    ->info("Prefix of the routes for this context")
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->scalarNode('template_search')
                        ->info("General template for search results")
                        ->defaultValue('')
                    ->end()
                    ->integerNode('results_per_page')
                        ->defaultValue(10)
                        ->info("Nombre of result per page")
                    ->end()

                    ->arrayNode('types')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                    ->info("Name of the type")
                                ->end()
                                ->scalarNode('label')
                                    ->info("Display name")
                                ->end()
                                ->scalarNode('entity')
                                    ->isRequired()
                                    ->info("Entity name")
                                ->end()
                                ->scalarNode('identifier')
                                    ->defaultValue('id')
                                    ->info("Identifier of the entity")
                                ->end()
                                ->scalarNode('stringifier')
                                    ->defaultValue('title')
                                    ->info("Attribut used to generate the stringifier")
                                ->end()
                                ->booleanNode('default_menu_item_picker')
                                    ->defaultValue(true)
                                    ->info("Generate an item picker for menu edition")
                                ->end()
                                ->scalarNode('manager')
                                    ->info("Name of the service for this type")
                                ->end()


                                ->booleanNode('searchable')
                                    ->defaultValue(true)
                                    ->info("Define if the type is searchable")
                                ->end()
                                ->booleanNode('has_index')
                                    ->defaultValue(true)
                                    ->info("Define if the type has an index page")
                                ->end()
                                ->booleanNode('has_rss')
                                    ->defaultValue(true)
                                    ->info("Define if the type has an RSS flux")
                                ->end()
                                ->integerNode('max_per_page')
                                    ->defaultValue(10)
                                    ->info("Number of item per page")
                                ->end()
                                ->integerNode('results_per_page')
                                    ->defaultValue(10)
                                    ->info("Number of item on search results per page")
                                ->end()
                                ->scalarNode('template_search')
                                    ->info("Templage path for search result")
                                ->end()
                                ->scalarNode('template_index')
                                    ->info("Template path for index")
                                ->end()
                                ->scalarNode('controller_index')
                                    ->defaultValue('KalamuCmsCoreBundle:Content:index')
                                    ->info("Controller for index page")
                                ->end()
                                ->scalarNode('controller_read')
                                    ->defaultValue('KalamuCmsCoreBundle:Content:read')
                                    ->info("Controller for individual page")
                                ->end()
                                ->scalarNode('controller_rss')
                                    ->defaultValue('KalamuCmsCoreBundle:Content:rss')
                                    ->info("Controller for RSS flux")
                                ->end()
                                ->scalarNode('template')
                                    ->info("Default template for individual (template name or path)")
                                ->end()
                                ->arrayNode('templates')
                                    ->fixXmlConfig('name')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('title')
                                                ->isRequired()
                                                ->info("Display name of the template")
                                            ->end()
                                            ->scalarNode('template')
                                                ->info("Path to the template")
                                                ->isRequired()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->info("List of templates if the entity implements TemplateableInterface")
                                ->end()
                                ->arrayNode('contexts')
                                    ->fixXmlConfig('name')
                                    ->prototype('array')
                                        ->children()
                                            ->booleanNode('has_index')
                                                ->info("Define if the type has an index page")
                                            ->end()
                                            ->booleanNode('has_rss')
                                                ->info("Define if the type has an RSS flux")
                                            ->end()
                                            ->integerNode('max_per_page')
                                                ->info("Number of item per page")
                                            ->end()
                                            ->scalarNode('template_index')
                                                ->info("Template path for index")
                                            ->end()
                                            ->scalarNode('controller_index')
                                                ->info("Controller for index page")
                                            ->end()
                                            ->scalarNode('controller_read')
                                                ->info("Controller for individual page")
                                            ->end()
                                            ->scalarNode('controller_rss')
                                                ->info("Controller for RSS flux")
                                            ->end()
                                            ->scalarNode('template')
                                                ->info("Default template for individual (template name or path")
                                            ->end()
                                            ->arrayNode('templates')
                                                ->fixXmlConfig('name')
                                                ->prototype('array')
                                                    ->children()
                                                        ->scalarNode('title')
                                                            ->isRequired()
                                                            ->info("Display name of the template")
                                                        ->end()
                                                        ->scalarNode('template')
                                                            ->info("Path to the template")
                                                            ->isRequired()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                                ->info("List of templates if the entity implements TemplateableInterface")
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->beforeNormalization()
                    ->ifArray()
                    ->then(function ($config) {

                        // Delete contexts if not enabled
                        if(!isset($config['enable_contexts']) || !$config['enable_contexts']){
                            unset($config['contexts']);
                            foreach($config['types'] as $i => $type){
                                if(isset($config['types'][$i]['contexts'])){
                                    unset($config['types'][$i]['contexts']);
                                }
                            }
                        }

                        foreach($config['types'] as $i => $type){
                            if(!isset($type['label']) || !$type['label']){
                                $config['types'][$i]['label'] = $type['name'];
                            }
                        }

                        return $config;
                    })
                ->end()

                ->validate()
                    ->always()
                        ->then(function($config){
                            // Default template for search if not defined by type
                            foreach($config['types'] as $i => $type){
                                if(!isset($type['template_search'])){
                                    $config['types'][$i]['template_search'] = $config['template_search'];
                                }
                            }

                            // context not enabled
                            if(isset($config['enable_contexts']) && $config['enable_contexts']){
                                $defined_contexts = array_keys($config['contexts']);
                                foreach($config['types'] as $type){
                                    foreach(array_keys($type['contexts']) as $context){
                                        if(!in_array($context, $defined_contexts)){
                                            throw new \InvalidArgumentException(sprintf("The '%s' context specified on the '%s' type has not been defined.", $context, $type['name']));
                                        }
                                    }
                                }

                                if(!isset($config['default_context'])){
                                    foreach($config['contexts'] as $name => $context){
                                        if('/' === $context['prefix']){
                                            $config['default_context'] = $name;
                                            break;
                                        }
                                    }
                                }
                                if(!isset($config['default_context'])){
                                    throw new \InvalidArgumentException(sprintf("The default context must be defined."));
                                }
                            }

                            return $config;
                        })
                    ->end()
                ->end()
                ;

        return $treeBuilder;
    }
}
