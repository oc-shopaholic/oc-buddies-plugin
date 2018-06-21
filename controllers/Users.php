<?php namespace Lovata\Buddies\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Class Users
 * @package Lovata\Buddies\Controllers
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Users extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.FormController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    /**
     * Users constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Lovata.Buddies', 'main-menu-buddies', 'side-menu-buddies-user');
    }
}