<?php

use yii\db\Migration;

/**
 * Class m201009_112304_add_column_in_invoices_table
 */
class m201009_112304_add_column_in_invoices_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('invoices', 'branch_id', $this->integer()->notNull());
        $this->addColumn('invoices', 'customer_id', $this->integer()->notNull());


        $this->createIndex(
            'idx-invoices-branch_id',
            'invoices',
            'branch_id'
        );
        
        $this->addForeignKey(
            'fk-invoices-branch_id',
            'invoices',
            'branch_id',
            'branch',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-invoices-customer_id',
            'invoices',
            'customer_id'
        );


        $this->addForeignKey(
            'fk-invoices-customer_id',
            'invoices',
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
        $this->dropIndex('invoices', 'idx-invoices-branch_id');
        $this->dropForeignKey('invoices', 'fk-invoices-branch_id');
       
        $this->dropIndex('invoices', 'idx-invoices-customer_id');
        $this->dropForeignKey('invoices', 'fk-invoices-customer_id');

        $this->dropColumn('invoices', 'branch_id');
        $this->dropColumn('invoices', 'customer_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201009_112304_add_column_in_invoices_table cannot be reverted.\n";

        return false;
    }
    */
}
