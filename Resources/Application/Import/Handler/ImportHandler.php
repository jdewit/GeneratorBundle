<?php
namespace {{ bundle_namespace }}\Import\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Avro\CsvBundle\Util\Reader;
use Symfony\Component\Security\Core\SecurityContextInterface;
use {{ bundle_namespace }}\Entity\{{ entity | ucFirst }}Manager;
{% for field in fields %}
{% if field.type == 'manyToOne' %}
use {{ field.targetEntity }}Manager;
{% endif %}
{% endfor %}

/*
 * Imports {{ entity | camelCaseToTitle | lower }}s into the database
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}ImportHandler 
{
    protected $form;
    protected $request;
    protected $reader;
    protected $batchSize = 20;
    protected $imported = array();
    protected $skipped = array();
    protected $user;
    protected ${{ entity_cc }}Manager;
{% for field in fields %}
{% if field.type == 'manyToOne' %}
    protected ${{ field.targetEntityName }}Manager;
{% endif %}
{% endfor %}

    public function __construct(Form $form, Request $request, Reader $reader, SecurityContextInterface $context, {{ entity }}Manager ${{ entity_cc }}Manager{% for field in fields %}{% if field.type == 'manyToOne' %}, {{ field.targetEntityName | ucFirst }}Manager ${{ field.targetEntityName }}Manager{% endif %}{% endfor %})
    {
        $this->form = $form;
        $this->request = $request;
        $this->reader = $reader;
        $this->user = $context->getToken()->getUser();
        $this->{{ entity_cc }}Manager = ${{ entity_cc }}Manager;
{% for field in fields %}
{% if field.type == 'manyToOne' %}
        $this->{{ field.targetEntityName }}Manager = ${{ field.targetEntityName }}Manager;
{% endif %}
{% endfor %}
    }

    /*
     * Process CSV Form 
     *
     * @return boolean true if valid
     * @return array errors if invalid
     */
    public function process() 
    {
        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $file = $this->form['file']->getData();
                $delimiter = $this->form['delimiter']->getData();

                $this->reader->open($file, $delimiter);

                $this->headers = $this->reader->getHeaders(); 

                $i = 0;
                while ($row = $this->reader->getRow()) {
                    if (($i % $this->batchSize) == 0) {
                        $this->import($row, true, true);
                    } else {
                        $this->import($row, false, false);
                    }
                    ++$i;
                }

                $this->{{ entity_cc }}Manager->flush(true);

                return true;
            } else { 
                $errorArray = array();
                foreach ($this->form->getChildren() as $field) {
                    $errors = $field->getErrors();
                    if ($errors) {
                        $errorArray[$field->getName()] = strtr($errors[0]->getMessageTemplate(), $errors[0]->getMessageParameters());
                    }
                }

                return $errorArray;
            }

        } 

        return false;
    }

    /*
     * Import {{ entity | camelCaseToTitle }} into database
     *
     * @param array $row An array of {{ entity | camelCaseToTitle | lower }}s
     * @param boolean $andFlush Flush em if true
     * @param boolean $andClear Clear em if true
     * @return true if successful
     */
    public function import($row, $andFlush, $andClear) 
    {
        ${{ entity_cc }}Id = $row[array_search('id', $this->headers)];
        if (${{ entity_cc }}Id) {
            ${{ entity_cc }} = $this->{{ entity_cc }}Manager->create();
            ${{ entity_cc }}->setLegacyId(${{ entity_cc }}Id); 
{% for field in fields %}
{% if field.type == 'manyToOne' %}
            ${{ field.fieldName }}Id = $row[array_search('{{ field.fieldName }}_id', $this->headers)];
            if (${{ field.fieldName }}Id) {
                ${{ field.fieldName }} = $this->{{ field.fieldName }}Manager->findOneBy(array('legacyId' => ${{ field.fieldName }}Id));
                if (${{ field.fieldName }}) {
                    ${{ entity_cc }}->set{{ field.fieldName | ucFirst }}(${{ field.fieldName }});
                }
            }
{% else %}
            ${{ entity_cc }}->set{{ field.fieldName | ucFirst }}(array_search('{{ field.fieldName | camelCaseToUnderscore }}', $this->headers) ? $row[array_search('{{ field.fieldName | camelCaseToUnderscore }}', $this->headers)] : null);
{% endif %}
{% endfor %}
        }
    
        $this->{{ entity_cc }}Manager->update(${{ entity_cc }}, $andFlush, $andClear);

        $this->addImported(${{ entity_cc }}Id);

        return true;
    }

    /*
     * Add id to successful imports
     *
     * @param string {{ entity }} id
     */
    public function addImported(${{ entity_cc }}Id)
    {
        $this->imported[] = ${{ entity_cc }}Id;
    }

    /*
     * Add id to skipped imports
     *
     * @param string {{ entity }} id
     */
    public function addSkipped(${{ entity_cc }}Id)
    {
        $this->skipped[] = ${{ entity_cc }}Id;
    }

    /*
     * @return array of successful ids
     */
    public function getImported()
    {
        return $this->imported;
    }

    /*
     * @return array of skipped ids
     */
    public function getSkipped()
    {
        return $this->skipped;
    }

    /*
     * Set batch size
     */
    public function setBatchSize($batchSize) 
    {
        $this->batchSize = $batchSize;
    }
}
