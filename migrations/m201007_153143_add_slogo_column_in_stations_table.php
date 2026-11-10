<?php

use yii\db\Migration;

/**
 * Class m201007_153143_add_slogo_column_in_stations_table
 */
class m201007_153143_add_slogo_column_in_stations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('stations', 'slogo', $this->string(200)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('stations', 'slogo');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201007_153143_add_slogo_column_in_stations_table cannot be reverted.\n";

        return false;
    }
    */
}
