
<?php echo $this->Html->css('projects/list-grid') ?>
<?php echo $this->Html->css('projects/dropdown'); ?>
<?php echo $this->Html->css('projects/animate'); ?>
<?php echo $this->Html->css('projects/templates'); ?>
<?php echo $this->Html->script('projects/create.workspace', array('inline' => true)); ?>
<script type="text/javascript">
	$('html').addClass('no-scroll');
</script>



<style type="text/css">
	.image-box-wrapper {
	  border: 1px solid #ccc;
	  border-radius: 3px;
	  display: inline-block;
	  padding: 10px;
	  width: 100%;
	}
	.image-box {
	    float: left;
	    margin-right: 15px;
	    margin-bottom: 3px;
	    margin-top: 3px;
	    padding: 5px;
	    border: 1px solid #ccc;
	    background-color: #eaeaea;
	    border-radius: 3px;
	}

	.image-box .confirm_doc_delete {
		border-radius: 50%;
	    padding: 0px 5px 2px 5px;
	    background-color: transparent;
	    border: none;
	    color: #fff !important;
	    display: inline-block;
	}
	.image-box .confirm_doc_delete .deleteblack{
		margin-top: 2px;
	}
	
	.image-box .imagename {
		display: inline-block;
		margin: 2px 5px 0 5px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 500px;
		vertical-align: top;
	}
	.no-scroll {
		overflow: hidden;
	}
	.data-wrapper {
		overflow: overlay;
		padding: 0;
		padding-top: 10px;
		border: 1px solid #dddddd;
		border-top: none;
	}

</style>

<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left" ><?php echo $page_heading; ?>
					<p class="text-muted date-time" ><?php echo $page_subheading; ?></p>
				</h1>
			</section>
		</div>
				<?php echo $this->Session->flash(); ?>

		<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header" style="background: rgb(239, 239, 239) none repeat scroll 0px 0px; cursor: move; border-bottom: none; border: 1px solid #dddddd; padding: 8px 10px;">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

							<div class="col-sm-6">

							</div>
							<div class="col-sm-6">
								<div class="right-btns">
									<a class="btn btn-success btn-sm save_template_updates tipText" title="Save and Stay on Page" data-savetype="update"  > Update </a>
									<a class="btn btn-success btn-sm save_template_updates" data-savetype="save"  > Save </a>
									<a class="btn btn-danger btn-sm" href="<?php echo Router::Url(array( "controller" => "templates", "action" => "create_workspace", $project_id, $template_category_id, 'admin' => FALSE ), true); ?>"  id="cancel_template"> Cancel </a>
								</div>

							</div>
                        </div>

						<div class="box-body data-wrapper" >
							<div class="tab-pane" id="template_tab">

								<div id="template_form" class="template_form">
									<?php echo $this->element('../Templates/partials/template_update_form'); ?>
								</div>

							</div>

						</div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<script type="text/javascript" >

	$(() => {

		// RESIZE MAIN FRAME
		($.adjust_resize = function(){
			$('.data-wrapper').animate({
				minHeight: (($(window).height() - $('.data-wrapper').offset().top) ) - 17,
				maxHeight: (($(window).height() - $('.data-wrapper').offset().top) ) - 17
			}, 1)
		})();
		// WHEN DOM STOP LOADING CHECK AGAIN FOR MAIN FRAME RESIZING
		var interval = setInterval(function() {
			if (document.readyState === 'complete') {
				$.adjust_resize();
				clearInterval(interval);
			}
		}, 1);
		// RESIZE FRAME ON SIDEBAR TOGGLE EVENT
		$(".sidebar-toggle").on('click', function() {
			$.adjust_resize();
			const fix = setInterval( () => { window.dispatchEvent(new Event('resize')); }, 300 );
			setTimeout( () => clearInterval(fix), 1500);
		})
		// RESIZE FRAME ON WINDOW RESIZE EVENT
		$(window).resize(function() {
			$.adjust_resize();
		})

	})
/* ******** SAVE TEMPLATE DATA *************** */
	$('body').delegate('.save_template_updates', 'click', function(e) {
        e.preventDefault()
		var errorFlag = false;
		$('.element_title', $form).each(function(){
			var val = $.trim($(this).val());
			if(val.length > 0 && val !== undefined){
				if(val.length > 0 && val.length < 5){
					$(this).parent().find('.error-message.text-danger').html('Minimum length must be 5 characters');
					errorFlag = true;
				}
			}
			else{
				$(this).parent().find('.error-message.text-danger').html('Title is required');
				errorFlag = true;
			}
		})

		$('.task_description', $form).each(function(){
			var val = $.trim($(this).val());
			if( val == "" || val == undefined ) {
				$(this).parent().find('.error-message.text-danger').html('Description is required');
				errorFlag = true;
			}
		})
		
		
		 
		$('.area-title', $form).each(function(){
			var val = $.trim($(this).val());
			if( val == "" || val == undefined || val.length < 1 ) {
				$(this).parent().find('.error-message.text-danger').html('Title is required');
				errorFlag = true;
			}
		})

		$('.area-purpose', $form).each(function(){
			var val = $.trim($(this).val());
			if( val == "" || val == undefined ) {
				$(this).parent().find('.error-message.text-danger').html('Purpose is required');
				errorFlag = true;
			}
		})
		

		if( !errorFlag ) {

			$(this).css('pointer-events','none');
			//$(".panel-body").removeClass('chars_left');

			var $this = $(this),
			$form = $('#modelFormSaveTemplate');

			// return;
			var buttonType = $(this).data('savetype');
			$.ajax({
				type:'POST',
				dataType:'JSON',
				data: $form.serialize(),
				url: $js_config.base_url + 'templates/save_template_updates/'+buttonType,
				global: false,
				success: function( response, status, jxhr ) {

					if(response.success) {
						if(buttonType == 'save'){
							window.location.href = $js_config.base_url + 'templates/create_workspace/' + $js_config.project_id + '/' + $('#TemplateRelationTemplateCategoryId').val()+'/'+response.type;
						}else{
							location.reload();

						}
					}
					else {

						$this.css('pointer-events','auto');
						if( ! $.isEmptyObject( response.content ) ) {

							$.each( response.content, function( ele, msg) {

								var $element = $form.find('[name="data[TemplateRelation]['+ele+']"]')
								var $parent = $element.parent();

								if( $parent.find('span.error-message.text-danger').length  ) {
									$parent.find('span.error-message.text-danger').text(msg);
									$parent.find('span.chars_left').text('');
								}
							})

						}
					}
				}
			})

		}

    });

</script>

<style type="text/css">
#btn_select_workspace {
    margin: 25px 0 0 !important;
}
.box-header .box-title {
	text-transform : capitalize;
}


.inner-view .date-time {

    font-weight: normal !important;

}


.select_msg {
   	     color: #bbbbbb;
    display: block;
    font-size: 30px;
    height: 50px;
    margin: 47px 0;
    text-align: center;
    vertical-align: middle;
    width: 100%;
    text-transform: uppercase;
}
#template_text {
    display: none;
	font-weight: normal;
}
#myTempletTabs {
    cursor: pointer !important;
	margin-bottom: 10px;
}
#myTempletTabs li a {
    border: none;
}
#myTempletTabs li a:hover {
    background: none;
	color: #333;
}
#myTempletTabs li:first-child  {
    border-right: 2px solid #4cae4c;
}

.right-btns {
	float: right;
	margin-top: 6px;
}
#myTempletTabs > li > a {
	padding: 5px 15px;
}

.templates_list {
	padding: 0;
}
.templates_list li {
	list-style: outside none none;
}
.templates_list li .box {
	border-bottom: 1px solid #67a028;
	border-left: 1px solid #67a028;
	border-right: 1px solid #67a028;
	max-height: 155px;
	min-height: 155px;
}
.templates_list ul {
	padding: 0;
}
.templates_list ul > li > div.box {
	transition: background-color 0.5s ease 0s;
}
.templates_list ul > li > div.box:hover {
	background: rgba(105, 165, 142, 0.2) none repeat scroll 0 0;
}

.template_form {
	padding: 5px 15px;
}
</style>