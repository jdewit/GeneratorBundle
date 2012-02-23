<?php
namespace {{ bundle_namespace }}\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

use {{ bundle_namespace }}\Entity\{{ entity }};
use {{ bundle_namespace }}\Entity\{{ entity }}Interface;
use {{ bundle_namespace }}\Entity\{{ entity }}ManagerInterface;

class {{ entity }}FormHandler
{
    protected $request;
    protected ${{ entity_cc }}Manager;
    protected $form;

    public function __construct(Form $form, Request $request, {{ entity }}ManagerInterface ${{ entity_cc }}Manager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->{{ entity_cc }}Manager = ${{ entity_cc }}Manager;
    }

    public function process({{ entity }}Interface ${{ entity_cc }} = null)
    {
        if (null === ${{ entity_cc }}) {
            ${{ entity_cc }} = $this->{{ entity_cc }}Manager->create{{ entity }}('');
        }

        $this->form->setData(${{ entity_cc }});

        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess(${{ entity_cc }});

                return true;
            }
        }

        return false;
    }

    protected function onSuccess({{ entity }}Interface ${{ entity_cc }})
    {
        $this->{{ entity_cc }}Manager->update{{ entity }}(${{ entity_cc }});
    }
}
