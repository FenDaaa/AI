<?php
$db = new mysqli('localhost', 'root', '', 'ai');
$db->set_charset("utf8"); 
if ($db->connect_error) {
    echo "Can not connect to DB, Please try later or contact administrator!";
	echo $conn->connect_error;
    exit();
}

function addtask($taskid, $name) {
    $user = "Junjun";
    $sql = "insert into jobs (taskid,submitter,name) values ('".$taskid."','".$user."','".$name."')";
    // echo $sql_add_task;
    if($GLOBALS['db']->query($sql)) {
        return true;
    }
    else {
        return false;
    }
}

function getconsolestarttask($taskid){
    $sql = "select * from jobs where taskid =".$taskid;
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_all(MYSQLI_ASSOC);
    return $rs;
}

function getjob($id){
    $sql = "select * from jobs where id =".$id;
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_array(MYSQLI_ASSOC);
    return $rs;
}

function addresult($id,$res,$modelid){
    $tag = true;
    foreach ($res as $value) {
        $sql = "insert into result (jobid,tagname,probability,model) values ('".$id."','".$value['tagName']."','".$value['probability']."',".$modelid.")";

        if($GLOBALS['db']->query($sql)) {
            continue;
        }
        else {
            $tag = false;
        }
    }

    return $tag;
}

function getalltaskgroup(){
    $sql = "select * from jobs where taskid > 0 GROUP BY taskid ORDER BY taskid DESC";
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_all(MYSQLI_ASSOC);
    return $rs;
}

function getprocess($taskid){
    $sql_total = "select COUNT(*) as total from jobs where taskid = ".$taskid;
    $rst_total = $GLOBALS['db']->query($sql_total);
    $rs_total  = $rst_total->fetch_array(MYSQLI_ASSOC);

    $sql_num = "select COUNT(*) as num from resultview where taskid = ".$taskid;
    $rst_num = $GLOBALS['db']->query($sql_num);
    $rs_num  = $rst_num->fetch_array(MYSQLI_ASSOC);

    return round($rs_num['num']/2/$rs_total['total']*100);
}

function getjobstatus($id,$tagname,$model) {
    $sql = "select * from result where jobid = ".$id." and tagname = '".$tagname."' and model = ".$model." limit 1";
//    var_dump($sql);
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_array(MYSQLI_ASSOC);
    return $rs['probability'];
}

function getstatusnum($taskid){
    $data = [];

    $data['in_progress'] = 0;
    $data['passed'] = 0;
    $data['warning'] = 0;
    $data['failed'] = 0;
    $data['status'] = 0;

    //根据taskid获取从job表获取数据
    $tasks = getconsolestarttask($taskid);

//    $good = 'locale';
//    $fail = 'unlocale';
    //根据taskid从视图里获取modelid,passtag,failtag
    $models = getworkmodels($taskid);

    foreach ($tasks as $task){
        $temp = 0;

        foreach ($models as $model) {
            //根据id和tagname获取从result表里获取probability的值
            $goodpro = getjobstatus($task['id'],$model['passtag'],$model['model']);
            $failpro = getjobstatus($task['id'],$model['failtag'],$model['model']);

            if($failpro - $goodpro > 0.2) {
                $temp = 0;
                $data['failed'] ++;
                updateresultstatus($taskid, 'failed', $data['failed']);
//                break;
            }
            if(0 < abs($goodpro - $failpro) && abs($goodpro - $failpro) < 0.2) {
                $temp = 0;
                $data['warning'] ++;
                updateresultstatus($taskid, 'warning', $data['warning']);
//                break;
            }
            if($goodpro - $failpro > 0.2) {
                $temp = 0;
                $data['passed'] ++;
                updateresultstatus($taskid, 'passed', $data['passed']);
//                break;
            }
            if($goodpro > $failpro) {
                $temp = 1;
                continue;
            }
            if(empty($goodpro) || empty($failpro)) {
                $temp = 0;
                $data['in_progress'] ++;
//                updateresultstatus($taskid, 'in_progress', $data['in_progress']);
//                break;
            }
//            else {
//                $temp = 0;
//                $data['inprogress'] ++;
//                break;
//            }
        }

//        if ($temp) {
//            $data['pass'] ++;
//        }
    }
    if($data['in_progress']+$data['passed']+$data['failed']+$data['warning'] == 0){
        $data['status'] = 0;
    }else{
        $data['status'] = (($data['passed']+$data['failed']+$data['warning'])/($data['in_progress']+$data['passed']+$data['failed']+$data['warning']));
        updateresultstatus($taskid, 'in_progress', $data['in_progress']);
        if($data['status'] == 1){
            updateresultstatus($taskid, 'flag', 1);
        }
    }
    return $data;
}

function getallmodel(){
    $sql = "select * from model order by disable, rank";
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_all(MYSQLI_ASSOC);
    return $rs;
}

function getavailablemodel(){
    $sql = "select * from model WHERE disable = 0";
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_all(MYSQLI_ASSOC);
    return $rs;
}

function getworkmodels($taskid) {
    $sql = "select DISTINCT model,passtag,failtag,modelname from resultview where taskid = ".$taskid." order by rank";
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_all(MYSQLI_ASSOC);

    return $rs;
}

function editonemodel($id,$name,$url,$predictionkey,$passtag,$failtag){
    $sql = "update model set name = '".$name."',url = '".$url."',predictionkey = '".$predictionkey."',passtag = '".$passtag."', failtag = '".$failtag."' where id = ".$id;
    var_dump($sql);
    if($GLOBALS['db']->query($sql)) {
        return true;
    }
    else {
        return false;
    }
}

function addonemodel($name,$url,$predictionkey,$passtag,$failtag){
    $getmaxranksql = "select max(rank) as maxrank from model";
    $rst = $GLOBALS['db']->query($getmaxranksql);
    $rs  = $rst->fetch_array(MYSQLI_ASSOC);
    $maxrank = $rs['maxrank']+1;
    $sql = "insert into model (name,url,predictionkey,passtag,failtag,rank) values ('".$name."','".$url."','".$predictionkey."','".$passtag."','".$failtag."','".$maxrank."')";
    if($GLOBALS['db']->query($sql)) {
        return true;
    }
    else {
        return false;
    }
}

function modelactive($id){
    $sql = "update model set disable = 0 where id =".$id;
    if($GLOBALS['db']->query($sql)) {
        return true;
    }
    else {
        return false;
    }
}

function modeldisable($id){
    $sql = "update model set disable = 1 where id =".$id;
    if($GLOBALS['db']->query($sql)) {
        return true;
    }
    else {
        return false;
    }
}

function order_model($dragbeforeidx,$orderbefore,$idbefore,$draglateridx){
    $sql = "update model set rank =".$draglateridx." where id =".$idbefore." and disable = 0";
    if($GLOBALS['db']->query($sql)){
        //上移
        if($dragbeforeidx > $draglateridx){
            $sql1 = "update model set rank =rank+1 where rank >= ".$draglateridx." and rank <".$dragbeforeidx." and id !=".$idbefore." and disable = 0";
            echo $sql1;
            if($GLOBALS['db']->query($sql1)) {
                return true;
            }else{
                return false;
            }
        }

        //下移
        if($dragbeforeidx < $draglateridx){
            $sql2 = "update model set rank =rank-1 where rank > ".$dragbeforeidx." and rank <=".$draglateridx." and id !=".$idbefore." and disable = 0";
            echo $sql2;
            if($GLOBALS['db']->query($sql2)) {
                return true;
            }else{
                return false;
            }
        }

    }
    else {
        return false;
    }
}

function getmodelbyid($id) {
    $sql = "select * from model where id =".$id;
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_array(MYSQLI_ASSOC);
    return $rs;
}

function getresultstatusbytaskid($taskid){
    $sql = "select * from result_status where taskid =".$taskid;
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_array(MYSQLI_ASSOC);
    return $rs;
}

function getallresultstatus(){
    $sql = "select taskid,flag from result_status";
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_all(MYSQLI_ASSOC);
    return $rs;
}

function updateresultstatus($taskid,$colname,$current_status_val){
    $sql = "update result_status set $colname = $current_status_val where taskid = $taskid";
    if($GLOBALS['db']->query($sql)) {
        return true;
    }else{
        return false;
    }
}

function addresultstatus($taskid, $flag){
    $sql = "insert into result_status (taskid,in_progress,passed,failed,warning,flag) values ('".$taskid."',0,0,0,0,$flag)";
    if($GLOBALS['db']->query($sql)) {
        return true;
    }
    else {
        return false;
    }
}

function getresultstatusflagbytaskid($taskid){
    $sql = "select taskid, flag from result_status where taskid =".$taskid;
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_array(MYSQLI_ASSOC);
    return $rs;
}

function getresultstatusflagbyflag0(){
    $sql = "select taskid, flag from result_status where flag = 0";
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_all(MYSQLI_ASSOC);
    return $rs;
}

function getcurrenttaskidflagis2(){
    $sql = "select taskid, flag from result_status where flag =2";
    $rst = $GLOBALS['db']->query($sql);
    $rs  = $rst->fetch_all(MYSQLI_ASSOC);
    return $rs;
}

function deletetaskbytaskid($taskid){
    $sql = "delete from jobs where taskid='".$taskid."'";
    if($GLOBALS['db']->query($sql)) {
        return true;
    }
    else {
        return false;
    }
}

function deleteresultstatusbytaskid($taskid){
    $sql = "delete from result_status where taskid='".$taskid."'";
    if($GLOBALS['db']->query($sql)) {
        return true;
    }
    else {
        return false;
    }
}
?>