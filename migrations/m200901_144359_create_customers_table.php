<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer}}`.
 */
class m200901_144359_create_customers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customers}}', [
            'id' => $this->primaryKey(),
            'fname' => $this->string(30)->notNull(),
            'lname' => $this->string(30)->notNull(),
            'gender' => $this->string(10)->notNull(),
            'phone' => $this->string(20)->notNull(),
            'company' => $this->string(50),
            'clogo' => $this->string(40),
            'position' => $this->string(30)->notNull(),
            'customer_no' => $this->string(30)->notNull(),
            'address' => $this->string(30)->notNull(),
            'email' => $this->string(30)->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%customers}}');
    }
}
