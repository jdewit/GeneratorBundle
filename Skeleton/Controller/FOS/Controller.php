<?php

namespace {{ bundleNamespace }}\Controller;

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
 * @Route("/{{ entityCC }}")
 */
class {{ entity }}Controller extends ContainerAware
{
    /**
     * Show all {{ entity }}s.
     *
     * @Route("/list/{filter}", name="{{ bundleAlias }}_{{ entityCC }}_list", defaults={"filter" = "All"})
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
                        ${{ entityCC }}s = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.manager')->search($form->getData());
                    break;
                    case 'Edit':

                    break;
                    case 'Export':

                    break;
                }
            }
        }

        return array(
            '{{ entityCC }}s' => ${{ entityCC }}s,
            'form' => $form->createView()
        );
    }      

    /**
     * Create a new {{ entityCC }}.
     *
     * @Route("/new", name="{{ bundleAlias }}_{{ entityCC }}_new")
     * @Template()     
     */
    public function newAction()
    {
        ${{ entityCC }}Form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');
        $formHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form.handler');

        $process = $formHandler->process();
        if ($process) {
            ${{ entityCC }} = ${{ entityCC }}Form->getData('{{ entityCC }}');
            if ($this->container->get('request')->isXmlHttpRequest()) {
                ${{ entityCC }} = $this->container->get('serializer')->serialize(${{ entityCC }}, 'json');
                $response = new Response('{"notice": "{{ entity }} created.", "data": '.${{ entityCC }}.' }');
                $response->headers->set('Content-Type', 'application/json');

                return $response; 
            } else {
                $this->container->get('session')->setFlash('success', '{{ entity }} created.');

                return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_edit', array('id' => ${{ entityCC }}->getId())), 301);
            }
        }

        return array(
            '{{ entityCC }}Form' => ${{ entityCC }}Form->createView(),
        );

    }
    
    /**
     * Edit one {{ entityCC | title }}, show the edit form.
     *
     * @Route("/edit/{id}", name="{{ bundleAlias }}_{{ entityCC }}_edit", defaults={"id" = false})
     * @Template()
     */
    public function editAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
        ${{ entityCC }}Form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');
        $formHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form.handler');

        $process = $formHandler->process(${{ entityCC }});
        if ($process) {
            ${{ entityCC }} = ${{ entityCC }}Form->getData('{{ entityCC }}');
            if ($this->container->get('request')->isXmlHttpRequest()) {
                ${{ entityCC }} = $this->container->get('serializer')->serialize(${{ entityCC }}, 'json');
                $response = new Response('{"notice": "{{ entityTitle }} updated.", "data": '.${{ entityCC }}.'}');
                $response->headers->set('Content-Type', 'application/json');

                return $response; 
            } else {
                $this->container->get('session')->setFlash('notice', '{{ entityTitle}} updated.');

                return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_list'));
            }
        }

        return array(
            '{{ entityCC}}Form' => ${{ entityCC }}Form->createView(),
            '{{ entityCC }}' => ${{ entityCC }},
        );
    }
    
    /**
     * Show one {{ entityCC }}.
     *
     * @Route("/show/{id}", name="{{ bundleAlias }}_{{ entityCC }}_show")
     * @Template()
     */
    public function showAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find{{ entity }}($id);

        return array(
            '{{ entityCC }}' => ${{ entityCC }},
        );
    }
    
    /**
     * Delete one {{ entity }}.
     *
     * @Route("/delete/{id}", name="{{ bundleAlias }}_{{ entityCC }}_delete", defaults={"id" = false})
     */
    public function deleteAction($id)
    {
        if ($id) {
            ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
            $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->softDelete(${{ entityCC }});
            
            if ($this->container->get('request')->isXmlHttpRequest()) {
                ${{ entityCC }} = $this->container->get('serializer')->serialize(${{ entityCC }}, 'json');
                $response = new Response('{"notice": "{{ entityTitle }} deleted.", "data": '.${{ entityCC }}.'}');
                $response->headers->set('Content-Type', 'application/json');

                return $response; 
            } else {
                $this->container->get('session')->setFlash('success', '{{ entityTitle }} deleted.');
            }     
        }

        return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_list'), 301);     
    }

    /**
     * Restore one {{ entity }}.
     *
     * @Route("/restore/{id}", name="{{ bundleAlias }}_{{ entityCC }}_restore", defaults={"id" = false})
     */
    public function restoreAction($id)
    {
        if ($id) {
            ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
            $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->restore(${{ entityCC }});
            
            if ($this->container->get('request')->isXmlHttpRequest()) {
                ${{ entityCC }} = $this->container->get('serializer')->serialize(${{ entityCC }}, 'json');
                $response = new Response('{"notice": "{{ entityTitle }} restored.", "data": '.${{ entityCC }}.'}');
                $response->headers->set('Content-Type', 'application/json');

                return $response; 
            } else {
                $this->container->get('session')->setFlash('notice', '{{ entityTitle }} restored.');
            }     
        }

        return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_list'), 301);     
    }

    /**
     * Batch process for {{ entityCC }}.
     *  
     * @Route("/batch", name="{{ bundleAlias }}_{{ entityCC }}_batch")
     */
    public function batchAction()
    {
        $selected = $this->container->get('request')->get('selected');

        if ($selected) {
            $action = $this->container->get('request')->get('form_action');
            
            ${{ entityCC }}Manager = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager');

            switch ($action) {
                case 'Delete':
                    ${{ entityCC }}Manager->delete{{ entity }}s($selected);
                    $this->container->get('session')->setFlash('notice', '{{ entity }} deleted.');
                break;
                case 'Restore':
                    ${{ entityCC }}Manager->restore{{ entity }}s($selected);
                    $this->container->get('session')->setFlash('notice', '{{ entity }} restored.');
                break;

            }
        } else {
            $this->container->get('session')->setFlash('error', 'No {{ entityCC }}s were selected.');
        }
        return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}'));     
    }


}
