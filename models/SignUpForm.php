<?php


namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Class SignUpForm
 * @package app\models
 */
class SignUpForm extends Model
{
    public $username;
    public $email;
    public $phone;
    public $password;
    public $password_repeat;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'password_repeat'], 'required'],
            [['username'], 'string', 'max' => 45],
            [['email', 'password', 'password_repeat'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 15],
            [['email'], 'email'],
            [['password'], 'compare'],
            [['username'], 'unique'],
            [['email'], 'unique'],
        ];
    }

    /**
     * Signs up a user by provided data
     *
     * @return bool
     * @throws \Exception
     */
    public function signup(): bool
    {
        if ($this->validate()) {
            $user = new User([
                'username' => $this->username,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            $user->setPassword($this->password);
            $user->generateApiToken();

            if ($user->save()) {
                $playerRole = Yii::$app->authManager->getRole(User::ROLE_PLAYER);
                Yii::$app->authManager->assign($playerRole, $user->id);

                $user->updateTokenExpirationDate();

                return true;
            }
        }

        return false;
    }
}