<?php
/**
 * @author mult1mate
 * Date: 05.01.16
 * Time: 18:21
 * @var string $date_begin
 * @var string $date_end
 * @var array  $report
 */
echo $this->render('_menu');
$this->title = 'Task Manager - Report';
$taskRun = new \petargit\cron\models\TaskRun();
?>
<form class="form-inline" method="GET">
    <div class="form-group">
        <label for="date_begin" class="control-label">Date begin</label>
        <input type="date" value="<?=$dateBegin?>" name="date_begin" id="date_begin" class="form-control">
    </div>
    <div class="form-group">
        <label for="date_end" class="control-label">Date end</label>
        <input type="date" value="<?=$dateEnd?>" name="date_end" id="date_end" class="form-control">
    </div>
    <div class="form-group">
        <input type="submit" value="Update" class="btn btn-primary">
    </div>
</form>
<table class="table">
    <tr>
        <th>Task</th>
        <th>Avg. time</th>
        <th>Success</th>
        <th>Started</th>
        <th>Error</th>
        <th>All</th>
    </tr>
    <?php foreach ($report as $r): ?>
        <tr>
            <td><?= $r['command'] ?></td>
            <td><?= $taskRun->getExecutionTimeHR($r['time_avg'])?></td>
            <td><?= $r['completed'] ?></td>
            <td><?= $r['started'] ?></td>
            <td><?= $r['error'] ?></td>
            <th><?= $r['runs'] ?></th>
        </tr>
    <?php endforeach; ?>
</table>
