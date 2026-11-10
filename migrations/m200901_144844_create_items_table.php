<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%item}}`.
 */
class m200901_144844_create_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%items}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'brand'=> $this->text(),
            'description' => $this->text(),
            'bprice' => $this->double()->notNull(),
            'sprice' => $this->double()->notNull(),
            'vat' => $this->string(3),
            'uid' => $this->integer()->notNull(),
            'unit' => $this->string(40)->notNull(),
            'supplier' => $this->string(),
            'sphone' => $this->string()
        ]);
        
        $this->createIndex(
            'idx-user-uid',
            'items',
            'uid'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-user-uid',
            'items',
            'uid',
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
        $this->dropTable('{{%items}}');
    }
}
