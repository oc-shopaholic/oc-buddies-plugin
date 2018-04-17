<?php namespace Lovata\Buddies\Components;

use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\Toolbox\Classes\Component\ComponentSubmitForm;
use Lovata\Toolbox\Traits\Helpers\TraitValidationHelper;

/**
 * Class Buddies
 * @package Lovata\Buddies\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class Buddies extends ComponentSubmitForm
{
    use TraitValidationHelper;

    /** @var null|User */
    protected $obUser = null;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.buddies::lang.component.buddies',
            'description' => 'lovata.buddies::lang.component.buddies_desc',
        ];
    }

    /**
     * Init plugin method
     */
    public function init()
    {
        $this->obUser = AuthHelper::getUser();
        parent::init();
    }

    /**
     * Get redirect page property list
     * @return array
     */
    protected function getRedirectPageProperties()
    {
        if (empty($this->obUser)) {
            return [];
        }

        return ['id' => $this->obUser->id];
    }
}
