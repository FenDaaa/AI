<?php

require_once '../conf/sql.php';

if (empty($_FILES['upload'])) {
    echo json_encode(['error'=>'No files found for upload.']);
    // or you can throw an exception
    return; // terminate
}

// get the files posted
$files = $_FILES['upload'];
$selected_model = empty($_POST['select_model']) ? '' : $_POST['select_model'];
$selected_model_str = empty($selected_model) ? 'none' : implode(",",$selected_model);
$file_path = __DIR__ ."\\workspace";

$taskid = time();
$target_dir = $file_path . '\\' . $taskid;
$result = [];
$result_status_init = addresultstatus($taskid,2);
//var_dump($files);
//var_dump($file_path);

try{
    if (!file_exists($target_dir) && !mkdir($target_dir, 0777, true)) {
        $result['status'] = -1;
        $result['msg'] = 'Task created failed!';
        echo json_encode($result);
        return;
    } else if (!is_writeable($target_dir)) {
        $result['status'] = -2;
        $result['msg'] = 'No right';
        echo json_encode($result);
        return;
    }
    //移动文件
    foreach ($files['name'] as $key => $file) {
        $file_full_path = $target_dir . '\\'. $file;

        if (!(move_uploaded_file($files["tmp_name"][$key], $file_full_path) && file_exists($file_full_path))) { //移动失败
            $result['status'] = -3;
            $result['msg'] = '写入文件内容错误';
            echo json_encode($result);
            return;
        } else { //移动成功

            //写入数据库
            if(!addtask($taskid,$file)) {
                $result['status'] = -4;
                $result['msg'] = '数据写入文件错误';
                echo json_encode($result);
                return;
            }
//            var_dump(addtask($taskid,$file));
            $result['status'] = 0;
            $result['msg'] = '文件上传成功';
            $result['taskid'] = $taskid;
        }
    }
}
catch(Exception $e){
    $result['status'] = -4;
    $result['msg'] = $e->getMessage();
}

$command = "start /b php console.php ".$taskid." ".$selected_model_str." >nul";
pclose(popen($command, 'r'));

//var_dump($result);
echo json_encode($result);
return;

