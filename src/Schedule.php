<?php

namespace Formatcc\Yii2Schedule;

use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;

class Schedule
{
    /**
     * 所有的任务
     * @var array
     */
    protected $events = [];


    /**
     * 设置所有任务
     * @param $schedules
     */
    public function setSchedules($schedules){
        if($schedules && $schedules instanceof \Closure){
            call_user_func($schedules, $this);
        }
    }

    /**
     * Add a new callback event to the schedule.
     *
     * @param  string  $callback
     * @param  array   $parameters
     * @return \Illuminate\Console\Scheduling\Event
     */
    public function call($callback, array $parameters = [])
    {
        $this->events[] = $event = new CallbackEvent($callback, $parameters);

        return $event;
    }

    /**
     * Add a new Artisan command event to the schedule.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return \Illuminate\Console\Scheduling\Event
     */
    public function command($command, array $parameters = [])
    {
        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));

        if (defined('HHVM_VERSION')) {
            $binary .= ' --php';
        }

        if (defined('ARTISAN_BINARY')) {
            $artisan = ProcessUtils::escapeArgument(ARTISAN_BINARY);
        } else {
            $artisan = 'artisan';
        }

        return $this->exec("{$binary} {$artisan} {$command}", $parameters);
    }

    /**
     * Add a new command event to the schedule.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return \Illuminate\Console\Scheduling\Event
     */
    public function exec($command, array $parameters = [])
    {
        if (count($parameters)) {
            $command .= ' '.$this->compileParameters($parameters);
        }

        $this->events[] = $event = new Event($command);

        return $event;
    }

    /**
     * Compile parameters for a command.
     *
     * @param  array  $parameters
     * @return string
     */
    protected function compileParameters(array $parameters)
    {
        return collect($parameters)->map(function ($value, $key) {
            return is_numeric($key) ? $value : $key.'='.(is_numeric($value) ? $value : ProcessUtils::escapeArgument($value));
        })->implode(' ');
    }

    /**
     * 获取所有的任务
     * @return array
     */
    public function events()
    {
        return $this->events;
    }

    /**
     * 获取所有到期的任务
     * @return array
     */
    public function dueEvents()
    {
        return array_filter($this->events, function ($event) {
            return $event->isDue();
        });
    }
}
