<?php

use yii\db\Migration;

/**
 * Class m200805_091209_create_user_promo_payment_table
 *
 * Handles the creation of table `{{%user_promo_payment}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%payment}}`
 * - `{{%promo}}`
 */

class m200805_091209_create_user_promo_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_promo_payment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->bigInteger()->unsigned()->notNull(),
            'payment_id' => $this->bigInteger()->unsigned()->notNull(),
            'promo_id' => $this->bigInteger()->unsigned()->notNull(),
        ]);
        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_promo_payment-user_id}}',
            '{{%user_promo_payment}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'NO ACTION'
        );
        // add foreign key for table `{{%payment}}`
        $this->addForeignKey(
            '{{%fk-user_promo_payment-payment_id}}',
            '{{%user_promo_payment}}',
            'payment_id',
            '{{%payment}}',
            'id',
            'CASCADE',
            'NO ACTION'
        );
        // add foreign key for table `{{%promo}}`
        $this->addForeignKey(
            '{{%fk-user_promo_payment-promo_id}}',
            '{{%user_promo_payment}}',
            'promo_id',
            '{{%promo}}',
            'id',
            'CASCADE',
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
            '{{%fk-user_promo_payment-user_id}}',
            '{{%user_promo_payment}}'
        );
        // drops foreign key for table `{{%payment}}`
        $this->dropForeignKey(
            '{{%fk-user_promo_payment-payment_id}}',
            '{{%user_promo_payment}}'
        );
        // drops foreign key for table `{{%promo}}`
        $this->dropForeignKey(
            '{{%fk-user_promo_payment-promo_id}}',
            '{{%user_promo_payment}}'
        );

        $this->dropTable('{{%user_promo_payment}}');
    }
}
