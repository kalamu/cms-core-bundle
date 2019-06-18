<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle\Twig;

use Kalamu\DashboardBundle\Model\AbstractConfigurableElement;
use Kalamu\DashboardBundle\Model\AbstractElement;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;

class KalamuExtension extends \Twig_Extension
{

    protected $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('render_dashboard_element', array($this, 'renderElement'), ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_link_url', array($this, 'cmsLinkUrl')),
        );
    }

    public function renderElement($context, $type, $name, array $formParameters = [], array $additionnalParameters = [])
    {
        $element = $this->container->get('kalamu_dashboard.element_manager')->getElement($context, $type, $name);

        $params = array();
        if($element instanceof AbstractConfigurableElement){
            $form = $this->getConfigForm($element, 'show');

            $datas = $this->extractFormDatas($formParameters, $form->getName());
            $form->submit( $datas );
            if($form->isValid()){
                $params = $form->getData();
            }else{
                return json_encode(array('error' => $this->container->get('translator')->trans('element.parameters.invalid.error', array(), 'kalamu') ));
            }
        }

        $element->setParameters( array_merge($additionnalParameters, $params) );

        return $element->render( $this->container->get('templating'), 'publish' );
    }

    public function cmsLinkUrl(array $infos, $parameters = array(), $referenceType = null){
        return $this->container->get('kalamu_cms_core.link_manager')->generateUrl($infos, $parameters, $referenceType);
    }


    /**
     * Get the config form of an element
     *
     * @param AbstractElement $element
     * @param string $intention
     * @return Form
     */
    protected function getConfigForm(AbstractElement $element, $intention){
        if($element instanceof AbstractConfigurableElement){

            $baseForm = $this->container->get('form.factory')->create(FormType::class, null, array('csrf_protection' => false));
            $form = $element->getForm( $baseForm ) ?: $baseForm;
            if(is_string($form)){
                $form = $this->container->get('form.factory')->create($form, null, array('csrf_protection' => false));
            }
            if(!$form instanceof Form){
                throw new \Exception(sprintf("Method getForm of element '%s' must return a Form instance: %s given", $element->getTitle(), is_object($form) ? get_class($form) : gettype($form) ));
            }

        }else{
            $form = $this->container->get('form.factory')->create(FormType::class, null, array('csrf_protection' => false));
        }

        return $form;
    }


    /**
     * Extract form data from parameters
     *
     * @param array $parameters
     * @param string $form_name
     * @return array
     */
    protected function extractFormDatas(array $parameters, $form_name){
        $datas = [];

        foreach($parameters as $parameter){
            if(0 !== strpos($parameter['name'], $form_name.'[')){
                continue;
            }

            $datas[] = $parameter['name'].'='.urlencode($parameter['value']);
        }

        parse_str(implode('&', $datas), $output);
        return $output[$form_name];
    }
}
