<?php

use yii\db\Migration;

/**
 * Class m200720_093146_add_promo_table
 */
class m200720_093146_add_promo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%promo}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'name' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'imageUrl' => $this->string(),
            'cost' => $this->decimal(10, 2)->unsigned()->notNull(),
            'promocode' => $this->string(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'expiration_at' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%promo}}');
    }
}
