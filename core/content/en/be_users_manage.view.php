
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
        After deleting this user you <b>won't be able to restore the data!</b>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" onclick="user_delete()">Delete</button>
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
  <strong>Hooray!</strong> User created succesfully.
  <div class="float-right mr-2">
    <a href="/<?=$this->conf_array['backend']?>/users/"><strong>Go back</strong></a>
  </div>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>


<h2><?= ucfirst($action)?> user</h2>
<form class="ml-3" method="POST" action="<?= $_SERVER['REQUEST_URI']?>">

    <div class="d-block mb-3">
        <p class="m-0 text-bold">Email: </p>
        <input type="email" name="mod_email" placeholder="email address" required value="<?=(isset($mod_email)?$mod_email:'')?>">
    </div>
    <div class="d-block mb-3">
        <p class="m-0 text-bold">Password:</p>
        <div class="d-inline">
            <input class="mr-2" id="pass" <?= ($action=="add"?"type='password'":"type='text'") ?> name="mod_password" placeholder="password" requiered value="<?=(isset($mod_password)?$mod_password:'')?>" >
            <?= ($action=="edit"? '<input class="btn mt-1 btn-success" id="encrypt_pass" type="button" onclick="epass()" value="Encrypt pass">':'') ?>
        </div>
    </div>
    
    <div class="d-block mb-3">
        <p class="m-0 text-bold">Name: </p>
        <input type="text" name="mod_name" id="mod" placeholder="name" required value="<?=(isset($mod_name)?$mod_name:'')?>">
        <!-- Problem: the catalog may be in another language than eng. ok. then content_catalog. but, what about page_catalog? same as content_catalog? use the title? -->
    </div>
    <div class="d-block mb-3">
        <p class="m-0 text-bold"> </p>
        
    </div>
    <div class="d-block mb-3">
        <p class="m-0 text-bold"><label for="status">Status: </label></p>
        <select name="mod_status" id="status">
            <?php
                $sdata=array();
                $sdata['data']=$this->conf_array['statuses'];
                $sdata['type']='key2value';
                $sdata['excp']=2;
                $sdata['id']=$this->lang_id;
                
                if(isset($mod_status)){
                    echo "<option value='$mod_status'>".$this->conf_array['statuses'][$mod_status]."</option>";
                    $sdata['excp1']=$mod_status;
                }

                echo Html::selectList($sdata);
            ?>
        </select>
    </div>
    <div class="d-block mb-3">
        <p class="m-0 text-bold">Role: </p>
        <select name="mod_role" id="mod_role">
            <?php
                $sdata=array();
                $sdata['data']=$this->conf_array['roles'];
                $sdata['type']='key2value';
                $sdata['id']=$this->lang_id;
                
                if(isset($mod_role)){
                    echo "<option value='$mod_role'>".$this->conf_array['roles'][$mod_role]."</option>";
                    $sdata['excp']=$mod_role;
                }

                echo Html::selectList($sdata);
            ?>
        </select>
    </div>
    <div class="d-block mb-3">
        <p class="m-0 text-bold"> </p>
        
    </div>
    <div class="d-inline mb-3">       
        <input type="button" value="Upload" onclick="user_upload()" class="btn mr-1 btn-primary"/>
        <input type="reset" value="Reset values" class="btn m-1 btn-warning"/>
        
        <?php
            if($action=="edit"){
        ?>
            <input type="button" class="btn m-1 btn-danger" value="Delete" data-toggle="modal" data-target="#deleteModal">
        <?php
            }
        ?>
    </div>


    <input type="hidden" value="<?=Auth::formToken()?>" name="token">
    <input type="hidden" value="<?= $this->action ?>" id="action" name="hidden" requiered="false">
</form>
<script>

    function epass(){
        var pass=$("#pass").val();
        $.ajax({
            type: "POST",
            url: "/core/ajax/pass_encrypt.ajax.php",
            data: {
                'ajax_pass': pass,
            },
            dataType:'text', 
            success: function(response){
                if(response.trim()!=='error') {
                    $("#pass").val(response);
                }

            }
        });
    }

    function user_delete(){
        $.ajax({
            type: "POST",
            url: "/core/ajax/user_delete.ajax.php",
            data:{
                'token':            $('input[name="token"]').val(),
                'id':               '<?=(isset($mod_id)?$mod_id:'')?>',
            },
            dataType:'text', 
            success: function(response){
                if(response.includes('200')){
                    $('#alert-green').removeClass("d-none");
                    $('#succ_msg').html(response);

                    window.location.replace('/<?= $this->conf_array['backend']?>/users/');

                } else {
                    $('#err_msg').html(response);
                    $('#alert-error').removeClass("d-none");
                }

            }
        });
    }
    
    
    
    function user_upload(){

        var selectedStatus=         $("select[name='mod_status']").children("option:selected").val();
        var selectedRole=           $("select[name='mod_role']").children("option:selected").val();

        $.ajax({
            type: "POST",
            url: "/core/ajax/user_upload.ajax.php",
            data:{
                'hidden':           $('input[name="hidden"]').val(),
                'token':            $('input[name="token"]').val(),
                'id':               '<?=isset($mod_id)?$mod_id:''?>',
                'mod_email':        $('input[name="mod_email"]').val(),
                'mod_password':     $('input[name="mod_password"]').val(),
                'mod_name':         $('input[name="mod_name"]').val(),
                'mod_status':       selectedStatus,
                'mod_role':         selectedRole,
            },
            dataType:'text', 
            success: function(response){
                if(response.includes('200')){
                    $('#alert-error').addClass("d-none");
                    $('#alert-green').removeClass("d-none");
                    $('#succ_msg').html(response.split('200')[1]);

                } else {
                    $('#alert-green').addClass("d-none");
                    $('#err_msg').html(response);
                    $('#alert-error').removeClass("d-none");

                    
                    if(response.toLowerCase().includes()=='email'){
                        $('[name="mod_email"]').addClass("wrong");
                    }else{
                        $('[name="mod_email"]').removeClass("wrong");
                    }

                    if(response.toLowerCase().includes()=='name'){
                        $('[name="mod_name"]').addClass("wrong");
                    }else{
                        $('[name="mod_name"]').removeClass("wrong");
                    }

                    if(response.toLowerCase().includes()=='password'){
                        $('input[name="mod_password"]').addClass("wrong");
                    }else{
                        $('input[name="mod_password"]').removeClass("wrong");
                    }

                    if(response.toLowerCase().includes()=='role'){
                        $('[name="mod_role"]').addClass("wrong");
                    }else{
                        $('[name="mod_role"]').removeClass("wrong");
                    }

                    if(response.toLowerCase().includes()=='status'){
                        $('[name="mod_status"]').addClass("wrong");
                    }else{
                        $('[name="mod_status"]').removeClass("wrong");
                    }
                }

            }
        });
    }
</script>
