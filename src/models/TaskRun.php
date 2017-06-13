<?php

namespace petargit\cron\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class TaskRun extends ActiveRecord
{
    const STATUS_STARTED   = '0';
    const STATUS_COMPLETED = '1';
    const STATUS_ERROR     = '-1';

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                [
                    'class' => TimestampBehavior::className(),
                    'createdAtAttribute' => 'started_at',
                    'updatedAtAttribute' => 'finished_at',
                ]
            ]
        );
    }

    public function rules()
    {
        return [
            [['task_id', 'status'], 'required'],
            [['task_id', 'status', 'started_at', 'finished_at'], 'integer'],
            [['task_id', 'status', 'started_at', 'finished_at', 'execution_time', 'output'], 'safe'],
        ];
    }

    public static function tableName()
    {
        return 'tasks_runs';
    }

    public static function getLast($taskID = null, $limit = 100)
    {
        $query = TaskRun::find()
            ->with('task')
            ->orderBy(['started_at' => SORT_DESC])
            ->limit($limit);

        if ($taskID) {
            $query->where('task_id = :task_id', [':task_id' => $taskID]);
        }

        return $query->all();
    }

    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    public function getExecutionTimeHR($time = null)
    {

        if (!$time)
            $time = $this->execution_time;

        $hours = (int) ($time / 60 / 60);
        $minutes = (int) ($time / 60) - $hours * 60;
        $seconds = (int) $time - (($hours * 60 * 60) - ($minutes * 60));

        if ($seconds > 0)
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        else
            return sprintf('%dms', ceil($time * 1000));
    }

    public function getStatusHR()
    {
        switch ($this->status) {
            case TaskRun::STATUS_COMPLETED:
                return 'Completed';
                break;
            case TaskRun::STATUS_ERROR:
                return 'Error';
                break;
            case TaskRun::STATUS_STARTED:
                return 'Started';
                break;
            default:
                return 'Unknown';
        }
    }


    public static function getReport($dateBegin, $dateEnd)
    {
        $sql = "
        SELECT
            t.command, t.id,
            SUM(CASE WHEN tr.status = :status_started THEN 1 ELSE 0 END) AS started,
            SUM(CASE WHEN tr.status = :status_completed THEN 1 ELSE 0 END) AS completed,
            SUM(CASE WHEN tr.status = :status_error THEN 1 ELSE 0 END) AS error,
            round(AVG(tr.execution_time), 2) AS time_avg,
            count(*) AS runs
        FROM
          " . self::tableName() .  " AS tr
        LEFT JOIN
            " . Task::tableName() . " AS t ON t.id = tr.task_id
        WHERE
            tr.started_at BETWEEN :date_begin AND :date_end
        GROUP BY
            command
        ORDER BY
            tr.id";

        return \Yii::$app->db->createCommand($sql, [
            ':date_begin' => strtotime($dateBegin),
            ':date_end'   => strtotime($dateEnd . ' + 1 day'),
            ':status_started' => self::STATUS_STARTED,
            ':status_completed' => self::STATUS_COMPLETED,
            ':status_error' => self::STATUS_ERROR
        ])->queryAll();
    }
}
