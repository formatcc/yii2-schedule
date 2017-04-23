<?php

namespace Formatcc\Yii2Schedule;

use yii\console\Controller;

class ScheduleController extends Controller
{

    /**
     * 任务对象
     */
    protected $schedule;

    /**
     * 回调设置所有任务的函数
     * @var \Closure
     */
    public $schedules;

    /**
     * 创建任务对象
     */
    public function init()
    {
        $this->schedule = new Schedule();
        $this->schedule->setSchedules($this->schedules);
    }

    /**
     * 执行任务
     */
    public function actionRun()
    {

        $events = $this->schedule->dueEvents();
        return;

        $eventsRan = 0;

        foreach ($events as $event) {
            if (! $event->filtersPass($this->laravel)) {
                continue;
            }

            $this->line('<info>Running scheduled command:</info> '.$event->getSummaryForDisplay());

            $event->run($this->laravel);

            ++$eventsRan;
        }

        if (count($events) === 0 || $eventsRan === 0) {
            $this->info('No scheduled commands are ready to run.');
        }
    }

}
