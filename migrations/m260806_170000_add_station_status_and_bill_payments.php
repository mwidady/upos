<?php

use yii\db\Migration;

class m260806_170000_add_station_status_and_bill_payments extends Migration
{
    private function hasColumn($table, $column)
    {
        $schema = $this->db->schema->getTableSchema($table, true);
        return $schema !== null && isset($schema->columns[$column]);
    }

    private function hasTable($table)
    {
        return $this->db->schema->getTableSchema($table, true) !== null;
    }

    public function safeUp()
    {
        if ($this->hasTable('stations') && !$this->hasColumn('stations', 'status')) {
            $this->addColumn('stations', 'status', $this->smallInteger()->notNull()->defaultValue(1)->after('slogo'));
        }

        if (!$this->hasTable('station_bill_payments')) {
            $this->createTable('{{%station_bill_payments}}', [
                'id' => $this->primaryKey(),
                'station_id' => $this->integer()->notNull(),
                'amount' => $this->decimal(12, 2)->notNull(),
                'reference_no' => $this->string(100)->notNull(),
                'paid_by' => $this->string(150)->notNull(),
                'paid_date' => $this->date()->notNull(),
                'notes' => $this->text()->null(),
                'notification_status' => $this->string(20)->notNull()->defaultValue('PENDING'),
                'notified_at' => $this->dateTime()->null(),
                'created_by' => $this->integer()->notNull(),
                'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);

            $this->createIndex('idx-station-bill-payments-station_id', 'station_bill_payments', 'station_id');
            $this->createIndex('idx-station-bill-payments-created_by', 'station_bill_payments', 'created_by');

            $this->addForeignKey(
                'fk-station-bill-payments-station_id',
                'station_bill_payments',
                'station_id',
                'stations',
                'id',
                'CASCADE'
            );

            $this->addForeignKey(
                'fk-station-bill-payments-created_by',
                'station_bill_payments',
                'created_by',
                'user',
                'id',
                'CASCADE'
            );
        }
    }

    public function safeDown()
    {
        echo "m260806_170000_add_station_status_and_bill_payments cannot be reverted.\n";
        return false;
    }
}
