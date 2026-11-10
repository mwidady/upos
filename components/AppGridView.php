<?php

namespace app\components;

use Yii;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Inflector;

class AppGridView extends GridView
{
    public $panelHeadingTemplate = '<div class="grid-panel-header">{title}<div class="grid-export-actions">{export}</div></div><div class="clearfix"></div>';
    public $toolbar = ['{toggleData}'];
    public $responsive = true;
    public $responsiveWrap = false;
    public $hover = true;
    public $striped = true;
    public $bordered = true;

    public function init()
    {
        $title = Yii::$app->view->title;
        if (!$title) {
            $title = Inflector::camel2words(Yii::$app->controller->id);
        }

        $defaults = [
            'heading' => '<i class="glyphicon glyphicon-globe"></i> ' . Html::encode($title),
            'type' => self::TYPE_PRIMARY,
            'before' => '',
            'after' => Html::a(
                '<i class="glyphicon glyphicon-repeat"></i>',
                ['/' . Yii::$app->controller->route],
                ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => 'Refresh']
            ),
        ];
        $this->panel = array_merge($defaults, $this->panel);

        if (empty($this->exportConfig)) {
            $this->exportConfig = [self::EXCEL => [], self::PDF => []];
        }

        parent::init();
    }
}
