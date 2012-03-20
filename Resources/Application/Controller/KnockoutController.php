<?php

namespace {{ bundle_namespace }}\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * {{ entity }} controller.
 *
 * @Route("/{{ entity_cc }}")
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}Controller extends ContainerAware
{
     /**
     * List {{ entity | camelCaseToTitle | lower }}s.
     *
     * @Route("/list", name="{{ bundle_alias }}_{{ entity_cc }}_list")
     * @Template()     
     */
    public function listAction()
    {
        $form = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}Search.form');
        $form->bindRequest($this->container->get('request'));

        if ('POST' == $this->container->get('request')->getMethod()) {
            if ($form->isValid()) {
                ${{ entity_cc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->search($form->getData());
                $response = new Response(json_encode(array(
                    'status' => 'OK',
                    'data' => ${{ entity_cc }}s
                )));
            } else {
                $response = new Response(json_encode(array(
                    'status' => 'FAIL',
                    'notice' => 'Search failed. Please try again.' 
                )));
            }
            $response->headers->set('Content-Type', 'application/json');

        } else {
            ${{ entity_cc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->search();

            $response = array(
                '{{ entity_cc }}s' => ${{ entity_cc }}s,
                '{{ entity_cc }}Form' => $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form')->createView(),
                'searchForm' => $form->createView()
            );
        }

        return $response; 
    }

    /**
     *  Get {{ entity_cc | camelCaseToTitle | lower }} form.
     *
     * @Route("/getForm/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_getForm", defaults={"id"=false})
     * @Template
     */
    public function getFormAction($id)
    {
        $form = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form');

        ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);

        return array(
            '{{ entity_cc }}' => ${{ entity_cc }},
            '{{ entity_cc }}Form' => $form->createView()
        ); 
    }

    /**
     * Create a new {{ entity_cc | camelCaseToTitle | lower }}.
     *
     * @Route("/new", name="{{ bundle_alias }}_{{ entity_cc }}_new")
     * @method("post")
     */
    public function newAction()
    {
        $form = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form');
        $formHandler = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form.handler');

        $process = $formHandler->process();
        if ($process) {
            $response = new Response(json_encode(array(
                'status' => 'OK',
                'notice' => '{{ entity | camelCaseToTitle | lower | ucFirst }} created.'
            )));
        } else {
            $response = new Response(json_encode(array(
                'status' => 'FAIL',
                'notice' => '{{ entity | camelCaseToTitle | lower | ucFirst }} not created.',
                'errors' => $process
            )));
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     * Edit one {{ entity_cc | camelCaseToTitle | lower }}.
     *
     * @Route("/edit/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_edit", defaults={"id" = false})
     * @method("post")
     */
    public function editAction($id)
    {
        ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);
        $form = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form');
        $formHandler = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form.handler');

        $process = $formHandler->process(${{ entity_cc }});
        if ($process) {
            $response = new Response(json_encode(array(
                'status' => 'OK',
                'notice' => '{{ entity | camelCaseToTitle | lower | ucFirst }} updated.'
            )));
        } else {
            $response = new Response(json_encode(array(
                'status' => 'FAIL',
                'notice' => '{{ entity | camelCaseToTitle | lower | ucFirst }} not updated.',
                'errors' => $process
            )));
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     * Delete one {{ entity | camelCaseToTitle | lower }}.
     *
     * @Route("/delete/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_delete", defaults={"id" = false})
     * @method("post")
     */
    public function deleteAction($id)
    {
        ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);
        $process = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->softDelete(${{ entity_cc }});

        if ($process) {
            ${{ entity_cc }} = $this->container->get('serializer')->serialize(${{ entity_cc }}, 'json');

            $response = new Response(json_encode(array(
                'status' => 'OK',
                'notice' => '{{ entity | camelCaseToTitle | lower | ucFirst }} deleted.'
            )));
        } else {
            $response = new Response(json_encode(array(
                'status' => 'FAIL',
                'notice' => 'Unable to delete {{ entity | camelCaseToTitle | lower | ucFirst }}.'
            )));
        }
        $response->headers->set('Content-Type', 'application/json');
        
        return $response; 
    }

    /**
     * Restore one {{ entity | camelCaseToTitle | lower }}.
     *
     * @Route("/restore/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_restore", defaults={"id" = false})
     * @method("post")
     */
    public function restoreAction($id)
    {
        ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);
        $process = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->restore(${{ entity_cc }});
        if ($process) {
            $response = new Response(json_encode(array(
                'status' => 'OK',
                'notice' => '{{ entity | camelCaseToTitle | lower | ucFirst }} restored.'
            )));
        } else {
            $response = new Response(json_encode(array(
                'status' => 'FAIL',
                'notice' => 'Unable to restore {{ entity | camelCaseToTitle | lower | ucFirst }}.'
            )));
        } 
        $response->headers->set('Content-Type', 'application/json');
        
        return $response; 
    }

    /**
     * Batch delete {{ entity_cc | camelCaseToTitle | lower }}.
     *  
     * @Route("/batchDelete", name="{{ bundle_alias }}_{{ entity_cc }}_batchDelete")
     * @method("post")
     */
    public function batchDeleteAction()
    {
        $selected = $this->container->get('request')->get('selected');
        ${{ entity_cc }}Manager = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager');

        $i = 0;
        foreach ($selected as $id) {
            ${{ entity_cc }} = ${{ entity_cc }}Manager->find($id);

            if (!next($selected) || ($i % 10 == 0)) {
                ${{ entity_cc }}Manager->softDelete(${{ entity_cc }}, true, true);
            } else {
                ${{ entity_cc }}Manager->softDelete(${{ entity_cc }}, false);
            }
            ++$i;
        }

        $response = new Response(json_encode(array(
            'status' => 'OK',
            'notice' => $i.' {{ entity_cc | camelCaseToTitle | lower }}s deleted.'
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     * Batch restore {{ entity_cc | camelCaseToTitle | lower }}.
     *  
     * @Route("/batchRestore", name="{{ bundle_alias }}_{{ entity_cc }}_batchRestore")
     * @method("post")
     */
    public function batchRestoreAction()
    {
        $selected = $this->container->get('request')->get('selected');
        ${{ entity_cc }}Manager = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager');

        $i = 0;
        foreach ($selected as $id) {
            ${{ entity_cc }} = ${{ entity_cc }}Manager->find($id);

            if (!next($selected) || ($i % 10 == 0)) {
                ${{ entity_cc }}Manager->restore(${{ entity_cc }}, true, true);
            } else {
                ${{ entity_cc }}Manager->restore(${{ entity_cc }}, false);
            }
            ++$i;
        }

        $response = new Response(json_encode(array(
            'status' => 'OK',
            'notice' => $i.' {{ entity_cc | camelCaseToTitle | lower }}s restored.'
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     *  Import {{ entity_cc | camelCaseToTitle | lower }}s via csv.
     *
     * @Route("/import", name="{{ bundle_alias }}_{{ entity_cc }}_import")
     * @Template
     */
    public function importAction()
    {
        $form = $this->container->get('avro_csv.csv.form');
        $importHandler = $this->container->get('avro_crm.client_import.handler');

        $process = $importHandler->process();
        if ($process) {
            $this->container->get('session')->setFlash('success', count($importHandler->getImported()).' {{ entity_cc | camelCaseToTitle | lower }}s imported.');

            return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_cc }}_list'));
        } 

        return array(
            'form' => $form->createView()
        );
    }
}
