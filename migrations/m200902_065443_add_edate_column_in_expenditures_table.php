<?php

use yii\db\Migration;

/**
 * Class m200902_065443_add_edate_column_in_expenditures_table
 */
class m200902_065443_add_edate_column_in_expenditures_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('expenditures', 'edate', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('expenditures', 'edate');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200902_065443_add_edate_column_in_expenditures_table cannot be reverted.\n";

        return false;
    }
    */
}
