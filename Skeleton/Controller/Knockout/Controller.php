<?php

namespace {{ bundleNamespace }}\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * {{ entity }} controller.
 *
 * @Route("/{{ entityCC }}")
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}Controller extends ContainerAware
{
     /**
     * List {{ entity | camelCaseToTitle | lower }}s.
     *
     * @Route("/list", name="{{ bundleAlias }}_{{ entityCC }}_list")
     * @Template()     
     */
    public function listAction()
    {
        $form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}Search.form');
        $form->bindRequest($this->container->get('request'));

        if ('POST' == $this->container->get('request')->getMethod()) {
            if ($form->isValid()) {
                $response = new Response('{
                    "status" => "OK",
                    "data" => '.$this->container->get('serializer')->serialize($this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->search($form->getData()), 'json').'
                }');
            } else {
                $response = new Response('{
                    "status": "FAIL",
                    "notice": "Search failed. Please try again." 
                }');
            }
            $response->headers->set('Content-Type', 'application/json');

        } else {
            $response = array(
                '{{ entityCC }}s' => $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->search(),
                '{{ entityCC }}Form' => $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form')->createView(),
                'searchForm' => $form->createView()
            );
        }

        return $response; 
    }

    /**
     * Get {{ entity | camelCaseToTitle | lower }}s.
     *
     * @Route("/get/{id}", name="{{ bundleAlias }}_{{ entityCC }}_get", defaults={"id" = false})
     * @method("post")     
     */
    public function getAction($id)
    {
        ${{ entityCC }}s = $this->container->get('avro_crm.job_manager')->findBy(array('isDeleted' => false));

        ${{ entityCC }}s = $this->container->get('serializer')->serialize(${{ entityCC }}, 'json');

        $response = new Response('{"filter": "'.$id.'", "data": '.${{ entityCC }}s.' }');
        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     *  Get {{ entityCC | camelCaseToTitle | lower }} form.
     *
     * @Route("/getForm/{id}", name="{{ bundleAlias }}_{{ entityCC }}_getForm", defaults={"id"=false})
     * @Template
     */
    public function getFormAction($id)
    {
        $form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');

        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);

        return array(
            '{{ entityCC }}' => ${{ entityCC }},
            '{{ entityCC }}Form' => $form->createView()
        ); 
    }

    /**
     * Create a new {{ entityCC | camelCaseToTitle | lower }}.
     *
     * @Route("/new", name="{{ bundleAlias }}_{{ entityCC }}_new")
     * @method("post")
     */
    public function newAction()
    {
        $form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');
        $formHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form.handler');

        $process = $formHandler->process();
        if ($process === true) {
            $response = new Response('{
                "status": "OK",
                "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} created.",
                "data": '.$this->container->get('serializer')->serialize($form->getData(), 'json').'
            }');
        } else {
            $response = new Response('{
                "status": "FAIL",
                "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} not created.",
                "errors": '.$this->container->get('serializer')->serialize($process, 'json').'
            }');
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     * Edit one {{ entityCC | camelCaseToTitle | lower }}.
     *
     * @Route("/edit/{id}", name="{{ bundleAlias }}_{{ entityCC }}_edit", defaults={"id" = false})
     * @method("post")
     */
    public function editAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
        $form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');
        $formHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form.handler');

        $process = $formHandler->process(${{ entityCC }});
        if ($process === true) {
            $response = new Response('{
                "status": "OK",
                "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} updated.",
                "data": '.$this->container->get('serializer')->serialize($form->getData(), 'json').'
            }');
        } else {
            $response = new Response('{
                "status": "FAIL",
                "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} not updated.",
                "errors": '.$this->container->get('serializer')->serialize($process, 'json').'
            }');
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     * Delete one {{ entity | camelCaseToTitle | lower }}.
     *
     * @Route("/delete/{id}", name="{{ bundleAlias }}_{{ entityCC }}_delete", defaults={"id" = false})
     * @method("post")
     */
    public function deleteAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
        $process = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->softDelete(${{ entityCC }});

        if ($process === true) {
            $response = new Response('{
                "status": "OK",
                "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} deleted."
            }');
        } else {
            $response = new Response('{
                "status": "OK",
                "notice": "Unable to delete {{ entity | camelCaseToTitle | lower | ucFirst }}."
            }');
        }
        $response->headers->set('Content-Type', 'application/json');
        
        return $response; 
    }

    /**
     * Restore one {{ entity | camelCaseToTitle | lower }}.
     *
     * @Route("/restore/{id}", name="{{ bundleAlias }}_{{ entityCC }}_restore", defaults={"id" = false})
     * @method("post")
     */
    public function restoreAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
        $process = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->restore(${{ entityCC }});
        if ($process === true) {
            $response = new Response('{
                "status": "OK",
                "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} restored."
            }');
        } else {
            $response = new Response('{
                "status": "OK",
                "notice": "Unable to restore {{ entity | camelCaseToTitle | lower | ucFirst }}."
            }');
        } 
        $response->headers->set('Content-Type', 'application/json');
        
        return $response; 
    }

    /**
     * Batch delete {{ entityCC | camelCaseToTitle | lower }}.
     *  
     * @Route("/batchDelete", name="{{ bundleAlias }}_{{ entityCC }}_batchDelete")
     * @method("post")
     */
    public function batchDeleteAction()
    {
        $selected = $this->container->get('request')->get('selected');
        ${{ entityCC }}Manager = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager');

        $i = 0;
        foreach ($selected as $id) {
            ${{ entityCC }} = ${{ entityCC }}Manager->find($id);

            if (!next($selected) || ($i % 10 == 0)) {
                ${{ entityCC }}Manager->softDelete(${{ entityCC }}, true, true);
            } else {
                ${{ entityCC }}Manager->softDelete(${{ entityCC }}, false);
            }
            ++$i;
        }

        $response = new Response('{
            "status": "OK",
            "notice": '.$i.' {{ entityCC | camelCaseToTitle | lower }}s deleted."
        }');

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     * Batch restore {{ entityCC | camelCaseToTitle | lower }}.
     *  
     * @Route("/batchRestore", name="{{ bundleAlias }}_{{ entityCC }}_batchRestore")
     * @method("post")
     */
    public function batchRestoreAction()
    {
        $selected = $this->container->get('request')->get('selected');
        ${{ entityCC }}Manager = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager');

        $i = 0;
        foreach ($selected as $id) {
            ${{ entityCC }} = ${{ entityCC }}Manager->find($id);

            if (!next($selected) || ($i % 10 == 0)) {
                ${{ entityCC }}Manager->restore(${{ entityCC }}, true, true);
            } else {
                ${{ entityCC }}Manager->restore(${{ entityCC }}, false);
            }
            ++$i;
        }

        $response = new Response('{
            "status": "OK",
            "notice": '.$i.' {{ entityCC | camelCaseToTitle | lower }}s restored."
        }');

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

    /**
     *  Import {{ entityCC | camelCaseToTitle | lower }}s via csv.
     *
     * @Route("/import", name="{{ bundleAlias }}_{{ entityCC }}_import")
     * @Template
     */
    public function importAction()
    {
        $form = $this->container->get('avro_csv.csv.form');
        $importHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_import.handler');

        $process = $importHandler->process();
        if ($process === true) {
            $this->container->get('session')->setFlash('success', count($importHandler->getImported()).' {{ entityCC | camelCaseToTitle | lower }}s imported.');

            return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_list'));
        } 

        return array(
            'form' => $form->createView()
        );
    }
}
