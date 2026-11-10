<?php

use yii\db\Migration;

class m260921_100000_create_expense_types_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%expense_types}}', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer()->notNull(),
            'name' => $this->string(60)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->null(),
        ]);

        $this->createIndex('ux-expense-types-branch-name', 'expense_types', ['branch_id', 'name'], true);
        $this->createIndex('idx-expense-types-created-by', 'expense_types', 'created_by');
        $this->addForeignKey('fk-expense-types-branch', 'expense_types', 'branch_id', 'branch', 'id', 'CASCADE');
        $this->addForeignKey('fk-expense-types-created-by', 'expense_types', 'created_by', 'user', 'id', 'SET NULL');

        $this->addColumn('expenditures', 'expense_type_id', $this->integer()->null()->after('etype'));
        $this->alterColumn('expenditures', 'etype', $this->string(60)->notNull());
        $this->createIndex('idx-expenditures-expense-type', 'expenditures', 'expense_type_id');
        $this->addForeignKey(
            'fk-expenditures-expense-type',
            'expenditures',
            'expense_type_id',
            'expense_types',
            'id',
            'SET NULL'
        );

        $this->execute(
            "INSERT INTO expense_types (branch_id, name, status, created_by, created_at)
             SELECT e.branch_id, TRIM(e.etype), 1, MIN(e.user_id), NOW()
             FROM expenditures e
             WHERE e.branch_id IS NOT NULL AND TRIM(COALESCE(e.etype, '')) <> ''
             GROUP BY e.branch_id, TRIM(e.etype)"
        );

        foreach (['Domestic', 'Transport', 'Petty Cash'] as $typeName) {
            $this->execute(
                "INSERT INTO expense_types (branch_id, name, status, created_at)
                 SELECT b.id, :name, 1, NOW()
                 FROM branch b
                 WHERE NOT EXISTS (
                     SELECT 1 FROM expense_types et
                     WHERE et.branch_id = b.id AND et.name = :nameCheck
                 )",
                [':name' => $typeName, ':nameCheck' => $typeName]
            );
        }

        $this->execute(
            'UPDATE expenditures e
             INNER JOIN expense_types et
                ON et.branch_id = e.branch_id AND et.name = TRIM(e.etype)
             SET e.expense_type_id = et.id'
        );

        $routeExists = (new \yii\db\Query())
            ->from('auth_item')
            ->where(['name' => '/expense-types/*'])
            ->exists($this->db);
        if (!$routeExists) {
            $this->insert('auth_item', [
                'name' => '/expense-types/*',
                'type' => 2,
                'description' => 'Manage expense types',
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }

        $parents = (new \yii\db\Query())
            ->select('parent')
            ->from('auth_item_child')
            ->where(['child' => '/expenditures/*'])
            ->column($this->db);
        foreach ($parents as $parent) {
            $exists = (new \yii\db\Query())
                ->from('auth_item_child')
                ->where(['parent' => $parent, 'child' => '/expense-types/*'])
                ->exists($this->db);
            if (!$exists) {
                $this->insert('auth_item_child', ['parent' => $parent, 'child' => '/expense-types/*']);
            }
        }
    }

    public function safeDown()
    {
        $this->delete('auth_item_child', ['child' => '/expense-types/*']);
        $this->delete('auth_item', ['name' => '/expense-types/*']);
        $this->dropForeignKey('fk-expenditures-expense-type', 'expenditures');
        $this->dropIndex('idx-expenditures-expense-type', 'expenditures');
        $this->dropColumn('expenditures', 'expense_type_id');
        $this->alterColumn('expenditures', 'etype', $this->string(30)->notNull());
        $this->dropTable('{{%expense_types}}');
    }
}

