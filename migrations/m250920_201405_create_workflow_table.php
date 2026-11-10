<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%workflow}}`.
 */
class m250920_201405_create_workflow_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%workflow}}', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull(),
            'workflow_code' => $this->string(25)->notNull()
        ]);

        $this->createIndex(
            'idx-workflow-branch_id',
            'workflow',
            'branch_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-workflow-branch_id',
            'workflow',
            'branch_id',
            'branch',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%workflow}}');
    }
}
