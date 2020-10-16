<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%chat_user}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%chat}}`
 */
class m200818_084217_create_chat_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%chat_user}}', [
            'id' => $this->bigPrimaryKey()->unsigned()->notNull(),
            'user_id' => $this->bigInteger()->unsigned()->notNull(),
            'chat_id' => $this->bigInteger()->unsigned()->notNull(),
        ]);

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-chat_user-user_id}}',
            '{{%chat_user}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // add foreign key for table `{{%chat}}`
        $this->addForeignKey(
            '{{%fk-chat_user-chat_id}}',
            '{{%chat_user}}',
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
            '{{%fk-chat_user-user_id}}',
            '{{%chat_user}}'
        );

        // drops foreign key for table `{{%chat}}`
        $this->dropForeignKey(
            '{{%fk-chat_user-chat_id}}',
            '{{%chat_user}}'
        );

        $this->dropTable('{{%chat_user}}');
    }
}
