

<?php //echo $this->Html->script('/plugins/ckeditor/ckeditor' , array('inline' => true)); ?>
<?php //echo $this->Html->script('projects/manage_project' , array('inline' => true)); ?>
<?php echo $this->Html->script('/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5' , array('inline' => true)); ?>

<script type="text/javascript" >
$(function() {

	$.fn.line_break = function() {
		var html = $(this).html()
		html = html.replace(/<br\s*\/?>/g, " ");
		$(this).html(html)
	}

	var wysi_count_chars = function(event) {

			var $e = $(this.textarea.element)

			var data = $e.data();

			var wysiData = data.wysihtml5

			var wysiEditor = wysiData.editor

			var wysiConfig = wysiEditor.config

			var wysiLimit = wysiConfig.limit

			var wysiText = $e.val()

			$e.line_break() // removes all <br>

			var len = wysiText.length

			if( len > wysiLimit ) {
				$e.parents('div:first').find("span.error").html("<font style='color:red; font-size: 11px'>You've exceeded the maximum by " + ( len - wysiLimit ) + " characters.  Anything past the limit will not be saved.</font>");
				return false;
			}
			else {
				$e.parents('div:first').find("span.error").css({'font-size' : '11px'}).html(( wysiLimit - len ) + " characters left.");
			}
	}


	$.wysihtml5_config = {
		'font-styles': false,
		'color': false,
		'emphasis': true,
		'lists': false,
		'html': false,
		'link': false,
		'image': false,
		'accept_return': false,
		'limit': 100,
		'events':
			{
				'focus': wysi_count_chars,
				'blur': wysi_count_chars
			}
	};

	var title_config = $.wysihtml5_config;
	$.extend( title_config, { 'parserRules': { 'tags': { 'br': { 'remove': 1 } } } })

	$("#txa_title").wysihtml5( title_config );

	$("#txa_objective").wysihtml5( $.extend( $.wysihtml5_config, {'lists': true, 'limit': 250, 'parserRules': { 'tags': { 'br': { 'remove': 0 } } }}, $.wysihtml5_config) );
	$("#txa_description").wysihtml5( $.extend( $.wysihtml5_config, {'lists': true, 'limit': 500, 'parserRules': { 'tags': { 'br': { 'remove': 0 } } }}, $.wysihtml5_config)  );

})
</script>


<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"> Projects </h1>
				 <?php
					// LOAD PARTIAL FILE FOR TOP DD-MENUS
					echo $this->element('../Projects/partials/project_settings', array('val' => 'testing'));
				?>

			</section>
		</div>


		<div class="box-content">

			<div class="row ">
				<div class="col-xs-12">
					<div class="box noborder margin-top">
						<div class="box-header nopadding">
						<?php /* ?>
							<h3 class="box-title">
								<?php echo $text_val; ?> Project
							</h3>

							<p class="text-muted date-time">
								<span>Update project detail entered.</span>
							</p>

							<div class="box-tools">
								<div class="btn-group">

									<a class="btn btn-success btn-sm tipText" href="<?php echo $this->request->referer(); ?>"   title="<?php tipText('go-back' ); ?>"><i class="fa fa-fw fa-chevron-left"></i> Back</a>

								</div>
							</div>
						<?php */ ?>
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
							</div>
                            </div>
							<!-- END MODAL BOX -->

						</div>

						<div class="box-body border-top">
							<?php
							echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'manage_project'), 'role' => 'form', 'id' => 'frm_manage_project', 'class' => 'clearfix'));
							?>


								<?php echo $this->Form->input('UserProject.id', array('type' => 'hidden','label' => false, 'div' => false, 'class' => '')); ?>
								<?php echo $this->Form->input('Project.id', array('type' => 'hidden','label' => false, 'div' => false, 'class' => '')); ?>
									<div class="form-group col-md-12">

										<label for="title" class="col-md-2">Title</label>
										<div class="col-md-7">
											<?php echo $this->Form->textarea('Project.title', [ 'class'	=> 'form-control', 'id' => 'txa_title', 'escape' => true, 'rows' => 2, 'placeholder' => 'max chars allowed 100' ] ); ?>
											<span class="error" ></span>
											<?php echo $this->Form->error('Project.title'); ?>
										</div>

									</div>

									<div class="form-group col-md-12">

										<label for="title" class="col-md-2">Objective</label>
										<div class="col-md-7">
											<?php echo $this->Form->textarea('Project.objective', [ 'class'	=> 'form-control', 'id' => 'txa_objective', 'escape' => true, 'rows' => 6, 'placeholder' => 'max chars allowed 250' ] ); ?>
											<span class="error" ></span>
											<?php echo $this->Form->error('Project.objective'); ?>
										</div>

									</div>

									<div class="form-group col-md-12">

										<label for="title" class="col-md-2">Description</label>
										<div class="col-md-7">

											<?php echo $this->Form->textarea('Project.description', [ 'class'	=> 'form-control', 'id' => 'txa_description', 'escape' => true, 'rows' => 10, 'placeholder' => 'max chars allowed 500' ] ); ?>
											<span class="error" ></span>
											<?php echo $this->Form->error('Project.description'); ?>
										</div>

									</div>



							<div class="form-footer" style="text-align: center;  margin-bottom: 20px">
								<button class="btn btn-info" type="submit"><?php echo $text_val; ?></button>
								<button class="btn btn-success" type="reset">Cancel</button>

							</div>

							<?php echo $this->Form->end(); ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.wysihtml5-toolbar li {
	margin: 0 5px 1px 0 !important;
}
#cke_1_contents {
	border: 1px solid #cccccc !important;
}
#cke_2_contents, #cke_3_contents {
	border: 0px solid #cccccc !important;
}
.cke_top{
	border: none !important;
	padding: 2px 2px 0 2px !important;
	white-space:normal;
	-moz-box-shadow:0 1px 0 #fff inset;
	-webkit-box-shadow:0 1px 0 #fff inset;
	box-shadow:0 1px 0 #fff inset;
	background: none !important;
	background-image: -webkit-gradient(linear,left top,left bottom,from(#e9e9e9),to(#eeeeee)) !important;
	background-image:-moz-linear-gradient(top,#e9e9e9,#eeeeee) !important;
	background-image:-webkit-linear-gradient(top,#e9e9e9,#eeeeee) !important;
	background-image:-o-linear-gradient(top,#e9e9e9,#eeeeee) !important;
	background-image:-ms-linear-gradient(top,#e9e9e9,#eeeeee) !important;
	background-image:linear-gradient(top,#e9e9e9,#eeeeee) !important;
	filter:progid:DXImageTransform.Microsoft.gradient(gradientType=0,startColorstr='#e9e9e9',endColorstr='#eeeeee') !important;
}
.cke_toolgroup {
	border: none !important;
	margin: 0 1px 0 0 !important;
}
.cke_1.cke.cke_reset.cke_chrome.cke_editor_txa_title.cke_ltr.cke_browser_gecko  {
	border:  0px solid #cccccc !important;
}
.cke_toolgroup  {
	background: transparent !important;
}
.cke_bottom  {
	background: transparent !important;
	border-top:  1px solid #cccccc !important;
}

.cke_button_off {
	background: -moz-linear-gradient(center top , #f2f2f2, #cccccc) repeat scroll 0 0 #cccccc;
    box-shadow: 0 0 1px rgba(0, 0, 0, 0.3) inset;
	margin: 1px !important;
}
.cke_button_off:hover {
	background: none !important;
}

.cke_button_on {
	background: -moz-linear-gradient(center top , #f2f2f2, #cccccc) repeat scroll 0 0 #cccccc;
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.7) inset, 0 1px 0 rgba(0, 0, 0, 0.2);
	margin: 1px !important;
}
.cke_button_on:hover {
	background:-moz-linear-gradient(center top , #f2f2f2, #cccccc) repeat scroll 0 0 #cccccc !important;
}
/* cke_editable cke_editable_themed cke_contents_ltr */
</style>