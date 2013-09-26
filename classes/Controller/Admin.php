<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller_Template
{
    public $template = 'admin';
    
    protected $_session;
    protected $_config;
    protected $_user;
    
    protected $_view;
    
    public function before()
    {
        parent::before();
        
        $this->_session = Session::instance();
        $this->_config = Kohana::$config->load('admin');
        $this->_user = $this->_session->get('admin_user');
        
        try {
            $contentView = strtolower($this->request->controller() . '/' . $this->request->action());
            $this->_view = View::factory($contentView);
        } catch (Exception $e) {
            $this->_view = View::factory('empty');
        }
        
        View::set_global('config', $this->_config);
    }
    
    public function after()
    {
        $this->template->content = $this->_view;
        
        parent::after();
    }
    
    public function action_index()
    {
        if (!$this->_user) {
            HTTP::redirect(URL::base() . 'admin/login');
            exit;
        }
    }
    
    public function action_login()
    {
        if ($this->_user) {
            HTTP::redirect(URL::base() . 'admin');
            exit;
        }
        
        $data = !empty($_POST) ? $_POST : array();
        
        $prepopulatedFields = array();
        
        $form = new Admin_Form_Login($data);
        $form->setPrepopulatedFields($prepopulatedFields);
        $validation = $form->validate();
        $formState = $form->getFormState();
        
        if ($formState['state'] == Admin_Form_Add::STATE_FORM_SENDED_OK) {
            if ($formState['data']['username'] == $this->_config['auth']['username'] && $formState['data']['password'] == $this->_config['auth']['password']) {
                $this->_session->set('admin_user', 1);
                HTTP::redirect(URL::base() . 'admin/index');
                exit;
            }
            
            $form->setGlobalErrorMessage('Niepoprawny login i/lub hasło');
            $formState = $form->getFormState();
        }

        $this->_view->set('form', $formState);
    }
    
    public function action_logout()
    {
        $this->_session->set('admin_user', null);
        HTTP::redirect(URL::base() . 'admin/login');
        exit;
    }
    
    public function action_module()
    {
        if (!$this->_user) {
            HTTP::redirect(URL::base() . 'admin/login');
            exit;
        }
        
        $module = $this->request->param('module');
        $moduleAction = $this->request->param('moduleAction');
        
        $className = 'Admin_Module_' . ucfirst($module);
        if (!class_exists($className)) {
            echo 'no module';
            exit;
        }
        
        $moduleClass = new $className($module);
        
        if (!$moduleClass instanceof Admin_Module) {
            unset($moduleClass);
            echo 'incorrect module';
            exit;
        }
        
        $moduleAction = $moduleAction . 'View';
        $this->_view = $moduleClass->$moduleAction($this->request);
    }

    protected function _getUserIp()
    {
        $ip = '';
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR"); 
        return $ip;
    }
}
