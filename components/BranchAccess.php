<?php

namespace app\components;

use Yii;

class BranchAccess
{
    public static function isSuperAdmin()
    {
        return !Yii::$app->user->isGuest && Yii::$app->user->can('Super Admin');
    }

    public static function apply($query, $column, $branchId)
    {
        if (!self::isSuperAdmin()) {
            $query->andWhere([$column => $branchId]);
        }

        return $query;
    }

    public static function sqlCondition($column, $branchId, &$params, $parameter = 'branchId')
    {
        if (self::isSuperAdmin()) {
            return '1=1';
        }

        $placeholder = ':' . $parameter;
        $params[$placeholder] = $branchId;

        return $column . ' = ' . $placeholder;
    }
}
