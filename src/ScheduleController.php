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

    public $scheduleFile;

    public function options($actionID){
        return array_merge(parent::options($actionID),
            $actionID == 'run' ? ['scheduleFile'] : []
        );
    }

    /**
     * 创建任务对象
     */
    public function init(){
        $this->schedule = new Schedule();
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
        $scheduleFile = \Yii::getAlias($this->scheduleFile);
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
