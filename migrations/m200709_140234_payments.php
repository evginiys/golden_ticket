<?php

use yii\db\Migration;

/**
 * Class m200709_140234_payments
 */
class m200709_140234_payments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment', 'amount', $this->decimal(10, 2)->unsigned()->notNull());
        $this->addColumn('payment', 'currency', $this->integer(3)->unsigned()->notNull()->defaultValue(0));
        $this->addColumn('payment', 'to_user_id', $this->bigInteger(19)->unsigned());
        $this->addColumn('payment', 'from_user_id', $this->bigInteger(19)->unsigned());
        $this->addColumn('payment', 'ticket_id', $this->bigInteger()->unsigned());
        $this->addColumn('payment', 'type', $this->integer(3)->unsigned()->notNull());
        $this->addColumn('payment', 'status', $this->integer(3)->unsigned()->notNull());
        $this->addColumn('payment', 'comment', $this->text());
        $this->addColumn('payment', 'created_at', $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('payment', 'updated_at', $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        $this->addForeignKey('fk_payment_from_user_id', 'payment', 'from_user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_payment_to_user_id', 'payment', 'to_user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_payment_ticket', 'payment', 'ticket_id', 'ticket', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('payment', 'amount');
        $this->dropColumn('payment', 'currency');
        $this->dropColumn('payment', 'to_user_id');
        $this->dropColumn('payment', 'from_user_id');
        $this->dropColumn('payment', 'type');
        $this->dropColumn('payment', 'status');
        $this->dropColumn('payment', 'comment');
        $this->dropColumn('payment', 'created_at');
        $this->dropColumn('payment', 'updated_at');
    }
}
