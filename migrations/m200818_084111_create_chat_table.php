<?php

use app\models\Chat;
use yii\db\Migration;
use yii\helpers\Json;

/**
 * Handles the creation of table `{{%chat}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m200818_084111_create_chat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%chat}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->notNull(),
            'game_id' => $this->bigInteger()->unsigned(),
            'user_id' => $this->bigInteger()->unsigned(),
            'type' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'name' => $this->text(),
        ]);

        $this->addForeignKey(
            '{{%fk-chat-game_id}}',
            '{{%chat}}',
            'game_id',
            '{{%game}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-chat-user_id}}',
            '{{%chat}}',
            'user_id',
            '{{%user}}',
            'id',
            'NO ACTION',
            'NO ACTION'
        );
        $commonChat = new Chat();
        $commonChat->type = Chat::TYPE_COMMON;
        $commonChat->name = "common";
        $commonChat->created_at = date('Y-m-d H:i:s');
        if (!$commonChat->save()) {
            throw new Exception($commonChat->getErrors());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-chat-user_id}}',
            '{{%chat}}'
        );

        // drops foreign key for table `{{%game}}`
        $this->dropForeignKey(
            '{{%fk-chat-game_id}}',
            '{{%chat}}'
        );

        $this->dropTable('{{%chat}}');
    }
}
