<?php //echo $this->Html->script('projects/plugins/wysihtml5.editor' , array('inline' => true)); ?>
<style>
	.border-red { border-color: #dd4b39; }
	.border-blue { border-color: #0073b7; }
	.border-maroon { border-color: #d81b60; }
	.border-aqua { border-color: #00c0ef; }
	.border-yellow { border-color: #f39c12; }
	.border-teal { border-color: #39cccc; }
	.border-purple { border-color: #605ca8; }
	.border-navy { border-color: #001f3f; }
	.border-green { border-color: #67a028; }
	.border-gray { border-color: #bcbcbc; }


	.col-form-label{ margin: 5px 0;}
	.col-xs-2{ width:	9.6667% !important;margin: 5px 0;}
	.col-xs-10{	 width:	88.3333% !important;}

	.template_info {
	    background: #00aff0 none repeat scroll 0 0;
	    border-radius: 50%;
	    color: #ffffff;
	    font-size: 10px;
	    height: 18px;
	    line-height: 20px;
	    padding: 0 8px;
	    width: 20px;
	}
	.wsp_wrapper {
		border: 1px solid #cccccc;
		border-radius: 3px;
		margin: 0 0 10px;
		padding: 0;
	}
	.area_zone_wrapper {
		border: 1px solid #cccccc;
		/* padding: 10px; */
		border-radius: 3px;
	}
	.zone-header {
		background-color: #eeeeee;
		display: block;
		/* margin: -10px -10px 10px; */
		padding: 5px;
	}
	.area_zones {
		max-height: 710px;
		overflow-x: hidden;
		overflow-y: auto;
		/* padding: 15px; */
	}
	.area_group {
		/* border-bottom: 3px solid #f0f0f0;
		padding: 15px; */
	}
	.element_btns {
		display: block;
		padding: 5px 10px;
	}
	.wsp_wrapper textarea, .area_zone_wrapper textarea {
		resize: none;
	}
	.task_desc {
		padding:0 15px;
	}
	.elements_wrapper {

		margin: 0 0 10px;
	}

	.elements_inner {
		margin: 10px;
		padding: 0;
		position: relative;
		z-index: 1;
		/*border: 1px solid #ccc;*/
		clear:both;
		/* box-shadow: 2px 2px 5px rgba(120, 120, 120, 0.4); */
	}
	.element_title {
		width: 100%;
	}
	.inp_title_wrap {
		position: relative;
	}
	.open_el_colors {
		margin-top: 5px;
	}

	.color_box_wrapper {
	    background: #ffffff none repeat scroll 0 0;
	    border: 1px solid #dddddd;
	    border-radius: 5px;
	    left: auto;
	    margin: 0;
	    padding: 6px;
	    position: absolute;
	    right: 0;
	    top: 28px;
	    width: 284px;
	    z-index: 9999;
	}


	/**************************/

	.area_group h2 {
		background-color: #eeeeee;
		border-bottom: 1px solid #ccc;
		border-top: 1px solid #ccc;
		display: block;
		font-size: 13px;
		padding: 5px;
	}
	.remove_el {
		/*position: absolute;
		right: 60px;
		top: 9px;
		margin-right: 10px;*/
		cursor: pointer;
	}
	.el-ex-col {
	    cursor: pointer;
	    /*position: absolute;
	    margin-top: 1px;
	    right: 5px;
	    top: 8px;*/
	}
	/*.el-ex-col:hover {
	    background-color: #447c17;
	    color: #fff;
	}*/


	.panel-heading h5.heading_title {
		margin: 0;
		width: 80%;
		cursor: pointer;
		display: inline-block;
	}
	.panel-heading[aria-expanded="true"] i.fa.el-ex-col::before  {
		content: "";
	}
	.panel-heading[aria-expanded="false"] i.fa.el-ex-col::before  {
		content: "";
	}


	.table-wrapper {
		border: 1px solid #a9a9a9;
		border-radius: 4px;
		padding: 1px;
		width: 100%;
		max-width:120px;
		display:table;
	}
	.table-wrapper .table {
		margin: 0;
		width: 100%;
		height:82px;
	}
	.table-wrapper .table td {
		border: 1px solid #fff;
		/* border-radius: 5px; */
		padding: 0px 0;
		vertical-align: middle;
	}

	.remove_db_el {
	    /*position: absolute;
	    right: 60px;
	    top: 9px;
	    margin-right: 10px;*/
	    cursor: pointer;
	}
	/*.remove_db_el:hover, .remove_el:hover {
	    background-color: #dd4c3a;
	    color: #fff;
	}*/

	.wsp_wrapper .panel-heading label{ margin-bottom : 0;}

	.move_down_el{
		/*position: absolute;
	    right: 41px;
	    top: 9px;
	    margin-right: 10px;*/
	    cursor: pointer;
	}

	.move_up_el{
		/*position: absolute;
	    right: 22px;
	    top: 9px;
	    margin-right: 10px;
	    */
	    cursor: pointer;
	}


	.LeftFloat
	{
	    float: left
	}
	.RightFloat
	{
	    float: right
	}

	.FieldContainer
	{
	}
	 .OrderingField
	{

	}
	.OrderingField div.Commands
	{
	    width: 60px;
	}
	/*button
	{
	    width: 60px;
	}*/
	.elements_inner .panel-heading .pull-right i.fa {
	    float: left;
	}
	.OrderingField .error-message {
		display:block;
	}
	.elements_wrapper .panel-body .form-group .element-title-h{
		width:auto;
	}

	
	
	
	@media screen and (max-width:1400px) {
	.panel-heading h5.heading_title {	
		width: 80%;	
	}	

	@media screen and (max-width:1299px) {
		.panel-heading h5.heading_title {
			width: 75%;
		}
	}
	@media screen and (max-width:1024px) {
		.panel-heading h5.heading_title {
			margin: 0;
			width: 65%;
			cursor: pointer;
			display: inline-block;
			  text-overflow: ellipsis;
	    overflow: hidden;
	    height: 16px;
	    white-space: nowrap;
		}
	}

	@media screen and (max-width:767px) {
		.box-body .template_form {
	    padding: 5px 0px;
	}

	}

	@media screen and (max-width:599px) {
	.elements_inner .panel-heading .pull-right i.fa{
		padding: 1px 2px;
	}
	.panel-heading h5.heading_title {
	    margin: 0;
	    width: 66%;
	}
	}



</style>
	<?php
	echo $this->Form->create('TemplateRelation', array('url' => array('controller' => 'templates', 'action' => 'save_template' ), 'type' => 'multipart/form-data', 'class' => 'form-bordered', 'id' => 'modelFormSaveTemplate' ));

	echo $this->Form->input('TemplateRelation.id', [ 'type' => 'hidden' ] );

	echo $this->Form->input('TemplateRelation.template_id', [ 'type' => 'hidden' ] );

	echo $this->Form->input('TemplateRelation.user_id', [ 'type' => 'hidden', 'value' => $this->Session->read('Auth.User.id') ] );

	//pr($this->request->data['TemplateRelation']['thirdparty_id']);

	?>
	<div class="row">
		<div class="form-group col-sm-4">
			<label class=" " for="description">Library Folder:</label>
			<?php echo $this->Form->input(
					'template_category_id',
					array('options' => $template_categories, 'empty' => 'Please Select', 'class' => 'form-control', 'label' => false, 'div' => false, 'style' => 'width: 90%;')
				);
			?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>
		<?php if($this->Session->read('Auth.User.role_id') == 1){ ?>
		<div class="form-group col-sm-3">
			<label class=" " for="description">User Type:</label>
			<?php
				$userType = array('2'=>'OpusView', '3'=>'Third Party');
				$selectUserType = (isset($this->request->data['TemplateRelation']['thirdparty_id']) && !empty($this->request->data['TemplateRelation']['thirdparty_id']))? 3 : 2;
				echo $this->Form->input(
					'user_type',
					array('options' => $userType,'select'=>$selectUserType, 'class' => 'form-control', 'label' => false, 'div' => false, 'style' => 'width: 90%;','default'=>$selectUserType)
				);
			?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>
		<?php }
			$thirdpartyusers = $this->Template->getThirdPartyUser();
		if( isset($thirdpartyusers) && !empty($thirdpartyusers) && count($thirdpartyusers) > 0 ){
		?>
		<div class="form-group col-sm-4" id="thirdpartyusers">
			<label class=" " for="description">Third Party Users:</label>
			<?php
			$thirdpartyusers = $this->Template->getThirdPartyUser();
			echo $this->Form->input(
					'thirdparty_id',
					array('options' => $thirdpartyusers, 'empty' => 'Please Select', 'class' => 'form-control', 'label' => false, 'div' => false, 'style' => 'width: 80%;')
				);
			?>
			<span style="" class="error-message text-danger"> </span>
			<span class="error chars_left" ></span>
		</div>
		<?php } else { ?>

			<div class="form-group col-sm-4" id="thirdpartyusers">
			<label class=" " for="description">Third Party Users:</label>
			<?php
			echo $this->Form->input(
					'thirdparty_id',
					array('empty' => 'No user found', 'class' => 'form-control', 'label' => false, 'div' => false, 'style' => 'width: 80%;')
				);
			?>
			<span style="" class="error-message text-danger">No user found</span>
			<span class="error chars_left" ></span>
		</div>

		<?php } ?>
	</div>


<div class="wsp_wrapper panel  <?php echo str_replace('bg-', 'panel-', $this->data['TemplateRelation']['color_code']); ?>">

	<div class="panel-heading">
		<label class=" " for="title">Knowledge Template:</label>

		<div class="pull-right ws_color_wrapper" style="position: relative;">

			<small class="ws_color_box" style="display: none;">
				<small class="colors btn-group" style="width:100%;">
					<b data-color="bg-red" data-pcolor="panel-red" class="btn btn-default btn-xs el_color_box tipText" data-tcolor="#dd4b39" title="Red"><i class="fa fa-square text-red"></i></b>
					<b data-color="bg-blue" data-pcolor="panel-blue" class="btn btn-default btn-xs el_color_box tipText" data-tcolor="#0073b7" title="Blue"><i class="fa fa-square text-blue"></i></b>
					<b data-color="bg-maroon" data-pcolor="panel-maroon" class="btn btn-default btn-xs el_color_box tipText" data-tcolor="#d81b60" title="Maroon"><i class="fa fa-square text-maroon"></i></b>
					<b data-color="bg-aqua" data-pcolor="panel-aqua" class="btn btn-default btn-xs el_color_box tipText" data-tcolor="#00c0ef" title="Aqua"><i class="fa fa-square text-aqua"></i></b>
					<b data-color="bg-yellow" data-pcolor="panel-yellow" class="btn btn-default btn-xs el_color_box tipText" data-tcolor="#f39c12" title="Yellow"><i class="fa fa-square text-yellow"></i></b>
					<b data-color="bg-teal" data-pcolor="panel-teal" class="btn btn-default btn-xs el_color_box tipText" data-tcolor="#39cccc" title="Teal"><i class="fa fa-square text-teal"></i></b>
					<b  data-color="bg-purple" data-pcolor="panel-purple" class="btn btn-default btn-xs el_color_box tipText" data-tcolor="#605ca8" title="Purple"><i class="fa fa-square text-purple"></i></b>
					<b data-color="bg-navy" data-pcolor="panel-navy" class="btn btn-default btn-xs el_color_box tipText" data-tcolor="#001f3f" title="Navy"><i class="fa fa-square text-navy"></i></b>
					<b data-color="bg-green" data-pcolor="panel-green" class="btn btn-default btn-xs el_color_box tipText" data-tcolor="#67a028" title="Green"><i class="fa fa-square text-green"></i></b>
				</small>
			</small>
			<a class="btn btn-default btn-xs open_ws_colors tipText" data-title="Color Theme"><i class="brushblack"></i>
				<?php echo $this->Form->input('TemplateRelation.color_code', [ 'type' => 'hidden', 'class' => 'hidden ws_color' ] );  ?>
			</a>
		</div>

	</div>
	<div class="panel-body">
	<div class="form-group">
	    <label class="" for="title">Title:</label>
		<?php echo $this->Form->text('TemplateRelation.title', [ 'class'=> 'form-control','required'=>false, 'id' => 'wsp_title', 'escape' => true, 'placeholder' => '50 chars' ] );   ?>
		<span style="" class="error-message text-danger"> <?php //pr($errors); ?></span>
		<span class="error chars_left" ></span>
	</div>


	<div class="form-group">
		<label class=" " for="description">Description:</label>
		<?php echo $this->Form->textarea('TemplateRelation.description', [ 'class'	=> 'form-control', 'required'=>false, 'id' => 'wsp_description', 'escape' => true, 'rows' => 3, 'placeholder' => '500 chars' ] ); ?>
		<span style="" class="error-message text-danger"> </span>
		<span class="error chars_left" ></span>
	</div>
	</div>
</div>

<div class="area_zone_wrapper">
	<div class="zone-header" for="">Knowledge Template Areas:</div>
	<div class="area_zones">



		<?php $template_area = template_area($template_id, false);

		 //pr($template_area); die;

		$temp_original_id = (isset($template_original_id) && !empty($template_original_id)) ? $template_original_id : 0 ;
		$total_area = count($template_area);

		?>

		<?php if( isset($template_area) && !empty($template_area) ) { ?>

		<?php foreach( $template_area as $i => $value ) {
				$val = $value['AreaRelation'];
		?>
		<div class="area_group" data-area="<?php echo $i; ?>">
			<h2><span>Area <?php echo $i+1; ?></span></h2>
			<div class="area_wrapper clearfix">
				<div class="col-sm-3 col-md-2">
					<?php echo $this->element('../Templates/partials/area_template', ['template_id' => $temp_original_id, 'selection' => $i,'allow'=>1]); ?>
					<div class="element_btns margin-top nopadding">
						<a class="btn btn-warning btn-sm btn_create_elements"><i class="workspace-icon"></i> Task</a>
					</div>
				</div>
				<div class="col-sm-9 col-md-10">
					<input class="form-control" type="hidden" value="<?php echo $val['id']; ?>" name="area[<?php echo $i; ?>][id]">
					<div class="form-group clearfix">
						<label class="col-xs-2 col-form-label" for="">Title:</label>
						<div class="col-xs-10">
							<input class="form-control area-title" type="text" value="<?php echo htmlentities(substr($val['title'], 0, 100)); ?>" name="area[<?php echo $i; ?>][title]" placeholder="100 chars">
							<span style="" class="error-message text-danger"> </span>
						</div>
					</div>
					<div class="form-group clearfix">
						<label class="col-xs-2 col-form-label" for="">Purpose:</label>
						<div class="col-xs-10">
							<textarea placeholder="250 chars" name="area[<?php echo $i; ?>][purpose]" rows="3" class="form-control area-purpose"><?php echo htmlentities(substr($val['description'], 0, 250)); ?></textarea>
							<span style="" class="error-message text-danger"> </span>
						</div>
					</div>
				</div>
			</div>

			<div class="elements_wrapper FieldContainer">


				<?php $area_elements = relational_elements($val['id'], false);  ?>

				<?php if( isset($area_elements) && !empty($area_elements) ) { ?>

					<?php
					$p=1;
					$aecount = count($area_elements);
					foreach( $area_elements as $ei => $ev ) {
						$elval = $ev['ElementRelation'];

					?>

					<div class="elements_inner panel <?php echo str_replace('border-', 'panel-', $elval['color_code']); ?> OrderingField">


					   	<div class="panel-heading" data-toggle="collapse" href=" ">
							<h5 class="heading_title">Title: <?php echo str_replace("<br>"," ",$elval['title']); ?></h5>
							<div class="pull-right">
							    <?php
 
								if(isset($ev['ElementRelationDocument']) && !empty($ev['ElementRelationDocument'])){ 
									$cd = count($ev['ElementRelationDocument']);
									$countD = ( $cd ==1) ? $cd.' Document' : $cd.' Documents'; 
								?>
								<i class="noteswhite18  btn btn-xs tipText" title="<?php echo $countD; ?>"></i>					 
								<?php } ?>
								
								<i class="closewhite18 remove_db_el btn btn-xs tipText" title="Delete Task"></i>
								<i class="fa fa-long-arrow-down move_down_el btn btn-xs tipText buttonupdown" title="Move Down" data-value="down"></i>
								<i class="fa fa-long-arrow-up move_up_el btn btn-xs tipText buttonupdown" title="Move Up" data-value="up"></i>
								<i class="fa fa-chevron-down pull-right el-ex-col btn btn-xs chevron_toggleable" data-toggle="collapse" href="#el_accordion_<?php echo $elval['id'] ?>"></i>
							</div>
						</div>
						<input class="form-control element_id" type="hidden" value="<?php echo $elval['id']; ?>" name="area[<?php echo $i; ?>][element][<?php echo $ei; ?>][id]">
						<div class="panel-body collapse" id="el_accordion_<?php echo $elval['id'] ?>" data-time="<?php echo $ei ?>">
							<div class="form-group clearfix inp_title_wrap">
							<label class="col-md-1 col-form-label element-title-h">Task Title: </label>
								<div class="col-md-10 element-control-input">
								<input class="form-control element_title pull-left" type="text" value="<?php echo htmlentities(str_replace("<br>"," ",$elval['title'])); ?>" name="area[<?php echo $i; ?>][element][<?php echo $ei; ?>][element_title]" placeholder="50 chars">
									<span class="error-message text-danger">
									</span>
								</div>
							<a class="btn btn-default btn-xs open_el_colors pull-right tipText" data-title="Color Theme">
							<i class="brushblack" ></i>
							<input name="area[<?php echo $i; ?>][element][<?php echo $ei; ?>][task_color]" value="<?php echo $elval['color_code']; ?>" class="hidden task_color">
							</a>
							</div>
							<div class="form-group clearfix task_desc">
								<label class="">Task Description: </label>
								<textarea name="area[<?php echo $i; ?>][element][<?php echo $ei; ?>][task_description]" rows="2" class="form-control task_description" placeholder="750 chars"><?php echo str_replace("<br>"," ",$elval['description']); ?></textarea>
								<span class="error-message text-danger"></span>
							</div>

							<div class="form-group clearfix " style="margin-left: 15px;">
								<label for="doc_file_sub">Attachment:</label>
								<div class="input-group">
									<div class="input-group-addon"><i class="uploadblackicon"></i></div>
									<span title="" class="docUpload icon_btn bg-white border-radius-right tipText" data-original-title="Click to upload multiple files">
										<input name="data[area][<?php echo $i; ?>][element][<?php echo $ei; ?>][element_file][]" class="form-control upload comment-uploads"   placeholder="Upload Multiple Files" multiple="multiple" type="file">
										<span class="text-blue" id="upText">Upload Multiple Documents</span>
									</span>
								</div>
							</div>



							<?php

							if(isset($ev['ElementRelationDocument']) && !empty($ev['ElementRelationDocument'])){ ?>
								<div class="clearfix col-xs-12 form-group uploaded_list">
								<div class="image-box-wrapper">
								<?php
									foreach($ev['ElementRelationDocument'] as $Attachment){
										$id = $Attachment['id'];

										//$id = $FeedbackAttachment['id'];
										$upload_path = WWW_ROOT . TEMPLATE_DOCUMENTS . DS ;
										$upload_file = $upload_path . $Attachment['file_name'];

										$ftype = pathinfo($upload_file);
										if (isset($ftype) && !empty($ftype)) {
											// pr($ftype);
											$dirname = ( isset($ftype['dirname']) && !empty($ftype['dirname'])) ? $ftype['dirname'] : '';
											$basename = ( isset($ftype['basename']) && !empty($ftype['basename'])) ? $ftype['basename'] : '';
											$filename = ( isset($ftype['filename']) && !empty($ftype['filename'])) ? $ftype['filename'] : '';
											$extension = ( isset($ftype['extension']) && !empty($ftype['extension'])) ? $ftype['extension'] : '';
										}
								?>
								<?php $downloadURL = Router::Url(array('controller' => 'templates', 'action' => 'download_template_doc', $id, 'admin' => FALSE), TRUE); ?>
								<div class="image-box">
									<span class="download_asset icon_btn icon_btn_sm icon_btn_teal">
										<span class="icon_text"><?php echo $extension; ?></span>
									</span>
									<a href="<?php echo $downloadURL ?>"  class="imagename" href="javascript:void(0);"><?php echo $basename; ?></a>
									<a class="confirm_doc_delete btn_file_link" href="#" data-remote="<?php echo $downloadURL ?>" data-id="<?php echo $id; ?>" data-file="<?php echo $basename; ?>"><i class="deleteblack tipText" data-title="Delete"></i></a>
								</div>
								<?php } ?>

								</div>
								</div>
							<?php } ?>


						</div>

					</div>

					<?php
					$p++;
					} ?>

				<?php } ?>

			</div>
		</div>

		<?php } ?>

		<?php } ?>



	</div>
</div>
<div class="panel-heading" style="padding: 10px 0px;">
	<a class="btn btn-success btn-sm save_template_updates tipText" title="Save and Stay on Page" data-savetype="update"> Update </a>
	<a class="btn btn-success btn-sm save_template_updates" data-savetype="save"> Save </a>
	 <a class="btn btn-danger btn-sm" href="<?php echo Router::Url(array( "controller" => "templates", "action" => "create_workspace", $project_id, $template_category_id, 'admin' => FALSE ), true); ?>"  id="cancel_template"> Cancel </a>
</div>
<?php echo $this->Form->end(); ?>


<script type="text/javascript">
$(function(){

	//$(".elements_wrapper").sortable();
	//$('.chevron_toggleable').on('click', function() {
	$('body').delegate('.chevron_toggleable', 'click', function(event){
		$(this).toggleClass('fa-chevron-down fa-chevron-up');
	});


	$('body').delegate('.element_title', 'keyup focus', function(event){
		event.preventDefault();
		var text = $(this).val(),
			$elements_inner = $(this).parents('.elements_inner:first'),
			$heading = $elements_inner.find('.panel-heading h5');
		var characters = 50;

		$heading.text('Title: ' + text);
		var $error_el = $(this).parent().find('.error-message');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
	})

	$('body').delegate('.task_description', 'keyup focus', function(event){
        var characters = 750;
        event.preventDefault();
        var $error_el = $(this).parent().find('.error-message');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })

	$('body').delegate('.area-title', 'keyup focus', function(event){
        var characters = 100;
        event.preventDefault();
        var $error_el = $(this).parent().find('.error-message');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })

	$('body').delegate(".area-purpose", 'keyup focus', function(event){
        var characters = 250;
        event.preventDefault();
        var $error_el = $(this).parent().find('.error-message');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })

	$('body').delegate('#wsp_title', 'keyup focus', function(event){
        var characters = 50;
        event.preventDefault();
        var $error_el = $(this).parents('.form-group').find('.error');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })
	$('body').delegate('#wsp_description', 'keyup focus', function(event){
        var characters = 500;
        event.preventDefault();
        var $error_el = $(this).parents('.form-group').find('.error');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })
	$('body').delegate('#wsp_key_result_target', 'keyup focus', function(event){
        var characters = 250;
        event.preventDefault();
        var $error_el = $(this).parents('.form-group').find('.error');
        if (typeof $.input_char_count !== 'undefined' && $.isFunction($.input_char_count)) {
            $.input_char_count(this, characters, $error_el);
        }
    })

/*
	var elm = [ $('#wsp_title') ];
	wysihtml5_editor.set_elements(elm)
	$.wysihtml5_config = $.get_wysihtml5_config()

	var title_config = $.extend( {}, {'remove_underline': true}, $.wysihtml5_config)

	// var title_config = $.wysihtml5_config;
	$.extend( title_config, { 'parserRules': { 'tags': { 'br': { 'remove': 0 },'ul': { 'remove': 0 } ,'li': { 'remove': 0 },'b': { 'remove': 0 },'i': { 'remove': 0 } ,'blockquote': { 'remove': 0 } ,'ol': { 'remove': 0 } ,'u': { 'remove': 0 }     } } })


	$("#wsp_title").wysihtml5( title_config );
*/




	/*
	 * @access  public
	 * @todo  	Bind click event on all color pickers of each element
	 * @return  None
	 * */
	$('body').delegate('.open_ws_colors', 'click', function (event) {
		event.preventDefault();
		var $ws_colors = $(this).prev('.ws_color_box');

		$ws_colors.slideDown(200);
	});

	/*
	 * @access  public
	 * @todo  	Bind click event on Body to hide any visible color pickers if clicked outside of Target and color picker
	 * @return  None
	 * */
	$('body').on('click', function (e) {
		$('.open_ws_colors').each(function () {
			if ( !$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.ws_color_box').has(e.target).length === 0) {
				$(this).prev('.ws_color_box').slideUp(300)//.hide( "drop", { direction: "left" }, 400, function(){  } );
			}
		});
	});

	/*
	 * @access  public
	 * @todo  	Set data of each color bucket and color boxes
	 * @return  None
	 * */
	$('.open_ws_colors').each( function () {
		var $bucket = $(this)

		var $ws_color_box = $bucket.prev('.ws_color_box')

		$bucket.data('colors', $ws_color_box)
		$ws_color_box.data('color_bucket', $bucket)
	})

	/*
	 * @access  public
	 * @todo  	Set data of each color bucket and color boxes
	 * @return  None
	 * */

	$('body').delegate('.ws_color_box .el_color_box', 'click', function (e) {
		e.preventDefault();

		var $this = $(this),
			$wsp_wrapper = $(this).parents('.wsp_wrapper:first'),
			$ws_color_wrapper = $(this).parents('.ws_color_wrapper:first'),
			data = $this.data(),
			tcolor = data.tcolor,
			color_code = data.color,
			pcolor_code = data.pcolor,
			$ws_color = $ws_color_wrapper.find('.ws_color');

		var cls = $wsp_wrapper.attr('class')

		var foundClass = (cls.match(/(^|\s)panel-\S+/g) || []).join('')
		if (foundClass != '') {
			$wsp_wrapper.removeClass(foundClass)
		}
		$wsp_wrapper.addClass(pcolor_code);

		$wsp_wrapper.css('border-color', tcolor);
		$ws_color.val("").val(color_code);

	})

	<?php if( isset($this->request->data['TemplateRelation']['thirdparty_id']) && !empty($this->request->data['TemplateRelation']['thirdparty_id']) ){ ?>
		$("#thirdpartyusers").show()
	<?php } else { ?>
		$("#thirdpartyusers").hide()
	<?php } ?>

	$('body').delegate('#TemplateRelationTemplateCategoryId', 'click', function (event) {
		event.preventDefault();

		if( $(this).val() > 0 ){
			$('#TemplateRelationUserType').prop("disabled", false);
		} else {
			$("select#TemplateRelationUserType").prop('selectedIndex', 0);
			$('#TemplateRelationUserType').prop("disabled", false);
			$("#TemplateRelationRelationId").prop('selectedIndex',0);
			$("#TemplateRelationRelationId").prop("disabled", false);
			//$("#thirdpartyusers").hide();
		}

	});

	/*
		Third Party User will be show If user select the User type 'Thirt Party User'
	*/
	$('body').delegate('#TemplateRelationUserType', 'click', function (e) {
		$that = $(this);

		if( $that.val() == 3 ){

			$("#TemplateRelationRelationId").prop("disabled", false);
			$("#thirdpartyusers").show();

		} else {
			$("#TemplateRelationRelationId").prop('selectedIndex',0);
			$("#TemplateRelationRelationId").prop("disabled", true);
			$("#thirdpartyusers").hide();
		}
	});



	$('.prophover').popover({
        placement : 'left',
        trigger : 'hover',
        html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
    })


//$(".FieldContainer").sortable({ items: ".OrderingField", distance: 10 });
$('body').delegate('.buttonupdown', 'click', function() {
    var btn = $(this);
    var val = btn.data('value');
    if (val == 'up')
        moveUp(btn.parents('.OrderingField'));
    else
        moveDown(btn.parents('.OrderingField'));
});

//===============================================
		  // $('.move_up_el,.move_down_el').hide();

			if( $.check_browser() == 3) {

				$("iframe").each(function () {
					var title_wysi = $('#txa_title').data("wysihtml5");
					var iframe = title_wysi.editor.composer.iframe


					$(this).load(function (event) {
						if( $(this).is(iframe)) {

							var $body = $(this).contents().find('body')

							$body.bind('keyup', function(events){

								if(events.keyCode == 13) {
									events.preventDefault();

									$('br', $(this)).replaceWith('');

									return;
								}
							})

						}
					});
				});

			}
			else if( $.check_browser() == 1 ||  $.check_browser() == 2 ||  $.check_browser() == 4 ) {
				// For Google Chrome. Opera, Safari and IE
				var title_wysi = $('#txa_title').data("wysihtml5");
				if( title_wysi ) {
					var edtor = $('#txa_title').data("wysihtml5").editor;

					if( edtor ) {
						var iram = edtor.composer.iframe;

						$(iram).attr('scrolling', 'no')
						$(iram).attr('seamless', 'seamless')
						var $body = $(iram).contents().find('body')
						$body.on('keyup', function(event){

							if(event.keyCode == 13) {
								event.preventDefault();

								$('br', $(this)).replaceWith('');
								return;
							}
						})
					}
				}
			}

		//===============================================


})

function moveUp(item) {
    var prev = item.prev();
    if (prev.length == 0)
        return;
    prev.css('z-index', 999).css('position','relative').animate({ top: item.height() }, 250);
    item.css('z-index', 1000).css('position', 'relative').animate({ top: '-' + prev.height() }, 300, function () {
        prev.css('z-index', '').css('top', '').css('position', '');
        item.css('z-index', '').css('top', '').css('position', '');
        item.insertBefore(prev);
        $.removeBtns();
    });
}
function moveDown(item) {
    var next = item.next();
    if (next.length == 0)
        return;
    next.css('z-index', 999).css('position', 'relative').animate({ top: '-' + item.height() }, 250);
    item.css('z-index', 1000).css('position', 'relative').animate({ top: next.height() }, 300, function () {
        next.css('z-index', '').css('top', '').css('position', '');
        item.css('z-index', '').css('top', '').css('position', '');
        item.insertAfter(next);
        $.removeBtns();
    });
}

		$.removeBtns = function() {

		$('.move_up_el,.move_down_el').css({opacity: 1, visibility: 'visible'});

		$('.area_group').each(function(){

			var $area_group = $(this);
			var len = $('.FieldContainer > .OrderingField', $area_group).length;

			$area_group.find('.FieldContainer').find('.OrderingField').each(function(i){

				if( len == 1 ){
					$(this,$area_group).find('.move_down_el').hide();
					$(this,$area_group).find('.move_up_el').hide();
				} else {
					$(this,$area_group).find('.move_down_el').show();
					$(this,$area_group).find('.move_up_el').show();
				}

				if($(this,$area_group).index() == 0) {
					$(this,$area_group).find('.move_up_el').css({opacity: 0, visibility: 'hidden'});
				}
				else if($(this,$area_group).index() == len-1) {
					$(this,$area_group).find('.move_down_el').css({opacity: 0, visibility: 'hidden'});
				}

			})
		})

	}
	$(".elements_wrapper").sortable({
		update: function( event, ui ) {
			$.removeBtns()
		}
	})
	$.removeBtns();



</script>