# Yii2 schedule



Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require formatcc/yii2-schedule
```



Add The following command to Linux Crontab

```
* * * * * php path/to/yii schedule/run --schedules=@app/config/schedules.php >> /dev/null 2>&1
```

Or

```
* * * * * php path/to/yii schedule/run --f=@app/config/schedules.php >> /dev/null 2>&1
```