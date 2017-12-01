<?php namespace Lovata\Buddies\Models;

use Lovata\Buddies\Plugin;
use Kharanenka\Helper\CCache;
use October\Rain\Database\Model;

/**
 * Class Settings
 * @package Lovata\Buddies\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Settings extends Model
{

    const CACHE_TAG = 'buddies-settings';

    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'lovata_buddies_settings';
    public $settingsFields = 'fields.yaml';

    /**
     * Get setting value from cache
     * @param string $sCode
     * @return null|string
     */
    public static function getValue($sCode)
    {
        if (empty($sCode)) {
            return '';
        }

        $arTags = [Plugin::CACHE_TAG, self::CACHE_TAG];

        //Get value from cache
        $sResult = CCache::get($arTags, $sCode);
        if (!empty($sResult)) {
            return $sResult;
        }

        //Get value
        $sResult = self::get($sCode);

        //Set cache data
        CCache::forever($arTags, $sCode, $sResult);

        return $sResult;
    }

    /**
     * After save method
     */
    public function afterSave()
    {
        //Clear cache data
        $arValue = $this->value;
        $arKeyList = array_keys($arValue);

        foreach ($arKeyList as $sKey) {
            CCache::clear([Plugin::CACHE_TAG, self::CACHE_TAG], $sKey);
        }
    }
}
