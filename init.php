<?php defined('SYSPATH') or die('No direct script access.');

Route::set('admin_module', 'admin/module/<module>(/<moduleAction>)')->defaults(
    array(
        'controller' => 'admin',
        'action'     => 'module',
        'moduleAction' => 'list',
    )
);
