<?php

use yii\db\Migration;

class m260921_090000_create_rbac_tables extends Migration
{
    private function hasTable($table)
    {
        return $this->db->schema->getTableSchema($table, true) !== null;
    }

    private function hasIndex($table, $indexName)
    {
        foreach ($this->db->schema->getTableIndexes($table, true) as $index) {
            if ($index->name === $indexName) {
                return true;
            }
        }

        return false;
    }

    private function hasForeignKey($table, $foreignKeyName, $column, $foreignTable, $foreignColumn)
    {
        foreach ($this->db->schema->getTableForeignKeys($table, true) as $foreignKey) {
            if ($foreignKey->name === $foreignKeyName
                || ($foreignKey->columnNames === [$column]
                    && strcasecmp($foreignKey->foreignTableName, $foreignTable) === 0
                    && $foreignKey->foreignColumnNames === [$foreignColumn])) {
                return true;
            }
        }

        return false;
    }

    private function runIfMissing(callable $exists, callable $operation)
    {
        if ($exists()) {
            return;
        }

        try {
            $operation();
        } catch (\yii\db\Exception $exception) {
            if (!$exists()) {
                throw $exception;
            }
        }
    }

    private function ensureIndex($table, $indexName, $columns)
    {
        $this->runIfMissing(
            function () use ($table, $indexName) {
                return $this->hasIndex($table, $indexName);
            },
            function () use ($table, $indexName, $columns) {
                $this->createIndex($indexName, $table, $columns);
            }
        );
    }

    private function ensureForeignKey($name, $table, $column, $foreignTable, $foreignColumn, $onDelete, $onUpdate)
    {
        $this->runIfMissing(
            function () use ($table, $name, $column, $foreignTable, $foreignColumn) {
                return $this->hasForeignKey($table, $name, $column, $foreignTable, $foreignColumn);
            },
            function () use ($name, $table, $column, $foreignTable, $foreignColumn, $onDelete, $onUpdate) {
                $this->addForeignKey($name, $table, $column, $foreignTable, $foreignColumn, $onDelete, $onUpdate);
            }
        );
    }

    public function safeUp()
    {
        $tableOptions = $this->db->driverName === 'mysql' ? 'ENGINE=InnoDB' : null;

        if (!$this->hasTable('auth_rule')) {
            $this->createTable('{{%auth_rule}}', [
                'name' => $this->string(64)->notNull(),
                'data' => $this->binary(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY ([[name]])',
            ], $tableOptions);
        }

        if (!$this->hasTable('auth_item')) {
            $this->createTable('{{%auth_item}}', [
                'name' => $this->string(64)->notNull(),
                'type' => $this->smallInteger()->notNull(),
                'description' => $this->text(),
                'rule_name' => $this->string(64),
                'data' => $this->binary(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY ([[name]])',
            ], $tableOptions);
        }

        if (!$this->hasTable('auth_item_child')) {
            $this->createTable('{{%auth_item_child}}', [
                'parent' => $this->string(64)->notNull(),
                'child' => $this->string(64)->notNull(),
                'PRIMARY KEY ([[parent]], [[child]])',
            ], $tableOptions);
        }

        if (!$this->hasTable('auth_assignment')) {
            $this->createTable('{{%auth_assignment}}', [
                'item_name' => $this->string(64)->notNull(),
                'user_id' => $this->string(64)->notNull(),
                'created_at' => $this->integer(),
                'PRIMARY KEY ([[item_name]], [[user_id]])',
            ], $tableOptions);
        }

        $this->ensureIndex('auth_item', 'idx-auth_item-type', 'type');
        $this->ensureIndex('auth_assignment', 'idx-auth_assignment-user_id', 'user_id');

        $this->ensureForeignKey('fk-auth_item-rule_name', 'auth_item', 'rule_name', 'auth_rule', 'name', 'SET NULL', 'CASCADE');
        $this->ensureForeignKey('fk-auth_item_child-parent', 'auth_item_child', 'parent', 'auth_item', 'name', 'CASCADE', 'CASCADE');
        $this->ensureForeignKey('fk-auth_item_child-child', 'auth_item_child', 'child', 'auth_item', 'name', 'CASCADE', 'CASCADE');
        $this->ensureForeignKey('fk-auth_assignment-item_name', 'auth_assignment', 'item_name', 'auth_item', 'name', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        echo "m260921_090000_create_rbac_tables cannot be reverted safely.\n";

        return false;
    }
}
