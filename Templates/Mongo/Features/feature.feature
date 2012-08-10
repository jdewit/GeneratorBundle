Feature: {{ entity }} Feature
    
    Scenario: Create a new {{ entityCC }}
        Given I am logged in as a user
        And I am on "/{{ entityCC }}/new"
{% for field in fields %}{% if (field.type == 'manyToOne') or (field.type == 'manyToMany') %}
        And I select "mr" from "Title"
{% elseif field.type == 'integer' %} 
        And I fill in "{{ field.fieldName|title }}" with 1
{% elseif field.type == 'string' %}
        And I fill in "{{ field.fieldName|title }}" with "string"
{% elseif field.type == 'text' %}
        And I fill in "{{ field.fieldName|title }}" with "text string"
{% endif %}{% endfor %}
        And I press "Create {{ entity }}"
        Then I should see "{{ entity }} created"

    Scenario: Create another new {{ entityCC }}
        Given I am logged in as a user
        And I am on "/{{ entityCC }}/new"
{% for field in fields %}{% if (field.type == 'manyToOne') or (field.type == 'manyToMany') %}
        And I select "mr" from "Title"
{% elseif field.type == 'integer' %} 
        And I fill in "{{ field.fieldName|title }}" with 1
{% elseif field.type == 'string' %}
        And I fill in "{{ field.fieldName|title }}" with "string"
{% elseif field.type == 'text' %}
        And I fill in "{{ field.fieldName|title }}" with "text string"
{% endif %}{% endfor %}
        And I press "Create {{ entity }}"
        Then I should see "{{ entity }} created"

    Scenario: Edit a {{ entityCC }}
        Given I am logged in as a user
        And I am on "/{{ entityCC }}/2"
        And I press "Update {{ entity }}"
        Then I should see "{{ entity }} updated"

    Scenario: Delete a {{ entityCC }}
        Given I am logged in as a user
        And I am on "/{{ entityCC }}/2"
        And I press "Delete {{ entity }}"
        Then I should see "{{ entity }} Deleted"

