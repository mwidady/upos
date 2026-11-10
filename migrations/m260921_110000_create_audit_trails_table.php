<?php

use yii\db\Migration;
use yii\db\Query;

class m260921_110000_create_audit_trails_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%audit_trails}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->integer()->null(),
            'branch_id' => $this->integer()->null(),
            'action' => $this->string(20)->notNull(),
            'model' => $this->string(150)->notNull(),
            'record_id' => $this->string(100)->null(),
            'description' => $this->string(255)->null(),
            'old_values' => $this->text()->null(),
            'new_values' => $this->text()->null(),
            'route' => $this->string(255)->null(),
            'ip_address' => $this->string(45)->null(),
            'user_agent' => $this->string(500)->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx-audit-trails-user', 'audit_trails', 'user_id');
        $this->createIndex('idx-audit-trails-branch-created', 'audit_trails', ['branch_id', 'created_at']);
        $this->createIndex('idx-audit-trails-action', 'audit_trails', 'action');
        $this->createIndex('idx-audit-trails-model', 'audit_trails', 'model');
        $this->addForeignKey('fk-audit-trails-user', 'audit_trails', 'user_id', 'user', 'id', 'SET NULL');
        $this->addForeignKey('fk-audit-trails-branch', 'audit_trails', 'branch_id', 'branch', 'id', 'SET NULL');

        $route = '/audit-trails/*';
        if (!(new Query())->from('auth_item')->where(['name' => $route])->exists($this->db)) {
            $this->insert('auth_item', [
                'name' => $route, 'type' => 2, 'description' => 'View audit trail',
                'created_at' => time(), 'updated_at' => time(),
            ]);
        }
        $parents = (new Query())->select('parent')->from('auth_item_child')->where(['child' => '/rbac/*'])->column($this->db);
        if ((new Query())->from('auth_item')->where(['name' => 'Manage Role'])->exists($this->db)) {
            $parents[] = 'Manage Role';
        }
        foreach (array_unique($parents) as $parent) {
            if (!(new Query())->from('auth_item_child')->where(['parent' => $parent, 'child' => $route])->exists($this->db)) {
                $this->insert('auth_item_child', ['parent' => $parent, 'child' => $route]);
            }
        }
    }

    public function safeDown()
    {
        $this->delete('auth_item_child', ['child' => '/audit-trails/*']);
        $this->delete('auth_item', ['name' => '/audit-trails/*']);
        $this->dropTable('{{%audit_trails}}');
    }
}
