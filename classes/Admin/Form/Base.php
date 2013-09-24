<?php

abstract class Admin_Form_Base
{
    protected $_validationInstance;
    protected $_data;
    protected $_errors;
    protected $_errorsDictionaryFile;
    protected $_state;
    protected $_globalErrorMessage;
    protected $_formMarkerField;
    
    protected $_additionalParams;
    
    const STATE_FORM_NOT_SENDED = 'not_sended';
    const STATE_FORM_SENDED_ERROR = 'errors';
    const STATE_FORM_SENDED_OK = 'ok';
    
    public function __construct($data, $additionalParams = array(), $dictionaryFile = 'forms/basic_errors')
    {
        $this->_validationInstance = Validation::factory($data);
        $this->_errorsDictionaryFile = $dictionaryFile;
        $this->_state = self::STATE_FORM_NOT_SENDED;
        $this->_data = $data;
        $this->_errors = array();
        $this->_additionalParams = $additionalParams;
    }
    
    public function getFormState()
    {
        return array(
            'state' => $this->_state,
            'errors' => $this->_errors,
            'data' => $this->_data,
            'global_error' => $this->_globalErrorMessage
        );
    }
    
    public function setGlobalErrorMessage($errorMessage)
    {
        $this->_state = self::STATE_FORM_SENDED_ERROR;
        $this->_globalErrorMessage = $errorMessage;
    }
    
    public function setFormMarkerField($fieldName)
    {
        $this->_formMarkerField = $fieldName;
    }
    
    abstract protected function validationRules();
    
    public function validate()
    {
        if (!$this->_data) {
            return;
        }
        
        $this->validationRules();
        
        if (!isset($this->_data[$this->_formMarkerField])) {
            return;
        }
        
        if ($this->_validationInstance->check()) {
            $this->_state = self::STATE_FORM_SENDED_OK;
            return true;
        }
        
        $this->_state = self::STATE_FORM_SENDED_ERROR;
        $this->_errors = $this->_validationInstance->errors($this->_errorsDictionaryFile);
        
        return false;
    }
    
    public function setPrepopulatedFields($fields)
    {
        $this->_data = $this->_data + $fields;
    }

    protected function addRule($field, $ruleName, $params = null)
    {
        if ($params === null) {
            $this->_validationInstance->rule($field, $ruleName);
        } else {
            $this->_validationInstance->rule($field, $ruleName, $params);
        }
    }
}