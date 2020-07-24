<?php

use yii\db\Migration;

/**
 * Class m200724_065726_add_archive_url_and_archive_hash_to_game
 */
class m200724_065726_add_archive_url_to_game extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('game', 'archive_url', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('game', 'archive_url');
    }
}
