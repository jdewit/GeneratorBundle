<?php
namespace {{ bundle_namespace }}\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

use {{ bundle_namespace }}\Entity\{{ entity }};
use {{ bundle_namespace }}\Entity\{{ entity }}Manager;

class {{ entity }}FormHandler
{
    protected $request;
    protected ${{ entity_lc }}Manager;
    protected $form;

    public function __construct(Form $form, Request $request, {{ entity }}Manager ${{ entity_lc }}Manager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->{{ entity_lc }}Manager = ${{ entity_lc }}Manager;
    }

    public function process({{ entity }} ${{ entity_lc }} = null)
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

    protected function onSuccess({{ entity }} ${{ entity_lc }})
    {
        $this->{{ entity_lc }}Manager->update{{ entity }}(${{ entity_lc }});
    }
}
