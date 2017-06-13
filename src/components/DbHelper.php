<?php
namespace petargit\cron\components;

/**
 * Class DbHelper
 * Contains common sql queries
 * @package vm\cron
 * @author  mult1mate
 * Date: 05.01.16
 * Time: 18:08
 */
class DbHelper
{
    /**
     * returns query for summary report
     * @return string
     */
    public static function getReportSql()
    {
        return "
        SELECT t.command, t.task_id,
        SUM(CASE WHEN tr.status = 'started' THEN 1 ELSE 0 END) AS started,
        SUM(CASE WHEN tr.status = 'completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN tr.status = 'error' THEN 1 ELSE 0 END) AS error,
        round(AVG(tr.execution_time),2) AS time_avg,
        count(*) AS runs
        FROM task_runs AS tr
        LEFT JOIN tasks AS t ON t.task_id=tr.task_id
        WHERE tr.ts BETWEEN ? AND ? + INTERVAL 1 DAY
        GROUP BY command
        ORDER BY tr.task_id";
    }
}
