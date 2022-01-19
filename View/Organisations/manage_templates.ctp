
<?php
 //echo $this->Html->script(array('/plugins/iCheck/icheck.min', array('inline' => true)));
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multi', array('inline' => true));
echo $this->Html->css('projects/manage_templates');


 ?>

<script type="text/javascript">
	$(function(){

		//$('#templateCategory').select();

		$.template_list = $('#userTemplateid').multiselect({
	        buttonClass: 'btn btn-default aqua',
	        buttonWidth: '100%',
	        buttonContainerWidth: '85%',
	        numberDisplayed: 2,
	        maxHeight: '318',
	        checkboxName: 'template_list',
	        includeSelectAllOption: true,
	        // enableHTML: true,
	        enableFiltering: true,
	        filterPlaceholder: 'Search Templates',
	        enableCaseInsensitiveFiltering: true,
	        templates: {
                button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><span class="multiselect-selected-text"></span> <span class="arrow fa fa-arrow-down"></span></button>',
                ul: '<ul class="multiselect-container dropdown-menu"></ul>',
                filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>',
                filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
                li: '<li><a tabindex="0"><label></label></a></li>',
                divider: '<li class="multiselect-item divider"></li>',
                liGroup: '<li class="multiselect-item multiselect-group"><label></label></li>'
            },
	        onChange: function(option, checked) {
	            // console.log($(this))
	        },
	        onDropdownHidden: function(option, closed, select) {

	            var templates = $('#template_list').val();
	            // console.log(templates);
	            if(templates != null ) {
		            var nonAdmin = false;
		            $.each(templates, function(i, v){
		            	var data = $('#template_list option[value='+v+']').data('admin');
		            	if(!data){
		            		$('#option_move').prop('checked', false).removeAttr('checked').addClass('disabled').attr('disabled','disabled');
		            		$('#option_copy').prop('checked', true);

		            		$('[for=option_move]').attr('data-original-title','Only Author Can Move');
		            		nonAdmin = true;
		            		// $('.non-admin').show();
		            	}
		            })
		            if(!nonAdmin) {
		            	$('#option_move').removeClass('disabled').removeAttr('disabled');
	            		// $('.non-admin').hide();
	            		$('[for=option_move]').removeAttr('data-original-title');
		            }
		        }
		        else {
		        	$('#option_move').removeClass('disabled').removeAttr('disabled');
		        }

	        }
	    });
	})
</script>
<!-- END MODAL BOX -->
<div class="row copytemplatetodomain" >
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
                <h1 class="pull-left"><?php echo $viewData['page_heading']; ?>
                    <p class="text-muted date-time" style="padding: 4px 0px;">
                        <span style="text-transform: none;"><?php echo $viewData['page_subheading']; ?></span>
                    </p>
                </h1>
            </section>
		</div>
		<!-- END HEADING AND MENUS -->

	<!-- Content Header (Page header) -->
	 <div class="box-content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box noborder-top">

				<?php echo $this->Session->flash(); ?>

		<section class="box-body no-padding">
			<?php $class = 'collapse';
					if(isset($in) && !empty($in)){
						$class = 'in';
					}
			?>
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box no-box-shadow box-success">
					<div class="box-header border-bottom">
						<div class="col-sm-12 col-md-12">
							<div class="col-sm-6 col-md-6">
								<p style="margin:0; padding:7px 0;">Domain:&nbsp;<?php
									$whatINeed = explode(WEBDOMAIN, $_SERVER['HTTP_HOST']);
									$whatINeed = $whatINeed[0];
									echo "<a class='tipText' title='https://".$whatINeed.WEBDOMAIN."' target='_blank' href='https://".$whatINeed.WEBDOMAIN."' style='text-transform:none !important;'>".$whatINeed.WEBDOMAIN."</a>";
								?>
							</div>
							<div class="col-sm-6 col-md-6"><button id="frmReset" class="btn btn-danger pull-right">Reset</button></div>
						</div>

					</div><!-- /.box-header -->
					<div class="box-body table-responsive">
					<?php echo $this->Form->create('managetemplate', array( 'type' => 'file', 'class' => 'form-bordered', 'enctype' => 'multipart/form-data', 'id' => 'orgManageTemplates')); ?>
						<div class="col-sm-12 col-md-12 filter-email">
						<div class="row">&nbsp;</div>
						<div class="row">
								<div class="col-sm-4 col-md-4">

										<label class="pull-right lable-tital">Select Folder: &nbsp;</label>

								</div>
								<div class="col-sm-8 col-md-8 ">
									<div class="form-group">
										<div class="filter-email-inside">
										<?php
										echo $this->Form->input('Template.category_id ', array('options' => $templateCategories,  'empty' => 'Select Folder', 'name'=>'templatefolder', 'label' => false, 'div' => false, 'class' => 'form-control','id'=>'templateCategory','required'));

										?>

										<label id="folder_template_msg" class="text-red normal errormsg" style="font-weight:normal; font-size: 11px;"></label>
										</div>
									</div>
								</div>
						</div>
						<div class="row" id="intrnltemplate" style="display:none;" >
								<div class="col-sm-4 col-md-4">

										<label class="pull-right lable-tital">Select Templates: &nbsp;</label>

								</div>
								<div class="col-sm-8 col-md-8 ">
									<div class="form-group">
										<div class="filter-email-inside" id="filter-domain-template">
										<?php
										$newArray = array_map(function($v){
											return trim(strip_tags($v));
										}, $internaltemplate);

										echo $this->Form->input('User.managedomain_id ', array('options' => '','label' => false, 'div' => false, 'class' => 'form-control','id'=>'userTemplateid','multiple'=>'multiple','required','style'=>'display:none')); ?>

										<label id="template_msg" class="text-red normal errormsg" style="font-weight:normal; font-size: 11px;"></label>
										</div>
									</div>
								</div>
						</div>
						<div class="row" id="copytodomains" style="display:none;" >
							<div class="col-sm-4 col-md-4">

									<label class="pull-right lable-tital">Associated Domain: &nbsp;</label>

							</div>
							<div class="col-sm-8 col-md-8 ">
								<div class="form-group">
									<div class="filter-email-inside">
									<?php
									echo $this->Form->input('User.managedomain_id ', array('options' => $alldomains,  'empty' => 'Select Associated Domain', 'label' => false, 'div' => false, 'onchange'=>'domainUsers(this.value)','class' => 'form-control org_domains','id'=>'userDomain','required')); ?>
									<label id="domain_msg" class="text-red normal errormsg" style="font-weight:normal; font-size: 11px;"></label>
									</div>
								</div>
							</div>
						</div>
						<div class="row" id="destinationfolder" style="display:none;">
							<div class="col-sm-4 col-md-4">

									<label class="pull-right lable-tital">Select Destination Folder: &nbsp;</label>

							</div>
							<div class="col-sm-8 col-md-8 ">
								<div class="form-group">
									<div class="filter-email-inside">
									<?php
									echo $this->Form->input('Template.category_id ', array('options' => $templateCategories,  'empty' => 'Select Destination Folder', 'label' => false, 'div' => false, 'class' => 'form-control','id'=>'templateCategoryDist','required')); ?>
									<label id="category_msg" class="text-red normal errormsg" style="font-weight:normal; font-size: 11px;"></label>
									</div>
								</div>
							</div>
						</div>
						<div class="row" id="domianusers" style="display:none;">
							<div class="col-sm-4 col-md-4">

									<label class="pull-right lable-tital">Domain Users: &nbsp;</label>

							</div>
							<div class="col-sm-8 col-md-8 ">
								<div class="form-group">
									<div class="filter-email-inside" id="filter-domain-users">
									<?php
										echo $this->Form->input('User.id', array('empty' => 'Select User to Copy Templates', 'label' => false, 'div' => false, 'class' => 'form-control','type'=>'select','required')); ?>
										<label id="domainuser_msg" class="text-red normal errormsg" style="font-weight:normal; font-size: 11px;"></label>
									</div>
								</div>
							</div>
						</div>
						<div class="row" id="fromTemplate" style="display:none;">
							<div class="col-sm-4 col-md-4">

									<label class="pull-right lable-tital">From Templates: &nbsp;</label>

							</div>
							<div class="col-sm-8 col-md-8 ">
								<div class="form-group">
									<div class="filter-email-inside filter-checkbox" id="filter-domain-users">
										<ul class="managetemplate">
											<li>Remove Reviews :&nbsp;&nbsp;<input class="extracont" name="tmpreviews" value="reviews" type="Checkbox"></li>
											<li>Remove Likes :&nbsp;&nbsp;<input class="extracont"  name="tmplikes" value="likes" type="Checkbox"></li>
											<li>Remove Documents :&nbsp;&nbsp;<input class="extracont" name="tmpdocuments" value="documents" type="Checkbox"></li>
										</ul>
										<label id="copywith" class="text-red normal errormsg" style="font-weight:normal; font-size: 11px;"></label>
									</div>
								</div>
							</div>
						</div>

						<div class="row" id="fromTemplateImage" style="display:none;">
							<div class="col-sm-4 col-md-4">

									<label class="pull-right lable-tital">Templates Image: &nbsp;</label>

							</div>
							<div class="col-sm-8 col-md-8 ">
								<div class="form-group">
									<div class="filter-email-inside filter-tmpimg" id="filter-domain-users">
										<input type="file" name="temp_image[]" id="template_image">
										<label id="template_img" class="text-red normal errormsg" style="font-weight:normal; font-size: 11px;"></label>
									</div>
								</div>
							</div>
						</div>




						<div class="row" >
							<div class="col-sm-4 col-md-4">

									<label class="pull-right lable-tital">&nbsp;</span>

							</div>
							<div class="col-sm-8 col-md-8 ">
								<div class="form-group">
									<div class="filter-email-inside" id="filter-domain-users">
										<button name="moveto" type="button" style="display:none;" id="movetodomain" data-title="Copy to Template Folder" class="btn btn-success tipText pull-right">Copy</button>
									</div>
								</div>
							</div>
						</div>
						</div>
						</form>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
				<!------ Add New Classification ------>
				<div class="modal fade" id="deleteBox" tabindex="-1" role="dialog" aria-hidden="true"></div><!-- /.modal -->
			</div></div>
		</section>
					</div>
				</div>
			</div>
		 </div>
	   </div>
	</div>
 </div>
 <script>

function domainUsers(domainname){

	if( domainname  ){
		$that = domainname;

		var userUrl = '<?php echo SITEURL; ?>organisations/getDomainUsers';
		$("#domain_msg").hide().text('');

		/* $('select#userTemplateid option').removeAttr("selected");
		$('select#userTemplateid option:first-child').attr('selected','selected'); */

		$('select#templateCategoryDist option').removeAttr("selected");
		$('select#templateCategoryDist option:first-child').attr('selected','selected');

		$('select#domain_userid option').removeAttr("selected");
		$('select#domain_userid option:first-child').attr('selected','selected');

		$(".extracont checked").removeAttr("checked");
		$('.extracont').attr('checked', false);

		$.ajax({
			url : userUrl,
			type: "POST",
			dataType: 'json',
			data: $.param({domain_id:$that}),
			global: true,
			async:false,
			success:function(response){
					console.log(response);
					if($.trim(response) != ''){

						 $('#destinationfolder').show();
						 $('#domianusers').show();
						 $('#fromTemplate').show();
						 $('#movetodomain').show();
						 $('#fromTemplateImage').show();

						 $('#filter-domain-users').html(response);
					} else {

						 $("#domain_msg").show().text("Associated domain is empty, Please check with other domain");
						 $('#destinationfolder').hide();
						 $('#domianusers').hide();
						 $('#fromTemplate').hide();
						 $('#movetodomain').hide();
						 $('#fromTemplateImage').hide();

					}


			}
		})
	}

}


$( document ).delegate("#movetodomain", "click", function() {

	var $subdomainid = $("#userDomain").val();
	var $template_image = $("#template_image").val();
	var $domainUserid = $("#domain_userid").val();
	var $templateCategory = $("#templateCategory").val();
	var $templateCategoryDist = $("#templateCategoryDist").val();
	var templateMove = '<?php echo SITEURL; ?>organisations/templateMoveto';

	var $template_id = [];
	$.each($("#userTemplateid option:selected"), function(){
		$template_id.push($(this).val());
	});

	var $multiSelecttemplate_id = [];
	$.each($(".multiselect-selected-text"), function(){
		if( $(this).val() != "None selected" ){
			$multiSelecttemplate_id.push($(this).val());
		}
	});

	var $extracont = [];
	$(".extracont:checked").each(function() {
		var nameAttr = $(this).val();
		var item = {};
		item[nameAttr] = nameAttr;
        $extracont.push(item);
    });

	if( $templateCategory.length > 0 && $.trim($subdomainid) != '' && $.trim($domainUserid) != '' && $.trim($templateCategory) != '' ){

		var $form = $('form#orgManageTemplates'); // You need to use standard javascript object here
		var formData = new FormData($form[0]), // You need to use standard javascript object here
			$fileInput = $form.find('input#template_image'),
			file = $fileInput[0].files[0];

		if ($fileInput.val() !== "" && file !== undefined) {
				var name = file.name,
					size = file.size,
					type = file.type;

				formData.append('extension_valid', 2);
				formData.append('upload_image', file.name);
				formData.append('file_image', $fileInput[0].files[0]);
				valid_flag = true;
				sizeMB = parseInt(size / (1024*1024))


		} else {
				formData.append('file_name', '');
				formData.append('extension_valid', 1);
		}


		$.each($("#userTemplateid option:selected"), function(){
			formData.append('template_id[]', $(this).val());
		});
		$(".extracont:checked").each(function() {
			var nameAttr = $(this).val();
			var item = {};
			item[nameAttr] = nameAttr;
			formData.append('copywith', item);
		});
		formData.append('subdomain_id', $subdomainid);
		formData.append('domainUserid', $domainUserid);
		formData.append('category_id', $templateCategoryDist);

		$.ajax({
			url : templateMove,
			type: "POST",
			dataType: 'json',
			data: formData,
			//data: $.param({template_id:$template_id,subdomain_id:$subdomainid,domainUserid:$domainUserid,category_id:$templateCategory,copywith:$extracont}),
			global: true,
			cache: false,
				contentType: false,
				processData: false,
			//async:false,
			success:function(response){
					console.log(response.domainUserid);

					if($.trim(response) != ''){

						if( response.templateCategory !='' ){
							$('#folder_template_msg').show().html(response.template_id);
						 } else {
							 $('#folder_template_msg').hide();
						 }

						 if( response.template_id !='' ){
							$('#template_msg').show().html(response.template_id);
						 } else {
							 $('#template_msg').hide();
						 }

						 if( response.subdomain_id !='' ){
							$('#domain_msg').show().html(response.subdomain_id);
						 } else {
							 $('#domain_msg').hide();
						 }

						 if( response.domainUserid !='' ){
							$('#domainuser_msg').show().html(response.domainUserid);
						 } else {
							 $('#domainuser_msg').hide();
						 }

						 if( response.category_id !='' ){
							$('#category_msg').show().html(response.category_id);
						 } else {
							 $('#category_msg').hide();
						 }

						 if( response.success == true ){
							 $("#userTemplateid").multiselect('refresh');
							 $("#userTemplateid").multiselect('rebuild');
							location.reload();
						 }
					}
			}
		})

	 } else {

		 if( $("#templateCategory").val().length =='' || $("#templateCategory").val().length == 0 ){
			$('#folder_template_msg').show().html('Please select template folder.');
		 } else {
			 $('#folder_template_msg').hide();
		 }

		 if( $template_id.length =='' || $template_id.length == 0 || $multiSelecttemplate_id.length == 0 ){
			$('#template_msg').show().html("Please select template.");
		 } else {
			 $('#template_msg').hide();
		 }

		 if( $subdomainid =='' || $subdomainid.length == 0 ){
			$('#domain_msg').show().html("Please select domain.");
		 } else {
			 $('#domain_msg').hide();
		 }

		 if( $templateCategoryDist =='' || $templateCategoryDist.length == 0 ){
			$('#category_msg').show().html("Please select destination folder.");
		 } else {
			 $('#category_msg').hide();
		 }

		 if( $domainUserid =='' || $domainUserid.length == 0 ){
			$('#domainuser_msg').show().html("Please select domain user.");
		 } else {
			 $('#domainuser_msg').hide();
		 }
	}


});

$(document).on('change', '#templateCategory', function(event){
		event.preventDefault();
		var folderName = $(this).find("option:selected").text();
		$that = $(this).val();
		var tmpUrl = '<?php echo SITEURL; ?>organisations/getTemplate';
		$("#template_msg").hide().text('');

		// reset form all feilds
		$('#userTemplateid').multiselect('deselectAll')
		$('#userTemplateid').multiselect('refresh')

		$('select#userTemplateid option').removeAttr("selected");
		$('select#userTemplateid option:first-child').attr('selected','selected');

		$('select#userDomain option').removeAttr("selected");
		$('select#userDomain option:first-child').attr('selected','selected')

		$('select#templateCategoryDist option').removeAttr("selected");
		$('select#templateCategoryDist option:first-child').attr('selected','selected');

		$('select#domain_userid option').removeAttr("selected");
		$('select#domain_userid option:first-child').attr('selected','selected');

		$(".extracont checked").removeAttr("checked");
		$('.extracont').attr('checked', false);

		$('#userTemplateid option:selected').removeAttr("selected");

		if( $that.length > 0 ){
			$.ajax({
				url : tmpUrl,
				type: "POST",
				dataType: 'json',
				data: $.param({folder_id:$that}),
				global: true,
				async:false,
				success:function(response){
					if($.trim(response) != ''){

						$('#intrnltemplate').show();
						$('#copytodomains').show();
						$('#userTemplateid').empty();

						$('#userTemplateid').append(function() {

							var output = '';
							$.each(response, function(key, value) {
									output += '<option value="' + key + '">' +value + '</option>';
							});
							return output;

						});

						$('#userTemplateid').multiselect('rebuild');
						$('#userTemplateid').multiselect('refresh');

						$('#userTemplateid option:selected').removeAttr("selected");

						$('select#userDomain option').removeAttr("selected");
						$('select#userDomain option:first-child').attr('selected','selected')

						$('select#templateCategoryDist option').removeAttr("selected");
						$('select#templateCategoryDist option:first-child').attr('selected','selected');

						$('select#domain_userid option').removeAttr("selected");
						$('select#domain_userid option:first-child').attr('selected','selected');

						$(".extracont checked").removeAttr("checked");
						$('.extracont').attr('checked', false);

					} else {

						$("#template_msg").show().text("Folder is empty, Please check with other folders");
						$('#intrnltemplate').hide();
						$('#copytodomains').hide();
						$('#destinationfolder').hide();
						$('#domianusers').hide();
						$('#fromTemplate').hide();
						$('#fromTemplateImage').hide();
						$('#movetodomain').hide();
						$('#userTemplateid option:selected').removeAttr("selected");
					}

				}
			})

		}
});

$(document).on('change', '.org_domains1111', function(event){
		event.preventDefault();

			$that = $(this);

			var userUrl = '<?php echo SITEURL; ?>organisations/getDomainUsers';

			BootstrapDialog.show({
				title: 'Confirmation',
				message: 'Are you sure you want perform Copy to selected Associated Domain?',
				type: BootstrapDialog.TYPE_DANGER,
				draggable: true,
				buttons: [
				{
					label: ' Yes',
					cssClass: 'btn-success',
					autospin: true,
					action: function (dialogRef) {
						$.when(
							$.ajax({
								url : userUrl,
								type: "POST",
								data: $.param({domain_id:$that}),
								global: true,
								async:false,
								success:function(response){ }
							})
						).then(function( data, textStatus, jqXHR ) {

							if($.trim(data) != 'success'){
								$('#Recordedit').html(data);
							}else{
								 $('#filter-domain-users').html(data);
							}

							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
						})
					}
				},
				{
					label: ' No',
					//icon: '',
					cssClass: 'btn-danger',
					action: function (dialogRef) {
						dialogRef.close();
					}
				}
				]
			});


});

$( document ).ready(function() {

	$( document ).delegate("#frmReset", "click", function() {
		$('#orgManageTemplates')[0].reset();
		$('#userTemplateid').multiselect('deselectAll')
		$('#userTemplateid').multiselect('refresh')
	});
	setTimeout(function(){
		$("#successFlashMsg").hide('slow');
	}, 4000);
	$( "#frmReset" ).trigger( "click" );

});


 </script>