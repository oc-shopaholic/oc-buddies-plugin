<?php namespace Lovata\Buddies\Classes\Event;

use Lovata\Buddies\Models\User;
use Lovata\Buddies\Models\Property;
use Lovata\Buddies\Controllers\Users;

/**
 * Class ExtendCategoryModel
 * @package Lovata\Buddies\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
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
            $this->extendUserFields($obWidget);
        });
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
