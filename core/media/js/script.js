$(document).ready(function() {
    
    tinymce.init({
        selector: "#mytextarea",
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | image | formatselect | code | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist | ' +
        'removeformat',

        images_upload_url: '/core/ajax/upload.ajax.php',
        images_upload_base_path: '/files/',

        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '/core/ajax/upload.ajax.php');
            xhr.onload = function() {
              var json;
        
              if (xhr.status != 200) {
                failure('HTTP Error: ' + xhr.status);
                return;
              }
	console.log(xhr.responseText);
              json = JSON.parse(xhr.responseText);
        console.log(typeof json);
              if (!json || typeof json.location != 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
              }
              success(json.location);
            };
            formData = new FormData();
            if( typeof(blobInfo.blob().name) !== undefined )
                fileName = blobInfo.blob().name;
            else
                fileName = blobInfo.filename();

            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);

        }
    });
    

 
    var cbody=tinymce.get('mytextarea').getContent();


    if(cbody=='403' || cbody=='404'){
        var msg;

        if(cbody=='404'){
            msg="File not found";
        }else if(cbody=='403'){
            msg="Can't edit a controller through the CMS!";
        }

        console.log('error');
        $('#body_err_msg').html(msg);
        $('#body_err').removeClass('d-none');

        $('#div-textarea').addClass('d-none');

        textarea_id='';

    } else {
        console.log('no error');
        $('#body_err').addClass('d-none');
        
        $('#div-textarea').removeClass('d-none');

        textarea_id='#mytextarea';
    }

    var table = $('table');

    $('#page_id')
    .wrapInner('<span title="sort this column"/>')
    .each(function(){
        
        var th = $(this),
        thIndex = th.index(),
        inverse = false;
        
            th.click(function(){
                
                table.find('td').filter(function(){
                    
                    return $(this).index() === thIndex;
                    
                }).sortElements(function(a, b){
                    
                    return $.text([a]) > $.text([b]) ?
                    inverse ? -1 : 1
                    : inverse ? 1 : -1;
                    
                }, function(){
                    
                    // parentNode is the element we want to move
                    return this.parentNode; 
                    
                });
                
                inverse = !inverse;
                
            });
            
        });




    
});


function encrypt_pass(){
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

function err(err){
    alert(err);
    $('#alert-error').removeClass('d-none');
    $('#err_msg').html(err+ "<input type='hidden' name='body' value='404'>");
}

function bodyText(){
    console.log('asdsd');
    if(document.getElementById("custom").checked){
        $('#body_msg').removeClass('d-none')
        $('#body_text').addClass('d-none')
    } else {
        $('#body_text').removeClass('d-none')
        $('#body_msg').addClass('d-none')
    }
}
