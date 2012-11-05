<?php

function logTime($time, $name){
    $file = fopen(TIME_LOG_PATH.$name.'.txt', "a");
    if(!$file){
        exit();
    }
    fputs($file, $time);
    fputs($file, "\n");
    fclose($file);
}

?>
