<?php
/**
 * Created by PhpStorm.
 * User: Fx
 * Date: 2018/7/31
 * Time: 16:10
 */

require_once '../conf/sql.php';
$action = empty($_POST['action']) ? $_GET['action'] : $_POST['action'];

switch ($action) {
    case 'reportlist':
        reportlist();
        break;
    case 'refreshrow':
        refreshrow();
        break;
    default:
        break;
}


function reportlist(){
    $currenttask = [];
    $finishedtask = [];
    $tasks = getalltaskgroup();
    $allresultstatus = getallresultstatus();
    $allresultstatusonlytaskid = array_column($allresultstatus,'taskid');
    $result_status_flags = getresultstatusflagbyflag0();
    foreach ($tasks as $k => $task){
        $finishedtasknum = getresultstatusbytaskid($task['taskid']);
        if (!in_array($task['taskid'], $allresultstatusonlytaskid)) {
            $result_status_init = addresultstatus($task['taskid'],0);
            $runnum = getstatusnum($task['taskid']);
            $currenttask[$k]['taskid'] = $task['taskid'];
            $currenttask[$k]['status'] = round($runnum['status'], 2) * 100;
            $currenttask[$k]['time'] = date("Y-m-d H:i:s", $task['taskid']);
            $currenttask[$k]['submitter'] = $task['submitter'];
            $currenttask[$k]['result'] = ['passed' => $runnum['passed'], 'failed' => $runnum['failed'], 'warning' => $runnum['warning'], 'in_progress' => $runnum['in_progress']];
        } else {
            $finishedtask[$k]['taskid'] = $task['taskid'];
            if ($finishedtasknum['in_progress'] + $finishedtasknum['passed'] + $finishedtasknum['failed'] + $finishedtasknum['warning'] == 0) {
                $finishedtask[$k]['status'] = 0;
            } else {
                $finishedtask[$k]['status'] = round(($finishedtasknum['passed'] + $finishedtasknum['failed'] + $finishedtasknum['warning']) / ($finishedtasknum['in_progress'] + $finishedtasknum['passed'] + $finishedtasknum['failed'] + $finishedtasknum['warning']), 2) * 100;
            }
            $finishedtask[$k]['time'] = date("Y-m-d H:i:s", $task['taskid']);
            $finishedtask[$k]['submitter'] = $task['submitter'];
            $finishedtask[$k]['result'] = ['passed' => $finishedtasknum['passed'], 'failed' => $finishedtasknum['failed'], 'warning' => $finishedtasknum['warning'], 'in_progress' => $finishedtasknum['in_progress']];
        }
    }
    $totaltask = array_merge($currenttask,$finishedtask);
    echo json_encode($totaltask);
}

function refreshrow(){
    $currenttaskid = getcurrenttaskidflagis2();
    $currenttask = [];
    $tasks = getalltaskgroup();
    $allresultstatus = getallresultstatus();
    if(!empty($currenttaskid)){
//        $currentresultflag = getresultstatusflagbytaskid($currenttaskid['taskid']);
//        $allresultstatusonlytaskid = array_column($allresultstatus,'taskid');
        foreach ($currenttaskid as $k_id => $val_id){
            foreach ($tasks as $k => $task){
                if($task['taskid'] == $val_id['taskid']){
                    $runnum = getstatusnum($task['taskid']);
                    $currenttask[$k_id]['status'] = round($runnum['status'],2)*100;
                    $currenttask[$k_id]['taskid'] = $val_id['taskid'];
                    $currenttask[$k_id]['time'] = date("Y-m-d H:i:s",$task['taskid']);
                    $currenttask[$k_id]['submitter'] = $task['submitter'];
                    $currenttask[$k_id]['result'] = ['passed'=>$runnum['passed'],'failed'=>$runnum['failed'],'warning'=>$runnum['warning'],'in_progress'=>$runnum['in_progress']];
                }
            }
        }
        echo json_encode($currenttask);
    }else{
        echo '-100';
    }
}