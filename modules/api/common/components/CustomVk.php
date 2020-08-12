<?php

namespace app\modules\api\common\components;

use yii\authclient\clients\VKontakte;

/**
 * Class CustomVk
 * @package app\modules\api\common\components
 */
class CustomVk extends VKontakte
{
    public $validateAuthState = false;
public $clientId='7560911';
public $clientSecret='F57bExi332oV0ZoeL521';
    /**
     * @var array list of attribute names, which should be requested from API to initialize user attributes.
     * @since 2.0.4
     */
    public $attributeNames = [
        'uid',
        'first_name',
        'last_name',
        'nickname',
        'screen_name',
        'sex',
        'bdate',
        'city',
        'country',
        'timezone',
        'photo',
        'email'
    ];

    public function defaultName()
    {
        return 'customVk';
    }
}