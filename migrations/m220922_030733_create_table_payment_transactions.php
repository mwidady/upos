<?php

use yii\db\Migration;

/**
 * Class m220922_030733_create_table_payment_transactions
 */
class m220922_030733_create_table_payment_transactions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payment_transactions}}', [
            'id' => $this->primaryKey(),
            'amount_paid' => $this->double(),
            'amount_remain' => $this->double(),
            'initial_amount' => $this->double(),
            'paid_through' => $this->string()->null(),
            'account_no' => $this->string()->null(),
            'paid_by' => $this->string(),
            'received_by' => $this->integer(),
            'invoice_id' => $this->integer(),
            'paid_date' =>  $this->timestamp(),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        $this->createIndex(
            'idx-user-received_by',
            'payment_transactions',
            'received_by'
        );

        $this->createIndex(
            'idx-invoice-invoice_id',
            'payment_transactions',
            'invoice_id'
        );

       
        $this->addForeignKey(
            'fk-user-received_by',
            'payment_transactions',
            'received_by',
            'user',
            'id'
        );

        $this->addForeignKey(
            'fk-invoice-invoice_id',
            'payment_transactions',
            'invoice_id',
            'invoices',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%payment_transactions}}');
    }
}
