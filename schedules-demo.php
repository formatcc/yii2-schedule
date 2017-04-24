<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2017/4/23
 * Time: 下午10:34
 */


$schedule->call(function($param){
    $count =0;
    while(1){
        if($count>10){
            break;
        }
        Yii::error($count);
//        echo $count."\n";
//        var_dump($param);
        $count++;
        sleep(1);
    }
}, ['a'=>11, 'b'=>22])->cron("* * * * * *")->runInBackground()->name("aa")->withoutOverlapping();

//withoutOverlapping
