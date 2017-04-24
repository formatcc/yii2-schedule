<?php

namespace Formatcc\Yii2Schedule;

use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;

class Schedule {
    /**
     * 所有的任务
     * @var array
     */
    protected $events = [ ];

    /**
     * Add a new callback event to the schedule.
     *
     * @param $callback
     * @param array $parameters
     * @return CallbackEvent
     */
    public function call( $callback, array $parameters = [ ] ) {
        $this->events[] = $event = new CallbackEvent( $callback, $parameters );
        return $event;
    }

    /**
     * Add a new Yii command event to the schedule.
     *
     * @param $command
     * @return Event
     */
    public function command( $command) {
        $binary = ProcessUtils::escapeArgument( ( new PhpExecutableFinder )->find( false ) );

        if ( defined( 'HHVM_VERSION' ) ) {
            $binary .= ' --php';
        }

        if ( defined( 'YII_BINARY' ) ) {
            $yii = ProcessUtils::escapeArgument( YII_BINARY );
        }
        else {
            $yii = 'yii';
        }

        return $this->exec( "{$binary} {$yii} {$command}" );
    }

    /**
     * Add a new command event to the schedule.
     *
     * @param $command
     * @return Event
     */
    public function exec($command) {
        $this->events[] = $event = new Event( $command );
        return $event;
    }

    /**
     * 获取所有的任务
     * @return array
     */
    public function events() {
        return $this->events;
    }

    /**
     * 获取所有到期的任务
     * @return array
     */
    public function dueEvents() {
        return array_filter( $this->events, function ( Event $event ) {
            return $event->isDue();
        } );
    }

    /**
     * 检测运行时需要的目录是否存在
     * @return bool
     */
    public function checkRuntime(){
        $dir = \Yii::getAlias("@runtime").DIRECTORY_SEPARATOR."schedule";
        if (is_dir($dir) || @mkdir($dir, "0777")) {
            return true;
        }
        return false;
    }
}
