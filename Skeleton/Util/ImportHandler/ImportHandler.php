<?php
namespace {{ bundleNamespace }}\Util\ImportHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Avro\CsvBundle\Util\Reader;
use Symfony\Component\Security\Core\SecurityContextInterface;
use {{ bundleNamespace }}\Entity\{{ entity | ucFirst }}Manager;
{% for field in uniqueManyToOneRelations %}
use {{ field.targetEntity }}Manager;
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
    protected ${{ entityCC }}Manager;
{% for field in uniqueManyToOneRelations %}
    protected ${{ field.targetEntityName }}Manager;
{% endfor %}

    public function __construct(Form $form, Request $request, Reader $reader, SecurityContextInterface $context, {{ entity }}Manager ${{ entityCC }}Manager{% for field in uniqueManyToOneRelations %}, {{ field.targetEntityName | ucFirst }}Manager ${{ field.targetEntityName }}Manager{% endfor %})
    {
        $this->form = $form;
        $this->request = $request;
        $this->reader = $reader;
        $this->user = $context->getToken()->getUser();
        $this->{{ entityCC }}Manager = ${{ entityCC }}Manager;
{% for field in uniqueManyToOneRelations %}
        $this->{{ field.targetEntityName }}Manager = ${{ field.targetEntityName }}Manager;
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

                $this->{{ entityCC }}Manager->flush(true);

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
        ${{ entityCC }}Id = $row[array_search('id', $this->headers)];
        if (${{ entityCC }}Id) {
            ${{ entityCC }} = $this->{{ entityCC }}Manager->create();
            ${{ entityCC }}->setLegacyId(${{ entityCC }}Id); 
{% for field in fields %}
{% if field.type == 'manyToOne' %}
            ${{ field.fieldName }}Id = $row[array_search('{{ field.fieldName }}_id', $this->headers)];
            if (${{ field.fieldName }}Id) {
                ${{ field.fieldName }} = $this->{{ field.fieldName }}Manager->findOneBy(array('legacyId' => ${{ field.fieldName }}Id));
                if (${{ field.fieldName }}) {
                    ${{ entityCC }}->set{{ field.fieldName | ucFirst }}(${{ field.fieldName }});
                }
            }
{% elseif field.type != 'oneToMany' and field.type != 'manyToMany' %}
            ${{ entityCC }}->set{{ field.fieldName | ucFirst }}(array_search('{{ field.fieldName | camelCaseToUnderscore }}', $this->headers) ? $row[array_search('{{ field.fieldName | camelCaseToUnderscore }}', $this->headers)] : null);
{% endif %}
{% endfor %}

            $this->{{ entityCC }}Manager->update(${{ entityCC }}, $andFlush, $andClear);
            $this->addImported(${{ entityCC }}Id);
        }


        return true;
    }

    /*
     * Add id to successful imports
     *
     * @param string {{ entity }} id
     */
    public function addImported(${{ entityCC }}Id)
    {
        $this->imported[] = ${{ entityCC }}Id;
    }

    /*
     * Add id to skipped imports
     *
     * @param string {{ entity }} id
     */
    public function addSkipped(${{ entityCC }}Id)
    {
        $this->skipped[] = ${{ entityCC }}Id;
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
