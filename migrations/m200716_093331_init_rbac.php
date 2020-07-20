<?php

use app\models\User;
use yii\db\Migration;

/**
 * Class m200716_093331_init_rbac
 */
class m200716_093331_init_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $playerRole = Yii::$app->authManager->createRole(User::ROLE_PLAYER);
        $playerRole->description = ucfirst(User::ROLE_PLAYER);
        Yii::$app->authManager->add($playerRole);

        $adminRole = Yii::$app->authManager->createRole(User::ROLE_ADMIN);
        $adminRole->description = 'Administrator';
        Yii::$app->authManager->add($adminRole);

        $bannedRole = Yii::$app->authManager->createRole(User::ROLE_BANNED);
        $bannedRole->description = ucfirst(User::ROLE_BANNED);
        Yii::$app->authManager->add($bannedRole);

        $playerPermission = Yii::$app->authManager->createPermission(User::ROLE_PLAYER . '_common');
        $playerPermission->description = 'Common ' . User::ROLE_PLAYER . ' actions';
        Yii::$app->authManager->add($playerPermission);
        Yii::$app->authManager->addChild($playerRole, $playerPermission);

        $adminPermission = Yii::$app->authManager->createPermission(User::ROLE_ADMIN . '_common');
        $adminPermission->description = 'Common administrator actions';
        Yii::$app->authManager->add($adminPermission);
        Yii::$app->authManager->addChild($adminRole, $adminPermission);

        $bannedPermission = Yii::$app->authManager->createPermission(User::ROLE_BANNED . '_common');
        $bannedPermission->description = 'Common ' . User::ROLE_BANNED . ' actions';
        Yii::$app->authManager->add($bannedPermission);
        Yii::$app->authManager->addChild($bannedRole, $bannedPermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->authManager->removeAll();
    }
}
