<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%messages}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%chat}}`
 */
class m200818_085609_create_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%messages}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->bigInt()->notNull(),
            'message' => $this->text()->notNull(),
            'chat_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-messages-user_id}}',
            '{{%messages}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-messages-user_id}}',
            '{{%messages}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `chat_id`
        $this->createIndex(
            '{{%idx-messages-chat_id}}',
            '{{%messages}}',
            'chat_id'
        );

        // add foreign key for table `{{%chat}}`
        $this->addForeignKey(
            '{{%fk-messages-chat_id}}',
            '{{%messages}}',
            'chat_id',
            '{{%chat}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-messages-user_id}}',
            '{{%messages}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-messages-user_id}}',
            '{{%messages}}'
        );

        // drops foreign key for table `{{%chat}}`
        $this->dropForeignKey(
            '{{%fk-messages-chat_id}}',
            '{{%messages}}'
        );

        // drops index for column `chat_id`
        $this->dropIndex(
            '{{%idx-messages-chat_id}}',
            '{{%messages}}'
        );

        $this->dropTable('{{%messages}}');
    }
}
