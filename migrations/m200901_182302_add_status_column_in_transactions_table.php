<?php

use yii\db\Migration;

/**
 * Class m200901_182302_add_status_column_in_transactions_table
 */
class m200901_182302_add_status_column_in_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('transactions', 'status', $this->string(20));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('transactions', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200901_182302_add_status_column_in_transactions_table cannot be reverted.\n";

        return false;
    }
    */
}
