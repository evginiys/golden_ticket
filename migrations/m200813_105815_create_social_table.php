<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%social}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m200813_105815_create_social_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%social}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->bigInteger()->unsigned()->defaultValue(NULL),
            'social_id' => $this->integer(),
            'social_client' => $this->text(),
        ]);

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-social-user_id}}',
            '{{%social}}',
            'user_id',
            '{{%user}}',
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
            '{{%fk-social-user_id}}',
            '{{%social}}'
        );

        $this->dropTable('{{%social}}');
    }
}
