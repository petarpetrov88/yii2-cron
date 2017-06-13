<?php

namespace petargit\cron\models;

use petargit\cron\components\TaskInterface;
use petargit\cron\components\TaskRunInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * @author mult1mate
 * Date: 20.12.15
 * Time: 20:54
 * @property int    $task_id
 * @property string $time
 * @property string $command
 * @property string $status
 * @property string $comment
 * @property string $ts
 * @property string $ts_updated
 */
class Task extends ActiveRecord
{
    const TASK_STATUS_ACTIVE   = '1';
    const TASK_STATUS_INACTIVE = '0';
    const TASK_STATUS_DELETED  = '-1';

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'class' => TimestampBehavior::className(),
            ]
        );
    }

    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time', 'route', 'status'], 'required'],
            [['time'], 'string', 'max' => 64],
            [['route', 'params', 'command'], 'string', 'max' => 255],
            [['created_at', 'updated_at', 'status'], 'integer', 'max' => 11],
            [['time', 'command', 'route', 'params', 'status', 'comment', 'created_at', 'updated_at'], 'safe']
        ];
    }

    public static function getList()
    {
        return self::find()
            ->where(['NOT IN', 'status', [Task::TASK_STATUS_DELETED]])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->all();
    }

    public function getStatusHR()
    {
        switch ($this->status) {
            case Task::TASK_STATUS_ACTIVE:
                return 'Active';
                break;
            case Task::TASK_STATUS_INACTIVE:
                return 'Inactive';
                break;
            case Task::TASK_STATUS_DELETED:
                return 'Deleted';
                break;
            default:
                return 'Unknown';
        }
    }
}
