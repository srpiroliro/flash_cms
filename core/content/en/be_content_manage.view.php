
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Are you sure?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        After deleting this page you <b>won't be able to restore the data!</b><br>
        You can simply <b>disable the page</b> so no one can access it by just changing the status to "Removed".
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" onclick="content_delete()">Delete</button>
      </div>
    </div>
  </div>
</div>

<div class="d-none alert alert-danger alert-dismissible fade show" role="alert" id="alert-error">
    <strong>Error!</strong> <br>
    <span id="err_msg"></span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="d-none alert alert-success alert-dismissible fade show" role="alert" id="alert-green">
  <strong>Hooray!</strong> Page added succesfully.

  <div class="float-right mr-2">
    <a href="/<?=$this->conf_array['backend']?>/content/"><strong>Go back</strong></a>
  </div>

  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<h2><?= ucfirst($action)?> content</h2>
<form class="ml-4" method="POST" action="<?= $_SERVER['REQUEST_URI']?>">
    <div class="d-block mb-3">
        <p class="m-0 text-bold">Title:</p>
        <input type="text" name="content_title" id="title" placeholder="title" required value="<?=(isset($content_title)?$content_title:'')?>">
    </div>
    <div class="d-block mb-3">
        <p class="m-0 text-bold">Language:</p>
        <select name="lang_id" id="lang">       
            <?php
                $sdata=array();
                $sdata['data']=$this->conf_array['multilang'];
                $sdata['type']='value2value';
                $sdata['id']=$this->lang_id;
                
                if(isset($language)){
                    echo "<option value='$language'>$language</option>";
                    $sdata['excp']=$language;
                }
                

                echo Html::selectList($sdata);
            ?>
        </select>
    </div>
    <div class="d-block mb-3">
        <p class="m-0 text-bold">Url:</p>
        <input type="text" name="content_catalog" id="url" placeholder="url" onclick="bodyText()" required value="<?=(isset($url)?$url:'')?>">
    </div>
    <div class="d-block mb-3">
        <p class="m-0 text-bold">Body:</p>
        <div id="body_text" class="<?=(isset($custom)?($custom==1?'d-none':''):'')?>">
            <div id="div-textarea" class="content-editor  p-0 w-60">
                <textarea id="mytextarea" name="content_body">
                    <?=(isset($body)?($body?$body:''):'')?>
                </textarea>
            </div>
            <div id="body_err" class="d-none">
                <div class="alert alert-danger" id="body_err_alert">
                    <strong>Error!</strong><br>
                    <span id="body_err_msg"></span>
                </div>   
            </div>
        </div>

        <div id="body_msg" <?=(isset($custom)?($custom==1?'':'class="d-none"'):'class="d-none"')?>>
            <i>Custom content doesn't allow body editing.</i>
        </div>
    </div>
    <div class="d-block mb-3">
        <p class="m-0 text-bold">Status:</p>
        <select name="page_status" id="status">
            <?php
                $sdata=array();
                $sdata['data']=$this->conf_array['statuses'];
                $sdata['type']='key2value';
                $sdata['id']=$this->lang_id;
                if($user_role!="admin") $sdata['excp']=2;
                
                
                if(isset($status)){
                    echo "<option value='$status'>".$this->conf_array['statuses'][$status]."</option>";
                    $sdata['excp1']=$status;
                }

                echo Html::selectList($sdata);
            ?>
        </select>
    </div>
    <div class="d-block mb-3">
        <p class="m-0 text-bold">Custom:  </p>
        <input type="checkbox" onclick="bodyText()" name="page_custom" id="custom" value="true" <?=(isset($custom)?($custom==1?'checked':''):'')?>>
    </div>

    <div class="d-inline mb-3">
        <input type="button" value="Upload" onclick="content_upload()" class="btn mr-1 btn-primary"/>
        <input type="reset" value="Reset values" class="btn  m-1  btn-warning"/>
        <?php
            if($action=="edit"){
                if($user_role=="admin"){
        ?>

        <input type="button" class="btn m-1  btn-danger" value="Delete" data-toggle="modal" data-target="#deleteModal">
                    
        <?php } else { ?>

        <input type="button" class="btn m-1  btn-danger" value="Remove" onclick="content_delete()">

        <?php
                } 
            } 
        ?>
    </div>
        <!--tr>
            <td>Heading:</td>
            <td>
                <input type="text" name="heading" id="heading" placeholder="Heading" required value="">
            </td>
        </tr>
        <tr>
            <td>Description:</td>
            <td>
                <input type="text" name="description" id="description" placeholder="Description" required value="">
            </td>
        </tr-->


    <input type="hidden" value="<?=Auth::formToken()?>" name="token">
    <input type="hidden" value="<?=isset($content_id)?$content_id:''?>" name="id">
    <input type="hidden" value="<?=$action?>" name="action">
</form>



<script>

    

    function content_delete(){
        $.ajax({
            type: "POST",
            url: "/core/ajax/content_delete.ajax.php",
            data:{
                'token':            $('input[name="token"]').val(),
                'id':               $('input[name="id"]').val(),
                'custom':           '<?=(isset($custom)?$custom:'')?>',
                'lang_id':          '<?=(isset($language)?$language:'')?>'
            },
            dataType:'text', 
            success: function(response){
                if(response.includes('200')){
                    $('#alert-green').removeClass("d-none");
                    $('#succ_msg').html(response);

                    window.location.replace('/<?= $this->conf_array['backend']?>/content/');

                } else {
                    $('#err_msg').html(response);
                    $('#alert-error').removeClass("d-none");
                }

            }
        });
    }
    
    
    
    function content_upload(){
        var selectedLang = $("select[name='lang_id']").children("option:selected").val();
        var selectedStatus = $("select[name='page_status']").children("option:selected").val();
        
        if($('#body_err').is(':visible')){
            body='error';
        } else {
            body=tinymce.get("mytextarea").getContent();
        }

        $.ajax({
            type: "POST",
            url: "/core/ajax/content_upload.ajax.php",
            data:{
                'token':            $('input[name="token"]').val(),
                'id':               $('input[name="id"]').val(),
                'action':           $('input[name="action"]').val(),
                'content_title':    $('input[name="content_title"]').val(),
                'lang_id':          selectedLang,
                'content_catalog':  $('input[name="content_catalog"]').val(),
                'content_body':     body.replace('<p>&nbsp;</p>',''),
                'page_status':      selectedStatus,
                'page_custom':      document.getElementById("custom").checked,
            },
            dataType:'text', 
            success: function(response){

                console.log(response);
                
                if(response.includes('200')){
                    $('#alert-error').addClass("d-none");
                    $('#alert-green').removeClass("d-none");
                    $('#succ_msg').html(response.split('200')[1]);

                } else {
                    $('#alert-green').addClass("d-none");
                    $('#err_msg').html(response);
                    $('#alert-error').removeClass("d-none");

                    if(response.toLowerCase().includes()=='title') {
                        $('input[name="content_title"]').addClass("wrong");
                    }else{
                        $('input[name="content_title"]').removeClass("wrong");
                    }

                    if(response.toLowerCase().includes()=='lang'){
                        $('[name="lang_id"]').addClass("wrong");
                    }else{
                        $('[name="lang_id"]').removeClass("wrong");
                    }

                    if(response.toLowerCase().includes()=='url'){
                        $('input[name="content_catalog"]').addClass("wrong");
                    }else{
                        $('input[name="content_catalog"]').removeClass("wrong");
                    }

                    if(response.toLowerCase().includes()=='content'){
                        $('[name="content_body"]').addClass("wrong");
                    }else{
                        $('[name="content_body"]').removeClass("wrong");
                    }

                    if(response.toLowerCase().includes()=='status'){
                        $('[name="page_status"]').addClass("wrong");
                    }else{
                        $('[name="page_status"]').removeClass("wrong");
                    }

                    if(response.toLowerCase().includes()=='custom'){
                        $('[name="page_custom"]').addClass("wrong");
                    }else{
                        $('[name="page_custom"]').removeClass("wrong");
                    }
                }

            }
        });
    }


</script>