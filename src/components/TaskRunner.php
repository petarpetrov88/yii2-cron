<?php
namespace petargit\cron\components;

use Cron\CronExpression;
use petargit\cron\models\Task;
use petargit\cron\models\TaskRun;
use yii\base\Exception;

/**
 * Class TaskRunner
 * Runs tasks and handles time expression
 * @author  mult1mate
 * @package vm\cron
 * Date: 07.02.16
 * Time: 12:50
 */
class TaskRunner
{
    /**
     * Runs active tasks if current time matches with time expression
     *
     * @param array $tasks
     */
    public static function checkAndRunTasks($tasks)
    {
        foreach ($tasks as $task) {
            if (Task::TASK_STATUS_ACTIVE != $task->status)
                continue;

            $cron = CronExpression::factory($task->time);

            if ($cron->isDue())
                self::runTask($task);
        }
    }

    /**
     * @param \petargit\cron\models\Task $task
     *
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function runTask(Task $task)
    {
        $run = new TaskRun();
        $run->setAttributes([
            'task_id' => $task->id,
            'status' => TaskRun::STATUS_STARTED,
        ]);

        if (!$run->save()) {
            throw new Exception('Could not save run statics.');
        }

        $startTime = microtime(true);

        $result = self::parseAndRunCommand($task->route, $task->params);

        $run->output = $result['output'];
        $run->status = ($result['success'])? TaskRun::STATUS_COMPLETED : TaskRun::STATUS_ERROR;
        $run->execution_time = round((microtime(true) - $startTime), 2);

        if (!$run->save()) {
            throw new Exception('Could not save task run stats');
        }

        return $run->output;
    }

    /**
     * Parses given command, creates new class object and calls its method via call_user_func_array
     *
     * @param string $command
     *
     * @return mixed
     */
    public static function parseAndRunCommand($route, $params = null)
    {
        $descriptorspec = [
            1 => array("pipe", "w"),
            2 => array("pipe", "w"),
        ];

        $execPath = realpath(\Yii::getAlias('@app') . '/../');
        $process = proc_open(trim(sprintf('cd %s && php yii %s %s', $execPath, $route, $params)), $descriptorspec, $pipes);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        if ($stdout && !$stderr) {
            return [
                'success' => true,
                'output' => $stdout
            ];
        } else if ($stderr && !$stdout) {
            return [
                'success' => false,
                'output' => $stderr
            ];
        } else {
            return [
                'success' => false,
                'output' => 'This command has no output.'
            ];
        }
    }

    /**
     * Returns next run dates for time expression
     *
     * @param string $time
     * @param int    $count
     *
     * @return array
     */
    public static function getRunDates($time, $count = 10)
    {
        try {
            $cron  = CronExpression::factory($time);
            $dates = $cron->getMultipleRunDates($count);
        } catch (\Exception $e) {
            return [];
        }

        return $dates;
    }
}
