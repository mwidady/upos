<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\StationBillPayment;


//AppAsset::register($this);
use kartik\sidenav\SideNav;
?>
<!--Header-part-->
<div id="header">
    <h2 style="margin-left:60px; padding-top:15px">
        <?= Html::a('POS', ['/site/index']); ?>
    </h2>
</div>
<!--close-Header-part-->


<!--start-top-serch-->
<div id="search">
    <a title="" href="#"><i class="icon icon-user"></i> <span class="text">Welcome
            <?php
            $username = \app\models\User::find()->where(['id' => Yii::$app->user->id])->one();
            echo ucfirst($username->first_name) . " " . ucfirst($username->last_name);
            ?>
        </span>
    </a>
      &nbsp;
    <?= Html::a(
        '<i class="icon icon-share-alt"></i> <span class="text">Logout</span>',
        ['/site/logout'],
      //  ['data-method' => 'post']
        );
?>
</div>
<!--close-top-serch-->
<!--sidebar-menu-->
<div id="sidebar">

    <?php
    $curentpage = Yii::$app->getRequest()->getPathInfo();

    $controlerName = Yii::$app->controller->id;
    $pendingStationBillNotifications = StationBillPayment::find()
        ->where(['notification_status' => StationBillPayment::STATUS_PENDING])
        ->count();
   
    echo SideNav::widget([
        //'type' => SideNav::TYPE_SUCCESS,
        'options' => ['id' => 'sidebar'],
        //'heading' => '<i class="icon icon-home"></i> Dashboard',
        'encodeLabels' => false,
        'items' => [
            [
                'url' => ['/site/index'],
                'label' => 'Dashboard',
                'icon' => 'home',
                'visible' => Yii::$app->user->can('Manange Site'),
                'active' => ($curentpage == 'site/index')
            ],
            [
                'url' => Url::toRoute('/items/sales'),
                'label' => 'Sales',
                'icon' => 'signal',
                'visible' => Yii::$app->user->can('sale-item'),
                'active' => ($curentpage == 'items/sales')
            ],
            [
                'url' => Url::toRoute('/invoices/index'),
                'label' => 'Invoices',
                'icon' => 'record',
                'visible' => Yii::$app->user->can('Manage Invoice'),
                'active' => ($curentpage == 'invoices/index')
            ],
            [
                'url' => Url::toRoute('/payment-transactions/index'),
                'label' => 'Payments',
                'icon' => 'list',
                'visible' => Yii::$app->user->can('Manage Transaction'),
                'active' => ($curentpage == 'payment-transactions/index')
            ],
            [
                'url' => Url::toRoute('/items/index'),
                'label' => 'Items',
                'icon' => 'list',
                'visible' => Yii::$app->user->can('Manage Item'),
                'active' => ($controlerName == 'items')
            ],
            [
                'url' => Url::toRoute('/expenditures'),
                'label' => 'Expenses',
                'icon' => 'tint',
                'visible' => Yii::$app->user->can('Manage Expenditure'),
                'active' => ($controlerName == 'expenditures')
            ],
            [
                'label' => 'Stocks <span class="pull-right badge btn-danger">3</span>',
                'icon' => 'th-list',
                'visible' => Yii::$app->user->can('Manage Item'),
                'items' => [
                    ['label' => 'Stock Details', 'icon' => 'list', 'url' => Url::toRoute('/quantity/stock'),'active' => ($curentpage == 'quantity/stock')],
                    ['label' => 'Add Quantity', 'icon' => 'list', 'url' => Url::toRoute('/quantity/create'),'active' => ($curentpage == 'quantity/create')],
                    ['label' => 'Quantitity Logs', 'icon' => 'list', 'url' => Url::toRoute('/quantity/index'),'active' => ($curentpage == 'quantity/index')],
                ],
            ],
            [
                'label' => 'Reports',
                'icon' => 'list',
                'visible' => Yii::$app->user->can('Manage Transaction'),
                'active' => in_array($curentpage, [
                    'transactions/index',
                    'transactions/profits',
                    'invoices/index',
                    'items/profitsn',
                ]),
                'items' => [
                    [
                        'label' => 'Transactions',
                        'icon' => 'list',
                        'url' => Url::toRoute('/transactions/index'),
                        'active' => ($curentpage == 'transactions/index')
                    ],
                    [
                        'label' => 'Invoices',
                        'icon' => 'record',
                        'url' => Url::toRoute('/invoices/index'),
                        'active' => ($curentpage == 'invoices/index')
                    ],
                    [
                        'label' => 'Debit',
                        'icon' => 'warning-sign',
                        'url' => Url::toRoute(['/invoices/index', 'InvoicesSearch[status]' => 'NOT PAID']),
                        'active' => ($curentpage == 'invoices/index' && (Yii::$app->request->get('InvoicesSearch')['status'] ?? '') == 'NOT PAID')
                    ],
                    [
                        'label' => 'Sales Profits',
                        'icon' => 'file',
                        'url' => Url::toRoute('/transactions/profits'),
                        'active' => ($curentpage == 'transactions/profits')
                    ],
                    [
                        'label' => 'Profits',
                        'icon' => 'copy',
                        'url' => Url::toRoute('/items/profitsn'),
                        'visible' => Yii::$app->user->can('Manage Item'),
                        'active' => ($curentpage == 'items/profitsn')
                    ],
                ],
            ],
           
            [
                'url' => Url::toRoute('/customers/index'),
                'label' => 'Customers',
                'icon' => 'th',
                'visible' => Yii::$app->user->can('Manage Customer'),
                'active' => ($curentpage == 'customers/index')
            ],
            [
                'label' => 'Settings <span class="pull-right badge btn-danger">7</span>',
                'icon' => 'edit',
                'items' => [
                    ['label' => 'Users', 'icon' => 'list', 'url' => Url::toRoute('/user/index'),'active' => ($curentpage == 'user/index'),'visible' => Yii::$app->user->can('Manage User')],
                    ['label' => 'Branches', 'icon' => 'list', 'url' => Url::toRoute('/branch/index'),'active' => ($curentpage == 'branch/index'),'visible' => Yii::$app->user->can('Manage Branch')],
                    ['label' => 'Stations', 'icon' => 'list', 'url' => Url::toRoute('/stations/index'),'active' => ($curentpage == 'stations/index'),'visible' => Yii::$app->user->can('Manage Station')],
                    ['label' => 'Expense Types', 'icon' => 'list', 'url' => Url::toRoute('/expense-types/index'),'active' => ($controlerName == 'expense-types'),'visible' => Yii::$app->user->can('Manage Expenditure')],
                    ['label' => 'Audit Trail', 'icon' => 'time', 'url' => Url::toRoute('/audit-trails/index'),'active' => ($controlerName == 'audit-trails'),'visible' => Yii::$app->user->can('Manage Role')],
                    ['label' => 'Station Bill Payments' . ($pendingStationBillNotifications > 0 ? ' <span class="pull-right badge btn-warning">' . $pendingStationBillNotifications . '</span>' : ''), 'icon' => 'list', 'url' => Url::toRoute('/station-bill-payments/index'),'active' => ($controlerName == 'station-bill-payments'),'visible' => Yii::$app->user->can('Manage Station')],
                    ['label' => 'Batches', 'icon' => 'list', 'url' => Url::toRoute('/batches/index'),'active' => ($curentpage == 'batches/index'),'visible' => Yii::$app->user->can('Manage Batch')],
                    [
                        'url' => Url::toRoute('/workflow/index'),
                        'label' => 'Workflow',
                        'icon' => 'signal',
                        'visible' => Yii::$app->user->can('Manage Station'),
                        'active' => ($curentpage == 'workflow/index')
                    ],
                    [
                        'label' => 'Change Password',
                        'icon' => 'list',
                        'url' => Url::toRoute('/user/changepassword'),
                        'active' => ($curentpage == 'user/changepassword')
                    ],
                    [
                        'label' => 'Manage Access',
                        'icon' => 'list',
                        'url' => Url::toRoute('/rbac'),
                        'visible' => Yii::$app->user->can('Manage Role'),
                        'items' => [
                            ['label' => 'Assign Role', 'icon' => 'list', 'url' => Url::toRoute('/rbac/assignment/index'),'active' => ($controlerName == 'assignment'),'visible' => Yii::$app->user->can('Manage Role')],
                            ['label' => 'Roles', 'icon' => 'list', 'url' => Url::toRoute('/rbac/role/index'),'active' => ($controlerName == 'role'),'visible' => Yii::$app->user->can('Manage Role')],
                            ['label' => 'Permissions', 'icon' => 'list', 'url' => Url::toRoute('/rbac/permission/index'),'active' => ($controlerName == 'permission'),'visible' => Yii::$app->user->can('Manage Role')],
                            ['label' => 'Routes', 'icon' => 'list', 'url' => Url::toRoute('/rbac/route/index'),'active' => ($controlerName == 'route'),'visible' => Yii::$app->user->can('Manage Role')],
                            ['label' => 'Rules', 'icon' => 'list', 'url' => Url::toRoute('/rbac/rule/index'),'active' => ($controlerName == 'rule'),'visible' => Yii::$app->user->can('Manage Role')],
                        ]
                    ]
                ]
            ],
            [
                'url' => Url::toRoute('/faq/index'),
                'label' => 'Help',
                'icon' => 'signal',
                'active' => ($curentpage == 'faq/index')
            ],

        ],
    ]);
    ?>
</div>
<!--sidebar-menu-->
