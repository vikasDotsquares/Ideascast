<?php echo $this->Html->css('projects/list-grid') ?>
<?php echo $this->Html->css('projects/dropdown'); ?>
<?php echo $this->Html->css('projects/animate'); ?>

<?php echo $this->Html->script('projects/create.workspace', array('inline' => true)); ?>


<style type="text/css">
	#btn_select_workspace {
	    margin: 25px 0 0 !important;
	}
	.box-header .box-title {
		text-transform : capitalize;
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
	.text-muted.date-time {
		font-weight: normal;
	}

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
	.image-box .imagename {
		display: inline-block;
		margin: 0 5px;
	}
	.image-box .confirm_doc_delete {
		border-radius: 50%;
	    padding: 0px 5px 2px 5px;
	    background-color: transparent;
	    border: none;
	    color: #fff !important;
	    display: inline-block;
	}

	.OrderingField .error-message {
		display:block;
	}
	.elements_wrapper .panel-body .form-group .element-title-h{
		width:auto;
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
	@media (max-width:479px) {
		.form-groups .custom-dropdown{
		   width:62% !important;
		}
	}

</style>
<script type="text/javascript">
$(function() {

	$("#no_of_zones option:eq(0)").prop('selected', true);

})
</script>
<?php  $cid = (isset($this->params['pass']['1']) && !empty($this->params['pass']['1'])) ? $this->params['pass']['1'] : 0; ?>
<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left" style="min-height: 50px;"><?php echo $page_heading; ?>

					<p class="text-muted date-time" id="project_text"><?php echo $page_subheading; ?></p>
				</h1>
			</section>
		</div>


		<div class="box-content">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header" style="background: rgb(239, 239, 239) none repeat scroll 0px 0px; cursor: move; border-bottom: none; border: 1px solid #dddddd; padding: 8px 10px; ">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->

							<div class="col-sm-6">
								<div class="form-groups">
									<!-- <label>Area/Zones: </label> -->
									<label class="custom-dropdown" style="width: 40%; margin-top: 3px;">
										<select class="aqua"   name="no_of_zones" id="no_of_zones" data-cid="<?php echo $cid; ?>">
											<option value="">Select Areas</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
											<option value="11">11</option>
											<option value="12">12</option>
											<option value="14">14</option>
											<option value="15">15</option>
										</select>
									</label>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="right-btns">
									<a class="btn btn-success btn-sm disabled" id="save_template"> Add </a>
									<a class="btn btn-danger btn-sm" href="<?php echo Router::Url(array( "controller" => "templates", "action" => "create_workspace",0,'admin' => FALSE ), true); ?>" id="cancel_template"> Cancel </a>
								</div>

							</div>
                        </div>

						<div class="box-body data-wrapper" >
							<div class="tab-pane" id="template_tab">
								<ul id="new_templates" class="clearfix templates_list">
									<div class="select_msg" > SELECT AREAS </div>
								</ul>

								<div id="template_form" class="template_form">

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
	//
	$('html').addClass('no-scroll');
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

/* ******** NO OF ZONES SELECT BOX CHANGE EVENT *************** */
	$('body').delegate('#no_of_zones', 'change', function(e) {
        e.preventDefault()

		var $this = $(this),
			zones = $this.val();
		var cid = $this.data('cid');

		$('#template_form').html("")
		if( zones > 0 && zones != "" && zones !== undefined ) {
			$.ajax({
				type:'POST',
				// dataType:'JSON',
				data: {},
				url: $js_config.base_url + 'templates/get_selected_templates/' + zones +'/'+ cid,
				global: false,
				success: function( response, status, jxhr ) {
					$('#new_templates').html(response)
				}
			})
		}
		else {
			$('#new_templates').html('<div class="select_msg" > SELECT ZONES IN WORKPLACE </div>')
			$('#save_template').addClass('disabled');
		}
    });

/* ******** NO OF ZONES SELECT BOX CHANGE EVENT *************** */
	$('body').delegate('#save_template', 'click', function(e) {
        e.preventDefault()

		var $this = $(this),
			$form = $('#modelFormSaveTemplate');

		$this.attr('disabled','disabled');
		// return;

		var errorFlag = false;
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


		$('.element_title', $form).each(function(){
			var val = $.trim($(this).val());
			if(val.length > 0 && val !== undefined){
				if(val.length < 5){
					$(this).parent().find('.error-message.text-danger').html('Minimum length must be 5 characters');
					// $(this).focus();
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

		if( !errorFlag ) {
			$.ajax({
				type:'POST',
				dataType:'JSON',
				data: $form.serialize(),
				url: $js_config.base_url + 'templates/save_template',
				global: true,
				success: function( response, status, jxhr ) {
					if(response.success) {
						window.location = $js_config.base_url + 'templates/create_workspace/0/' + $('#TemplateRelationTemplateCategoryId').val()+'/'+response.type
						//window.location.reload();
					}
					else {
						$this.removeAttr('disabled');
						if( ! $.isEmptyObject( response.content ) ) {
							$.each( response.content, function( ele, msg) {

								var $element = $form.find('[name="data[TemplateRelation]['+ele+']"]')
								var $parent = $element.parent();

								if( $parent.find('span.error-message.text-danger').length  ) {
									$parent.find('span.error-message.text-danger').text(msg)
								}
							})

						}
					}
				}
			})
		} else {
			$this.removeAttr('disabled');
		}

    });
$(function() {
	$('a[href="#"],a[href=""]').attr('href', 'javascript:;');
})
</script>
