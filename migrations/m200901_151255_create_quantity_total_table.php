<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quantity_total}}`.
 */
class m200901_151255_create_quantity_total_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quantity_total}}', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer()->notNull(),
            'quantity_t' => $this->integer()->notNull(),
            'last_update' => 'timestamp on update current_timestamp',
        ]);

        $this->createIndex(
            'idx-quantitytotal_item_id',
            'quantity_total',
            'item_id'  
        );

        $this->addForeignKey(
            'fk-quantitytotal-item_id',
            'quantity_total',
            'item_id',
            'items',
            'id',
            'CASCADE'
        );
        }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%quantity_total}}');
    }
}
