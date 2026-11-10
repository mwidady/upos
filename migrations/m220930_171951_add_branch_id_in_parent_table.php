<?php

use yii\db\Migration;

/**
 * Adds branch ownership to tables that support branch-scoped data.
 */
class m220930_171951_add_branch_id_in_parent_table extends Migration
{
    private function hasColumn($table, $column)
    {
        $schema = $this->db->schema->getTableSchema($table, true);

        return $schema !== null && isset($schema->columns[$column]);
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

    private function hasForeignKey($table, $foreignKeyName)
    {
        foreach ($this->db->schema->getTableForeignKeys($table, true) as $foreignKey) {
            if ($foreignKey->name === $foreignKeyName) {
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
            // MySQL may commit earlier DDL before a migration fails.
            if (!$exists()) {
                throw $exception;
            }
        }
    }

    private function ensureBranchColumn($table, $indexName, $foreignKeyName)
    {
        if ($this->db->schema->getTableSchema($table, true) === null) {
            return;
        }

        $this->runIfMissing(
            function () use ($table) {
                return $this->hasColumn($table, 'branch_id');
            },
            function () use ($table) {
                $this->addColumn($table, 'branch_id', $this->integer()->null());
            }
        );

        $this->runIfMissing(
            function () use ($table, $indexName) {
                return $this->hasIndex($table, $indexName);
            },
            function () use ($table, $indexName) {
                $this->createIndex($indexName, $table, 'branch_id');
            }
        );

        $this->runIfMissing(
            function () use ($table, $foreignKeyName) {
                return $this->hasForeignKey($table, $foreignKeyName);
            },
            function () use ($table, $foreignKeyName) {
                $this->addForeignKey(
                    $foreignKeyName,
                    $table,
                    'branch_id',
                    'branch',
                    'id',
                    'CASCADE'
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->ensureBranchColumn('items', 'idx-item-branch_id', 'fk-item-branch_id');
        $this->ensureBranchColumn('customers', 'idx-customer-branch_id', 'fk-customer-branch_id');
        $this->ensureBranchColumn('expenditures', 'idx-expenditure-branch_id', 'fk-expenditure-branch_id');
        $this->ensureBranchColumn('invoices', 'idx-invoice-branch_id', 'fk-invoice-branch_id');
        $this->ensureBranchColumn('faq', 'idx-faq-branch_id', 'fk-faq-branch_id');
        $this->ensureBranchColumn('batches', 'idx-batch-branch_id', 'fk-batch-branch_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220930_171951_add_branch_id_in_parent_table cannot be reverted.\n";

        return false;
    }
}
