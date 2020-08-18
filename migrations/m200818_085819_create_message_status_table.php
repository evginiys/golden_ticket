<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%message_status}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%messages}}`
 */
class m200818_085819_create_message_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%message_status}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->bigInt()->notNull(),
            'message_id' => $this->integer()->notNull(),
            'is_read' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-message_status-user_id}}',
            '{{%message_status}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-message_status-user_id}}',
            '{{%message_status}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `message_id`
        $this->createIndex(
            '{{%idx-message_status-message_id}}',
            '{{%message_status}}',
            'message_id'
        );

        // add foreign key for table `{{%messages}}`
        $this->addForeignKey(
            '{{%fk-message_status-message_id}}',
            '{{%message_status}}',
            'message_id',
            '{{%messages}}',
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
            '{{%fk-message_status-user_id}}',
            '{{%message_status}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-message_status-user_id}}',
            '{{%message_status}}'
        );

        // drops foreign key for table `{{%messages}}`
        $this->dropForeignKey(
            '{{%fk-message_status-message_id}}',
            '{{%message_status}}'
        );

        // drops index for column `message_id`
        $this->dropIndex(
            '{{%idx-message_status-message_id}}',
            '{{%message_status}}'
        );

        $this->dropTable('{{%message_status}}');
    }
}
