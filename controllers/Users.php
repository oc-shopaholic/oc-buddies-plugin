<?php namespace Lovata\Buddies\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Users extends Controller
{
    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Lovata.Buddies', 'main-menu-buddies', 'side-menu-buddies-user');
    }
}