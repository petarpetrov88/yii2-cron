<?php
namespace petargit\cron\controllers;

use petargit\cron\models\Task;
use petargit\cron\models\TaskRun;
use petargit\cron\components\TaskLoader;
use petargit\cron\components\TaskManager;
use petargit\cron\components\TaskRunner;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @author mult1mate
 * Date: 20.12.15
 * Time: 20:56
 */
class TasksController extends Controller
{
    private static $tasks_controllers_folder;
    private static $tasks_namespace;

    public function init()
    {
        parent::init();
        self::$tasks_controllers_folder = \Yii::$app->getModule('cron-management')->tasksControllersFolder;
        self::$tasks_namespace          = \Yii::$app->getModule('cron-management')->tasksNamespace;
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'tasks'   => Task::getList(),
            'methods' => TaskLoader::getAllMethods(self::$tasks_controllers_folder, self::$tasks_namespace),
        ]);
    }

    public function actionCreate()
    {
        $model = new Task();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::to('/cron-management/tasks/index'));
        }

        return $this->render('create', [
            'model' => $model,
            'methods' => TaskLoader::getAllMethods(self::$tasks_controllers_folder, self::$tasks_namespace),
        ]);
    }

    public function actionEdit()
    {
        $id = \Yii::$app->request->get('task_id', false);

        if (!$id)
            throw new BadRequestHttpException();

        $task = Task::findOne($id);

        if (!$task)
            throw new NotFoundHttpException('Task with id ' . $id . ' was not found!');

        if ($task->load(\Yii::$app->request->post()) && $task->save()) {
            $this->redirect(Url::to('/cron-management/tasks/index'));
        }

        return $this->render('edit', [
            'model'    => $task,
            'methods' => TaskLoader::getAllMethods(self::$tasks_controllers_folder, self::$tasks_namespace),
        ]);
    }

    public function actionLog()
    {
        $taskID = \Yii::$app->request->get('task_id', null);
        $runs    = TaskRun::getLast($taskID);

        return $this->render('log', ['runs' => $runs]);
    }

    public function actionGetDates()
    {
        $time  = \Yii::$app->request->post('time', false);

        if (!$time)
            throw new BadRequestHttpException();

        $dates = TaskRunner::getRunDates($time);

        if (empty($dates)) {
            return;
        }

        return $this->renderAjax('dates', [
            'dates' => $dates
        ]);
    }

    public function actionGetOutput()
    {
        $taskRunID = \Yii::$app->request->get('run_id', false);

        if (!$taskRunID)
            throw new BadRequestHttpException();

        $run = TaskRun::find()->where(['id' => $taskRunID])->one();

        if (!$run)
            throw new NotFoundHttpException();

        return $this->renderAjax('output', [
            'output' => $run->output
        ]);
    }

    public function actionTasksReport()
    {
        $dateBegin = \Yii::$app->request->get('date_begin', date('Y-m-d', strtotime('-6 day')));
        $dateEnd   = \Yii::$app->request->get('date_end', date('Y-m-d'));

        return $this->render('report', [
            'report'     => TaskRun::getReport($dateBegin, $dateEnd),
            'dateBegin' => $dateBegin,
            'dateEnd'   => $dateEnd,
        ]);
    }

    public function actionRunTask()
    {
        $taskID = \Yii::$app->request->post('task_id');

        if (!$taskID)
            throw new BadRequestHttpException();

        $task = Task::find()->where(['id' => $taskID])->one();

        if (!$task)
            throw new NotFoundHttpException();

        $output = TaskRunner::runTask($task);

        $this->renderContent($output . '<hr />');
    }










    public function actionExport()
    {
        return $this->render('export');
    }

    public function actionParseCrontab()
    {
        if (isset($_POST['crontab'])) {
            $result = TaskManager::parseCrontab($_POST['crontab'], new Task());
            echo json_encode($result);
        }
    }

    public function actionExportTasks()
    {
        if (isset($_POST['folder'])) {
            $tasks  = Task::getList();
            $result = [];
            foreach ($tasks as $t) {
                $line     = TaskManager::getTaskCrontabLine($t, $_POST['folder'], $_POST['php'], $_POST['file']);
                $result[] = nl2br($line);
            }
            echo json_encode($result);
        }
    }











    public function actionTasksUpdate()
    {
        if (isset($_POST['task_id'])) {
            $tasks = Task::findAll($_POST['task_id']);
            foreach ($tasks as $t) {
                /**
                 * @var Task $t
                 */
                $action_status = [
                    'Enable'  => TaskInterface::TASK_STATUS_ACTIVE,
                    'Disable' => TaskInterface::TASK_STATUS_INACTIVE,
                    'Delete'  => TaskInterface::TASK_STATUS_DELETED,
                ];
                $t->setStatus($action_status[$_POST['action']]);
                $t->save();
            }
        }
    }


}
