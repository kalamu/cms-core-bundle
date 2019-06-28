<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\ElasticaBundle\Event\TransformEvent;
use Kalamu\CmsCoreBundle\Manager\ContentTypeManager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

/**
 * Listener to update documents in elasticsearch
 */
class ElasticSearchListener implements EventSubscriberInterface
{

    /**
     * @var \Kalamu\CmsCoreBundle\Manager\ContentTypeManager
     */
    protected $typeManager;

    /**
     *
     * @var \Symfony\Bundle\TwigBundle\TwigEngine
     */
    protected $templating;

    /**
     *
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $request_stack;

    public function __construct(ContentTypeManager $typeManager, TwigEngine $Templating, RequestStack $RequestStack)
    {
        $this->typeManager = $typeManager;
        $this->templating = $Templating;
        $this->request_stack = $RequestStack;
    }

    public function setProperties(TransformEvent $Event)
    {
        $object = $Event->getObject();
        $document = $Event->getDocument();

        try{
            $manager = $this->typeManager->getManagerForContent($object);
        }catch(\Exception $e){
            return null;
        }

        $document->set('_kalamu_type', $manager->getName());
        $document->set('_kalamu_identifier', $manager->getObjectIdentifier($object));

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach(array('title', 'slug', 'resume') as $property){
            if($accessor->isReadable($object, $property)){
                $document->set($property, $accessor->getValue($object, $property));
            }
        }

        foreach(array('published_at', 'published_until') as $property){
            if($accessor->isReadable($object, $property)){
                $value = $accessor->getValue($object, $property);
                $document->set($property, $value ? $value->format('Y-m-d') : null);
            }
        }

        if($accessor->isReadable($object, 'content')){
            if(!$this->request_stack->getMasterRequest()){
                $this->request_stack->push(Request::create('/'));
            }

            $content = $this->templating->render('KalamuCmsCoreBundle:Content:_content.html.twig', array('entity' => $object));
            $document->set('content', strip_tags($content));
        }

        if($accessor->isReadable($object, 'publish_status')){
            $PublishStatus = $accessor->getValue($object, 'publish_status');
            $document->set('publish_status', strval($PublishStatus));
            $document->set('_kalamu_published', ($PublishStatus ? $PublishStatus->getVisible() : false));
        }

        if($accessor->isReadable($object, 'context_publication')){
            $contexts = $accessor->getValue($object, 'context_publication');
            $document->set('_kalamu_contexts', array_map(function($context) {
                return $context->getName();
            }, $contexts->toArray()));
        }

        if($accessor->isReadable($object, 'terms')){
            $terms = $accessor->getValue($object, 'terms');
            $document->set('terms', array_map('strval', $terms->toArray()));
        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            TransformEvent::POST_TRANSFORM => 'setProperties',
        );
    }
}