<?php namespace Lovata\Buddies\Tests\Unit\Item;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Buddies\Models\User;
use Lovata\Buddies\Classes\Item\UserItem;

/**
 * Class UserItemTest
 * @package Lovata\Buddies\Tests\Unit\Item
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA User
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class UserItemTest extends CommonTest
{
    /** @var  User */
    protected $obElement;

    protected $arCreateData = [
        'email'                 => 'email@email.com',
        'name'                  => 'name',
        'last_name'             => 'last_name',
        'middle_name'           => 'middle_name',
        'phone_list'            => ['123', '321'],
        'property'              => ['birthday' => '2017-10-21'],
        'password'              => 'test',
        'password_confirmation' => 'test',
    ];

    /**
     * Check item fields
     */
    public function testItemFields()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'User item data is not correct';

        $arCreatedData = $this->arCreateData;
        $arCreatedData['id'] = $this->obElement->id;

        unset($arCreatedData['password']);
        unset($arCreatedData['password_confirmation']);

        //Check item fields
        $obItem = UserItem::make($this->obElement->id);
        foreach ($arCreatedData as $sField => $sValue) {
            self::assertEquals($sValue, $obItem->$sField, $sErrorMessage);
        }
    }

    /**
     * Check update cache item data, after update model data
     */
    public function testItemClearCache()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'User item data is not correct, after model update';

        $obItem = UserItem::make($this->obElement->id);
        self::assertEquals('name', $obItem->name, $sErrorMessage);

        //Check cache update
        $this->obElement->name = 'test';
        $this->obElement->save();

        $obItem = UserItem::make($this->obElement->id);
        self::assertEquals('test', $obItem->name, $sErrorMessage);
    }

    /**
     * Check update cache item data, after remove element
     */
    public function testRemoveElement()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'User item data is not correct, after model remove';

        $obItem = UserItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty(), $sErrorMessage);

        //Remove element
        $this->obElement->delete();

        $obItem = UserItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty(), $sErrorMessage);
    }

    /**
     * Create data for test
     */
    protected function createTestData()
    {
        //Create new element data
        $arCreateData = $this->arCreateData;

        $this->obElement = User::create($arCreateData);
        $this->obElement = User::find($this->obElement->id);
    }
}