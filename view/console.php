<?php
/**
 * Created by PhpStorm.
 * @author liangw
 * @Date: 2018/6/6
 * @Time: 16:45
 */

require_once '../conf/sql.php';

$taskid = $argv[1];
$selected_model = $argv[2];
$selected_model_arr = $selected_model != 'none' ? explode(",",$selected_model) : '';
$selected_model_flag = empty($selected_model_arr) ? true : false;
$tasks = getconsolestarttask($taskid);

//$url = 'https://southcentralus.api.cognitive.microsoft.com/customvision/v2.0/Prediction/6dcd84c9-4613-47de-bdb2-78c91d91c567/image';
//$PredictionKey = '02e64f9bc1da4bf29997df7e961dac63';
$models = getavailablemodel();

$file_path = __DIR__ ."\\workspace";
$target_dir = $file_path . '\\' . $taskid;

foreach ($tasks as $task) {
    $body = [
        'img' => new CURLFile($target_dir.'\\'.$task['name'], 'image/png', $task['name'])
    ];

//    var_dump($body);
    if($selected_model_flag){
        if($models) {
            foreach ($models as $model) {
                $result = send($body,$model['url'],$model['predictionkey']);
    //    var_dump($result);
                $data = json_decode($result, true);
    //    var_dump($data['predictions']);
                addresult($task['id'],$data['predictions'],$model['id']);
            }
        }
    }else{
        foreach ($selected_model_arr as $s_id) {
            $model_item = getmodelbyid($s_id);
            $result = send($body,$model_item['url'],$model_item['predictionkey']);
            $data = json_decode($result, true);
            addresult($task['id'],$data['predictions'],$model_item['id']);
        }
    }
}

function send($body,$url,$PredictionKey){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Cache-Control: no-cache",
        "Content-Type: multipart/form-data",
        'Prediction-Key: '.$PredictionKey,
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    //设置链接超时时间为1分钟
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        print curl_error($ch);
    }
    curl_close($ch);

    return $result;

}
