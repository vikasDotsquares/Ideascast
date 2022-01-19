<?php //echo $this->Html->script('projects/plugins/wysihtml5.editor' , array('inline' => true)); ?>
<?php //echo $this->Html->css('projects/templates'); ?>
<style>
	.wsp_wrapper {
		border: 1px solid #cccccc;
		border-radius: 3px;
		margin: 0 0 10px;
		padding: 0;
	}

	.popover .popover-content .template_create  {
		font-size: 12px;
		font-weight: normal;
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
	    padding:6px ;
	    position: absolute;
	    right: 0;
	    top: 28px;
	    width: 284px;
	    z-index: 9999;
	}


	.col-form-label{ margin: 5px 0;}
	.col-xs-2{ width:	9.6667% !important;margin: 5px 0;}
	.col-xs-10{	 width:	88.3333% !important;}


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
		/* position: absolute;
		right: 32px;
		top: 9px;
		margin-right: 10px; */
		cursor: pointer;
	}
	.el-ex-col {
	    cursor: pointer;
	    /* position: absolute;
	    right: 5px;
	    top: 8px; */
	    margin-top: 1px;
	}
	/*.el-ex-col:hover {
	     background-color: #447c17;
	    color: #fff;
	}*/

	/*
	.remove_el:hover {
	    background-color: #dd4c3a;
	    color: #fff;
	}
	*/


	.panel-heading h5.heading_title {
		margin: 0;
		width: 90%;
		cursor: pointer;
	}
	.panel-heading[aria-expanded="true"] i.fa.el-ex-col::before  {
		content: "";
	}
	.panel-heading[aria-expanded="false"] i.fa.el-ex-col::before  {
		content: "";
	}
	.template_info {
	    background: #00aff0 none repeat scroll 0 0;
	    border-radius: 50%;
	    color: #ffffff;
	    font-size: 10px;
	    height: 20px;
	    line-height: 18px;
	    padding: 0 8px;
	    width: 20px;
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

	#thirdpartyusers{
		display:none;
	}


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
	button
	{
	    width: 60px;
	}

	.image-box .imagename {
		display: inline-block;
		margin: 0 5px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 500px;
		vertical-align: top;
	}

	@media (max-width:1365px) {
		.area_zones .col-xs-2 {
	    width: 15.6667% !important;

		}
		.area_zones .col-xs-10 {
	    width: 82.3333% !important;
	}
	}
	@media (max-width:991px) {
	.area_zones .table-wrapper .table {
	    width: 100% !important;
	}
		.area_zones .col-xs-2{
	    width: 24.6667% !important;
		}
		.area_zones .col-xs-10{
	    width: 75.3333% !important;
	}
	.area_group .panel-body .col-xs-1{
	    width: 15.33333333%;
	}
	@media (max-width:991px) {
	.area_wrapper tr td {
	  display: table-cell  !important;
	  width: auto  !important;
	}
	}

	@media (max-width:479px) {
	.area_zones .col-xs-2 {
	  padding-left: 0;
	}
	.element_btns {
	  margin-bottom: 10px !important;
	}
	.form-groups .custom-dropdown{
	   width:62% !important;
	}
	}
	@media screen and (max-width:1299px) {
		.panel-heading h5.heading_title {
			width: 80%;
		}
	}
	@media screen and (max-width:1024px) {
		.panel-heading h5.heading_title {
			margin: 0;
			width: 70%;
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
	echo $this->Form->create('TemplateRelation', array('url' => array('controller' => 'templates', 'action' => 'save_template' ), 'class' => 'form-bordered', 'id' => 'modelFormSaveTemplate' ,'enctype'=>'multipart/form-data' ));

	echo $this->Form->input('TemplateRelation.template_id', [ 'type' => 'hidden', 'value' => $template_id ] );

	echo $this->Form->input('TemplateRelation.user_id', [ 'type' => 'hidden', 'value' => $this->Session->read('Auth.User.id') ] );


	?>
	<div class="row">
		<div class="form-group col-sm-4">
			<label class=" " for="description">Library Folder:</label>
			<?php
			$default = (isset($this->params['pass']['1']) && !empty($this->params['pass']['1'])) ? $this->params['pass']['1'] : 0;

			echo $this->Form->input(
					'template_category_id',
					array('options' => $template_categories,'default'=>$default,'empty' => 'Please Select', 'class' => 'form-control', 'label' => false, 'div' => false, 'style' => 'width: 90%;')
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
				//$userType = array('2'=>'Jeera');
				echo $this->Form->input(
					'user_type',
					array('options' => $userType, 'class' => 'form-control', 'label' => false, 'div' => false, 'style' => 'width: 90%;')
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

<div class="wsp_wrapper panel panel-default">

	<div class="panel-heading" >
		<h5>Knowledge Template: </h5>

		<div class="pull-right ws_color_wrapper" style="position:relative;">
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
				<a class="btn btn-default btn-xs open_ws_colors tipText" data-title="Color Theme"><i class="brushblack"></i><input name="data[TemplateRelation][color_code]" value="bg-gray" class="hidden ws_color"></a>
			</div>
	</div>

	<div class="panel-body " >
		<div class="form-group">
			<label class=" " for="title">Title:</label>


			<?php echo $this->Form->text('TemplateRelation.title', [ 'class'	=> 'form-control','required'=>false, 'id' => 'template_wsp_title', 'escape' => true, 'placeholder' => '50 chars' ] );   ?>
			<span style="" class="error-message text-danger"> </span>
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
		<?php if( isset($total_area) && !empty($total_area) ) { ?>

		<?php for( $i = 0; $i < $total_area; $i++ ) { ?>
		<div class="area_group" data-area="<?php echo $i; ?>">
			<h2><span>Area <?php echo $i+1; ?></span></h2>

			<div class="area_wrapper clearfix">
				<div class="col-sm-3 col-md-2">
					<?php echo $this->element('../Templates/partials/area_template', ['template_id' => $template_id, 'selection' => $i]); ?>
					<div class="element_btns  margin-top nopadding">
					<a class="btn btn-warning btn-sm btn_create_elements"><i class="workspace-icon"></i> Task</a>
					</div>
				</div>
				<div class="col-sm-9 col-md-10">
					<div class="form-group clearfix">
						<label class="col-xs-2 col-form-label" for="">Title:</label>
						<div class="col-xs-10">
							<input class="form-control area-title" type="text" value="" placeholder="100 chars" name="area[<?php echo $i; ?>][title]">
							<span style="" class="error-message text-danger"> </span>
						</div>
					</div>
					<div class="form-group clearfix">
						<label class="col-xs-2 col-form-label" for="">Purpose:</label>
						<div class="col-xs-10">
							<!--<input class="form-control area-purpose" type="text" value="" >-->
							<textarea placeholder="250 chars" name="area[<?php echo $i; ?>][purpose]" rows="3" class="form-control area-purpose"></textarea>
							<span style="" class="error-message text-danger"> </span>
						</div>
					</div>
				</div>

			</div>

			<div class="elements_wrapper FieldContainer" >
			</div>
		</div>
		<?php } ?>

		<?php } ?>
	</div>
</div>
<?php echo $this->Form->end(); ?>


<script type="text/javascript">
$(function(){

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

	$('body').delegate('#template_wsp_title', 'keyup focus', function(event){
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

	$('body').delegate('#TemplateRelationTemplateCategoryId', 'click', function (event) {
		event.preventDefault();
		console.log($(this).val().length);

		if( $(this).val() > 0 ){
			$('#TemplateRelationUserType').prop("disabled", false);
		} else {
			$("select#TemplateRelationUserType").prop('selectedIndex', 0);
			$('#TemplateRelationUserType').prop("disabled", false);
			$("#TemplateRelationRelationId").prop('selectedIndex',0);
			$("#TemplateRelationRelationId").prop("disabled", false);
			$("#thirdpartyusers").hide();
		}

	});

	$('body').delegate('#TemplateRelationUserType', 'click', function (e) {
		$that = $(this);

		if( $that.val() == 3 ){
			console.log($that.val());

			$("#TemplateRelationRelationId").prop("disabled", false);
			$("#thirdpartyusers").show();

		} else {
			$("#TemplateRelationRelationId").prop('selectedIndex',0);
			$("#TemplateRelationRelationId").prop("disabled", true);
			$("#thirdpartyusers").hide();
		}
	});

	/*
		Third Party User will be show If user select the User type 'Thirt Party User'
	*/
	$('body').delegate('#TemplateRelationUserTypeNext', 'click', function (e) {
		$that = $(this);

		if( $that.val() == 3 ){
			console.log($that.val());
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

//===============================================

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
});


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