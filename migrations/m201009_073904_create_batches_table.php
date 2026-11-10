<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%batches}}`.
 */
class m201009_073904_create_batches_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%batches}}', [
            'id' => $this->primaryKey(),
            'start_at' => $this->integer()->notNull(),
            'end_at' => $this->integer()->notNull(),
            'generated_by' => $this->integer()->notNull(),
            'station_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-batches_generated_by',
            'batches',
            'generated_by'  
        );

        $this->addForeignKey(
            'fk-batches_generated_by',
            'batches',
            'generated_by',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-batches_station_id',
            'batches',
            'station_id'  
        );

        $this->addForeignKey(
            'fk-batches_station_id',
            'batches',
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
        $this->dropTable('{{%batches}}');
    }
}
