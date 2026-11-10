<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%expenditure}}`.
 */
class m200901_152011_create_expenditures_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%expenditures}}', [
            'id' => $this->primaryKey(),
            'ename' => $this->string()->notNull(),
            'etype' => $this->string(30)->notNull(),
            'amount' => $this->double()->notNull(),
            'company' => $this->string(50),
            'phone' => $this->string(12),
            'user_id' => $this->integer()->notNull()
        ]);

        $this->createIndex(
            'idx-expenditure_user_id',
            'expenditures',
            'user_id'  
        );

        $this->addForeignKey(
            'fk-expenditure_user_id',
            'expenditures',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%expenditures}}');
    }
}
