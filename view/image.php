<?php
/**
 * Created by PhpStorm.
 * @author liangw
 * @Date: 2018/6/7
 * @Time: 15:56
 */
require_once '../conf/sql.php';

$id = $_POST['id'];

$job = getjob($id);

echo '../view/workspace/'.$job['taskid'].'/'.$job['name'];