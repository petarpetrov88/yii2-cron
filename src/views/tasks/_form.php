<?php

use \petargit\cron\models\Task;
use yii\bootstrap\ActiveForm;

$form = ActiveForm::begin();
?>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="method">Methods</label>
            <select class="form-control" id="method">
                <option></option>
                <?php
                    foreach ($methods as $class => $classMethods) {
                        $arrClassName = explode('\\', $class);
                        $className = end($arrClassName);
                        $arrClassName = preg_split('/(?=[A-Z])/', $className);
                        foreach ($arrClassName AS $index => &$piece) {
                            if ($piece == '' || $piece == 'Controller') {
                                unset($arrClassName[$index]);
                                continue;
                            }
                            $piece = strtolower($piece);
                        }
                ?>
                    <optgroup label="<?=$class?>">
                        <?php
                            foreach ($classMethods as $method) {
                                $method = lcfirst(str_replace('action', '', $method));
                                $arrMethodName = preg_split('/(?=[A-Z])/', $method);

                                foreach ($arrMethodName AS &$piece) {
                                    $piece = strtolower($piece);
                                }
                                ?>
                                <option value="<?= $class . '::' . $method . '()' ?>" data-route="<?=implode('-', $arrClassName) . '/' . implode('-', $arrMethodName)?>"><?=$method?></option>
                            <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>
        </div>
        <?= $form->field($model, 'command')->textInput(['placeholder' => 'Controller::method']) ?>
        <?= $form->field($model, 'params')->textInput() ?>
        <?= $form->field($model, 'status')->dropDownList([
            Task::TASK_STATUS_ACTIVE   => 'Active',
            Task::TASK_STATUS_INACTIVE => 'Inactive',
            Task::TASK_STATUS_DELETED  => 'Deleted',
        ]) ?>
        <?= $form->field($model, 'route')->hiddenInput(['id' => 'route'])->label(false) ?>
        <?= $form->field($model, 'comment') ?>

        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="times">Predefined intervals</label>
            <select class="form-control" id="times">
                <option></option>
                <option value="* * * * *">Minutely</option>
                <option value="0 * * * *">Hourly</option>
                <option value="0 0 * * *">Daily</option>
                <option value="0 0 * * 0">Weekly</option>
                <option value="0 0 1 * *">Monthly</option>
                <option value="0 0 1 1 *">Yearly</option>
            </select>
        </div>
        <?= $form->field($model, 'time')->textInput(['placeholder' => '* * * * *']) ?>
        <pre>
*    *    *    *    *
-    -    -    -    -
|    |    |    |    |
|    |    |    |    |
|    |    |    |    +----- day of week (0 - 7) (Sunday = 0 or 7)
|    |    |    +---------- month (1 - 12)
|    |    +--------------- day of month (1 - 31)
|    +-------------------- hour (0 - 23)
+------------------------- min (0 - 59)
        </pre>
        <h4>Next runs</h4>
        <div id="dates_list"></div>
    </div>

<?php ActiveForm::end(); ?>

<?php
$url = \yii\helpers\Url::to('/cron-management/tasks/get-dates');
$js = <<<JS
    $('#method').change(function () {
        $('#task-command').val($(this).val());
        $('#route').val($(this).find(':selected').data('route'));
    });

    function getRunDates() {
        $.post('$url',
        {time: $('#task-time').val()},
        function (data) {
            $('#dates_list').html(data);
        });
    };

    var time = $('#task-time');

    time.change(function () {
        getRunDates();
    });

    if (time.val() != '')
        getRunDates();

    $('#times').change(function () {
        time.val($(this).val());
        getRunDates();
    });
JS;

$this->registerJS($js, \yii\web\View::POS_READY);