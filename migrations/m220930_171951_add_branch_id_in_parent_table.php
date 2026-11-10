<?php

use yii\db\Migration;

/**
 * Class m220930_171951_add_branch_id_in_parent_table
 */
class m220930_171951_add_branch_id_in_parent_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('items', 'branch_id', $this->integer()->null());
        $this->createIndex(
            'idx-item-branch_id',
            'items',
            'branch_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-item-branch_id',
            'items',
            'branch_id',
            'branch',
            'id',
            'CASCADE'
        );

        $this->addColumn('customers', 'branch_id', $this->integer()->null());
        $this->createIndex(
            'idx-customer-branch_id',
            'customers',
            'branch_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-customer-branch_id',
            'customers',
            'branch_id',
            'branch',
            'id',
            'CASCADE'
        );

       
        $this->addColumn('expenditures', 'branch_id', $this->integer()->null());
        $this->createIndex(
            'idx-expenditure-branch_id',
            'expenditures',
            'branch_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-expenditure-branch_id',
            'expenditures',
            'branch_id',
            'branch',
            'id',
            'CASCADE'
        );

        $this->addColumn('invoices', 'branch_id', $this->integer()->null());
        $this->createIndex(
            'idx-invoice-branch_id',
            'invoices',
            'branch_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-invoice-branch_id',
            'invoices',
            'branch_id',
            'branch',
            'id',
            'CASCADE'
        );

        $this->addColumn('faq', 'branch_id', $this->integer()->null());
        $this->createIndex(
            'idx-faq-branch_id',
            'faq',
            'branch_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-faq-branch_id',
            'faq',
            'branch_id',
            'branch',
            'id',
            'CASCADE'
        );

        $this->addColumn('batches', 'branch_id', $this->integer()->null());
        $this->createIndex(
            'idx-batch-branch_id',
            'batches',
            'branch_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-batch-branch_id',
            'batches',
            'branch_id',
            'branch',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220930_171951_add_branch_id_in_parent_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220930_171951_add_branch_id_in_parent_table cannot be reverted.\n";

        return false;
    }
    */
}
