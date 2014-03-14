<?php
abstract class Admin_ListAction_Base
{
    protected $_id = '';
    protected $_displayName = '';
    protected $_params;
    
    public function __construct($params)
    {
        $this->_params = $params;
    }
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function getDisplayName()
    {
        return $this->_displayName;
    }
    
    public function perform($model, $filters)
    {
        $this->_perform($model, $filters, $this->_params);
    }
}