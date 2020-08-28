<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%chat}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%game}}`
 */
class m200828_114018_add_game_id_column_to_chat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%chat}}', 'game_id', $this->bigInteger()->unsigned()->defaultValue(NULL));

        // add foreign key for table `{{%game}}`
        $this->addForeignKey(
            '{{%fk-chat-game_id}}',
            '{{%chat}}',
            'game_id',
            '{{%game}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%game}}`
        $this->dropForeignKey(
            '{{%fk-chat-game_id}}',
            '{{%chat}}'
        );

        $this->dropColumn('{{%chat}}', 'game_id');
    }
}
