<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m200901_140555_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(40)->notNull(),
            'last_name' => $this->string(40)->notNull(),
            'phone_number' => $this->string(12),
            'username' => $this->string(),
            'email' => $this->string(40)->unique(),
            'password' => $this->string(),
            'authKey' => $this->string(),
            'password_reset_token' => $this->string(),
            'user_image' => $this->string()->null(),
            'status' => $this->integer()->defaultValue(1),
            'updated_at' => 'timestamp on update current_timestamp',
            'created_at' => $this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
