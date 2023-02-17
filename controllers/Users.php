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
    public $relationConfig;

    /**
     * Users constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Lovata.Buddies', 'main-menu-buddies', 'side-menu-buddies-user');
    }
    
     public function update_onImpersonateUser($recordId)
    {
        $model = $this->formFindModelObject($recordId);

        Auth::impersonate($model);

        Flash::success('Вы залогинились');
    }
}
