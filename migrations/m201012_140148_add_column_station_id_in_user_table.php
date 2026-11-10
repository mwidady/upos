<?php

use yii\db\Migration;

/**
 * Class m201012_140148_add_column_station_id_in_user_table
 */
class m201012_140148_add_column_station_id_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'station_id', $this->integer()->null());
        $this->createIndex(
            'idx-user-station_id',
            'user',
            'station_id'
        );
        
        $this->addForeignKey(
            'fk-user-station_id',
            'user',
            'station_id',
            'stations',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('user', 'idx-user-station_id');
        $this->dropForeignKey('user', 'fk-user-station_id');
        $this->dropColumn('user', 'station_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201012_140148_add_column_station_id_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
