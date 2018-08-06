<?php
/**
 * Created by PhpStorm.
 * @author liangw
 * @Date: 2018/7/4
 * @Time: 17:34
 */

require_once '../conf/sql.php';


switch ($_POST['action']) {
    case 'add':
        addmodel();
        break;
    case 'disable':
        disablemodel();
        break;
    case 'activemodel':
        activemodel();
        break;
    case 'disablemodel':
        disablemodel();
        break;
    case 'modelorder':
        modelorder();
        break;
    case 'deletetask':
        deletetask();
        break;
    case 'stoptask':
        stoptask();
        break;
    default:
        break;
}

function addmodel() {
    if($_POST['id']) {
        if(editonemodel($_POST['id'],$_POST['name'],$_POST['url'],$_POST['predictionkey'],$_POST['passtag'],$_POST['failtag'])){
            header("Location: model.php");
            exit;
        } else {
            echo "<script>alert('Error')</script>";
//            header("Location: model.php");
            exit;
        }
    } else {
        if(addonemodel($_POST['name'],$_POST['url'],$_POST['predictionkey'],$_POST['passtag'],$_POST['failtag'])){
            header("Location: model.php");
            exit;
        } else {
            echo "<script>alert('Error')</script>";
            header("Location: model.php");
            exit;
        }
    }
}

function activemodel(){
    if(!empty($_POST['taskid'])){
        return modelactive($_POST['taskid']);
    }else{
        return false;
    }
}

function disablemodel(){
    if(!empty($_POST['taskid'])){
        return modeldisable($_POST['taskid']);
    }else{
        return false;
    }
}

function modelorder(){
    $orderbefore = !empty($_POST['orderbefore']) ? $_POST['orderbefore'] : '';
    $idbefore = !empty($_POST['idbefore']) ? $_POST['idbefore'] : '';
    $result = order_model($_POST['dragbeforeidx'],$orderbefore,$idbefore,$_POST['draglateridx']);
    if($result) {
        return true;
    }else {
        return false;
    }
}

function deletetask(){
    $result = [];
    $status = deletetaskbytaskid($_POST['taskid']);
    $result_status = deleteresultstatusbytaskid($_POST['taskid']);
    if($status && $result_status){
        $result['status'] = 100;
        $result['msg'] = 'success';
    }else{
        $result['status'] = -100;
        $result['msg'] = 'error';
    }
    echo json_encode($result);
}

function stoptask(){
    $result = [];
    $status = updateresultstatus($_POST['taskid'], 'flag', 1);
    if($status){
        $result['status'] = 100;
        $result['msg'] = 'success';
    }else{
        $result['status'] = -100;
        $result['msg'] = 'error';
    }
    echo json_encode($result);
}