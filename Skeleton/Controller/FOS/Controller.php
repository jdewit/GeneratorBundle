<?php

namespace {{ bundle_namespace }}\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * {{ entity }} controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 * @Route("/{{ entity_cc }}")
 */
class {{ entity }}Controller extends ContainerAware
{
    /**
     * Show all {{ entity }}s.
     *
     * @Route("/list/{filter}", name="{{ bundle_alias }}_{{ entity_cc }}_list", defaults={"filter" = "All"})
     * @Template()     
     */
    public function listAction($filter)
    {
        $form = $this->container->get('avro_crm.clientList.form');
        $form->bindRequest($this->container->get('request'));

        if ('POST' == $this->container->get('request')->getMethod()) {
            if ($form->isValid()) {
                $action = $form['action']->getData();
                switch($action) {
                    case 'Search':
                        ${{ entity_cc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.manager')->search($form->getData());
                    break;
                    case 'Edit':

                    break;
                    case 'Export':

                    break;
                }
            }
        }

        return array(
            '{{ entity_cc }}s' => ${{ entity_cc }}s,
            'form' => $form->createView()
        );
    }      

    /**
     * Create a new {{ entity_cc }}.
     *
     * @Route("/new", name="{{ bundle_alias }}_{{ entity_cc }}_new")
     * @Template()     
     */
    public function newAction()
    {
        ${{ entity_cc }}Form = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form');
        $formHandler = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form.handler');

        $process = $formHandler->process();
        if ($process) {
            ${{ entity_cc }} = ${{ entity_cc }}Form->getData('{{ entity_cc }}');
            if ($this->container->get('request')->isXmlHttpRequest()) {
                ${{ entity_cc }} = $this->container->get('serializer')->serialize(${{ entity_cc }}, 'json');
                $response = new Response('{"notice": "{{ entity }} created.", "data": '.${{ entity_cc }}.' }');
                $response->headers->set('Content-Type', 'application/json');

                return $response; 
            } else {
                $this->container->get('session')->setFlash('success', '{{ entity }} created.');

                return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_cc }}_edit', array('id' => ${{ entity_cc }}->getId())), 301);
            }
        }

        return array(
            '{{ entity_cc }}Form' => ${{ entity_cc }}Form->createView(),
        );

    }
    
    /**
     * Edit one {{ entity_cc | title }}, show the edit form.
     *
     * @Route("/edit/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_edit", defaults={"id" = false})
     * @Template()
     */
    public function editAction($id)
    {
        ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);
        ${{ entity_cc }}Form = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form');
        $formHandler = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form.handler');

        $process = $formHandler->process(${{ entity_cc }});
        if ($process) {
            ${{ entity_cc }} = ${{ entity_cc }}Form->getData('{{ entity_cc }}');
            if ($this->container->get('request')->isXmlHttpRequest()) {
                ${{ entity_cc }} = $this->container->get('serializer')->serialize(${{ entity_cc }}, 'json');
                $response = new Response('{"notice": "{{ entity | title }} updated.", "data": '.${{ entity_cc }}.'}');
                $response->headers->set('Content-Type', 'application/json');

                return $response; 
            } else {
                $this->container->get('session')->setFlash('notice', '{{ entity | title}} updated.');

                return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_cc }}_list'));
            }
        }

        return array(
            '{{ entity_cc}}Form' => ${{ entity_cc }}Form->createView(),
            '{{ entity_cc }}' => ${{ entity_cc }},
        );
    }
    
    /**
     * Show one {{ entity_cc }}.
     *
     * @Route("/show/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_show")
     * @Template()
     */
    public function showAction($id)
    {
        ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find{{ entity }}($id);

        return array(
            '{{ entity_cc }}' => ${{ entity_cc }},
        );
    }
    
    /**
     * Delete one {{ entity }}.
     *
     * @Route("/delete/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_delete", defaults={"id" = false})
     */
    public function deleteAction($id)
    {
        if ($id) {
            ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);
            $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->softDelete(${{ entity_cc }});
            
            if ($this->container->get('request')->isXmlHttpRequest()) {
                ${{ entity_cc }} = $this->container->get('serializer')->serialize(${{ entity_cc }}, 'json');
                $response = new Response('{"notice": "{{ entity | title }} deleted.", "data": '.${{ entity_cc }}.'}');
                $response->headers->set('Content-Type', 'application/json');

                return $response; 
            } else {
                $this->container->get('session')->setFlash('success', '{{ entity | title }} deleted.');
            }     
        }

        return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_cc }}_list'), 301);     
    }

    /**
     * Restore one {{ entity }}.
     *
     * @Route("/restore/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_restore", defaults={"id" = false})
     */
    public function restoreAction($id)
    {
        if ($id) {
            ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);
            $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->restore(${{ entity_cc }});
            
            if ($this->container->get('request')->isXmlHttpRequest()) {
                ${{ entity_cc }} = $this->container->get('serializer')->serialize(${{ entity_cc }}, 'json');
                $response = new Response('{"notice": "{{ entity | title }} restored.", "data": '.${{ entity_cc }}.'}');
                $response->headers->set('Content-Type', 'application/json');

                return $response; 
            } else {
                $this->container->get('session')->setFlash('notice', '{{ entity | title }} restored.');
            }     
        }

        return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_cc }}_list'), 301);     
    }

    /**
     * Batch process for {{ entity_cc }}.
     *  
     * @Route("/batch", name="{{ bundle_alias }}_{{ entity_cc }}_batch")
     */
    public function batchAction()
    {
        $selected = $this->container->get('request')->get('selected');

        if ($selected) {
            $action = $this->container->get('request')->get('form_action');
            
            ${{ entity_cc }}Manager = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager');

            switch ($action) {
                case 'Delete':
                    ${{ entity_cc }}Manager->delete{{ entity }}s($selected);
                    $this->container->get('session')->setFlash('notice', '{{ entity }} deleted.');
                break;
                case 'Restore':
                    ${{ entity_cc }}Manager->restore{{ entity }}s($selected);
                    $this->container->get('session')->setFlash('notice', '{{ entity }} restored.');
                break;

            }
        } else {
            $this->container->get('session')->setFlash('error', 'No {{ entity_cc }}s were selected.');
        }
        return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_cc }}'));     
    }


}