<?php

use yii\db\Migration;

/**
 * Class m200708_154555_tickets
 */
class m200708_154555_tickets extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('ticket_pack', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull(),
            'is_active' => $this->tinyInteger(1)->notNull()->defaultValue(1)
        ]);

        $this->createTable('ticket', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'ticket_pack_id' => $this->integer()->unsigned()->notNull(),
            'cost' => $this->decimal(10,2)->unsigned()->notNull(),
            'is_active' => $this->tinyInteger(1)->notNull()->defaultValue(1)
        ]);

        $this->addForeignKey('fk_ticket_pack_ticket', 'ticket', 'ticket_pack_id', 'ticket_pack', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('ticket');
        $this->dropTable('ticket_pack');
    }
}
