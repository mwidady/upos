<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class WorkflowConstant extends ActiveRecord
{
    // Example: Define constants
    const PARTIAL_PAYMENT   = 'PARTIAL_PAYMENT';
 

    // Example: another array (e.g., workflow types)
    public static $workflowTypes = [
        self::PARTIAL_PAYMENT=>self::PARTIAL_PAYMENT
    ];
}
