<style>

#element_options {
	-webkit-transition: all 0.3s ease-in-out;
	-moz-transition: all 0.3s ease-in-out;
	-o-transition: all 0.3s ease-in-out;
	transition: all 0.3s ease-in-out;
	margin: -19px 15px 0 0;
}
.sticky {
	left: auto;
	padding: 0;
	position: fixed;
	right: 15px;
	top: 180px;
	z-index: 99999;

-webkit-transition: all 0.3s ease-in-out;
-moz-transition: all 0.3s ease-in-out;
-o-transition: all 0.3s ease-in-out;
transition: all 0.3s ease-in-out;

	margin: -7px 15px 0 0;


}

div.el-icons span.btn input.error {
    border: 1px solid #CB0000;
}
</style>

<?php echo $this->Html->css('projects/alert'); ?>
<?php echo $this->Html->css('projects/ripple'); ?>
<?php //echo $this->Html->script('projects/bootbox', array('inline' => true)) ?>

<?php echo $this->Html->script('/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5', array('inline' => true)); ?>
<?php echo $this->Html->script('projects/ripple', array('inline' => true)); ?>
<?php echo $this->Html->script('projects/colored_tooltip', array('inline' => true)); ?>
<?php echo $this->Html->script('projects/elements_library', array('inline' => true)) ?>
<?php echo $this->Html->script('drag-drop-context/context-menu', array('inline' => true)) ?>
<?php echo $this->Html->script('projects/contextStandby', array('inline' => true)) ?>

<?php echo $this->Html->script('projects/manage_elements', array('inline' => true)); ?>


<script type="text/javascript" >
$(function() {

	$("body").delegate('#close_options', 'click', function( event ) {

		$("#element_options").inOutOptions({ show: false, element_id: 0 })
	})

	$(window).scroll(function() {

		if ($(this).scrollTop() > 10){
			$('#element_options').addClass("sticky");
		}
		else{
			$('#element_options').removeClass("sticky");
		}
	});

	$(".edit_element").on( 'click', function( event ) {
		event.preventDefault();

		var $this = $(this);
		var data = $this.data();
		var url = data.remote
		console.log(url + $this.parent().find("input#element_id").val())
		window.location.replace( url + $this.parent().find("input#element_id").val() )
		// window.location.href = url + $this.parent().find("input#element_id").val()
	})

	$("body").delegate('.sort_order_form .up', 'click', function( event ) {
		var $input = $(this).parent().parent().find('input[name=sort_order]');
		$input.val( parseInt($input.val(), 10) + 1);

		$input.trigger("change")
		$(this).update_sort_order();
	});

	$("body").delegate('.sort_order_form .down', 'click', function( event ) {
		var $input = $(this).parent().parent().find('input[name=sort_order]');
		$input.val( parseInt($input.val(), 10) - 1);

		$input.trigger("change")
		$(this).update_sort_order();
	});

	$("body").delegate('.sort_order_form input.sort_order_input', 'change', function( event ) {
		var $up = $(this).next('.up');
		var $down = $(this).prev('.down');

		if ( parseInt( $(this).val() ) == 1 ) {
			$down.prop("disabled", true)
			if (typeof $up.attr('disabled') !== typeof undefined && $up.attr('disabled') !== false) {
				$up.removeAttr("disabled")
			}
		}
		else if ( parseInt( $(this).val() ) == 1000 ) {
			$up.prop("disabled", true)
			if (typeof $down.attr('disabled') !== typeof undefined && $down.attr('disabled') !== false) {
				$down.removeAttr("disabled")
			}
		}
		else {
			if (typeof $up.attr('disabled') !== typeof undefined && $up.attr('disabled') !== false) {
				$up.removeAttr("disabled")
			}
			if (typeof $down.attr('disabled') !== typeof undefined && $down.attr('disabled') !== false) {
				$down.removeAttr("disabled")
			}
		}

		// SET SORT ORDER NUMBER TO DATABASE WITH AJAX
		$order = parseInt( $(this).val() );


	});

	$.fn.update_sort_order = function (event) {

		var $t = $(this)
		var $form_group = $t.parent().parent();// sort_order_form
		var url = $t.data('remote');
		var $td = $t.parents("td:first");

		var $prev = $form_group.find(".element_before");
		var $next = $form_group.find(".element_after");

		var $input = $form_group.find('input[name=sort_order]');

		var data = $.param({
			'data[Element][sort_order]': parseInt( $input.val() ),
			'data[Element][prev_element]': parseInt( $prev.val() ),
			'data[Element][next_element]': parseInt( $next.val() ),
			'data[Element][area_id]': $form_group.find('input[name=area_id]').val()
		});
		// return;
		$.ajax({
			type:'POST',
			data: data,
			dataType: 'JSON',
			url: url,
			global: true,
			success: function( response, status, jxhr ) {
				if( status == 'success' ) {
					//
					// console.log(response.content.queries);

					var inputEle = [];

					setTimeout( function() {
						var cell_id = $td.attr("id");
						var cell_body = $td.find(".box-body.clearfix.in");

						var dataValue = { data: response.content.elements_details };

						cell_body.html("")
						$.script_replace(dataValue, $('#elementTemplate'), cell_body)
						$td.wrap_elements();


					}, 1000);
				}
				else {
					console.log('error');
				}
			}
		});
	}

	$("body").delegate(".close", 'click', function(event) {
		event.preventDefault();
		var $alertMB = $(this).parents("#alertMessageBox")
		$alertMB.hide('slow', function(){ $alertMB.remove() })
	})

})
</script>


<div id="overlay">
      <div id="screen"></div>
      <div id="dialog-star" class="dialog">
        <div class="label-dialog text-green"><i class="fa fa-5x fa-star"></i></div>
        <div class="body-dialog">
          <p>The Star dialog is <span>modeless</span>. You can click on the check mark or anywhere outside of the dialog's body to clear it.</p>
        </div>
        <div class="ok-dialog"><i class="fa fa-fw fa-check fa-2x"></i></div>
      </div>
      <div id="dialog-anchor" class="dialog modal">
        <div class="label-dialog"><i class="icon-anchor"></i></div>
        <div class="body-dialog">
          <p>The Anchor dialog is <span>modal</span>. You must click on the check mark to acknowledge and clear it.</p>
        </div>
        <div class="ok-dialog"><i class="icon-ok-sign"></i></div>
      </div>
      <div id="dialog-beaker" class="dialog">
        <div class="label-dialog"><i class="icon-beaker"></i></div>
        <div class="body-dialog">
          <p>The Beaker dialog is <span>modeless</span>. You can click on the check mark or anywhere outside of the dialog's body to clear it.</p>
        </div>
        <div class="ok-dialog"><i class="icon-ok-sign"></i></div>
      </div>
      <div id="dialog-bug" class="dialog modal">
        <div class="label-dialog"><i class="icon-bug"></i></div>
        <div class="body-dialog">
          <p>The Bug dialog is <span>modal</span>. You must click on the check mark to acknowledge and clear it.</p>
        </div>
        <div class="ok-dialog"><i class="icon-ok-sign"></i></div>
      </div>
    </div>


<div class="row">
	<div class="col-xs-12">

		<div class="row">
			<section class="content-header clearfix">
				<h1 class="pull-left"><?php echo $data['page_heading'] ?>

					<p class="text-muted date-time">
						<span>Design your project with the difference.</span>
					</p>
				</h1>
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
						<!-- CONTENT HEADING -->
                        <div class="box-header nopadding noborder" style="background: none repeat scroll 0 0 #ecf0f5; height: 15px">

							<div class="btn-group pull-right" style="opacity: 0; display: none;" id="element_options" >

								<a href="#" class="btn btn-success btn-sm edit_element tipText" title="<?php echo tipText('Edit') ?>" data-remote="<?php echo SITEURL.'entities/update_element'; ?>/" ><i class="fa fa-pencil"></i></a>

								<input type="hidden" name="element_id" id="element_id" value="" />

								<button  title="<?php echo tipText('Cut') ?>" id="btn_cut" class="btn btn-success btn-sm btn_cut tipText"><i class="fa fa-cut"></i></button>
								<button  title="<?php echo tipText('Copy') ?>" id="btn_copy" class="btn btn-success btn-sm tipText btn_copy"><i class="fa fa-copy"></i></button>
								<button  title="<?php echo tipText('Paste') ?>" id="btn_paste" class="btn btn-success btn-sm tipText btn_paste"><i class="fa fa-paste"></i></button>
								<span class="btn btn-success btn-sm color_box_wrapper" >
									<span class="color_bucket tipText" title="<?php echo tipText('Edit Colors') ?>" ><i class="fa fa-paint-brush"></i></span>
									<div class="display_none el_colors">
										<div class="colors btn-group">
											<a href="#" data-color="panel-red" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Red"><i class="fa fa-square text-red"></i></a>
											<a href="#" data-color="panel-blue" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Blue"><i class="fa fa-square text-blue"></i></a>
											<a href="#" data-color="panel-maroon" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Maroon"><i class="fa fa-square text-maroon"></i></a>
											<a href="#" data-color="panel-aqua" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Aqua"><i class="fa fa-square text-aqua"></i></a>
											<a href="#" data-color="panel-yellow" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Yellow"><i class="fa fa-square text-yellow"></i></a>
											<a href="#" data-color="panel-orange" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Orange"><i class="fa fa-square text-orange"></i></a>
											<a href="#" data-color="panel-teal" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Teal"><i class="fa fa-square text-teal"></i></a>
											<a href="#" data-color="panel-purple" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Purple"><i class="fa fa-square text-purple"></i></a>

											<a href="#" data-color="panel-navy" data-remote="<?php echo SITEURL.'entities/update_color'; ?>/" class="btn btn-default btn-xs el_color_box" title="Navy"><i class="fa fa-square text-navy"></i></a>
										</div>
									</div>
								</span>
								<button  title="<?php echo tipText('Close Options') ?>" class="btn btn-danger btn-sm tipText" id="close_options"><i class="fa fa-times"></i></button>
							</div>

							<div id="myPopoverModal" class="popover popover-default">
								<div class="popover-content">
								</div>
								<div class="popover-footer">
									<button type="submit" class="btn btn-sm btn-primary">Submit</button><button type="reset" class="btn btn-sm btn-default">Reset</button>
								</div>
							</div>

							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
								</div>
                            </div>
							<!-- END MODAL BOX -->


                        </div>
						<!-- END CONTENT HEADING -->


					<div class="box-body border-top padding-top">

						<div id=""></div>
						<div id="workspace">
							<?php
								// LOAD PARTIAL WORKSPACE LAYOUT FILE FOR LOADING DYNAMIC WORKSPACE AREAS
								echo $this->element('../Projects/partials/admin_workspace_layou');
							?>
						</div>

					</div><!-- /.box-body -->
					</div><!-- /.box -->
				</div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
	/* .alert-box {
		border-radius: 0 0 10px 10px;
		color: #555555;
		font-family: Tahoma,Geneva,Arial,sans-serif;
		font-size: 11px;
		left: 50%;
		margin: 0 auto;
		padding: 10px 36px;
		position: absolute;
		top:  0px;
		width: 50%;
		z-index: 999999;
	    border-top: medium none !important;
	}
	.alert-box span {
		font-weight:bold;
		text-transform:uppercase;
	}
	.error {
		background-color:#ffecec ;
		border:1px solid #f5aca6;
	}
	.success {
		background-color:#e9ffd9 ;
		border:1px solid #a6ca8a;
	}
	.warning {
		background-color:#fff8c4 ;
		border:1px solid #f2c779;
	}
	.notice {
		background-color:#e3f7fc ;
		border:1px solid #8ed9f6;
	} */

.center_screen {
	width: auto;
	height: auto;
	position: fixed;
	left: 40%;
	top: 50%;
	margin-left: -150px;
	margin-top: -150px;
	z-index: 999999;
}
    </style>
<!--
	<div class="alert-box error"><i class="fa fa-fw fa-ban"></i><span>error: </span>Write your error message here.</div>
	<div class="alert-box success"><i class="fa fa-fw fa-check"></i><span>success: </span>Write your success message here.</div>
	<div class="alert-box warning"><i class="fa fa-fw fa-exclamation"></i><span>warning: </span>Write your warning message here.</div>
	<div class="alert-box notice"><i class="fa fa-fw fa-info"></i><span>notice: </span>Write your notice message here.</div>
	-->

		<!--
<div class="center_screen" id="alertMessageBox">
	<div class="columns grid_30 ">
		<div class="message alert style-4 rounded bordered">
			Message alert box<a class="close fade" href="#">x</a>
		</div>
		<br>
		<div class="success alert style-4 rounded bordered">
			Success alert box<a class="close slide" href="#">x</a>
		</div>
		<br>
		<div class="error alert style-4 rounded bordered">
			Error alert box<a class="close fade-slide" href="#">x</a>
		</div>
		<br>
		<div class="info alert style-4 rounded bordered">
			Info alert box<a class="close scale" href="#">x</a>
		</div>
		<br>
		<div class="warning alert style-4 rounded bordered">
			Warning alert box <a class="close fade" href="#">x</a>
		</div>
		<br>
		<div class="question alert style-4 rounded bordered">
			Question alert box<a class="close slide" href="#">x</a>
		</div>
		<div class="warning alert style-4 rounded bordered">
			Success alert box<a class="close slide" href="#">x</a>
		</div>
	</div>
</div>
			-->