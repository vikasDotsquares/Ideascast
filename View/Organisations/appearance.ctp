<style>
/* input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
   opacity: 1;
} */

input[type=number]::-webkit-inner-spin-button { 
    -webkit-appearance: block;
    cursor:pointer;
    display:block;
    width:8px;
    color: #333;
    text-align:center;
    position:relative;
	opacity: 1;
	margin-right:0;
}

input[type=number]::-webkit-inner-spin-button:before,
input[type=number]::-webkit-inner-spin-button:after {
    content: "^";
    position:absolute;
    right: 0;
	opacity: 1;
    font-family:monospace;
    line-height:
}

input[type=number]::-webkit-inner-spin-button:before {
    top:0px;
}

input[type=number]::-webkit-inner-spin-button:after {
    bottom:0px;
    -webkit-transform: rotate(180deg);
}

.password-policy .modal-header {
    background: #eee;
}
.password-policy .rows p{
    padding-bottom:10px;
}

.password-policy .pp h3{
    margin:10px 0;
}
.d-flex{display:-ms-flexbox; display:flex;}	 
.row section.content-header h1 p.text-muted span{ text-transform:none;}
    
.sp-button-container .sp-cancel {
	font-size: 12px;
	text-transform: capitalize;
}
 .sp-button-container .sp-cancel:hover {
     text-decoration: none;
    }
    .sp-container button.sp-choose{
        font-size: 12px;
    } 
	
	
img#logo-pic {
    object-fit: contain;
    width: 320px;
    height: 75px;
}
	
</style>

<?php 
echo $this->Html->css('projects/spectrum');
echo $this->Html->script('projects/spectrum', array('inline' => true));
 
?>
<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
 
		<!-- END HEADING AND MENUS -->
<!-- Password Policy` -->
<div class="row">
	<div class="col-xs-12">
	<div class="row">
	<section class="content-header clearfix">		    
				<h1 class="box-title pull-left" ><?php echo $viewData['page_heading']; 
				$userdetails =  $this->Common->getOrganisationId($this->Session->read('Auth.User.id'),$this->Session->read('Auth.User.role_id'));
				?>             
               
				<p class="text-muted date-time">
                        <span><?php echo $viewData['page_subheading']; ?></span> 
                    </p>					
		 		</h1>
		 
</section>
</div>		
<?php 	echo $this->Session->flash(); ?>
<div class="box-content">
	<div class="row">
	<div class="col-xs-12">
		<div class="box border-top margin-top">
			<div class="password-policy">				
		<?php 
			echo $this->Form->create('User', array( 'type' => 'file', 'class' => '', 'enctype' => 'multipart/form-data', 'id' => 'RecordFormedit'));  

			echo $this->Form->input('User.id', array('type' => 'hidden','label' => false, 'div' => false, 'class' => 'form-control'));  
			echo $this->Form->input('UserDetail.id', array('type' => 'hidden'));  


			$ud =  $this->ViewModel->get_user_data($this->Session->read('Auth.User.id'));
			$menuprofile =  $ud['UserDetail']['menu_pic'];
			$menuprofile_color =  $ud['UserDetail']['theme_color'];
			$menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;

			if(!empty($menuprofile) && file_exists(USER_PIC_PATH.$menuprofile)){
				$menuprofiles = SITEURL.USER_PIC_PATH.$menuprofile;
				}else{
				$menuprofiles = SITEURL.'images/appearance-logo.jpg';
				$menuprofiles = SITEURL.'images/SignInOpusViewLogo.png';
			}
		?>
			<div class="form-group">
				<div class="row">
					<div class="col-md-3 col-lg-2">
						<label class="signinlogo-heading">Sign In Logo:</label>
					</div>
					<div class="col-md-7 col-lg-7">
						 <?php echo $this->Form->input('UserDetail.menu_pic', array('type' => 'file','class' => 'form-control','style'=>'padding:0; height:24px;', 'accept'=>".png,.jpg,.jpeg,.bmp",'label'=>false)); ?>
						<p style="margin-top: 3px; font-size: 12px;">Recommended image dimensions are 320px by 75px. Maximum size allowed is 640px by 150px.</p>
						<div class="d-flex">
							<div>
							<img id="logo-pic" src="<?php echo $menuprofiles; ?>" alt="">
							</div>	
							<!--<a href="#" class="text-danger" style="margin-left: 5px;"><i class="fa fa-trash"></i></a>-->
							 <?php if(isset($ud['UserDetail']['menu_pic']) && !empty($ud['UserDetail']['menu_pic'])){?>
							 <i type="button" style="margin: 0px 0 0 5px;" value="Remove" id="<?php echo $menuprofile;?>" itemid="UserDetailMenuPic" class="img-cross-menu fa fa-trash text-red pull-right tipText" title="Delete Logo"></i>
							 <?php } ?>
						</div>	
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-md-3 col-lg-2">
						<label class="appear-heading">Sign In Color:</label>
					</div>
					<div class="col-md-9 col-lg-10">
						 <div class="col-sm-2 nopadding-left">
						<?php echo $this->Form->input('UserDetail.theme_color', array('type' => 'text','class' => 'form-control col-sm-3' ,'id'=>'showPaletteOnly-val','label'=>false)); ?>
						</div>
						<input type='text'   class="form-control col-sm-2"  name='showPaletteOnly' id='showPaletteOnly' value="<?php echo $menuprofile_color; ?>" placeholder="#5f9323" />
						 
					</div>
				</div>
			</div>
			<hr>
			<div class="form-group">
				<div class="row">
					<div class="col-md-3 col-lg-2">
						<label class="appear-heading">OpusView Name:</label>
					</div>
					<div class="col-md-7 col-lg-7">
						 
						<?php echo $this->Form->input('UserDetail.theme_name', array('type' => 'text','class' => 'form-control','maxlength' =>30  ,'label'=>false)); ?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-md-3 col-lg-2">
						<label class="appear-heading">OpusCast Name:</label>
					</div>
					<div class="col-md-7 col-lg-7">
						 
						<?php echo $this->Form->input('UserDetail.tt_name', array('type' => 'text','class' => 'form-control','maxlength' =>30  ,'label'=>false)); ?>
					</div>
				</div>
			</div>
			<hr>
			<div class="form-group text-right">
				<button type="submit"  class="btn btn-success">Save</button>
			</div>
		</form>
	</div>
		</div>
		</div>
	
	</div>
</div>

</div>
</div>
 

<script>
$(function() {
	

	
$("#showPaletteOnlys").spectrum({
    showPalette: true,
    palette: [
        ['black', 'white', 'blanchedalmond'],
        ['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']
    ]
});
        

$("#showPalette").spectrum({
    showPalette: true,
    palette: [
        ['black', 'white', 'blanchedalmond'],
        ['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']
    ],
	    hide: function(c) {
        var label = $("[data-label-for=" + this.id + "]");
        label.text("Hidden: " + c.toHexString());
    },
    change: function(c) {
        var label = $("[data-label-for=" + this.id + "]");
        label.text("Change called: " + c.toHexString());
    },
    move: function(c) {
        var label = $("[data-label-for=" + this.id + "]");
        label.text("Move called: " + c.toHexString());
    }
});


$("#showPaletteOnly").spectrum({
    //showPaletteOnly: true,
    //togglePaletteOnly: true,
    //togglePaletteMoreText: 'more',
   // togglePaletteLessText: 'less',
    //color: 'blanchedalmond',
	showPalette: false,
    palette: [
        ["#5f9322","#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
        ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
        ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
        ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
        ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
        ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
        ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
        ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
    ],
	hide: function(c) {
	var label = $("[id=" + this.id + "-val]");
	label.val(c.toHexString());
	},
	change: function(c) {
		var label = $("[id=" + this.id + "-val]");
		label.val(c.toHexString());
	},
	move: function(c) {
		var label = $("[id=" + this.id + "-val]");
		label.val(c.toHexString());
	}
});




$(".img-cross-menu").click(function(e){
    var url_logo = SITEURL+'images/SignInOpusViewLogo.png';
	 
    var id = $(this).attr("itemid");
    $("#"+id).val();
    var $tis =  $(this);

  	$.ajax({
            url : SITEURL+"organisations/remove_profile_pic",
            type: "POST",
            data : {id : id,value : $tis.attr("id")},
            beforeSend:function(){
               $tis.attr("value","Loading...");
			   $tis.hide();
            },
            success:function(response){
                    if($.trim(response) != 'success'){
						$tis.show();
						    
                            //$('#popup_modal').html(response);
                    }else{
                        //location.reload();
						 $('#logo-pic').attr("src",url_logo);
						 $('#UserDetailMenuPic').attr("value",'');
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







})
</script>