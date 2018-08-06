<?php
/**
 * Created by PhpStorm.
 * @author liangw
 * @Date: 2018/6/6
 * @Time: 10:31
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
<div class="autoscreen" style="margin-left: auto;margin-right: auto;text-align: center">
    <table id="list" class="table table-bordered table-condensed table-hover table-striped" data-toggle="table"
           data-toolbar="#toolbar"
           data-search="true"
           data-show-columns="true"
           data-show-export="true"
           data-detail-view="true"
           data-detail-formatter="detailFormatter">
    </table>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="myModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modeltitle"></h4>
            </div>
            <div class="modal-body">
                <p>
                    <img id="modalimage" src=""  style="max-height:100%;max-width:100%;">
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
<script type="text/javascript" src="../vendor/bootstrap-table/dist/bootstrap-table.js"></script>
<script type="text/javascript" src="../vendor/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js"></script>
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


    $(function () {
       $.ajax({
            data:{
                action: 'reportlist',
            },
           method: 'post',
           url: 'reportaction.php',
           dataType: 'json',
           success: function (datalist) {
               $('#list').bootstrapTable('load',datalist);
           },
           error: function () {
               console.log("查询数据出错");
               // location.reload();
           }
       });
    });

    tablelist = $('#list').bootstrapTable({
        idField: 'taskid',
        uniqueId: 'taskid',
        columns: [
            {
                field: 'taskid',
                title: 'Task ID'
            }, {
                field: 'submitter',
                title: 'Submitter'
            }, {
                field: 'time',
                title: 'Time'
            }, {
                field: 'status',
                title: 'Status',
                formatter: function(value,row,index){
                    var html = '';
                    if(value <= 0){
                        html = '<button type="button" class="btn btn-default btn-xs">No Run</button>';
                    }
                    if(0 < value < 100){
                        html = '<div class="progress" style="margin-bottom: 0px;">'+
                            '<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow='+parseInt(value)+' aria-valuemin="0" aria-valuemax="100" style="width: '+parseInt(value)+'%;color:black">'+
                            parseInt(value)+'%'+
                            '</div>'+
                            '</div>';
                    }
                    if(value >= 100){
                        html = '<button type="button" class="btn btn-success btn-xs">Completed</button>';
                    }
                    return html;
                }
            }, {
                field: 'result',
                title: 'Result',
                formatter: function(value,row,index){
                    return '<button type="button" class="btn btn-primary btn-xs" title="Not Run">'+value.in_progress+'</button>'+
                        '<button type="button" class="btn btn-success btn-xs" title="Passed">'+value.passed+'</button>'+
                        '<button type="button" class="btn btn-danger btn-xs" title="Failed">'+value.failed+'</button>'+
                        '<button type="button" class="btn btn-warning btn-xs" title="Warning">'+value.warning+'</button>';
                }
            },{
                title: 'Operation',
                width: '120px',
                formatter: function (value, row, index) {
                    return '<button type="button" onclick="deletetask('+row.taskid+')" data-taskid="'+row.taskid+'" class="btn btn-danger btn-xs" title="Delete Item" style="margin-right: 5px"><i class="glyphicon glyphicon-remove"></i></button>';
                }
            }
        ],
        pagination: true,
        pageSize: 15,                     //每页的记录行数（*）
        pageList: [15, 20, 30, 40],
    });

    function detailFormatter(index, row, $detail) {
        var html = [];
        $.post('tablelist.php',
            {
                taskid:row.taskid,
            },
            function (output){
                $detail.html(output);
            });
    }

    var refresh_flag = true;
    setInterval(function() {
        if($('.detail-view').length == 0 && refresh_flag){
            refreshRow();
        }else{
            console.log('no refresh');
        }
    }, 3000);

    function refreshRow() {
        $.ajax({
            data:{
                action: 'refreshrow',
            },
            method: 'post',
            url: 'reportaction.php',
            dataType: 'json',
            success: function (datalist) {
                if(datalist != -100){
                    changeData(datalist);
                }else{
                    refresh_flag = false;
                }
            },
            error: function () {
                console.log("查询数据出错");
            }
        });
    }
    
    function changeData(datalist) {
        for(var i = 0; i< datalist.length; i++){
            var rowindex = $("[data-uniqueid="+datalist[i].taskid+"]").data('index');
            $('#list').bootstrapTable('updateRow', {
                index: rowindex,
                row: datalist[i]
            });
        }
    }

    function deletetask(taskid) {
        if(confirm('Are you sure to delete '+taskid+' item?')){
            $.ajax({
                url: 'action.php',
                method: 'post',
                data:{
                    action: 'deletetask',
                    taskid: taskid
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data.status);
                    if(data.status == 100){
                        $('#list').bootstrapTable('refresh', {
                            silent: true,
                            url: 'reportaction.php?action=reportlist'
                        });
                        alert('Delete '+taskid+' success');
                    }
                },
                error: function () {
                    console.log("查询数据出错");
                }
            });
        }else{
            alert('Operation canceled!');
        }
    }
</script>
</html>
