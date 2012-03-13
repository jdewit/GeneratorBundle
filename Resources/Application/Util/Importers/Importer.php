<?php
namespace {{ bundle_namespace }}\Util\Importers;

use Symfony\Component\Security\Core\SecurityContextInterface;

use {{ bundle_namespace }}\Entity\{{ entity | ucFirst }}Manager;
{% for field in fields %}
{% if field.type == 'manyToOne' %}
use {{ field.targetEntity }}\{{ field.fieldName | ucFirst }}Manager;
{% endif %}
{% endfor %}

/*
 * Creates a {{ entity }} from an array
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}Importer 
{
    protected $user;
    protected ${{ entity_cc }}Manager;
{% for field in fields %}
{% if field.type == 'manyToOne' %}
    protected ${{ field.fieldName }}Manager;
{% endif %}
{% endfor %}

    public function __construct(SecurityContextInterface $context {% for field in fields %}{% if field.type == 'manyToOne' %}, {{ field.fieldName | ucFirst }}Manager ${{ field.fieldName }}Manager{% endif %}{% endfor %})
    {
        $this->user = $context->getToken()->getUser();
        $this->{{ entity_cc }}Manager = ${{ entity_cc }}Manager;
{% for field in fields %}
{% if field.type == 'manyToOne' %}
        $this->{{ field.fieldName }}Manager = ${{ field.fieldName }}Manager;
{% endif %}
{% endfor %}
        $this->imported = array();
        $this->skipped = array();
    }

    public function import(array $results) {
        if (is_array($results)) {
            $columns = array_shift($results);
            foreach($results as $result) {
                ${{ entity_cc }} = $this->{{ entity_cc }}Manager->create();
                ${{ entity_cc }}Id = $result[array_search('id', $columns)];
                ${{ entity_cc }}->setId(${{ entity_cc }}Id); 
{% for field in fields %}
{% if field.type == 'manyToOne' %}
                ${{ field.fieldName }}Id = $result[array_search('{{ field.fieldName }}_id', $columns)];
                if (${{ field.fieldName }}Id) {
                    ${{ field.fieldName }} = $this->{{ field.fieldName }}Manager->find(${{ field.fieldName }}Id);
                    if (${{ field.fieldName }}) {
                        ${{ entity_cc }}->set{{ field.fieldName | ucFirst }}(${{ field.fieldName }});
                    }
                }
{% else %}
                ${{ entity_cc }}->set{{ field.fieldName | ucFirst }}($result[array_search('{{ field.fieldName | camelCaseToUnderscore }}', $columns)]);
{% endif %}
{% endfor %}
                $this->{{ entity_cc }}Manager->update(${{ entity_cc }});

                $this->addImported(${{ entity_cc }}Id);
            }

            return true;
        }
    }

    public function addImported(${{ entity_cc }}Id)
    {
        $this->imported[] = ${{ entity_cc }}Id;
    }

    public function addSkipped(${{ entity_cc }}Id)
    {
        $this->skipped[] = ${{ entity_cc }}Id;
    }

    public function getImported()
    {
        return $this->imported;
    }

    public function getSkipped()
    {
        return $this->skipped;
    }

}
