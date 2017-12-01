<?php namespace Lovata\Buddies\Models;

use Lovata\Toolbox\Models\CommonProperty;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Sortable;

/**
 * Class Property
 * @package Lovata\Buddies\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 */
class Property extends CommonProperty
{
    use Sortable;
    use Sluggable;

    public $table = 'lovata_buddies_addition_properties';

    protected $slugs = [];
    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:lovata_buddies_addition_properties',
    ];

    public $attributeNames = [
        'name' => 'lovata.toolbox::lang.field.name',
        'code' => 'lovata.toolbox::lang.field.code',
    ];

    protected $fillable = [
        'active',
        'name',
        'code',
        'description',
        'type',
        'settings',
        'sort_order',
    ];

    /**
     * Before save method
     */
    public function beforeSave()
    {
        $this->slug = $this->setSluggedValue('slug', 'name');
    }
}
