<?php

use yii\db\Migration;

/**
 * Class m200901_182754_add_ts_time_column_in_transactions_table
 */
class m200901_182754_add_ts_time_column_in_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('transactions', 'ts_time', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('transactions', 'ts_time');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200901_182754_add_ts_time_column_in_transactions_table cannot be reverted.\n";

        return false;
    }
    */
}
