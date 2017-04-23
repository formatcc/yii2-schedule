Yii2 schedule


'controllerMap' => [
    'schedule' => [
        'class' => 'Formatcc\Yii2Schedule\ScheduleController',
        'schedules' => require(__DIR__ . '/schedules.php'),
    ],
],