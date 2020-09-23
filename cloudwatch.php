<?php

$outputFileName = "hoge.txt";
$fp1 = fopen($outputFileName, "w");

$cnt = 0;
$nextForwardToken = "";

while(1) {
    $fileName = "tmp_" . (string)$cnt . ".txt";

    $base = "aws logs get-log-events --log-group-name '<LOG_GROUP_NAME>' --log-stream-name '<LOG_STREAM_NAME>' --start-time <START_UNIX_TIME_MILLISEC> --end-time <END_UNIX_TIME_MILLISEC> --region us-east-1 --profile default ";
    $option = ($nextForwardToken != "" ? "--next-token '" . $nextForwardToken . "'" : "--start-from-head");

    $cmd = $base . $option . " > " . $fileName;
    exec($cmd);

    $fp = fopen($fileName, "r");
    $rawData = fread($fp, filesize($fileName));
    fclose($fp);

    $dataArray = json_decode($rawData, true);

    if(count($dataArray['events']) == 0) {
        break;
    }

    foreach ($dataArray['events'] as $event) {
        fwrite($fp1, $event['message']."\n");
    }

    $nextForwardToken = $dataArray["nextForwardToken"];
    $cnt++;
}

fclose($fp1);


?>
