<?php

use yii\db\Migration;

/**
 * Class m200411_140657_initial
 */
class m200411_140657_initial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `user` (
              `id` BIGINT(19) UNSIGNED NOT NULL AUTO_INCREMENT,
              `username` VARCHAR(45) NOT NULL,
              `email` VARCHAR(255) NOT NULL,
              `phone` VARCHAR(15) NULL DEFAULT NULL,
              `password` VARCHAR(255) NOT NULL,
              `token` VARCHAR(255) NOT NULL,
              `reset_password_token` VARCHAR(255) NULL DEFAULT NULL,
              `date_reset_password` DATETIME NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `username_UNIQUE` (`username` ASC),
              UNIQUE INDEX `email_UNIQUE` (`email` ASC),
              UNIQUE INDEX `token_UNIQUE` (`token` ASC))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8;
            
            CREATE TABLE IF NOT EXISTS `game` (
              `id` BIGINT(19) UNSIGNED NOT NULL AUTO_INCREMENT,
              `type` TINYINT(1) UNSIGNED NOT NULL,
              `date_start` DATETIME NOT NULL,
              `cost` DECIMAL(10,2) NOT NULL,
              `collected_sum` DECIMAL(10,2) NOT NULL DEFAULT 0,
              `date_end` DATETIME NULL DEFAULT NULL,
              `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8;
            
            CREATE TABLE IF NOT EXISTS `game_user` (
              `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
              `user_id` BIGINT(19) UNSIGNED NOT NULL,
              `game_id` BIGINT(19) UNSIGNED NOT NULL,
              `point` INT(10) UNSIGNED NOT NULL,
              `date_point` DATETIME NOT NULL,
              `is_correct` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_game_user_1_idx` (`game_id` ASC),
              INDEX `fk_game_user_2_idx` (`user_id` ASC),
              CONSTRAINT `fk_game_user_1`
                FOREIGN KEY (`game_id`)
                REFERENCES `game` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_game_user_2`
                FOREIGN KEY (`user_id`)
                REFERENCES `user` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8;
            
            CREATE TABLE IF NOT EXISTS `game_combination` (
              `id` BIGINT(19) UNSIGNED NOT NULL AUTO_INCREMENT,
              `game_id` BIGINT(19) UNSIGNED NOT NULL,
              `point` INT(10) UNSIGNED NOT NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_game_combination_1_idx` (`game_id` ASC),
              CONSTRAINT `fk_game_combination_1`
                FOREIGN KEY (`game_id`)
                REFERENCES `game` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8;
            
            CREATE TABLE IF NOT EXISTS `payment` (
              `id` BIGINT(19) UNSIGNED NOT NULL AUTO_INCREMENT,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8;";

        return $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200411_140657_initial cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200411_140657_initial cannot be reverted.\n";

        return false;
    }
    */
}
