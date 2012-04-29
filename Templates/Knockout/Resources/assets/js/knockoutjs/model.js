function {{ entityCC }}Model(options) {
    var self = this;
    self.{{ entityCC }} = options ? options.{{ entityCC }} : null,

    self.{{ entityCC }}Id = ko.observable();
{% for field in uniqueManyToOneRelations %} 
    self.available{{ field.targetEntityName | ucFirst }}s = ko.observableArray(avro.available{{ field.targetEntityName | ucFirst }}s);
{% endfor %}
{% for field in fields %}
{% if field.type == 'manyToOne' %}
    self.selected{{ field.fieldName | ucFirst }} = ko.observable();
{% elseif field.type == 'oneToMany' or field.type == 'manyToMany' %}
    self.selected{{ field.fieldName | ucFirst }} = ko.observableArray();
{% else %}
    self.{{ field.fieldName }} = ko.observable();
{% endif %}
{% endfor %}
    self.isDeleted = ko.observable();
    self.modalHeading = ko.observable();
    self.closeFormModal = function() {
        $('#{{ entityCC }}FormModal').modal('hide');
    }

    self.set{{ entity }} = function({{ entityCC }}) {
        $('#{{ entityCC }}FormModal').modal('show');
        if ({{ entityCC }}) {
            self.modalHeading('Edit {{ entityTitle }}');
            self.{{ entityCC }}Id({{ entityCC }}.id);
{% for field in fields %}
{% if field.fieldName == 'date' %}
            self.{{ field.fieldName }}({{ entityCC }}.{{ field.fieldName }});
{% elseif field.type == 'manyToMany' or field.type == 'oneToMany' %}
            self.selected{{ field.fieldName | ucFirst }}([]);
            $.each({{ entityCC }}.{{ field.fieldName }}, function() {
                self.selected{{ field.fieldName | ucFirst }}.push(this.id);
            });
{% elseif field.type == 'manyToOne' %}
            self.selected{{ field.fieldName | ucFirst }}({{ entityCC }}.{{ field.fieldName }} ? {{ entityCC }}.{{ field.fieldName }}.id : null);
{% elseif field.type == 'datetime' %}
            self.{{ field.fieldName }}({{ entityCC }}.{{ field.fieldName }});
{% else %}
            self.{{ field.fieldName }}({{ entityCC }}.{{ field.fieldName }});
{% endif %}
{% endfor %}
            self.isDeleted({{ entityCC }}.isDeleted);
        } else {
            self.modalHeading('New {{ entityTitle }}');
            self.{{ entityCC }}Id(null);
{% for field in fields %}
{% if field.fieldName == 'date' %}
            self.{{ field.fieldName }}(avro.getTodaysDate());
{% elseif field.type == 'manyToMany' or field.type == 'oneToMany' %}
            self.selected{{ field.fieldName | ucFirst }}([]);
{% elseif field.type == 'manyToOne' %}
            self.selected{{ field.fieldName | ucFirst }}(null);
{% elseif field.type == 'datetime' %}
            self.{{ field.fieldName }}(avro.getTodaysDate());
{% else %}
            self.{{ field.fieldName }}(null);
{% endif %}
{% endfor %}
            self.isDeleted(false);
        }
    }

    self.bindForm = function(form) {
        var $form = $(form);
        $form.avroAjaxSubmit({
            success: function(response){
                if (response['status'] == "OK") {
                    if (response['action'] == 'edit') {
                        avro.createNotice(response['notice']);
                        var {{ entityCC }} = ko.utils.arrayFirst(avro.{{ entityCC }}ListModel.{{ entityCC }}s(), function({{ entityCC }}) {
                            if ({{ entityCC }}.id == response['data']['id']) {
                                return true;
                            }
                        });
                        avro.{{ entityCC }}ListModel.{{ entityCC }}s.replace({{ entityCC }}, response['data']); 
                    } else {
                        avro.{{ entityCC }}ListModel.{{ entityCC }}s.unshift(response['data']); 
                    }
                    $('#{{ entityCC }}FormModal').modal('hide');
                } else {
                    $.each( response['errors'], function(field, message) {
                        $form.find('#error-container').append('<i class="sprite-error"></i> '+ message +'.').show();
                        $form.find('#{{ bundleAlias }}_{{ entityCC }}_'+ field).closest('.control-group').addClass('error');
                    });
                }
            }
        });
    };
}


