<?php

class Admin_Form_Add extends Admin_Form_Base
{
    protected function validationRules()
    {
        $this->setFormMarkerField('add_form');
        
        foreach ($this->_additionalParams['fields'] as $fieldName => $field) {
            if (isset($field['editable']) && $field['editable'] === false) {
                continue;
            }
            
            if (!empty($field['required']) && $field['required']) {
                $this->addRule($fieldName, 'not_empty');
            }
        }
    }
}