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
    protected $_recordsPerPage = 10;
    protected $_listActions = array();
    protected $_listActionsClasses = array();
    
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
    
    public function getFieldType($fieldName)
    {
        return $this->_fields[$fieldName]['type'];
    }
    
    public function getFieldForeign($fieldName)
    {
        return !empty($this->_fields[$fieldName]['foreign']) ? $this->_fields[$fieldName]['foreign'] : null;
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
        $filters = array();
        
        foreach ($this->_filterFields as $filter) {
            // get field type
            $fieldType = $this->getFieldType($filter);
            $foreignFilter = $this->getFieldForeign($filter);
            
            $filterRow = null;
            
            switch ($fieldType) {
                case 'boolean':
                    $filterRow = array('type' => 'boolean', 'name' => $filter);
                    break;
                case 'char':
                    if ($foreignFilter) {
                        $filterRow = array(
                            'type' => 'foreign',
                            'name' => $filter,
                            'choices' => $this->_model->getForeignKeyChoices($foreignFilter['table'], $filter)
                        );
                    } else {
                        $filterRow = array(
                            'type' => 'choices', 
                            'name' => $filter,
                            'choices' => $this->_model->getChoices($filter)
                        );
                    }
                    break;
            }
            
            if ($filterRow) {
                $filters[] = $filterRow;
            }
        }
        
        return $filters;
    }
    
    protected function _prepareListActions()
    {
        if ($this->_listActionsClasses) {
            return;
        }
        
        foreach ($this->_listActions as $action) {
            $this->addListAction($action);
        }
    }

    public function addListAction($action) 
    {
        $className = $action[0];
        $params = $action[1];
        
        try {
            $class = new $className($params);
            $this->_listActionsClasses[$class->getId()] = $class;
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, sprintf('[admin] Class for list admin not found (%s)', $className));
            return;
        }
    }
    
    public function getListActions()
    {
        return $this->_listActionsClasses;
    }
    
    public function listView($request)
    {
        if (!$this->listActionEnabled()) {
            throw new HTTP_Exception_404();    
        }
        
        // params and filters
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
	    $limit = $this->_recordsPerPage;
        
        // filters
        $filters = $this->_parseFilters($_GET);
        
        // fetch data
        $records = $this->_model->getAll($page, $limit, $filters);
        $recordsCount = $this->_model->countAll($filters);
        
        // prepare list actions
        $this->_prepareListActions();
        // execute list action if needed
        if (!empty($_POST['list_action']) && !empty($_POST['action'])) {
            if (empty($this->_listActionsClasses[$_POST['action']])) {
                throw new HTTP_Exception_404;
            }
            
            $this->_listActionsClasses[$_POST['action']]->perform($this->_model, $filters);
        }
        
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
        
        $data = array_merge($_POST, $_FILES);
        
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
                HTTP::redirect(str_replace(URL::base(), '', $this->getRecordEditUrl($recordId)));
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
        
        HTTP::redirect(str_replace(URL::base(), '', $this->getListUrl()));
        exit;
    }
    
    public function changeView($request)
    {
        if (!$this->changeActionEnabled()) {
            throw new HTTP_Exception_404();
        }
        
        $record = $this->_model->getOne($_GET['id']);
        
        $accessLogModelName = Kohana::$config->load('admin.access_log_model');
        
        if ($record && $accessLogModelName) {
            $accessModel = Model::factory($accessLogModelName);
            $accessModel->save($this->getName(), 'view_details', $record['id'], Session::instance()->get('admin_user'));
        }
        
        $data = array_merge($_POST, $_FILES);
        
        $prepopulatedFields = $record;
        
        $form = new Admin_Form_Add($data, array('fields' => $this->getFields()));
        $form->setPrepopulatedFields($prepopulatedFields);
        $validation = $form->validate();
        $formState = $form->getFormState();
        
        if ($formState['state'] == Admin_Form_Add::STATE_FORM_SENDED_OK) {
            $editedFields = array();
            foreach ($this->getFields() as $fieldName => $field) {
                if ($field['type'] != 'primary' && (!isset($field['editable']) || $field['editable'])) {
                    $editedFields[$fieldName] = !empty($data[$fieldName]) ? $data[$fieldName] : false;
                }
            }

            $updateStatus = $this->_model->save($record['id'], $editedFields);
        
            if (!$updateStatus) {
                $form->setGlobalErrorMessage('Błąd zapisu w bazie danych. Spróbuj ponownie za chwilę.');
                $formState = $form->getFormState();
            } else {
                HTTP::redirect(str_replace(URL::base(), '', $this->getRecordEditUrl($record['id'])));
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
            if (!empty($data['options_' . $filter['name']])) {
                $filters['filters'][$filter['name']] = $data['options_' . $filter['name']];
            } elseif (!empty($data['foreign_' . $filter['name']])) {
                $filters['filters'][$filter['name']] = $data['foreign_' . $filter['name']];
            }
        }
        
        return $filters;
    }
}