<?php

namespace Formatcc\Yii2Schedule;

use yii\console\Controller;

class ScheduleController extends Controller
{

    /**
     * The schedule instance.
     */
    protected $schedule;

    /**
     * @var string
     * schedule configuration file.
     */
    public $schedules;

    public function options($actionID){
        return array_merge(parent::options($actionID),
            $actionID == 'run' ? ['schedules'] : []
        );
    }


    /**
     * 创建任务对象
     */
    public function init(){
        $this->schedule = new Schedule();
        $this->schedule->checkRuntime();

        parent::init();
    }

    /**
     * 执行任务
     */
    public function actionRun(){
        $this->loadConfig();

        $events = $this->schedule->dueEvents(\Yii::$app);

        $eventsRan = 0;

        foreach ($events as $event) {
            if (! $event->filtersPass(\Yii::$app)) {
                continue;
            }
            $this->stdout('Running scheduled command: '.$event->getSummaryForDisplay()."\n");

            $event->run(\Yii::$app);

            ++$eventsRan;
        }

        if (count($events) === 0 || $eventsRan === 0) {
            $this->stdout('No scheduled commands are ready to run.');
        }
    }

    /**
     * 加载配置文件
     */
    private function loadConfig(){
        $scheduleFile = \Yii::getAlias($this->schedules);
        if (file_exists($scheduleFile) == false) {
            $this->stderr('Can not load schedule file '.$scheduleFile."\n");
            return;
        }

        $schedule = $this->schedule;
        call_user_func(function() use ($schedule, $scheduleFile) {
            include $scheduleFile;
        });
    }
}
