<?php
/**
 * @author mult1mate
 * Date: 21.12.15
 * Time: 0:29
 * @var $content
 */

use \yii\helpers\Url;
?>
<div class="row">
    <div class="container-fluid">
        <h2>Cron tasks manager</h2>
        <?php echo \yii\widgets\Menu::widget([
            'items' => [
                ['url' => [Url::to('/cron-management/tasks/index')], 'label' => 'Tasks list'],
                ['url' => [Url::to('/cron-management/tasks/create')], 'label' => 'Add new task'],
                ['url' => [Url::to('/cron-management/tasks/log')], 'label' => 'Logs'],
//                ['url' => [Url::to('/cron-management/tasks/export')], 'label' => 'Import/Export'],
                ['url' => [Url::to('/cron-management/tasks/tasks-report')], 'label' => 'Report']
            ],
            'options' => [
                'class' => 'nav nav-tabs nav-justified',
            ]
        ]); ?>
    </div>
    <br>
</div>
