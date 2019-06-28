<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * CompilerPass to register content types
 */
class ContentTypeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if($container->getParameter('kalamu_cms_core.enable_contexts')){
            $this->registerContextManager($container);
        }

        $this->registerContentManagers($container);
        $this->registerRouterContext($container);

    }

    /**
     * Configure the managers with the contexts if enabled
     * @param ContainerBuilder $container
     */
    protected function registerContextManager(ContainerBuilder $container){

        // Inject the context manager in the content manager
        $baseContentManager = $container->findDefinition('kalamu_cms_core.base_content.manager');
        $baseContentManager->addMethodCall('setContextManager', array(new Reference('kalamu_cms_core.manager.context')));

        // Make the context manager listen the requests
        $manager = $container->findDefinition('kalamu_cms_core.manager.context');
        $manager->addTag('kernel.event_listener', array(
            'event'     => 'kernel.request',
            'method'    => 'onKernelRequest'
        ));

        // Inject the context configuration in the manager
        $manager->addMethodCall('setDefaultContext', array(new Parameter('kalamu_cms_core.default_context')));
        $manager->addMethodCall('setDefaultContextInclusive', array(new Parameter('kalamu_cms_core.default_context_inclusive')));
        foreach($container->getParameter('kalamu_cms_core.contexts') as $name => $config){
            $manager->addMethodCall('registerContext', array($name, $config));
        }

    }

    /**
     * Register the content managers on the main manager
     *
     * @param ContainerBuilder $container
     * @return type
     */
    protected function registerContentManagers(ContainerBuilder $container){
        if (!$container->has('kalamu_cms_core.content_type.manager') || !$container->hasParameter('kalamu_cms_core.activated_types')) {
            return;
        }

        $managerTypes = $container->findDefinition('kalamu_cms_core.content_type.manager');
        $activated_types = $container->getParameter('kalamu_cms_core.activated_types');
        $contexts = $container->getParameter('kalamu_cms_core.contexts');

        foreach ($activated_types as $i => $type) {

            if(!isset($type['manager'])){
                $manager = new DefinitionDecorator('kalamu_cms_core.base_content.manager');
                $manager->setClass(new Parameter('kalamu_cms_core.content_type.default_manager.class'));

                $container->setDefinition('kalamu_cms_core.content_type_manager.'.$type['name'], $manager);
                $type['manager'] = 'kalamu_cms_core.content_type_manager.'.$type['name'];
                $activated_types[$i] = $type;
                $container->setParameter('kalamu_cms_core.activated_types', $activated_types);
            }else{
                $manager = $container->findDefinition( $type['manager'] );
            }

            $main_config = array_intersect_key($type, array_flip(array(
                'name', 'label', 'entity', 'identifier', 'stringifier')));
            $manager->addMethodCall('setMainConfiguration', array($main_config));

            $default_context_config = array_intersect_key($type, array_flip(array(
                'has_index', 'has_rss', 'max_per_page', 'template_index',
                'controller_index', 'template',  'controller_read',
                'controller_rss', 'templates'
                )));
            $manager->addMethodCall('setContextConfiguration', array($default_context_config));

            if(isset($type['contexts'])){
                foreach($type['contexts'] as $context => $context_configuration){
                    $context_configuration['title'] = $contexts[$context]['title'];
                    $manager->addMethodCall('setContextConfiguration', array(
                        $context_configuration,
                        $context,
                    ));
                }
            }

            $managerTypes->addMethodCall(
                'registerContentType',
                array($type['name'], $type['label'], new Reference($type['manager']))
            );
        }
    }

    /**
     * Register the contexts on the routing service
     * @param ContainerBuilder $container
     */
    protected function registerRouterContext(ContainerBuilder $container){
        if(!$container->hasParameter('kalamu_cms_core.contexts') || !$container->hasDefinition('kalamu_cms_core.router')){
            return;
        }

        $CmsRouter = $container->findDefinition('kalamu_cms_core.router');
        $CmsRouter->addMethodCall('setContexts', array($container->getParameter('kalamu_cms_core.contexts')));
    }
}