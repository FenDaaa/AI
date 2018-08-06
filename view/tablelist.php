<?php
/**
 * Created by PhpStorm.
 * @author liangw
 * @Date: 2018/6/6
 * @Time: 12:01
 */
require_once '../conf/sql.php';

$good = 'locale';
$fail = 'unlocale';

$taskid = $_POST['taskid'];

$tasks = getconsolestarttask($taskid);
$models = getworkmodels($taskid);

function checkjobs($id,$models){
    $label = array();

    $label['pro'] = array();
    $label['class'] = array();

    foreach ($models as $model) {
        $goodpro = getjobstatus($id,$model['passtag'], $model['model']);
        $failpro = getjobstatus($id,$model['failtag'], $model['model']);

        array_push($label['pro'], $goodpro);
        array_push($label['pro'], $failpro);

        if($failpro - $goodpro > 0.2) {
            array_push($label['class'], "danger");
            array_push($label['class'], "danger");

//            $label['class'] = "danger";
            $label['res'] = "Fail";
//            break;
        } else if(0 < abs($goodpro - $failpro) && abs($goodpro - $failpro) < 0.2) {
            array_push($label['class'], "warning");
            array_push($label['class'], "warning");

//            $label['class'] = "warning";
            $label['res'] = "Not Sure";
//            break;
        } else if(!($goodpro) || !($failpro)) {
            array_push($label['class'], "");
            array_push($label['class'], "");

//            $label['class'] = "";
            $label['res'] = "Not Run";
//            break;
        } else if($goodpro > $failpro) {
            array_push($label['class'], "success");
            array_push($label['class'], "success");

//            $label['class'] = "success";
            $label['res'] = "Pass";
        } else {
            array_push($label['class'], "");
            array_push($label['class'], "");

//            $label['class'] = "";
            $label['res'] = "Error";
        }
    }

    return $label;
}

?>
<link rel="stylesheet" href="../vendor/bootstrap-table/dist/bootstrap-table.css">
<div class="autoscreen" style="overflow: scroll;margin: auto;">
    <table class="table" style="font-size: 12px;">
        <thead>
        <tr>
            <th style="padding:22px 8px;" rowspan="2">#</th>
            <th style="padding: 22px 8px;" rowspan="2">Picture Name</th>
            <?php foreach ($models as $model) {
                ?>
                <th style="padding: 6px" colspan="2"><?= $model['modelname'] ?></th>
                <?php
            }
            ?>
        </tr>
        <tr>
            <?php foreach ($models as $model) {
                ?>
                <th style="padding: 8px"><?= $model['passtag'] ?></th>
                <th style="padding: 8px"><?= $model['failtag'] ?></th>
                <?php
                }
                ?>
        </tr>
        </thead>
        <tbody>
        <?php
            foreach ($tasks as $key => $task) {
                $label = checkjobs($task['id'],$models)
        ?>
            <tr>
                <th scope="row"><?= $key+1 ?></th>
                <td><a type="button" onclick="showtheimage(<?= $task['id'] ?>,'<?= $task['name'] ?>');"><?= $task['name'] ?></a></td>
                <?php foreach ($label['pro'] as $k => $item) {
                ?>
                    <td class="<?= $label['class'][$k] ?>"><?= round($item,3)*100 ?>%</td>
                <?php
                }
                ?>
            </tr>
        <?php
            }
        ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function(){
        var screenauto = window.screen.width;
        if(screenauto >= 1280 && screenauto <1440){
            $('.autoscreen').css('width',screenauto)
        }
        if(screenauto == 1440){
            $('.autoscreen').css('width',screenauto)
        }
    });
    function showtheimage(id,name) {
        $("#modalimage").attr('src','');
        $("#modeltitle").text("");
        $.post('image.php',
            {
                id: id,
            },
            function (output){
                $("#modalimage").attr('src', output);
                $('#myModal').modal('show');
                $("#modeltitle").text(name);
            });
    }
</script>