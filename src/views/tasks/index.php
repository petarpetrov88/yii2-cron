<?php
/**
 * @author mult1mate
 * Date: 21.12.15
 * Time: 0:38
 * @var array $tasks
 * @var array $methods
 */
use \yii\helpers\Url;

$this->title = 'Task Manager - Task list';
echo $this->render('_menu');
?>
<div class="row">
    <div class="container-fluid">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>&nbsp</th>
                    <th>Time</th>
                    <th>Command</th>
                    <th>Params</th>
                    <th>Status</th>
                    <th>Comment</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th colspan="3"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($tasks as $t):
                    $statusClass = (\petargit\cron\models\Task::TASK_STATUS_ACTIVE == $t->status) ? '' : 'text-danger';
                ?>
                    <tr>
                        <td>
                            <input type="checkbox" value="<?=$t->id?>" class="task_checkbox">
                        </td>
                        <td><?=$t->time?></td>
                        <td><?=$t->command?></td>
                        <td><?=$t->params?></td>
                        <td class="<?=$statusClass ?>"><?= $t->getStatusHR() ?></td>
                        <td><?=$t->comment?></td>
                        <td><?=date('Y-m-d H:i:s', $t->created_at)?></td>
                        <td><?=date('Y-m-d H:i:s', $t->updated_at)?></td>
                        <td>
                            <a href="<?=Url::to('/cron-management/tasks/edit?task_id=' . $t->id)?>">Edit</a>
                        </td>
                        <td>
                            <a href="<?=Url::to('/cron-management/tasks/log?task_id=' . $t->id)?>">Log</a>
                        </td>
                        <td>
                            <a href="<?= $t->id ?>" class="run_task">Run</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form class="form-inline">
            <div class="form-group">
                <label for="action">With selected</label>
                <select class="form-control" id="action">
                    <option>Enable</option>
                    <option>Disable</option>
                    <option>Delete</option>
                    <option>Run</option>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" value="Apply" class="btn btn-primary" id="execute_action">
            </div>
        </form>
        <div id="output_section" style="display: none;">
            <h3>Task output</h3>
            <pre id="task_output_container"></pre>
        </div>
    </div>
</div>

<?php
$runURL = Url::to('/cron-management/tasks/run');

$js = <<<JS
    function run_task(data) {
        if (confirm('Are you sure?')) {
            $('#output_section').show();
            $('#task_output_container').text('Running...');
            $.post('$runURL', data, function (data) {
                $('#task_output_container').html(data);
            }).fail(function () {
                alert('Server error has occurred');
            });
        }
    };

    $('#select_all').change(function () {
        if ($(this).prop('checked'))
            $('.task_checkbox').prop('checked', 'checked');
        else
            $('.task_checkbox').prop('checked', '');
    });


    $('.run_task').click(function () {
        run_task({task_id: $(this).attr('href')});
        return false;
    });
JS;
