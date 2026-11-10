<?php

use yii\db\Migration;

/**
 * Class m201007_150620_add_branch_id_column_in_user_table
 */
class m201007_150620_add_branch_id_column_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'branch_id', $this->integer()->null());

        $this->createIndex(
            'idx-branch-branch_id',
            'user',
            'branch_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-branch-branch_id',
            'user',
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
        $this->dropIndex('user', 'idx-branch-branch_id');
        $this->dropForeignKey('user', 'fk-branch-branch_id');
        $this->dropColumn('user', 'branch_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201007_150620_add_branch_id_column_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
