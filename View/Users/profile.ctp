<!-- EDIT Industry Classification -->
<?php
echo $this->Html->script('canvas', array('inline' => true));
echo $this->Html->script('canvas2image', array('inline' => true));
?>
<div class="modal-dialog ">
		<div class="modal-content ">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" style="font-size:24px;"> Image</h4>
			</div>
			<?php echo $this->Form->create('User', array( 'type' => 'file', 'class' => 'form-horizontal form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'RecordFormedit')); ?>
				<div class="modal-body">
					<?php echo $this->Form->input('User.id', array('type' => 'hidden','label' => false, 'div' => false, 'class' => 'form-control')); ?>
					<?php echo $this->Form->input('UserDetail.id', array('type' => 'hidden')); ?>

					<?php

					    $ud =  $this->ViewModel->get_user_data($uid);
					    // $ud =  $this->ViewModel->get_user_data($this->Session->read('Auth.User.id'));
						$menuprofile =  $ud['UserDetail']['menu_pic'];
                        $menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;

						if(!empty($menuprofile) && file_exists(USER_PIC_PATH.$menuprofile)){
							$menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;
							}else{
							$menuprofiles = SITEURL.'img/image_placeholders/logo_placeholder.gif';
						}
						$profile =  $ud['UserDetail']['profile_pic'];
						$profiles = SITEURL.USER_PIC_PATH.$profile;

						if(!empty($profile) && file_exists(USER_PIC_PATH.$profile)){
							$profiles = SITEURL.USER_PIC_PATH.$profile;
							}else{
							$profiles = SITEURL.'img/image_placeholders/logo_placeholder.gif';
						}
						$docimg =  $ud['UserDetail']['document_pic'];
						$docimgs = SITEURL.USER_PIC_PATH.$docimg;

						if(!empty($docimg) && file_exists(USER_PIC_PATH.$docimg)){
							$docimgs = SITEURL.USER_PIC_PATH.$docimg;
							}else{
							$docimgs = SITEURL.'img/image_placeholders/report_logo_placeholder.gif';
						}
					?>
					<div class="form-group imgHgt" >
					  <label for="UserClassification" class="col-lg-3 control-label" style="padding-top:1px;text-align:left;">Profile Picture:</label>
					  <div class="col-lg-9 profile-avatar  allpopuptabs">
					  	<ul class="nav nav-tabs" id="profile_pic_tabs">
						  <li class="active"><a data-toggle="tab" href="#img_current" id="pic_current">Current</a></li>
						  <li><a data-toggle="tab" href="#img_avatar" id="pic_avatar">Avatar</a></li>
						  <li><a data-toggle="tab" href="#img_uploads" id="pic_upload">Upload</a></li>
						</ul>
						<div class="tab-content">
						  <div id="img_current" class="tab-pane fade in active">
								<div class="profile-picture">
						 			<img src="<?php echo $profiles ?>" class="img-circledd main-profile-picture" alt="Personal Image" />
                                    <?php if(isset($this->request->data['UserDetail']['profile_pic']) && !empty($this->request->data['UserDetail']['profile_pic'])){?>

                                     <i type="button" value="Remove" id="<?php echo $this->Session->read('Auth.User.UserDetail.profile_pic');?>" itemid="UserDetailProfilePic" class="img-cross deleteblack pull-right tipText" title="Remove Profile Picture"></i>
                                    <?php } ?>
								</div>
						  </div>
						  <div id="img_avatar" class="tab-pane fade">
							 <div class="tab-block">
							  <div class="tab-left">
								<div class="profile-picture">
									<div   class="Avatar" rel="Background" style="position:relative;  width: 150px; height: 150px;  overflow: hidden;   display: block;border-radius:50%;">
									<img class="Hair" rel="Hair"   src="<?php echo SITEURL;?>images/Avatars/Hair/1.png" style="position:absolute;z-index:+6;">
									<img class="Additions" rel="Additions"  src="<?php echo SITEURL;?>images/Avatars/Additions/1.png"/ style="position:absolute;z-index:+5;">
									<img class="Face" rel="Face"   src="<?php echo SITEURL;?>images/Avatars/Face/1.png" style="position:absolute;z-index:+5;">
									<img class="Head" rel="Head"   src="<?php echo SITEURL;?>images/Avatars/Head/1.png" style="position:absolute;z-index:+4;">
									<img class="Body" rel="Body"   src="<?php echo SITEURL;?>images/Avatars/Body/1.png" style="position:absolute;z-index:+3;">
									<img class="Background" rel="Background"  src="<?php echo SITEURL;?>images/Avatars/Background/1.png"/ style="position:absolute;z-index:+1;">
									</div>
									<input name="data[UserDetail][urlImage]" id="urlImage" type="hidden" />
									<a class="tipText pull-right" id="avatar_reset" title="Reset"   >
										<img src ="<?php echo SITEURL;?>images/Avatars/avatar_reset.png" alt="avatar_reset" />
									</a>
								</div>
							  </div>
							  <div class="tab-right">
							  	  <div  class="avatar-tab">
									<div class="avatar-tab-left">
										<ul class="menu-tabs-vertical">
											<li class="active">
												<a href="#avatar_hair" class="tipText" title="Hair" data-toggle="tab">
													<img  src ="<?php echo SITEURL;?>images/Avatars/avatar_hair.png" alt="avatar_hair" />
												</a>
											</li>
											<li >
												<a href="#avatar_additions"  class="tipText" title="Additions" data-toggle="tab">
													<img src ="<?php echo SITEURL;?>images/Avatars/avatar_additions.png" alt="avatar_additions" />
												</a>
											</li>
											<li>
												<a href="#avatar_face" class="tipText" title="Face"  data-toggle="tab">
													<img src ="<?php echo SITEURL;?>images/Avatars/avatar_face.png" alt="avatar_face" />
												</a>
											</li>
											<li >
												<a href="#avatar_head" class="tipText" title="Head"  data-toggle="tab">
													<img src ="<?php echo SITEURL;?>images/Avatars/avatar_head.png" alt="avatar_head" />
												</a>
											</li>
											<li>
												<a href="#avatar_body" class="tipText" title="Body"  data-toggle="tab">
													<img src ="<?php echo SITEURL;?>images/Avatars/avatar_body.png" alt="avatar_body" />
												</a>
											</li>
											<li >
												<a href="#avatar_background" class="tipText" title="Background"  data-toggle="tab">
													<img src ="<?php echo SITEURL;?>images/Avatars/avatar_background.png" alt="avatar_background" />
												</a>
											</li>
											<li>
												<a  id="change" class="tipText" title="Random">
													<img src ="<?php echo SITEURL;?>images/Avatars/avatar_dice.png" alt="avatar_dice" />
												</a>
											</li>

										</ul>
									</div>
									<div class="avatar-tab-right">
										<div class="tab-content">
											<div class="tab-pane active" id="avatar_hair">
												<ul class="avatar-content-list">
													<?php for($i=1;$i<=79;$i++){ ?>
														<li><img rel="Hair" src ="<?php echo SITEURL."images/Avatars/Hair/".$i.".png"; ?>" alt="" /></li>
													<?php } ?>
												</ul>
											</div>
											<div class="tab-pane " id="avatar_additions">
												<ul class="avatar-content-list">
													<?php for($i=1;$i<=51;$i++){ ?>
														<li><img rel="Additions" src ="<?php echo SITEURL."images/Avatars/Additions/".$i.".png"; ?>" alt="" /></li>
													<?php } ?>
												</ul>
											</div>
											<div class="tab-pane " id="avatar_face">
												<ul class="avatar-content-list">
													<?php for($i=1;$i<=67;$i++){ ?>
														<li><img rel="Face" src ="<?php echo SITEURL."images/Avatars/Face/".$i.".png"; ?>" alt="" /></li>
													<?php } ?>
												</ul>
											</div>
											<div class="tab-pane " id="avatar_head">
												<ul class="avatar-content-list">
													<?php for($i=1;$i<=28;$i++){ ?>
														<li><img class="blank" rel="Head" src ="<?php echo SITEURL."images/Avatars/Head/".$i.".png"; ?>" alt="" /></li>
													<?php } ?>
												</ul>
											</div>
											<div class="tab-pane " id="avatar_body">
												<ul class="avatar-content-list">
													<?php for($i=1;$i<=42;$i++){ ?>
														<li><img rel="Body" src ="<?php echo SITEURL."images/Avatars/Body/".$i.".png"; ?>" alt="" /></li>
													<?php } ?>
												</ul>
											</div>
											<div class="tab-pane" id="avatar_background">
												<ul class="avatar-content-list">
													<?php for($i=1;$i<=36;$i++){ ?>
														<li><img rel="Background" src ="<?php echo SITEURL."images/Avatars/Background/".$i.".png"; ?>" alt="" /></li>
													<?php } ?>
												</ul>
											</div>
										</div>
									</div>
								</div>
							  </div>
							</div>

						  </div>
						  <div id="img_uploads" class="tab-pane fade">
						  <div class="form-group">
						  <div class="col-lg-12">
						    <?php echo $this->Form->input('UserDetail.profile_pic', array('type' => 'file','class' => 'form-control','style'=>'padding:0', 'accept'=>".png,.jpg,.jpeg,.bmp",'label'=>false)); ?>
						    <span class="error"> </span>
							 </div>
							 </div>
							<div class="form-group">

							  <div class="col-lg-12">
								<p class="text-center">Recommended image dimensions are 150 x 150 pixels.</p>
							  </div>
							 </div>
						  </div>
						</div>

					  </div>
					</div>
				</div>
				<div class="modal-footer clearfix">

					<button type="button" class="btn btn-success save-image" style="display: none;">Save</button>

					<button type="button" id="Discard" class="btn btn-danger image-close" data-dismiss="modal">Close</button>
				</div>
			</form>
	</div>
</div>
<script type="text/javascript" >
var uid = '<?php echo $uid; ?>';
$(function(){

	$.activeTab = null;
    $("#profile_pic_tabs").on('shown.bs.tab', function (e) {
        $.activeTab = $(e.target);
        $.profile_pic_update = true;
        if($.activeTab.is('#pic_avatar') || $.activeTab.is('#pic_upload')){
        	$('.save-image').show();
        	$('.image-close').text('Cancel');
        }
        else{
        	$('.save-image').hide();
        	$('.image-close').text('Close');
        }
        $('#UserDetailProfilePic').parents('.form-group').find('.error').html("");
    })

	$('.save-image').off('click').on('click', function(event) {
		event.preventDefault();
		$('#UserDetailProfilePic').parents('.form-group').find('.error').html("");
		$(this).prop('disabled', true);

		var contentType = false;
		if($.activeTab.is('#pic_avatar')){
			var image = $('#urlImage').val();
			var formData = new FormData();
			formData.append('profile_image', image);
			formData.append('image_type', 'blob');
			formData.append('uid', uid);
			$.ajax({
	            url: $js_config.base_url + 'users/save_profile_image',
	            type: "POST",
	            data: formData,
	            dataType: 'json',
	            processData: false,
	            contentType: false,
	            success: function(response) {
	            	if(response.success){
	            		$('#uimage_modal').modal('hide');
	            	}
	            }
	        })
	        $.profile_pic_update = true;
		}
		else if($.activeTab.is('#pic_upload')){
			var formData = new FormData();
			var $uploadImage = $('#UserDetailProfilePic');
			if($uploadImage.val() != '' && $uploadImage.val() != undefined){
				myfile = $uploadImage[0].files[0]['name'];
		        formData.append('profile_image', $uploadImage[0].files[0], myfile);
		        formData.append('image_type', 'upload');
				formData.append('uid', uid);
		        $.ajax({
		            url: $js_config.base_url + 'users/save_profile_image',
		            type: "POST",
		            data: formData,
		            dataType: 'json',
		            processData: false,
		            contentType: false,
		            success: function(response) {
		            	if(response.success){
		            		$('#uimage_modal').modal('hide');
		            	}
		            }
		        })
		    	$.profile_pic_update = true;
		    }
		    else{
		    	$('#UserDetailProfilePic').parents('.form-group').find('.error').html('Please select an image');
		    }
		    $(this).prop('disabled', false);
		}
	});


	$('#change').click(function(){
		$('.Avatar img:first-child').css('border','none');
		html2canvas(($('.Avatar').removeAttr('css')), {
			scale: 2,
	        onrendered: function(canvas) {
				theCanvas = canvas;
	            var myImage = canvas.toDataURL('image/png');
	            //downloadURI(myImage, "User.png");
				$('#urlImage').val(myImage);
				$('.Avatar img:first-child').css('border','solid 2px #ccc');

	      }
    });
	var ranBackGround = Math.floor((Math.random() * 35) + 1);
	var ranHead = Math.floor((Math.random() * 27) + 1);
	var ranHair = Math.floor((Math.random() * 78) + 1);
	//var ranFace = Math.floor((Math.random() * 66) + 1);

	var ranAdditions = Math.floor((Math.random() * 2));
	if(ranAdditions == 0){
		ranAdditions = 1;
	}else{
		ranAdditions = Math.floor((Math.random() * 50) + 2);
	}

	var ranFace = Math.floor((Math.random() * 2));
	if(ranFace == 0){
		ranFace = 1;
	}else{
		ranFace = Math.floor((Math.random() * 66) + 2);
	}

	var ranBody = Math.floor((Math.random() * 41) + 1);

	$('.Additions').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Additions').attr('rel')+'/'+ranAdditions+'.png');
	$('.Background').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Background').attr('rel')+'/'+ranBackGround+'.png');
	$('.Head').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Head').attr('rel')+'/'+ranHead+'.png');
	$('.Hair').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Hair').attr('rel')+'/'+ranHair+'.png');
	$('.Face').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Face').attr('rel')+'/'+ranFace+'.png');
	//$('.Face').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Face').attr('rel')+'/'+'1'+'.png');
	$('.Body').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Body').attr('rel')+'/'+ranBody+'.png');

})

$('#avatar_reset').click(function(){

	$('.Additions').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Additions').attr('rel')+'/1.png');
	$('.Background').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Background').attr('rel')+'/1.png');
	$('.Head').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Head').attr('rel')+'/1.png');
	$('.Hair').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Hair').attr('rel')+'/1.png');
	$('.Face').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Face').attr('rel')+'/1.png');
	$('.Body').attr('src','<?php echo SITEURL;?>images/Avatars/'+$('.Body').attr('rel')+'/1.png');

	$('.Avatar img:first-child').css('border','none');
	html2canvas(($('.Avatar').removeAttr('css')), {
		scale: 2,
        onrendered: function(canvas) {
			theCanvas = canvas;
            var myImage = canvas.toDataURL('image/png');
            //downloadURI(myImage, "User.png");
			$('#urlImage').val(myImage);
			$('.Avatar img:first-child').css('border','solid 2px #ccc');

      }
    });

})

function downloadURI(uri, name) {
    var link = document.createElement("a");

    link.download = name;
    link.href = uri;
    document.body.appendChild(link);
}


$('a[href="#img_avatar"]').click(function(){

	setTimeout(function(){
	$(".tab-pane.active .avatar-content-list").slimscroll({height:136,alwaysVisible: true});
		$('.Avatar img:first-child').css('border','none');
		html2canvas(($('.Avatar').removeAttr('css')), {
			scale: 2,
			onrendered: function(canvas) {
				theCanvas = canvas;
				var myImage = canvas.toDataURL('image/png');
				//downloadURI(myImage, "User.png");
				$('#urlImage').val(myImage);
				$('.Avatar img:first-child').css('border','solid 2px #ccc');
		  }
		});
	},200)
})


$('a[href="#img_current"],a[href="#img_uploads"]').click(function(){
	 $('#urlImage').val('');
})

$('.tab-pane').click(function(){
	setTimeout(function(){
	$(".tab-pane.active .avatar-content-list").slimscroll({height:136,alwaysVisible: true});
	},100)
})


$('.avatar-content-list img').click(function(){

	$(this).attr('rel');
	$('.Avatar img[rel='+$(this).attr('rel')+']').attr('src',$(this).attr('src'));
	$('.Avatar img:first-child').css('border','none');
	html2canvas(($('.Avatar').removeAttr('css')), {
		scale: 2,
        onrendered: function(canvas) {
			theCanvas = canvas;
            var myImage = canvas.toDataURL('image/png');
            //downloadURI(myImage, "User.png");
			$('#urlImage').val(myImage);
			$('.Avatar img:first-child').css('border','solid 2px #ccc');
      }
    });
})


$(".img-cross").click(function(e){
	// $.reloadProfile = true;
    var url = SITEURL+'img/image_placeholders/logo_placeholder.gif';
    $(this).prev().attr("src",url);
    var id = $(this).attr("itemid");
    $("#"+id).val();
    var $tis =  $(this);

  	$.ajax({
            url : SITEURL+"users/remove_profile_pic",
            type: "POST",
            data : {id : id,value : $(this).attr("id"), uid: uid},
            beforeSend:function(){
               $tis.attr("value","Loading...");
			   $tis.hide();
            },
            success:function(response){
                if($.trim(response) != 'success'){
					$tis.show();
                        //$('#uimage_modal').html(response);
                }else{
					if($js_config.USER.role_id == "2"){
                	$.socket.emit("image:upload", $js_config.USER.id);
					}
                    $.profile_pic_update = true;
                }
            },
            complete:function(){
                $tis.attr("value","Removed");
                setTimeout(function(){
                    $tis.remove();
                },500)
            },
            error: function(jqXHR, textStatus, errorThrown){

            }
	}) ;
	e.preventDefault();

});


$(".img-cross-doc").click(function(e){
    var url = SITEURL+'img/image_placeholders/report_logo_placeholder.gif';
    $(this).prev().attr("src",url);
    var id = $(this).attr("itemid");
    $("#"+id).val();
    var $tis =  $(this);


	/* $.get( "http://jeera.ideascast.com:9090/user/16/update", function( data ) {

		console.log( "Load was performed." );
	});
	return; */
  	$.ajax({
            url : SITEURL+"users/remove_profile_pic",
            type: "POST",
            data : {id : id,value : $(this).attr("id")},
            beforeSend:function(){
               $tis.attr("value","Loading...");
			   $tis.hide();
            },
            success:function(response){
                    if($.trim(response) != 'success'){
						$tis.show();
                            //$('#uimage_modal').html(response);
                    }else{
						if($js_config.USER.role_id == "2"){
                    	$.socket.emit("image:upload", $js_config.USER.id);
						}
                        //location.reload();
                    }
            },
            complete:function(){
                $tis.attr("value","Removed");
                setTimeout(function(){
                    $tis.remove();
                },500)
            },
            error: function(jqXHR, textStatus, errorThrown){

            }
	}) ;
	e.preventDefault();

});

$("#RecordFormedit").submit(function(e){
	//var postData = $(this).serializeArray();
	var postData = new FormData($(this)[0]);
	//alert(postData);
	var formURL = $(this).attr("action");
	$.ajax({
		url : formURL,
		type: "POST",
		data : postData,
		global : false,
		success:function(response){
			if($.trim(response) != 'success'){
				$('#uimage_modal').html(response);
			}else{
				if($js_config.USER.role_id == "2"){
				$.socket.emit("image:upload", $js_config.USER.id);
				}
				location.reload();

			}
		},
		cache: false,
        contentType: false,
        processData: false,
		error: function(jqXHR, textStatus, errorThrown){

		}
	});
	e.preventDefault();

});

})

</script>

<style>
	.error {
		font-size: 11px;
	}
    .img-circles{position: none !important;}
	.form-horizontal p {
	    padding: 7px 0 0;
	    text-align: left;
	}
	#RecordFormedit label { font-size : 13px ;}

	.form-horizontal img {
	    display: block;
	    /* margin: 5px 0 0; */
	    max-width: 100%;
	}

	.avatar-content-list img{
		cursor : pointer;
	}

	.imgHgt{ min-height : 223px;}

	.Avatar img {
	    display: block;
	    margin: 0;
	    max-width: 100%;
		/* cursor : pointer; */
	    border-radius: 50%;
	}

	.Avatar img:first-child{
		border: solid 2px #ccc;
	}


	#RecordFormedit #UserDetailDocumentPic, #UserDetailProfilePic { height : 24px;}
	#avatar_reset{

	position: relative;
	    left: 30px;
	    top: -16px;
	}

	.docImg{ margin-top: 15px;}
</style>
