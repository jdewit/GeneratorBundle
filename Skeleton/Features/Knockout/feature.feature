Feature: {{ entity }} Feature
    @javascript
    Scenario: Navigate to {{ entityCC }} list
        Given I am logged in as a user
        And I am on "/{{ entityCC }}/list"
        Then I should see "{{ entityCC | camelCaseToTitle }} List"

{% for field in fields %}{% if (field.type == 'manyToOne') %}
    @javascript
    Scenario: Create new {{ field.fieldName }} through {{ entityCC }} 
        Given I follow "New {{ entityCC | camelCaseToTitle }}"
        And I select "new" from "{{ bundleAlias }}_{{ entityCC }}[{{ field.fieldName }}]"
        And I fill in "Name" with "Test {{ field.fieldName | camelCaseToTitle }}"
        And I press "Create {{ field.fieldName | camelCaseToTitle }}"
        Then "Test {{ field.fieldName | camelCaseToTitle }}" in "{{ bundleVendor }}_{{ entityCC }}[{{ field.fieldName }}]" should be selected

{% endif %}{% endfor %}

    @javascript
    Scenario: Create a new {{ entityCC }}
        Given I follow "New {{ entityCC | camelCaseToTitle }}"
{% for field in fields %}{% if (field.type == 'manyToOne') or (field.type == 'manyToMany') %}
        And I select "Test {{ field.fieldName | camelCaseToTitle }}" from "{{ field.fieldName | camelCaseToTitle }}"
{% elseif field.type == 'decimal' %} 
        And I fill in "{{ field.fieldName | camelCaseToTitle }}" with "123.12"
{% elseif field.type == 'integer' %} 
        And I fill in "{{ field.fieldName | camelCaseToTitle }}" with "123"
{% elseif field.type == 'string' %}
{% if field.fieldName == 'date' %}
        And I fill in "{{ field.fieldName | camelCaseToTitle }}" with "2012-02-01"
{% else %}
        And I fill in "{{ field.fieldName | camelCaseToTitle }}" with "String"
{% endif %}
{% elseif field.type == 'text' %}
        And I fill in "{{ field.fieldName | camelCaseToTitle }}" with "This is text"
{% endif %}{% endfor %}
        And I press "Create {{ entity | camelCaseToTitle }}"
        Then I should see the alert "{{ entity | camelCaseToTitle }} created"

    @javascript
    Scenario: Edit a {{ entityCC }}
        Given I edit the first table item
{% for field in fields %}{% if field.type == 'decimal' %} 
        And I fill in "{{ field.fieldName | camelCaseToTitle }}" with "321.32"
{% elseif field.type == 'integer' %} 
        And I fill in "{{ field.fieldName | camelCaseToTitle }}" with "321"
{% elseif field.type == 'string' %}
        And I fill in "{{ field.fieldName | camelCaseToTitle }}" with "Updated string"
{% elseif field.type == 'text' %}
        And I fill in "{{ field.fieldName | camelCaseToTitle }}" with "This is updated text"
{% endif %}{% endfor %}
        And I press "Update {{ entity | camelCaseToTitle }}"
        Then I should see the alert "{{ entity | camelCaseToTitle }} updated"

    @javascript
    Scenario: Delete a {{ entityCC }} from form
        Given I edit the first table item
        And I follow "Delete {{ entity | camelCaseToTitle | lower }}"
        Then I should see the alert "{{ entity | camelCaseToTitle }} deleted"

    @javascript
    Scenario: Restore a {{ entityCC }} from form
        Given I follow "Deleted" 
        And I edit the first table item
        And I follow "Restore {{ entity | camelCaseToTitle | lower }}"
        Then I should see "{{ entity | camelCaseToTitle }} restored"

    @javascript
    Scenario: Batch Delete
        Given I follow "All" 
        And I check "check-all"
        And I follow "Batch delete"
        Then I should see the alert "{{ entity | camelCaseToTitle }}s deleted"

    @javascript
    Scenario: Test recent filter
        Given I follow "Recent"
        Then I should see "0 {{ entity | camelCaseToTitle | lower }}s found"

    @javascript
    Scenario: Test all filter
        Given I follow "All"
        Then I should see "0 {{ entity | camelCaseToTitle | lower }}s found"

    @javascript
    Scenario: Batch Restore
        Given I follow "Deleted" 
        And I check "check-all"
        And I follow "Batch restore"
        Then I should see the alert "{{ entity | camelCaseToTitle }}s restored"

    @javascript
    Scenario: Test delete filter
        Given I follow "Deleted"
        Then I should see "0 {{ entity | camelCaseToTitle | lower }}s found"

    @javascript
    Scenario: Batch Edit
        Given I check "check-all"
        And I follow "Batch edit"
        And I press all "Update {{ entity | camelCaseToTitle }}"
        Then I should see the alert "{{ entity | camelCaseToTitle }} updated"

    @javascript
    Scenario: Test empty Search
        Given I follow "Search"
        And press "Search {{ entityCC | camelCaseToTitle }}s"
        Then I should not see "0 {{ entity | camelCaseToTitle | lower }}s found"

    @javascript
    Scenario: Test wrong Search
        Given I follow "Search"
{% for field in fields %}{% if field.type == 'decimal' %} 
        And I fill in "{{ bundleAlias }}_{{ field.fieldName | camelCaseToTitle }}[{{ field.fieldName }}]" with "789"
{% elseif field.type == 'integer' %} 
        And I fill in "{{ bundleAlias }}_{{ entityCC }}_search[{{ field.fieldName }}]" with "789"
{% elseif field.type == 'string' %}
        And I fill in "{{ bundleAlias }}_{{ entityCC }}_search[{{ field.fieldName }}]" with "2012-01-01"
{% elseif field.type == 'text' %}
        And I fill in "{{ bundleAlias }}_{{ entityCC }}_search[{{ field.fieldName }}]" with "wrong text"
{% endif %}{% endfor %}
        And press "Search {{ entityCC | camelCaseToTitle }}s"
        Then I should see the alert "0 {{ entity | camelCaseToTitle | lower }}s found"

