<!DOCTYPE html>
<html lang="en">
<?php require_once '../conf/sql.php'; ?>
<?php include_once 'layout/head.php'; ?>

<body>
<?php include_once 'layout/nav.php'; ?>
<div>
    <form id="form-ai" role="form" method="post" enctype="multipart/form-data">
        <div class="row form-group">
            <div class="contentwidth col-md-10 col-md-offset-1">
                <label for="upload">Select files/folders</label>
                <div class="upload">
                    <input class="form-control" id="upload" name="upload[]" type="file" multiple>
                </div>
            </div>
        </div>
        <div class="row form-group" style="margin-top: 10px">
            <div class="col-md-10 col-md-offset-1">
                <label for="modelname">
                    Model Name
                </label>
                <div class="modelname">
                    <select id="select_model" name="select_model[]" class="selectpicker form-control" multiple data-live-search="true">
                        <?php
                            $modelname = getavailablemodel();
                            foreach ($modelname as $value){
                        ?>
                            <option value="<?=$value['id'] ?>" data-content="<span class='label label-info'><?=$value['name'] ?></span>"><?= $value['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </form>
        <div class="row">
            <div class="col-md-4 col-md-offset-1">
                <button id="submitButton" class="btn btn-info form-control" style="width: 100px">Upload</button>
            </div>
        </div>
</div>
</body>
<script>

    $("#upload").fileinput({
        // uploadUrl: "upload.php",
        showUpload: false,
        uploadAsync: false,
        allowedFileExtensions: ["jpg", "png"]
    });
    $('.selectpicker').selectpicker({
        width: $('.upload').css('width'),
    });
    $('#submitButton').on('click',function () {
        var imglength = $('#upload')[0].files.length;
        if(imglength == 0){
            alert('Please upload the file first!');
        }else{
            var formData = new FormData($('#form-ai')[0]);
            formData.append('upload',$(':file')[0].files);
            $.ajax({
                type:'post',
                url:'upload.php',
                data:formData,
                contentType: false,
                processData: false,
                success:function(data){
                    var result = JSON.parse(data);
                    if(result.status == 0) {
                        alert(result.msg);
                        window.location.href = "report.php";
                    }else{
                        alert(result.msg);
                    }
                },
                error:function(XmlHttpRequest,textStatus,errorThrown){
                    alert('error');
                }
            });
        }
    });

</script>
<style>
    .bootstrap-select .dropdown-menu li {
        width: 20%;
        display: inline-block;
    }
    .label {
        font-size: 100%;
    }
</style>
</html>