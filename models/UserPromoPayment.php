<?php

namespace app\models;

use Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 *
 * Class UserPromoPayment
 * @package app\models
 *
 * This is the model class for table "user_promo_payment".
 *
 * @property int $id
 * @property int $user_id
 * @property int $payment_id
 * @property int $promo_id
 *
 * @property Payment $payment
 * @property Promo $promo
 * @property User $user
 */
class UserPromoPayment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_promo_payment';
    }

    /**
     * @param int $userId
     * @param int $promoId
     * @return Promo
     * @throws Exception
     */
    public static function buyPromo(int $userId, int $promoId): Promo
    {
        $promo = Promo::findOne($promoId);
        if (!$promo) {
            throw new Exception('Not found product');
        }
        $paymentId = Payment::payForPromo($userId, $promo->cost);
        $userPromoPayment = new self([
            'user_id' => $userId,
            'promo_id' => $promoId,
            'payment_id' => $paymentId
        ]);
        if (!$userPromoPayment->save()) {
            throw new Exception($userPromoPayment->getErrors());
        }
        return $promo;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'payment_id', 'promo_id'], 'required'],
            [['user_id', 'payment_id', 'promo_id'], 'integer'],
            [['payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payment::class, 'targetAttribute' => ['payment_id' => 'id']],
            [['promo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promo::class, 'targetAttribute' => ['promo_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'payment_id' => 'Payment ID',
            'promo_id' => 'Promo ID',
        ];
    }

    /**
     * Gets query for [[Payment]].
     *
     * @return ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(Payment::class(), ['id' => 'payment_id']);
    }

    /**
     * Gets query for [[Promo]].
     *
     * @return ActiveQuery
     */
    public function getPromo()
    {
        return $this->hasOne(Promo::class(), ['id' => 'promo_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class(), ['id' => 'user_id']);
    }
}
