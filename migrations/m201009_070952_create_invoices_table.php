<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%invoices}}`.
 */
class m201009_070952_create_invoices_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%invoices}}', [
            'id' => $this->primaryKey(),
            'invoiceno' => $this->string(25)->notNull(),
            'amount' => $this->double()->notNull(),
            'generated_by' => $this->integer()->notNull(),
            'status' => $this->string(20)->notNull(),
            'paid_time' => $this->timestamp(),
            'paid_through' => $this->string(40),
            'payment_type' => $this->string(20),
            'paid_time' => $this->timestamp(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            //$this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP')
        ]);

        $this->createIndex(
            'idx-invoices_generated_by',
            'invoices',
            'generated_by'  
        );

        $this->addForeignKey(
            'fk-invoices_generated_by',
            'invoices',
            'generated_by',
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
        $this->dropTable('{{%invoices}}');
    }
}
