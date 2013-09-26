<?php

abstract class Admin_Module
{
    protected $_name = 'modulename';
    protected $_displayName = 'Module display name';
    protected $_actions = array('list', 'change', 'add', 'remove');
    protected $_fields = array(
        'id' => array('type' => 'primary', 'display_name' => 'ID'),
    );
    protected $_listFields = array('id');
    protected $_searchFields = array();
    protected $_filterFields = array();
    protected $_model;
    
    public function __construct($modelName)
    {
        $modelClassName = 'Model_' . ucfirst($modelName);
        $modelClass = new $modelClassName();
        if (!$modelClass instanceof Admin_Model_Scaffolding) {
            unset($modelClass);
            throw new Exception('Models used in admin module should implement Admin_Model_Scaffolding interface');
        }
        
        $this->_model = $modelClass;
        
        // urls
        $this->_listUrl = URL::base() . 'admin/module/' . $this->getName();
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function getDisplayName()
    {
        return $this->_displayName;
    }
    
    public function getListUrl()
    {
        return $this->_listUrl;
    }
    
    public function getRecordAddUrl()
    {
        return $this->_listUrl . '/add';
    }
    
    public function getRecordEditUrl($recordId)
    {
        return $this->_listUrl . '/change?id=' . $recordId;
    }
    
    public function getRecordRemoveUrl($recordId)
    {
        return $this->_listUrl . '/remove?id=' . $recordId;
    }
    
    public function getFields()
    {
        return $this->_fields;
    }
    
    public function getListFields()
    {
        return $this->_listFields;
    }
    
    public function getFieldDisplayName($fieldName)
    {
        return $this->_fields[$fieldName]['display_name'];
    }
    
    public function addActionEnabled()
    {
        return in_array('add', $this->_actions);
    }
    
    public function listActionEnabled()
    {
        return in_array('list', $this->_actions);
    }
    
    public function removeActionEnabled()
    {
        return in_array('remove', $this->_actions);
    }
    
    public function changeActionEnabled()
    {
        return in_array('change', $this->_actions);
    }
    
    public function isSearchEnabled()
    {
        return (bool) $this->_searchFields;
    }
    
    public function getSearchFields()
    {
        return $this->_searchFields;
    }
    
    public function getFilterFields()
    {
        return $this->_filterFields;
    }
    
    public function listView($request)
    {
        if (!$this->listActionEnabled()) {
            throw new HTTP_Exception_404();    
        }
        
        // params and filters
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
	    $limit = 10;
        
        // filters
        $filters = $this->_parseFilters($_GET);
        
        // fetch data
        $records = $this->_model->getAll($page, $limit, $filters);
        $recordsCount = $this->_model->countAll($filters);
        
        // pager
        $pager = Pagination::factory(
            array(
                'base_url' => $request->detect_uri(),
                'items_per_page' => $limit,
                'total_items' => $recordsCount,
                'view' => 'pagination/default'
            )
        );
        
        // view
        $view = View::factory('admin/module/list');
        $view->set('module', $this);
        $view->set('records', $records);
        $view->set('recordsCount', $recordsCount);
        $view->set('pager', $pager);
        $view->set('filters', $filters);
        
        return $view;
    }
    
    public function addView($request)
    {
        if (!$this->addActionEnabled()) {
            throw new HTTP_Exception_404();
        }
        
        $data = !empty($_POST) ? $_POST : array();
        
        $prepopulatedFields = array();
        
        $form = new Admin_Form_Add($data, array('fields' => $this->getFields()));
        $form->setPrepopulatedFields($prepopulatedFields);
        $validation = $form->validate();
        $formState = $form->getFormState();
        
        if ($formState['state'] == Admin_Form_Add::STATE_FORM_SENDED_OK) {
            // save into database
            $recordId = $this->_model->create($formState['data']);
            
            if (!$recordId) {
                $form->setGlobalErrorMessage('Błąd zapisu w bazie danych. Spróbuj ponownie za chwilę.');
                $formState = $form->getFormState();
            } else {
                // redirect to change form
                HTTP::redirect($this->getRecordEditUrl($recordId));
                exit;
            }
        }
        
        $view = View::factory('admin/module/add');
        $view->set('module', $this);
        $view->set('form', $formState);
        
        return $view;
    }
    
    public function removeView($request)
    {
        if (!$this->removeActionEnabled()) {
            throw new HTTP_Exception_404();
        }
        
        $record = $this->_model->getOne($_GET['id']);
        
        $this->_model->delete($record['id']);
        
        HTTP::redirect($this->getListUrl());
        exit;
    }
    
    public function changeView($request)
    {
        if (!$this->changeActionEnabled()) {
            throw new HTTP_Exception_404();
        }
        
        $record = $this->_model->getOne($_GET['id']);
        
        $data = !empty($_POST) ? $_POST : array();
        
        $prepopulatedFields = $record;
        
        $form = new Admin_Form_Add($data, array('fields' => $this->getFields()));
        $form->setPrepopulatedFields($prepopulatedFields);
        $validation = $form->validate();
        $formState = $form->getFormState();
        
        if ($formState['state'] == Admin_Form_Add::STATE_FORM_SENDED_OK) {
            $editedFields = array();
            foreach ($this->getFields() as $fieldName => $field) {
                if ($field['type'] != 'primary' && (!isset($field['editable']) || $field['editable'])) {
                    $editedFields[$fieldName] = !empty($data[$fieldName]) ? $data[$fieldName] : 0;
                }
            }

            $updateStatus = $this->_model->save($record['id'], $editedFields);
        
            if (!$updateStatus) {
                $form->setGlobalErrorMessage('Błąd zapisu w bazie danych. Spróbuj ponownie za chwilę.');
                $formState = $form->getFormState();
            } else {
                HTTP::redirect($this->getListUrl());
                exit;
            }
        }
        
        $view = View::factory('admin/module/add');
        $view->set('module', $this);
        $view->set('form', $formState);
        $view->set('record', $record);
        
        return $view;
    }
    
    protected function _parseFilters($data)
    {
        $filters = array('phrase' => null, 'search' => array(), 'filters' => array());
        
        if (!empty($data['query'])) {
            $filters['phrase'] = $data['query'];
            
            foreach ($this->getSearchFields() as $search) {
                $filters['search'][$search] = $data['query'];
            }
        }
        
        foreach ($this->getFilterFields() as $filter) {
            if (!empty($data['options_' . $filter])) {
                $filters['filters'][$filter] = $data['options_' . $filter];
            }
        }
        
        return $filters;
    }
}