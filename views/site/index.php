<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JqueryAsset;

/* @var $summary array */
/* @var $dailySales array */
/* @var $invoiceStatuses array */
/* @var $topProducts array */
/* @var $activity array */
/* @var $recentInvoices array */
/* @var $periodEnd DateTimeImmutable */

$this->title = 'Dashboard';

$compactNumber = function ($value) {
    $value = (float) $value;
    if ($value >= 1000000000) {
        return number_format($value / 1000000000, 1) . 'B';
    }
    if ($value >= 1000000) {
        return number_format($value / 1000000, 1) . 'M';
    }
    if ($value >= 1000) {
        return number_format($value / 1000, 1) . 'K';
    }
    return number_format($value, 0);
};

$dailyChart = [];
$dailyTicks = [];
foreach ($dailySales as $index => $row) {
    $dailyChart[] = [$index, $row['value']];
    $dailyTicks[] = [$index, $row['label']];
}

$statusColors = ['#167d75', '#e05a47', '#e6a23c', '#527da8', '#8294a6'];
$statusChart = [];
foreach ($invoiceStatuses as $index => $row) {
    $statusChart[] = [
        'label' => $row['label'],
        'data' => $row['value'],
        'color' => $statusColors[$index % count($statusColors)],
    ];
}
if (empty($statusChart)) {
    $statusChart[] = ['label' => 'No invoices', 'data' => 1, 'color' => '#dce3e6'];
}

$productChart = [];
$productTicks = [];
foreach (array_reverse($topProducts) as $index => $row) {
    $productChart[] = [(int) $row['quantity'], $index];
    $name = mb_strlen($row['name']) > 18 ? mb_substr($row['name'], 0, 16) . '...' : $row['name'];
    $productTicks[] = [$index, $name];
}

$activityChart = [];
$activityTicks = [];
foreach ($activity as $index => $row) {
    $activityChart[] = [$index, $row['value']];
    $activityTicks[] = [$index, $row['label']];
}

$this->registerCss(<<<CSS
html, body { overflow: hidden; }
#content { height: calc(100vh - 60px); min-height: 0; padding-bottom: 0; overflow: hidden; background: #edf1ef; }
#content-header, #footer { display: none; }
#content-body, #content-body > .col-md-12 { height: 100%; }
#content-body > .col-md-12 { padding: 0; }
.dashboard-shell { box-sizing: border-box; height: 100%; padding: 15px 18px; display: grid; grid-template-rows: 44px 76px minmax(190px, 1.12fr) minmax(150px, .88fr); gap: 12px; color: #273634; overflow: hidden; }
.dashboard-heading { display: flex; align-items: center; justify-content: space-between; min-width: 0; }
.dashboard-title { margin: 0; font-size: 23px; line-height: 1.1; color: #183b37; font-weight: 700; letter-spacing: -.4px; }
.dashboard-subtitle { margin: 3px 0 0; color: #7a8986; font-size: 11px; }
.dashboard-actions { display: flex; gap: 7px; }
.dashboard-actions a { border: 1px solid #d5dfdc; background: #fff; color: #31514d; padding: 6px 10px; font-size: 11px; font-weight: 600; box-shadow: 0 3px 10px rgba(30, 61, 56, .05); }
.dashboard-actions a:hover { background: #167d75; border-color: #167d75; color: #fff; }
.dashboard-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; min-height: 0; }
.dashboard-kpi { position: relative; overflow: hidden; padding: 11px 14px 9px; background: #fff; border: 1px solid #dce4e1; border-radius: 3px; box-shadow: 0 5px 16px rgba(28, 57, 52, .06); }
.dashboard-kpi:before { content: ''; position: absolute; inset: 0 auto 0 0; width: 4px; background: var(--accent); }
.dashboard-kpi-label { display: block; color: #7b8987; font-size: 10px; font-weight: 700; letter-spacing: .65px; text-transform: uppercase; }
.dashboard-kpi-value { display: block; margin-top: 3px; color: #183b37; font-size: 16px; line-height: 1; font-weight: 700; }
.dashboard-kpi-meta { display: block; margin-top: 4px; color: #8a9694; font-size: 10px; }
.dashboard-main, .dashboard-lower { display: grid; gap: 12px; min-height: 0; }
.dashboard-main { grid-template-columns: minmax(0, 2fr) minmax(260px, .9fr); }
.dashboard-lower { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(290px, 1.15fr); }
.dashboard-panel { min-height: 0; display: flex; flex-direction: column; background: #fff; border: 1px solid #dce4e1; border-radius: 3px; box-shadow: 0 5px 16px rgba(28, 57, 52, .05); overflow: hidden; }
.dashboard-panel-head { flex: 0 0 auto; display: flex; align-items: center; justify-content: space-between; padding: 9px 12px 7px; border-bottom: 1px solid #edf1ef; }
.dashboard-panel-title { margin: 0; color: #284743; font-size: 12px; font-weight: 700; letter-spacing: .1px; }
.dashboard-panel-note { color: #8b9896; font-size: 9px; text-transform: uppercase; letter-spacing: .45px; }
.dashboard-chart { position: relative; flex: 1 1 auto; min-height: 0; margin: 7px 10px 9px; }
.dashboard-pie { margin-right: 4px; }
.dashboard-empty { display: flex; align-items: center; justify-content: center; height: 100%; color: #91a09d; font-size: 11px; }
.dashboard-table-wrap { flex: 1 1 auto; min-height: 0; overflow: hidden; }
.dashboard-table { width: 100%; margin: 0; table-layout: fixed; border-collapse: collapse; }
.dashboard-table th { padding: 6px 9px; color: #899592; background: #f7f9f8; font-size: 9px; text-align: left; text-transform: uppercase; letter-spacing: .45px; }
.dashboard-table td { padding: 7px 9px; border-top: 1px solid #edf1ef; color: #415653; font-size: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dashboard-table td:nth-child(3), .dashboard-table th:nth-child(3) { text-align: right; }
.dashboard-status { display: inline-block; padding: 2px 6px; color: #a34035; background: #fceae7; font-size: 8px; font-weight: 700; }
.dashboard-status.is-paid { color: #11685f; background: #e2f3ef; }
.dashboard-tooltip { position: fixed; z-index: 9999; display: none; pointer-events: none; padding: 5px 7px; color: #fff; background: #183b37; border-radius: 2px; font-size: 10px; box-shadow: 0 4px 12px rgba(0,0,0,.18); }
.legend table { font-size: 9px !important; }
.legendColorBox > div { border: 0 !important; }
@media (max-width: 900px) {
    .dashboard-shell { padding: 10px; gap: 8px; grid-template-rows: 40px 68px minmax(170px, 1fr) minmax(145px, .78fr); }
    .dashboard-kpis, .dashboard-main, .dashboard-lower { gap: 8px; }
    .dashboard-lower { grid-template-columns: 1fr 1fr; }
    .dashboard-recent { display: none; }
    .dashboard-kpi-value { font-size: 16px; }
}
@media (max-width: 600px) {
    .dashboard-shell { padding: 7px; grid-template-rows: 34px 58px minmax(145px, 1fr) minmax(125px, .8fr); gap: 6px; }
    .dashboard-subtitle, .dashboard-actions span, .dashboard-kpi-meta, .dashboard-panel-note { display: none; }
    .dashboard-title { font-size: 17px; }
    .dashboard-actions a { padding: 4px 6px; }
    .dashboard-kpis, .dashboard-main, .dashboard-lower { gap: 6px; }
    .dashboard-kpi { padding: 7px 7px 5px; }
    .dashboard-kpi-label { font-size: 7px; letter-spacing: .2px; }
    .dashboard-kpi-value { font-size: 12px; }
    .dashboard-main { grid-template-columns: 1.45fr 1fr; }
    .dashboard-panel-head { padding: 5px 7px; }
    .dashboard-panel-title { font-size: 9px; }
    .dashboard-chart { margin: 4px; }
    .legend { display: none; }
}
CSS
);

$this->registerJsFile('@web/js/jquery.flot.min.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile('@web/js/jquery.flot.resize.min.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile('@web/js/jquery.flot.pie.min.js', ['depends' => [JqueryAsset::className()]]);

$chartData = Json::htmlEncode([
    'daily' => $dailyChart,
    'dailyTicks' => $dailyTicks,
    'statuses' => $statusChart,
    'products' => $productChart,
    'productTicks' => $productTicks,
    'activity' => $activityChart,
    'activityTicks' => $activityTicks,
]);

$this->registerJs(<<<JS
var dashboardData = {$chartData};
var moneyFormat = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });
var tooltip = $('<div class="dashboard-tooltip"></div>').appendTo('body');

function bindDashboardTooltip(selector, formatter) {
    $(selector).on('plothover', function (event, pos, item) {
        if (!item) {
            tooltip.hide();
            return;
        }
        tooltip.html(formatter(item)).css({ left: item.pageX + 9, top: item.pageY - 28 }).show();
    });
}

$.plot($('#sales-histogram'), [{ data: dashboardData.daily, color: '#167d75' }], {
    series: { bars: { show: true, barWidth: .58, align: 'center', fill: .9, lineWidth: 0 } },
    xaxis: { ticks: dashboardData.dailyTicks, tickLength: 0, font: { size: 9, color: '#7d8d89' } },
    yaxis: { min: 0, tickDecimals: 0, font: { size: 9, color: '#7d8d89' }, tickFormatter: function (value) { return value >= 1000000 ? (value / 1000000).toFixed(1) + 'M' : value >= 1000 ? (value / 1000).toFixed(0) + 'K' : value; } },
    grid: { hoverable: true, borderWidth: 0, color: '#dce4e1', margin: 4 }
});
bindDashboardTooltip('#sales-histogram', function (item) { return 'TZS ' + moneyFormat.format(item.datapoint[1]); });

$.plot($('#invoice-pie'), dashboardData.statuses, {
    series: { pie: { show: true, innerRadius: .52, radius: .82, stroke: { width: 2, color: '#fff' }, label: { show: false } } },
    legend: { show: true, position: 'se', backgroundOpacity: 0 },
    grid: { hoverable: true }
});
bindDashboardTooltip('#invoice-pie', function (item) { return item.series.label + ': ' + Math.round(item.series.data[0][1]); });

if (dashboardData.products.length) {
    $.plot($('#top-products'), [{ data: dashboardData.products, color: '#e6a23c' }], {
        series: { bars: { show: true, horizontal: true, barWidth: .55, align: 'center', fill: .92, lineWidth: 0 } },
        xaxis: { min: 0, tickDecimals: 0, font: { size: 8, color: '#7d8d89' } },
        yaxis: { ticks: dashboardData.productTicks, tickLength: 0, font: { size: 8, color: '#526662' } },
        grid: { hoverable: true, borderWidth: 0, margin: 3 }
    });
    bindDashboardTooltip('#top-products', function (item) { return Math.round(item.datapoint[0]) + ' units sold'; });
} else {
    $('#top-products').html('<div class="dashboard-empty">No product sales yet</div>');
}

$.plot($('#sales-activity'), [{ data: dashboardData.activity, color: '#527da8' }], {
    series: { lines: { show: true, fill: .18, lineWidth: 2 }, points: { show: true, radius: 3, fillColor: '#fff' } },
    xaxis: { ticks: dashboardData.activityTicks, tickLength: 0, font: { size: 8, color: '#667975' } },
    yaxis: { min: 0, tickDecimals: 0, font: { size: 8, color: '#7d8d89' } },
    grid: { hoverable: true, borderWidth: 0, margin: 3 }
});
bindDashboardTooltip('#sales-activity', function (item) { return Math.round(item.datapoint[1]) + ' transactions'; });
JS
);
?>

<div class="dashboard-shell">
    <div class="dashboard-heading">
        <div>
            <h1 class="dashboard-title">Sales overview</h1>
            <p class="dashboard-subtitle">Performance through <?= Html::encode($periodEnd->format('d M Y')) ?>, based on the latest recorded activity.</p>
        </div>
        <div class="dashboard-actions">
            <?= Html::a('<i class="icon-shopping-cart"></i> <span>New sale</span>', ['items/sales']) ?>
            <?= Html::a('<i class="icon-file"></i> <span>Invoices</span>', ['invoices/index']) ?>
            <?= Html::a('<i class="icon-user"></i> <span>Customers</span>', ['customers/index']) ?>
            <?= Html::a('<i class="icon-list-alt"></i> <span>Items</span>', ['items/index']) ?>
        </div>
    </div>

    <div class="dashboard-kpis">
        <div class="dashboard-kpi" style="--accent:#167d75">
            <span class="dashboard-kpi-label">Total invoiced</span>
            <strong class="dashboard-kpi-value">TZS <?= Html::encode($compactNumber($summary['sales_total'])) ?></strong>
            <span class="dashboard-kpi-meta"><?= number_format($summary['invoice_count']) ?> invoices recorded</span>
        </div>
        <div class="dashboard-kpi" style="--accent:#527da8">
            <span class="dashboard-kpi-label">Paid revenue</span>
            <strong class="dashboard-kpi-value">TZS <?= Html::encode($compactNumber($summary['paid_total'])) ?></strong>
            <span class="dashboard-kpi-meta"><?= number_format($summary['customer_count']) ?> customers</span>
        </div>
        <div class="dashboard-kpi" style="--accent:#e05a47">
            <span class="dashboard-kpi-label">Outstanding</span>
            <strong class="dashboard-kpi-value">TZS <?= Html::encode($compactNumber($summary['outstanding_total'])) ?></strong>
            <span class="dashboard-kpi-meta">Unpaid invoice value</span>
        </div>
        <div class="dashboard-kpi" style="--accent:#e6a23c">
            <span class="dashboard-kpi-label">Low stock</span>
            <strong class="dashboard-kpi-value"><?= number_format($summary['low_stock_count']) ?></strong>
            <span class="dashboard-kpi-meta"><?= number_format($summary['item_count']) ?> items · <?= number_format($summary['user_count']) ?> users</span>
        </div>
    </div>

    <div class="dashboard-main">
        <section class="dashboard-panel">
            <header class="dashboard-panel-head">
                <h2 class="dashboard-panel-title">7-day sales histogram</h2>
                <span class="dashboard-panel-note">Invoice value · TZS</span>
            </header>
            <div id="sales-histogram" class="dashboard-chart"></div>
        </section>
        <section class="dashboard-panel">
            <header class="dashboard-panel-head">
                <h2 class="dashboard-panel-title">Invoice status</h2>
                <span class="dashboard-panel-note">Distribution</span>
            </header>
            <div id="invoice-pie" class="dashboard-chart dashboard-pie"></div>
        </section>
    </div>

    <div class="dashboard-lower">
        <section class="dashboard-panel">
            <header class="dashboard-panel-head">
                <h2 class="dashboard-panel-title">Top-selling items</h2>
                <span class="dashboard-panel-note">Units</span>
            </header>
            <div id="top-products" class="dashboard-chart"></div>
        </section>
        <section class="dashboard-panel">
            <header class="dashboard-panel-head">
                <h2 class="dashboard-panel-title">Sales activity</h2>
                <span class="dashboard-panel-note">By time of day</span>
            </header>
            <div id="sales-activity" class="dashboard-chart"></div>
        </section>
        <section class="dashboard-panel dashboard-recent">
            <header class="dashboard-panel-head">
                <h2 class="dashboard-panel-title">Recent invoices</h2>
                <?= Html::a('View all', ['invoices/index'], ['class' => 'dashboard-panel-note']) ?>
            </header>
            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                    <tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($recentInvoices)): ?>
                        <tr><td colspan="4">No invoices recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentInvoices as $invoice): ?>
                            <tr>
                                <td><?= Html::encode($invoice['invoiceno']) ?></td>
                                <td><?= Html::encode(trim((string) $invoice['customer_name']) ?: 'Walk-in') ?></td>
                                <td><?= number_format($invoice['amount'], 0) ?></td>
                                <td><span class="dashboard-status <?= $invoice['status'] === 'PAID' ? 'is-paid' : '' ?>"><?= Html::encode($invoice['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
