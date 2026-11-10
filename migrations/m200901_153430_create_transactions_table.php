<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transactions}}`.
 */
class m200901_153430_create_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transactions}}', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer()->notNull(),
            'price' => $this->double()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'trans_date' => $this->timestamp()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'payment_type' => $this->string(20)->notNull(),
            'transaction_no' => $this->string(50)->notNull()
        ]);

        $this->createIndex(
            'idx-transactions-item_id',
            'transactions',
            'item_id'  
        );

        $this->addForeignKey(
            'fk-transactions-item_id',
            'transactions',
            'item_id',
            'items',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-transactions-user_id',
            'transactions',
            'user_id'  
        );

        $this->addForeignKey(
            'fk-transactions-user_id',
            'transactions',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-transactions-customer_id',
            'transactions',
            'customer_id'  
        );

        $this->addForeignKey(
            'fk-transactions-customer_id',
            'transactions',
            'customer_id',
            'customers',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transactions}}');
    }
}
