<?php

use yii\db\Migration;

/**
 * Class m201009_184316_add_column_invoice_id_in_transactions_table
 */
class m201009_184316_add_column_invoice_id_in_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('transactions', 'invoice_id', $this->integer()->null());
        $this->createIndex(
            'idx-transactions-invoice_id',
            'transactions',
            'invoice_id'
        );
        
        $this->addForeignKey(
            'fk-transactions-invoice_id',
            'transactions',
            'invoice_id',
            'invoices',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('transactions', 'idx-transactions-invoice_id');
        $this->dropForeignKey('transactions', 'fk-transactions-invoice_id');
        $this->dropColumn('transactions', 'invoice_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201009_184316_add_column_invoice_id_in_transactions_table cannot be reverted.\n";

        return false;
    }
    */
}
