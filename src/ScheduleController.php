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
     * 创建任务对象
     */
    public function init()
    {
    }

    /**
     * 执行任务
     */
    public function actionRun()
    {
        $this->schedule = new Schedule();

        $events = $this->schedule->dueEvents();

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
