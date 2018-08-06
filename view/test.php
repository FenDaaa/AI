<?php
/**
 * Created by PhpStorm.
 * User: Fx
 * Date: 2018/7/30
 * Time: 9:04
 */
print_r($_POST['select_model']);
print_r($_FILES['upload']);
foreach ($_FILES['upload']['name'] as $key => $val){
    echo $val;
}