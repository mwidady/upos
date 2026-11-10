<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%stations}}`.
 */
class m201007_131829_create_stations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%stations}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(30)->notNull(),
            'description' => $this->text()->notNull(),
            'address' => $this->string(40)->notNull(),
            'location' => $this->string(20)->notNull(),
            'tel' => $this->string(50),
            'fax' => $this->string(40),
            'email' => $this->string(30)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%stations}}');
    }
}
