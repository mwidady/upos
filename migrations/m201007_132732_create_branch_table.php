<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%branch}}`.
 */
class m201007_132732_create_branch_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%branch}}', [
            'id' => $this->primaryKey(),
            'station_id' =>$this->integer()->notNull(),
            'created_by' =>$this->integer()->notNull(),
            'name' => $this->string(30)->notNull(),
            'description' => $this->text()->notNull(),
            'address' => $this->string(40)->notNull(),
            'location' => $this->string(20)->notNull(),
            'tel' => $this->string(50),
            'fax' => $this->string(40),
            'email' => $this->string(30)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' =>$this->timestamp()->defaultValue(null)
        ]);

        $this->createIndex(
            'idx-station-station_id',
            'branch',
            'station_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-station-station_id',
            'branch',
            'station_id',
            'stations',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-user-created_by',
            'branch',
            'created_by'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-user-created_by',
            'branch',
            'created_by',
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
        $this->dropTable('{{%branch}}');
    }
}
