<?php

namespace app\models;

use Yii;
use yii\helpers\Json;
use yii\web\Request;

class AuditTrail extends \yii\db\ActiveRecord
{
    const ACTION_CREATE = 'CREATE';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';
    const ACTION_LOGIN = 'LOGIN';
    const ACTION_LOGOUT = 'LOGOUT';

    public static function tableName() { return 'audit_trails'; }

    public function rules()
    {
        return [
            [['action', 'model'], 'required'], [['user_id', 'branch_id'], 'integer'],
            [['old_values', 'new_values'], 'string'], [['created_at'], 'safe'],
            [['action'], 'string', 'max' => 20], [['model'], 'string', 'max' => 150],
            [['record_id'], 'string', 'max' => 100], [['description', 'route'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 45], [['user_agent'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User', 'branch_id' => 'Branch', 'record_id' => 'Record ID',
            'old_values' => 'Previous Values', 'new_values' => 'New Values',
            'ip_address' => 'IP Address', 'user_agent' => 'Browser / Device', 'created_at' => 'Date & Time',
        ];
    }

    public function getUser() { return $this->hasOne(User::className(), ['id' => 'user_id']); }
    public function getBranch() { return $this->hasOne(Branch::className(), ['id' => 'branch_id']); }

    public static function actionOptions()
    {
        return [
            self::ACTION_CREATE => 'Created', self::ACTION_UPDATE => 'Updated',
            self::ACTION_DELETE => 'Deleted', self::ACTION_LOGIN => 'Logged in', self::ACTION_LOGOUT => 'Logged out',
        ];
    }

    public static function record($action, $modelName, $recordId = null, array $oldValues = [], array $newValues = [], $description = null)
    {
        try {
            $audit = new static();
            $audit->action = $action;
            $audit->model = $modelName;
            $audit->record_id = $recordId === null ? null : (string) $recordId;
            $audit->description = $description;
            $audit->old_values = self::encodeValues($oldValues);
            $audit->new_values = self::encodeValues($newValues);
            if (Yii::$app->has('user') && !Yii::$app->user->isGuest) {
                $identity = Yii::$app->user->identity;
                $audit->user_id = Yii::$app->user->id;
                $audit->branch_id = isset($identity->branch_id) ? $identity->branch_id : null;
            }
            $request = Yii::$app->getRequest();
            if ($request instanceof Request) {
                $audit->route = Yii::$app->controller ? Yii::$app->controller->route : null;
                $audit->ip_address = $request->userIP;
                $audit->user_agent = substr((string) $request->userAgent, 0, 500);
            }
            return $audit->save(false);
        } catch (\Throwable $exception) {
            Yii::warning('Unable to write audit trail: ' . $exception->getMessage(), __METHOD__);
            return false;
        }
    }

    private static function encodeValues(array $values)
    {
        return $values ? Json::encode(self::filterSensitiveValues($values)) : null;
    }

    private static function filterSensitiveValues(array $values)
    {
        foreach ($values as $key => $value) {
            if (preg_match('/password|auth.?key|token|secret/i', (string) $key)) {
                $values[$key] = '[FILTERED]';
            } elseif (is_array($value)) {
                $values[$key] = self::filterSensitiveValues($value);
            }
        }
        return $values;
    }
}
