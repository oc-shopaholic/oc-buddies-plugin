<?php namespace Lovata\Buddies\Classes\Event;

use System\Models\MailTemplate;
use System\Controllers\Settings as SettingsController;

use Lovata\Buddies\Models\User;
use Lovata\Buddies\Models\Property;
use Lovata\Buddies\Controllers\Users;
use Lovata\Toolbox\Models\Settings as SettingsModel;

/**
 * Class ExtendCategoryModel
 * @package Lovata\Buddies\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendFieldHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        $obEvent->listen('backend.form.extendFields', function ($obWidget) {
            $this->extendSettingsFields($obWidget);
            $this->extendUserFields($obWidget);
        });
    }


    /**
     * Extend Product fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendSettingsFields($obWidget)
    {
        if (!$obWidget->getController() instanceof SettingsController || $obWidget->isNested) {
            return;
        }

        if (!$obWidget->model instanceof SettingsModel) {
            return;
        }

        $arFieldList = [
            'registration_mail_template'     => [
                'label'       => 'lovata.buddies::lang.field.registration_mail_template',
                'tab'         => 'lovata.toolbox::lang.tab.mail',
                'span'        => 'left',
                'type'        => 'dropdown',
                'emptyOption' => 'lovata.toolbox::lang.field.empty',
                'options'     => MailTemplate::listAllTemplates(),
            ],
            'restore_password_mail_template' => [
                'label'       => 'lovata.buddies::lang.field.restore_password_mail_template',
                'tab'         => 'lovata.toolbox::lang.tab.mail',
                'span'        => 'left',
                'type'        => 'dropdown',
                'emptyOption' => 'lovata.toolbox::lang.field.empty',
                'options'     => MailTemplate::listAllTemplates(),
            ],
        ];

        $obWidget->addTabFields($arFieldList);
    }

    /**
     * Extend fields for user model
     * @param \Backend\Widgets\Form $obWidget
     */
    public function extendUserFields($obWidget)
    {
        if (!$obWidget->getController() instanceof Users || $obWidget->isNested) {
            return;
        }

        // Only for the Product model
        if (!$obWidget->model instanceof User || $obWidget->context != 'update') {
            return;
        }

        $obPropertyList = Property::active()->orderBy('sort_order', 'asc')->get();
        if ($obPropertyList->isEmpty()) {
            return;
        }

        //Get widget data for properties
        $arAdditionPropertyData = [];
        /** @var Property $obProperty */
        foreach ($obPropertyList as $obProperty) {
            $arPropertyData = $obProperty->getWidgetData();
            if (!empty($arPropertyData)) {
                $arAdditionPropertyData[Property::NAME.'['.$obProperty->code.']'] = $arPropertyData;
            }
        }

        // Add fields
        if (!empty($arAdditionPropertyData)) {
            $obWidget->addTabFields($arAdditionPropertyData);
        }
    }
}
