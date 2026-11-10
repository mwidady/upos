<?php

namespace app\commands;

use app\models\Branch;
use app\models\Stations;
use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class SeedController extends Controller
{
    public function actionSuperAdmin()
    {
        $requiredTables = ['user', 'stations', 'branch', 'auth_item', 'auth_item_child', 'auth_assignment'];
        foreach ($requiredTables as $table) {
            if (Yii::$app->db->schema->getTableSchema($table, true) === null) {
                $this->stderr("Missing table {$table}; run yii migrate first.\n");

                return ExitCode::UNSPECIFIED_ERROR;
            }
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $admin = User::findOne(['username' => 'admin']);
            $isNewAdmin = $admin === null;
            if ($isNewAdmin) {
                $admin = new User();
            }

            $admin->first_name = 'Super';
            $admin->last_name = 'Admin';
            $admin->username = 'admin';
            $admin->email = $admin->email ?: 'admin@upos.local';
            $admin->phone_number = $admin->phone_number ?: '0000000000';
            // LoginForm currently validates legacy MD5 password hashes.
            $admin->password = md5('admin@123');
            $admin->status = User::STATUS_ENABLED;
            $admin->branch_id = null;
            $admin->station_id = null;
            if ($isNewAdmin || !$admin->authKey) {
                $admin->generateAuthKey();
            }
            if (!$admin->save(false)) {
                throw new \RuntimeException('Unable to save the admin user.');
            }

            $station = Stations::find()->where(['name' => 'Unknown'])->orderBy(['id' => SORT_ASC])->one();
            if ($station === null) {
                $station = new Stations();
                $station->name = 'Unknown';
                $station->description = 'System-wide administrator station';
                $station->address = 'Unknown';
                $station->location = 'Unknown';
                $station->email = 'unknown@upos.local';
            }
            $station->status = Stations::STATUS_ENABLED;
            if (!$station->save(false)) {
                throw new \RuntimeException('Unable to save the Unknown station.');
            }

            $branch = Branch::find()
                ->where(['station_id' => $station->id, 'name' => 'Unknown'])
                ->orderBy(['id' => SORT_ASC])
                ->one();
            if ($branch === null) {
                $branch = new Branch();
                $branch->station_id = $station->id;
                $branch->name = 'Unknown';
                $branch->description = 'System-wide administrator branch';
                $branch->address = 'Unknown';
                $branch->location = 'Unknown';
                $branch->email = 'unknown-branch@upos.local';
            }
            $branch->created_by = $admin->id;
            if (!$branch->save(false)) {
                throw new \RuntimeException('Unable to save the Unknown branch.');
            }

            $admin->branch_id = $branch->id;
            $admin->station_id = $station->id;
            if (!$admin->save(false)) {
                throw new \RuntimeException('Unable to assign the admin to the Unknown branch and station.');
            }

            $auth = Yii::$app->authManager;
            $role = $auth->getRole('Super Admin');
            if ($role === null) {
                $role = $auth->createRole('Super Admin');
                $role->description = 'Full system access across all branches and stations';
                $auth->add($role);
            } else {
                $role->description = 'Full system access across all branches and stations';
                $auth->update($role->name, $role);
            }

            $wildcard = $auth->getPermission('/*');
            if ($wildcard === null) {
                $wildcard = $auth->createPermission('/*');
                $wildcard->description = 'All application routes';
                $auth->add($wildcard);
            }

            $menuPermissions = [
                'Manange Site',
                'sale-item',
                'Manage Invoice',
                'Manage Transaction',
                'Manage Item',
                'Manage Expenditure',
                'Manage Customer',
                'Manage User',
                'Manage Branch',
                'Manage Station',
                'Manage Batch',
                'Manage Role',
            ];
            foreach ($menuPermissions as $permissionName) {
                if ($auth->getPermission($permissionName) === null) {
                    $permission = $auth->createPermission($permissionName);
                    $permission->description = 'System permission: ' . $permissionName;
                    $auth->add($permission);
                }
            }

            $children = $auth->getChildren($role->name);
            $allItems = array_merge($auth->getRoles(), $auth->getPermissions());
            foreach ($allItems as $item) {
                if ($item->name === $role->name || isset($children[$item->name])) {
                    continue;
                }
                if ($auth->canAddChild($role, $item)) {
                    $auth->addChild($role, $item);
                }
            }

            if ($auth->getAssignment($role->name, $admin->id) === null) {
                $auth->assign($role, $admin->id);
            }

            $transaction->commit();
            $this->stdout("Super Admin seeded. Username: admin\n");
            $this->stdout("Password: admin@123\n");
            $this->stdout("Branch/station: Unknown\n");

            return ExitCode::OK;
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            $this->stderr($exception->getMessage() . "\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
