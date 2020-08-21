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
            'user_id' => $this->bigInteger()->unsigned()->notNull(),
            'message' => $this->text()->notNull(),
            'chat_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-messages-user_id}}',
            '{{%messages}}',
            'user_id',
            '{{%user}}',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        // add foreign key for table `{{%chat}}`
        $this->addForeignKey(
            '{{%fk-messages-chat_id}}',
            '{{%messages}}',
            'chat_id',
            '{{%chat}}',
            'id',
            'NO ACTION',
            'NO ACTION'
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

        // drops foreign key for table `{{%chat}}`
        $this->dropForeignKey(
            '{{%fk-messages-chat_id}}',
            '{{%messages}}'
        );

        $this->dropTable('{{%messages}}');
    }
}
