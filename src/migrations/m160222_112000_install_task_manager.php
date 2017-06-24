<?php

use yii\db\Migration;

class m160222_112000_install_task_manager extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tasks}}', [
            'id' => $this->primaryKey(),
            'time' => $this->string(64)->notNull(),
            'command' => $this->string(255)->notNull(),
            'route' => $this->string(255)->notNull(),
            'params' => $this->string(255),
            'status' => $this->integer()->notNull(),
            'comment' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()
        ], $tableOptions);

        $this->createTable('{{%tasks_runs}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull(),
            'execution_time' => $this->decimal(6,2)->notNull()->defaultValue(0.00),
            'started_at' => $this->integer()->notNull(),
            'finished_at' => $this->integer(),
            'output' => $this->text(),
        ], $tableOptions);

        $this->createIndex('tasks_runs_task_id', '{{%tasks_runs}}', 'task_id');
        $this->createIndex('tasks_runs_task_ts', '{{%tasks_runs}}', 'started_at');
    }

    public function down()
    {
        $this->dropTable('{{%tasks}}');
        $this->dropTable('{{%tasks_runs}}');
    }
}
