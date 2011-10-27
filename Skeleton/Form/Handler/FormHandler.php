<?php
namespace {{ bundle_namespace }}\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

use {{ bundle_namespace }}\Entity\Interface\{{ entity }};
use {{ bundle_namespace }}\Entity\Manager\Interface\{{ entity }}ManagerInterface;

class {{ entity }}FormHandler
{
    protected $request;
    protected ${{ entity_lc }}Manager;
    protected $form;

    public function __construct(Form $form, Request $request, {{ entity }}ManagerInterface ${{ entity_lc }}Manager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->{{ entity_lc }}Manager = ${{ entity_lc }}Manager;
    }

    public function process({{ entity }}Interface ${{ entity_lc }} = null)
    {
        if (null === ${{ entity_lc }}) {
            ${{ entity_lc }} = $this->{{ entity_lc }}Manager->create{{ entity }}('');
        }

        $this->form->setData(${{ entity_lc }});

        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess(${{ entity_lc }});

                return true;
            }
        }

        return false;
    }

    protected function onSuccess({{ entity }}Interface ${{ entity_lc }})
    {
        $this->{{ entity_lc }}Manager->update{{ entity }}(${{ entity_lc }});
    }
}
