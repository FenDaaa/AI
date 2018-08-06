<?php
/**
 * Created by PhpStorm.
 * @author liangw
 * @Date: 2018/7/3
 * @Time: 11:12
 */

require_once '../conf/sql.php';
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once 'layout/head.php'; ?>
<link rel="stylesheet" href="../vendor/bootstrap-table/dist/bootstrap-table.css">
<link rel="stylesheet" href="../css/view.css">
<body>
<?php include_once 'layout/nav.php'; ?>
<div id="toolbar">
    <button type="button" class="btn btn-success" title="Add" onclick="addmodel();"><i class="glyphicon glyphicon-plus"></i> New Model</button>
</div>
<div class="screenauto" style="margin: 10px">
    <table id="modellist" class="table table-bordered table-condensed table-hover table-striped " data-toggle="table"
           data-toolbar="#toolbar"
           data-search="true"
           data-show-columns="true"
           data-show-export="true"
           data-reorderable-rows="true"
           data-use-row-attr-func="true"
    >
        <thead>
        <tr>
            <th data-field="id">Model ID</th>
            <th>Name</th>
            <th>URL</th>
            <th>Pass Tag</th>
            <th>Fail Tag</th>
            <th class="hidden">Order</th>
            <th>Operations</th>
        </tr>
        </thead>
        <tbody>
        <?php
            $models = getallmodel();

            foreach ($models as $model) {
                $icon = $model['disable'] == 0 ? '<i class="glyphicon glyphicon-ban-circle"></i>' : '<i class="glyphicon glyphicon-ok"></i>';
                $iconcolor = $model['disable'] == 0 ? "btn btn-danger btn-xs" : "btn btn-success btn-xs";
        ?>
            <tr data-disable="<?=$model['disable'] ?>">
                <td class="td<?=$model['disable'] ?>"><?= $model['id'] ?></td>
                <td class="td<?=$model['disable'] ?>"><?= $model['name'] ?></td>
                <td class="td<?=$model['disable'] ?>"><?= $model['url'] ?></td>
                <td class="td<?=$model['disable'] ?>"><?= $model['passtag'] ?></td>
                <td class="td<?=$model['disable'] ?>"><?= $model['failtag'] ?></td>
                <td class="td<?=$model['disable'] ?> hidden"><?= $model['rank'] ?></td>
                <td style="text-align: center">
                    <button type="button" class="btn btn-primary btn-xs td<?=$model['disable'] ?>" title="Edit"
                        onclick="editmodel('<?= $model['id'] ?>','<?= $model['name'] ?>','<?= $model['url'] ?>','<?= $model['predictionkey'] ?>','<?= $model['passtag'] ?>','<?= $model['failtag'] ?>', '<?= $model['disable'] ?>', '<?= $model['rank'] ?>');"
                    ><i class="glyphicon glyphicon-edit"></i> </button>
                    <button type="button" class="<?= $iconcolor ?>" title="Disable"
                        onclick="activetoggle('<?= $model['id'] ?>', '<?= $model['disable'] ?>', '<?= $model['name'] ?>')"
                    ><?=$icon ?></i> </button>
                </td>
            </tr>
        <?php
            }
        ?>
        </tbody>
    </table>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="mymodel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modeltitle"></h4>
            </div>
            <form id="model_form" action="action.php" method="post">
                <div class="modal-body">
                    <input class="form-control" id="model_id" name="id" style="display: none">
                    <input class="form-control" name="action" value="add" style="display: none">

                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" id="model_name" placeholder="Modal Name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>URL</label>
                        <input class="form-control" id="model_url" placeholder="URL" name="url" required>
                    </div>

                    <div class="form-group">
                        <label>Prediction Key</label>
                        <input class="form-control" id="predictionkey" placeholder="Prediction Key" name="predictionkey" required>
                    </div>

                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Pass Tag</label>
                                <input class="form-control" id="passtag" placeholder="Pass Tag" name="passtag" required>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label>Fail Tag</label>
                                <input class="form-control" id="failtag" placeholder="Fail Tag" name="failtag" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>

                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
<script>
    $(document).ready(function(){
        var screenauto = window.screen.width;
        if(screenauto >= 1280 && screenauto <1440){
            $('.autoscreen').css('width',screenauto);
        }
        if(screenauto == 1440){
            $('.autoscreen').css('width',screenauto);
        }
        else{
            $('.autoscreen').css('width',screenauto);
        }
    });
    function addmodel() {
        $("#modeltitle").text("添加模型");
        $('#model_form')[0].reset();

        $('#mymodel').modal('show');
    }

    function editmodel(id,name,url,predictionkey,passtag,failtag,disable,rank) {
        if(disable == 0){
            $("#modeltitle").text("编辑模板");
            $('#model_form')[0].reset();

            $('#model_id').val(id);
            $('#model_name').val(name);
            $('#model_url').val(url);
            $('#predictionkey').val(predictionkey);
            $('#passtag').val(passtag);
            $('#failtag').val(failtag);


            $('#mymodel').modal('show');
        }
        else{
            alert('请先激活');
        }
    }

    function activetoggle(id, disable, name) {
        console.log(disable);
        if(disable == 0 && confirm('你确定禁用'+name+'吗？')){
            $.post('action.php',
                {
                    action:'disablemodel',
                    taskid:id,
                },
                function (output){
                    location.reload();
                });
        }
        if(disable == 1 && confirm('你确定激活'+name+'吗？')){
            $.post('action.php',
                {
                    action:'activemodel',
                    taskid:id,
                },
                function (output){
                    location.reload();
                });
        }
    }

    $('#modellist').bootstrapTable({
        pagination: true,
        pageSize: 15,                     //每页的记录行数（*）
        pageList: [15, 20, 30, 40],
        onReorderRowsDrag:function (table, row) {
            dragbeforeidx = $(row).attr("data-index");
            orderbefore = $(row)[0].cells[5].innerHTML;
            idbefore = $(row)[0].cells[0].innerHTML;
            postflag = $(row).data('disable');
            console.log('idbefore==='+idbefore);
            console.log('orderbefore==='+orderbefore);
        },
        onReorderRowsDrop: function (table, row) {
            draglateridx = $(row).attr("data-index");
        },
        onReorderRow: function (newData) {
            if (dragbeforeidx != draglateridx) {
                console.log("dragbeforeidx==="+dragbeforeidx);
                console.log("draglateridx==="+draglateridx);
                if(postflag == 0){
                    $.post('action.php',
                        {
                            action: 'modelorder',
                            dragbeforeidx: dragbeforeidx,
                            orderbefore: orderbefore,
                            idbefore: idbefore,
                            draglateridx: draglateridx,

                        },
                        function (output){
                            location.reload();
                        }
                    )
                }else{
                    alert('请先激活后在移动');
                    location.reload();
                }
            }

        }
    })
</script>
</html>