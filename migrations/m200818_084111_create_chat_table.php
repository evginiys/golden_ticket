<?php

use yii\db\Migration;

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
            'id' => $this->primaryKey(),
            'user_id' => $this->bigInteger()->unsigned()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'name' => $this->text()->notNull(),
        ]);

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-chat-user_id}}',
            '{{%chat}}',
            'user_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'SET NULL'
        );
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

        $this->dropTable('{{%chat}}');
    }
}
