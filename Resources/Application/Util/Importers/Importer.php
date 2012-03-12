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
{% for field in fields %}
{% if field.type == 'manyToOne' %}
    protected ${{ field.fieldName }}Manager;
{% endif %}
{% endfor %}

    public function __construct(SecurityContextInterface $context {% for field in fields %}{% if field.type == 'manyToOne' %}, {{ field.fieldName | ucFirst }}Manager ${{ field.fieldName }}Manager{% endif %}{% endfor %})
    {
        $this->user = $context->getToken()->getUser();
{% for field in fields %}
{% if field.type == 'manyToOne' %}
        $this->{{ field.fieldName }} = ${{ field.fieldName }}Manager;
{% endif %}
{% endfor %}
    }

    public function import(array $results) {
        if (is_array($results)) {
            $columns = array_shift($results);
            foreach($results as $result) {
                ${{ entity_cc }} = $this->{{ entity_cc }}Manager->create();
                ${{ entity_cc }}->setId($result[array_search('id', $columns)]); 
{% for field in fields %}
{% if field.type == 'manyToOne' %}
                if ($result[array_search('{{ field.fieldName | camelCaseToUnderscore }}', $columns)]) {
                    ${{ field.fieldName }}Title = trim(implode(" ", preg_split('/(?=[A-Z])/', ucfirst($result[array_search('{{ field.fieldName | camelCaseToUnderscore }}', $columns)]))));
                    ${{ field.fieldName }} = $this->{{ field.fieldName }}Manager->findOneBy(array('name' => ${{ field.fieldName }}Title));
                    if (!${{ field.fieldName }}) {
                        ${{ field.fieldName }} = $this->{{ field.fieldName }}Manager->create();
                        ${{ field.fieldName }}->setName(${{ field.fieldName }}Title);
                        ${{ field.fieldName }} = $this->{{ field.fieldName }}Manager->update(${{ field.fieldName }}, false);
                    }
                    ${{ entity_cc }}->set{{ field.fieldName | ucFirst }}(${{ field.fieldName }});
                }
{% else %}
                ${{ entity_cc }}->set{{ field.fieldName | ucFirst }}($result[array_search('{{ entity_cc | camelCaseToUnderscore }}', $columns)]);
{% endif %}
{% endfor %}
                $this->{{ entity_cc }}Manager->update(${{ entity_cc }});

                return true;
            }
        }
    }
}
