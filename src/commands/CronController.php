<?php
/**
 * @author mult1mate
 * Date: 06.02.16
 * Time: 16:52
 */

namespace petargit\cron\commands;

use petargit\cron\models\Task;
use petargit\cron\components\TaskRunner;
use yii\console\Controller;

class CronController extends Controller
{
    public function actionCheckTasks()
    {
        TaskRunner::checkAndRunTasks(Task::getAll());
    }
}
