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
     * Add a new Artisan command event to the schedule.
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
        var_dump($this->events());
        return array_filter( $this->events, function ( Event $event ) {
            return $event->isDue();
        } );
    }
}
