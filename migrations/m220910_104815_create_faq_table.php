<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%faq}}`.
 */
class m220910_104815_create_faq_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%faq}}', [
            'id' => $this->primaryKey(),
            'question' => $this->text()->notNull(),
            'answer' => $this->text()->notNull(),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        $this->createIndex(
            'idx-user-createdby',
            'faq',
            'created_by'
        );

       
        $this->addForeignKey(
            'fk-user-createdby',
            'faq',
            'created_by',
            'user',
            'id'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%faq}}');
    }
}
