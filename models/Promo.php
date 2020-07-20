<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "promo".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $imageUrl
 * @property float $cost
 * @property string|null $promocode
 * @property string $created_at
 * @property string $expiration_at
 */
class Promo extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'cost', 'expiration_at'], 'required'],
            [['description'], 'string'],
            [['cost'], 'number', 'min' => 0.0],
            [['created_at', 'expiration_at'], 'safe'],
            [['name', 'imageUrl', 'promocode'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'imageUrl' => Yii::t('app', 'Image'),
            'cost' => Yii::t('app', 'Cost'),
            'promocode' => Yii::t('app', 'Promocode'),
            'created_at' => Yii::t('app', 'Created At'),
            'expiration_at' => Yii::t('app', 'Expiration At'),
        ];
    }
}
