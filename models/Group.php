<?php namespace Lovata\Buddies\Models;

use Carbon\Carbon;
use Kharanenka\Helper\CustomValidationMessage;
use Lovata\Buddies\Plugin;
use October\Rain\Auth\Models\Group as GroupBase;
use October\Rain\Database\Builder;
use October\Rain\Database\Collection;

/**
 * Class Group
 * @package Lovata\Buddies\Models
 * @author Andrey Kahranenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin Builder
 * @mixin \Eloquent
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|User[] $users
 */
class Group extends GroupBase
{
    use \October\Rain\Database\Traits\Validation;
    use CustomValidationMessage;
    
    protected $table = 'lovata_buddies_groups';

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required|between:3,64',
        'code' => 'required|regex:/^[a-zA-Z0-9_\-]+$/|unique:user_groups',
    ];
    public $customMessages = [];
    public $attributeNames = [];
    
    public $belongsToMany = [
        'users' => ['Lovata\Buddies\Models\User', 'table' => 'lovata_buddies_users_groups']
    ];
    
    protected $fillable = [
        'name',
        'code',
        'description'
    ];

    public function __construct(array $attributes = [])
    {
        $this->setCustomMessage(Plugin::NAME, ['required', 'between', 'unique']);
        $this->setCustomAttributeName(Plugin::NAME, ['name', 'code']);

        parent::__construct($attributes);
    }
}