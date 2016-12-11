<?php namespace Lovata\Buddies\Models;

use Carbon\Carbon;
use Kharanenka\Helper\CustomValidationMessage;
use Kharanenka\Scope\ActiveField;
use Kharanenka\Helper\CCache;
use Lovata\Buddies\Plugin;
use October\Rain\Database\Builder;
use October\Rain\Database\Collection;
use Model;
use Lang;

/**
 * Class Property
 * @package Lovata\Buddies\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * 
 * @mixin Builder
 * @mixin \Eloquent
 * 
 * @property $id
 * @property bool $active
 * @property string $title
 * @property string $code
 * @property string $description
 * @property string $type (input, textarea, select, checkbox)
 * @property array $settings
 * @property int $sort_order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Property extends Model {
    
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;
    use ActiveField;
    use CustomValidationMessage;

    const NAME = 'property';
    const CACHE_TAG_ELEMENT = 'buddies-property-element';
    const CACHE_TAG_LIST = 'buddies-property-list';

    const TYPE_INPUT = 'input';
    const TYPE_TEXT_AREA = 'textarea';
    const TYPE_SELECT = 'select';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_DATE = 'date';
    const TYPE_COLOR_PICKER = 'colorpicker';
    const TYPE_MEDIA_FINDER = 'mediafinder';
    
    public $table = 'lovata_buddies_addition_properties';

    public $rules = [
        'title' => 'required',
        'code' => 'required|unique:lovata_buddies_addition_properties',
    ];

    public $customMessages = [];
    public $attributeNames = [];
    public $dates = ['created_at', 'updated_at'];
    public $jsonable = ['settings'];
    public $appends = ['type_name'];
    
    protected $fillable = [
        'active',
        'title',
        'code',
        'description',
        'type',
        'settings',
        'sort_order',
    ];

    public function __construct(array $attributes = []) {

        $this->setCustomMessage(Plugin::NAME, ['required', 'unique']);
        $this->setCustomAttributeName(Plugin::NAME, ['title', 'code']);

        parent::__construct($attributes);
    }

    public function afterSave()
    {
        $this->clearCache();
    }

    public function afterDelete()
    {
        $this->clearCache();
    }

    /**
     * Get type list
     * @return array
     */
    public function getTypeOptions() {
        
        return [
            self::TYPE_INPUT => Lang::get('lovata.buddies::lang.type.'.self::TYPE_INPUT),
            self::TYPE_TEXT_AREA => Lang::get('lovata.buddies::lang.type.'.self::TYPE_TEXT_AREA),
            self::TYPE_CHECKBOX => Lang::get('lovata.buddies::lang.type.'.self::TYPE_CHECKBOX),
            self::TYPE_SELECT => Lang::get('lovata.buddies::lang.type.'.self::TYPE_SELECT),
            self::TYPE_DATE => Lang::get('lovata.buddies::lang.type.'.self::TYPE_DATE),
            self::TYPE_COLOR_PICKER => Lang::get('lovata.buddies::lang.type.'.self::TYPE_COLOR_PICKER),
            self::TYPE_MEDIA_FINDER => Lang::get('lovata.buddies::lang.type.'.self::TYPE_MEDIA_FINDER),
        ];
    }
    
    public function setTypeNameAttribute($sValue) {}

    /**
     * Get property type name
     * @return mixed|string
     */
    public function getTypeNameAttribute() {
        
        $sType = $this->attributes['type'];
        
        $arTypeNames = $this->getTypeOptions();
        if(!empty($sType) && isset($arTypeNames[$sType])) {
            return $arTypeNames[$sType];
        }
        
        return '';
    }

    /**
     * Get widget data
     * @return array
     */
    public function getWidgetData() {
        
        $arResult = [];
        
        switch($this->type) {
            /** INPUT TYPE */
            case self::TYPE_INPUT :
                $arResult = [
                    'type' => 'text',
                ];
                break;
            /** TEXT AREA TYPE */
            case self::TYPE_TEXT_AREA :
                $arResult = [
                    'type' => 'textarea',
                    'size' => 'large',
                ];
                break;
            /** SELECT TYPE */
            case self::TYPE_SELECT :
                
                //Get property variants
                $arValueList = $this->getPropertyVariants();
                if(empty($arValueList)) {
                    break;
                }

                //Add empty value
                $arValueList = ['0' => Lang::get('lovata.buddies::lang.field.empty')] + $arValueList;
                
                $arResult = [
                    'type' => 'dropdown',
                    'options' => $arValueList,
                ];
                break;
            /** CHECKBOX TYPE */
            case self::TYPE_CHECKBOX :

                //Get property variants
                $arValueList = $this->getPropertyVariants();
                if(empty($arValueList)) {
                    break;
                }

                $arResult = [
                    'type' => 'checkboxlist',
                    'options' => $arValueList,
                ];
                break;
            /** DATE AND TIME PICKER TYPE */
            case self::TYPE_DATE :

                $sMode = null;
                $arSettings = $this->settings;
                if(empty($arSettings) || !isset($arSettings['datepicker']) || empty($arSettings['datepicker'])) {
                    break;
                }
                
                $sMode = $arSettings['datepicker'];
                if(!in_array($sMode, ['date', 'time', 'datetime'])) {
                    break;
                }
                
                $arResult = [
                    'type' => 'datepicker',
                    'mode' => $sMode,
                ];
                break;
            /** COLOR PICKER TYPE */
            case self::TYPE_COLOR_PICKER :
                $arResult = [
                    'type' => 'colorpicker',
                ];
                break;
            /** FILE FINDER TYPE */
            case self::TYPE_MEDIA_FINDER :

                $sMode = null;
                $arSettings = $this->settings;
                if(empty($arSettings) || !isset($arSettings['mediafinder']) || empty($arSettings['mediafinder'])) {
                    break;
                }

                $sMode = $arSettings['mediafinder'];
                if(!in_array($sMode, ['file', 'image'])) {
                    break;
                }

                $arResult = [
                    'type' => 'mediafinder',
                    'mode' => $sMode,
                ];
                break;
        }
        
        //Get common widget settings
        if(!empty($arResult)) {
            
            //Get property tab
            $arResult['tab'] = 'lovata.buddies::lang.field.addition_properties';
            $arResult['span'] = 'left';

            //Get property name with measure
            $arResult['label'] = $this->title;
        }
        
        return $arResult;
    }

    /**
     * Get property variants from settings
     * @return array
     */
    public function getPropertyVariants() {
        
        $arValueList = [];
        
        //Get and check settings array
        $arSettings = $this->settings;
        if(empty($arSettings) || !isset($arSettings['list']) || empty($arSettings['list'])) {
            return $arValueList;
        }

        //Get property value variants
        foreach($arSettings['list'] as $arValue) {

            if(!isset($arValue['value']) || empty($arValue['value'])) {
                continue;
            }

            $arValueList[$arValue['value']] = $arValue['value'];
        }
        
        return $arValueList;
    }

    /**
     * Get property data
     * @return array
     */
    public function getData() {

        $arResult = [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'type' => $this->type,
            'description' => $this->description,
        ];

        return $arResult;
    }

    /**
     * Get cached data
     * @param $iElementID
     * @param null|User $obElement
     * @return array|null
     */
    public static function getCacheData($iElementID, $obElement = null)
    {
        if(empty($iElementID)) {
            return [];
        }

        //Get cache data
        $arCacheTags = [Plugin::CACHE_TAG, self::CACHE_TAG_ELEMENT];
        $sCacheKey = $iElementID;

        $arResult = CCache::get($arCacheTags, $sCacheKey);
        if(!empty($arResult)) {
            return $arResult;
        }

        //Get element object
        if(empty($obElement)) {
            $obElement = self::active()->find($iElementID);
        }

        if(empty($obElement)) {
            return [];
        }

        $arResult = $obElement->getData();

        //Set cache data
        $iCacheTime = 10080;
        CCache::put($arCacheTags, $sCacheKey, $arResult, $iCacheTime);

        return $arResult;
    }

    /**
     * Get property list
     * @return array|null|string
     */
    public static function getPropertyList() {

        //Get cache data
        $arCacheTags = [Plugin::CACHE_TAG, self::CACHE_TAG_LIST];
        $sCacheKey = self::CACHE_TAG_LIST;

        $arResult = CCache::get($arCacheTags, $sCacheKey);
        if(!empty($arResult)) {
            return $arResult;
        }

        /** @var Collection $obPropertyList */
        $obPropertyList = Property::active()->orderBy('sort_order', 'asc')->get();
        if($obPropertyList->isEmpty()) {
            return [];
        }

        $arResult = [];
        /** @var Property $obProperty */
        foreach ($obPropertyList as $obProperty) {
            $arResult[] = $obProperty->getCacheData($obProperty->id, $obProperty);
        }

        //Set cache data
        $iCacheTime = 10080;
        CCache::put($arCacheTags, $sCacheKey, $arResult, $iCacheTime);

        return $arResult;
    }

    /**
     * Clear cache data
     */
    public function clearCache()
    {
        //Clear product data
        CCache::clear([Plugin::CACHE_TAG, self::CACHE_TAG_ELEMENT], $this->id);
        CCache::clear([Plugin::CACHE_TAG, self::CACHE_TAG_LIST], self::CACHE_TAG_LIST);
    }
}