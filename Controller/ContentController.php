<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\Controller;

use Elastica\Exception\Connection\HttpException;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Kalamu\CmsCoreBundle\Exception\RedirectCmsLinkException;
use Kalamu\CmsCoreBundle\Exception\RedirectException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ContentController extends Controller
{

    /**
     * Homepage
     * @param Request $Request
     * @throws type
     */
    public function homepageAction(Request $Request){
        $homepage = $this->get('kalamu_dynamique_config')->get('cms_main_config[homepage_content]');
        if(!$homepage){
            throw $this->createNotFoundException("The home page has not yet been defined in configuration interface.");
        }

        $manager = $this->get('roho_cms.content_type.manager')->getType($homepage['type']);
        if(isset($homepage['identifier']) && $homepage['identifier']){

            return $this->forward($manager->getControllerRead($homepage['context']), array(
                'type'          => $homepage['type'],
                'identifier'    => $homepage['identifier'],
                'context'       => $homepage['context'],
                'is_home'       => true
            ));
        }else{
            return $this->forward($manager->getControllerIndex($homepage['context']), array(
                'type'          => $homepage['type'],
                'context'       => $homepage['context'],
                'is_home'       => true
            ));
        }

    }

    public function readAction(Request $Request, $type, $identifier, $context = null){
        $manager = $this->get('roho_cms.content_type.manager')->getType($type);
        $content = $manager->getPublicContent($identifier, $context);
        if(!$content){
            if($Request->attributes->getBoolean('is_home', false)){

                // For the home page it's allowed to not specify the context
                $baseQuery = $manager->getBasePublicQuery($context, false);
                $content = $baseQuery->andWhere('c.'.$manager->getIdentifier().' = :identifier')
                        ->setParameter(':identifier', $identifier)
                        ->getQuery()
                        ->getOneOrNullResult();

            }

            if(!$content){
                throw $this->createNotFoundException();
            }
        }

        try{
            return $this->render($manager->getTemplateFor($content, $context), array('content' => $content, 'manager' => $manager));
        }catch(\Twig_Error_Runtime $e){
            if($e->getPrevious() instanceof RedirectException){
                return $this->redirectToRoute($e->getPrevious()->getRoute(), $e->getPrevious()->getParameters());
            }elseif($e->getPrevious() instanceof RedirectCmsLinkException){
                $exception = $e->getPrevious();
                $url = $this->get('roho_cms.link_manager')->generateUrl($exception->getCmsLink(), $exception->getParameters(), $exception->getReferenceType());
                return $this->redirect($url);
            }else{
                throw $e;
            }
        }
    }

    public function readTermAction(Request $Request, $identifier){
        $master_manager = $this->get('roho_cms.content_type.manager');
        $manager = $master_manager->getType('term');
        $term = $manager->getPublicContent($identifier);
        $page = $Request->query->getInt('page', 1);
        if(!$term){
            throw $this->createNotFoundException();
        }

        $content_type = $term->getTaxonomy()->getApplyOn();
        if(count($content_type)>1){
            throw $this->createNotFoundException("Unable to guess the content type");
        }

        $manager_content = $master_manager->getType(current($content_type));
        $queryBuilder = $manager_content->getQueryBuilderForIndex($Request);
        $queryBuilder->leftJoin('c.terms', 'term')
                ->andWhere('term.id = :id_term')
                ->setParameter('id_term', $term->getId());
        $paginator = $this->get('knp_paginator')->paginate($queryBuilder, $page, $manager_content->maxPerPage());
        if($page>1 && $page>$paginator->getPageCount()){
            throw $this->createNotFoundException("There is not that much page.");
        }

        return $this->render($manager->getTemplateFor($term), array(
            'term' => $term,
            'paginator' => $paginator,
            'master_manager' => $master_manager,
            'manager_term' => $manager,
            'manager_content' => $manager_content));
    }

    public function indexAction(Request $Request, $type, $context = null){
        $manager = $this->get('roho_cms.content_type.manager')->getType($type);
        $page = $Request->query->getInt('page', 1);

        $queryBuilder = $manager->getQueryBuilderForIndex($Request, $context);
        $paginator = $this->get('knp_paginator')->paginate($queryBuilder, $page, $manager->maxPerPage());
        if($page>1 && $page>$paginator->getPageCount()){
            throw $this->createNotFoundException("There is not that much page.");
        }

        return $this->render($manager->getTemplateIndex($context), array('paginator' => $paginator, 'manager' => $manager));
    }

    public function rssAction(Request $Request, $type, $context = null){
        $manager = $this->get('roho_cms.content_type.manager')->getType($type);

        $contents = $manager->getQueryBuilderForIndex($Request, $context)->setMaxResults(100)->getQuery()->getResult();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/rss+xml; charset=UTF-8');
        return $this->render('RohoCmsBundle:SEO:rss.xml.twig', array('contents' => $contents, 'manager' => $manager, 'label' => $manager->getLabel()), $response);
    }

    public function sitemapAction(){
        $master_manager = $this->get('roho_cms.content_type.manager');

        $response = new Response();
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');
        return $this->render('RohoCmsBundle:SEO:sitemap.xml.twig', array('master_manager' => $master_manager), $response);
    }

    public function robotsAction(){
        $allow = $this->get('kalamu_dynamique_config')->get('cms_main_config[search_engine_allow]', false);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain; charset=UTF-8');
        return $this->render('RohoCmsBundle:SEO:robots.txt.twig', array('allow' => $allow), $response);
    }

    /**
     * Gère les recherche avec elastic search
     * @param Request $Request
     * @return type
     */
    public function searchAction(Request $Request){
        if(!$Request->query->has('q') || '' === $Request->query->get('q')){ // pas de recherche
            return $this->redirectToRoute('cms_homepage');
        }

        if($Request->query->has('type') && $this->get('roho_cms.content_type.manager')->hasType($Request->query->get('type'))){
            $content_type = $Request->query->get('type');
            $manager = $this->get('roho_cms.content_type.manager')->getType($content_type);
        }else{
            $content_type = null;
        }

        if($content_type){
            /* @var $finder TransformedFinder */
            $finder = $this->container->get('fos_elastica.finder.search.'.$content_type);
        }else{
            $finder = $this->container->get('fos_elastica.finder.kalamu');
        }

        $results = $finder->createPaginatorAdapter($Request->query->get('q'));
        try{
            $paginator = $this->get('knp_paginator')->paginate($results,
                $Request->query->getInt('page', 1),
                ($content_type ? $manager->resultsPerPage() : $this->getParameter('roho_cms.results_per_page')));
        }catch(HttpException $e){
            if($this->getParameter('kernel.debug')){
                throw $e;
            }else{
                throw new \Exception("Internal error - Search functionaly is not available for the moment.");
            }
        }

        return $this->render($content_type ? $manager->getTemplateSearch() : $this->getParameter('roho_cms.template_search'), array(
            'paginator'     => $paginator,
            'q'             => $Request->query->get('q')
        ));
    }
}