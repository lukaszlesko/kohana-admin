<?php
class Admin_View_MainMenu extends View
{
    public static function factory($file = NULL, array $data = NULL)
    {
        return new Admin_View_MainMenu($file, $data);
    }
    
    public function __construct($file = NULL, array $data = NULL)
    {
        if ($file === null) {
            $file = 'admin/common/menu';
        }
        
        $data = array('modules' => $this->_getModules());
        
        parent::__construct($file, $data);
    }
    
    protected function _getModules()
    {
        $config = Kohana::$config->load('admin');
        
        // introspect defined in config models to list all manageable things
        $modules = array();
        foreach ($config['modules'] as $module) {
            $className = 'Admin_Module_' . ucfirst($module);
            if (!class_exists($className)) {
                continue;
            }
        
            $moduleClass = new $className($module);
        
            if (!$moduleClass instanceof Admin_Module) {
                unset($moduleClass);
                continue;
            }
        
            $modules[] = $moduleClass;
        }
        
        return $modules;
    }
}