<?php

use \yii\helpers\Url;

$this->title = 'Task Manager - Task list';
echo $this->render('_menu');
?>
<div class="row">
    <div class="container-fluid">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Command</th>
                    <th>Status</th>
                    <th>Time</th>
                    <th>Started</th>
                    <th>Finished</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($runs as $run) { ?>
                    <tr>
                        <td><?=$run->task->command?></td>
                        <td><?=$run->getStatusHR()?></td>
                        <td><?=$run->getExecutionTimeHR()?></td>
                        <td><?= date('Y-m-d H:i:s', $run->started_at)?></td>
                        <td><?= date('Y-m-d H:i:s', $run->started_at)?></td>
                        <td>
                            <?php if (!empty($run->output)) { ?>
                                <a href="/cron-management/tasks/get-output?run_id=<?=$run->id?>" data-toggle="modal" data-target="#output-modal" class="show_output">Show output</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="modal fade bs-example-modal-lg in" role="dialog" tabindex="-1" aria-labelledby="myLargeModalLabel" id="output-modal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="myLargeModalLabel">Task run output</h4>
                    </div>
                    <div class="modal-body">Loading...</div>
                </div>
            </div>
        </div>
    </div>
</div>

