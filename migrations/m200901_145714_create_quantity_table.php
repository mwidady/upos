<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quantity}}`.
 */
class m200901_145714_create_quantity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quantity}}', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'q_date' => 'timestamp on update current_timestamp',
            'quantity' => $this->integer()->notNull()
        ]);

        $this->createIndex(
            'idx-quantity_item_id',
            'quantity',
            'item_id'  
        );

        $this->createIndex(
            'idx-quantity_user_id',
            'quantity',
            'user_id'  
        );


        $this->addForeignKey(
            'fk-item-item_id',
            'quantity',
            'item_id',
            'items',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user-user_id',
            'quantity',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%quantity}}');
    }
}
