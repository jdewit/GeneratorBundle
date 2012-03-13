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
 */
class {{ entity }}Controller extends ContainerAware
{
     /**
     * Get {{ entity | camelCaseToTitle | lower }}s.
     *
     * @Route("/get/{filter}", name="{{ bundle_alias }}_{{ entity_cc }}_get", defaults={"filter" = "Recent"})
     * @method("post")     
     */
    public function getAction($filter)
    {
        switch ($filter) {
            case 'Recent':
                ${{ entity_cc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->findRecent();
            break;
            case 'All':
                ${{ entity_cc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->findAllActive();
            break;
            case 'Deleted':
                ${{ entity_cc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->findAllDeleted();
            break;            
            default: 
                ${{ entity_cc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->findRecent();
            break;
        }

        ${{ entity_cc }}s = $this->container->get('serializer')->serialize(${{ entity_cc }}s, 'json');

        $response = new Response('{"filter": "'.$filter.'", "data": '.${{ entity_cc }}s.' }');
        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     * Search {{ entity_cc }}s.
     *
     * @Route("/search", name="{{ bundle_alias }}_{{ entity_cc }}_search")
     * @method("post")
     */
    public function searchAction()
    {
        $form = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}Search.form');
        $form->bindRequest($this->container->get('request'));

        if ($form->isValid()) {
            ${{ entity_cc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->search($form->getData());
            $response = new Response('{"status": "OK", "notice": "'.count(${{ entity_cc }}s).' {{ entity_cc }}s found", "data": '.$this->container->get('serializer')->serialize(${{ entity_cc }}s, 'json').'}');
        } else {
            $response = new Response('{"status": "FAIL", "notice": "Search failed. Please try again.", "data": '.json_encode($form->getErrors()).' }');
        }
        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

     /**
     * Show all {{ entity | camelCaseToTitle | lower }}s.
     *
     * @Route("/list", name="{{ bundle_alias }}_{{ entity_cc }}_list")
     * @Template()     
     */
    public function listAction()
    {
        return array(
            '{{ entity_cc }}s' => $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->findAllActive(),
            '{{ entity_cc }}Form' => $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form')->createView(),
            'searchForm' => $this->container->get('{{ bundle_alias }}.{{ entity_cc}}Search.form')->createView(),
        );
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

        if ($id) {
            ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);
        } else {
            ${{ entity_cc }} = null;
        }

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
        if (true === $process) {
            ${{ entity_cc }} = $form->getData('{{ entity_cc }}');
            ${{ entity_cc }} = $this->container->get('serializer')->serialize(${{ entity_cc }}, 'json');
            $response = new Response('{"status": "OK", "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} created.", "data": '.${{ entity_cc }}.' }');
        } else {
            $response = new Response('{"status": "FAIL", "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} not created.", "data": '.json_encode($process).' }');
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     * Edit one {{ entity_cc | camelCaseToTitle }}.
     *
     * @Route("/edit/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_edit", defaults={"id" = false})
     * @method("post")
     */
    public function editAction($id)
    {
        if ($id) {
            ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);
            $form = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form');
            $formHandler = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}.form.handler');

            $process = $formHandler->process(${{ entity_cc }});
            if (true === $process) {
                ${{ entity_cc }} = $form->getData('{{ entity_cc }}');
                ${{ entity_cc }} = $this->container->get('serializer')->serialize(${{ entity_cc }}, 'json');
                $response = new Response('{"status": "OK", "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} updated.", "data": '.${{ entity_cc }}.'}');
            }
        } else {
            $response = new Response('{"status": "FAIL", "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} not created.", "data": '.json_encode($process).' }');
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     * Delete one {{ entity | camelCaseToTitle }}.
     *
     * @Route("/delete/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_delete", defaults={"id" = false})
     * @method("post")
     */
    public function deleteAction($id)
    {
        if ($id) {
            ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);
            $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->softDelete(${{ entity_cc }});
            ${{ entity_cc }} = $this->container->get('serializer')->serialize(${{ entity_cc }}, 'json');
            $response = new Response('{"status": "OK", "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} deleted.", "data": '.${{ entity_cc }}.'}');
        } else {
            $response = new Response('{"status": "FAIL", "notice": "Unable to delete {{ entity_cc | camelCaseToTitle | lower }}.", "data": "null" }');
        } 
        $response->headers->set('Content-Type', 'application/json');
        
        return $response; 
    }

    /**
     * Restore one {{ entity | camelCaseToTitle }}.
     *
     * @Route("/restore/{id}", name="{{ bundle_alias }}_{{ entity_cc }}_restore", defaults={"id" = false})
     * @method("post")
     */
    public function restoreAction($id)
    {
        if ($id) {
            ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->find($id);
            $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager')->restore(${{ entity_cc }});
            ${{ entity_cc }} = $this->container->get('serializer')->serialize(${{ entity_cc }}, 'json');
            $response = new Response('{"status": "OK", "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} restored.", "data": '.${{ entity_cc }}.'}');
        } else {
            $response = new Response('{"status": "FAIL", "notice": "Unable to restore {{ entity_cc | camelCaseToTitle | lower }}.", "data": "null" }');
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
        if ($selected) {
            ${{ entity_cc }}Manager = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager');

            foreach ($selected as $id) {
                ${{ entity_cc }} = ${{ entity_cc }}Manager->find($id);
                ${{ entity_cc }}Manager->softDelete(${{ entity_cc }});
            }
        } 

        $response = new Response('{"notice": "'.count($selected).' {{ entity_cc | camelCaseToTitle | lower }}s deleted."}');
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
        if ($selected) {
            ${{ entity_cc }}Manager = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_manager');

            foreach ($selected as $id) {
                ${{ entity_cc }} = ${{ entity_cc }}Manager->find($id);
                ${{ entity_cc }}Manager->restore(${{ entity_cc }});
            }
        } 

        $response = new Response('{"notice": "'.count($selected).' {{ entity_cc | camelCaseToTitle | lower }}s restored."}');
        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     *  Import {{ entity_cc | camelCaseToTitle | lower }} via csv.
     *
     * @Route("/import", name="{{ bundle_alias }}_{{ entity_cc }}_import")
     * @Template
     */
    public function importAction()
    {
        $form = $this->container->get('avro_csv.csv.form');

        if ('POST' === $this->container->get('request')->getMethod()) {
            $formHandler = $this->container->get('avro_csv.csv.form.handler');
            $importer = $this->container->get('{{ bundle_alias }}.{{ entity_cc }}_importer');
            $process = $importer->import($formHandler->process());

            if ($process === true) {
                $this->container->get('session')->setFlash('success', count($importer->getImported()).' {{ entity_cc | camelCaseToTitle | lower }}s imported. '.count($importer->getSkipped()). ' {{ entity_cc | camelCaseToTitle | lower }}s skipped.' );

                return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}.{{ entity_cc }}_list'));
            } else {
                $this->container->get('session')->setFlash('error', 'Error importing CSV. Please try again.' );
            }
        }

        return array(
            'form' => $form->createView()
        );
    }
}
