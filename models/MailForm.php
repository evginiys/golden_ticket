<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Class MailForm
 * @package app\models
 *
 * @property string email
 * @property string subject
 * @property string message
 */
class MailForm extends Model
{
    public $email;
    public $subject;
    public $message;

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['message', 'email', 'subject'], 'required'],
            [['subject'],'string'],
            [['email'], 'email'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'message'=>Yii::t('app', 'Message'),
            'subject'=>Yii::t('app', 'Subject'),
            'email'=>Yii::t('app', 'Email'),
        ];
    }
}