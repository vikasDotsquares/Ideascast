
<?php echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true)); ?>
<?php echo $this->Html->css('projects/bootstrap-input') ?>
<style>
/* ============================================================
SWITCH RADIO BUTTONS
============================================================ */

.switch {
    margin: 0;
    display: inline-block;
}

.cmn-toggle {
    position: absolute;
    margin-left: -9999px;
    visibility: hidden;
}

.switch label {
    margin: 0;
}

.cmn-toggle + label {
    display: block;
    position: relative;
    cursor: pointer;
    outline: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

input.cmn-toggle-round + label {
    padding: 2px;
    width: 62px;
    height: 24px;
    background-color: #dddddd;
    -webkit-border-radius: 60px;
    -moz-border-radius: 60px;
    -ms-border-radius: 60px;
    -o-border-radius: 60px;
    border-radius: 60px;
}

input.cmn-toggle-round + label:before,
input.cmn-toggle-round + label:after {
    display: block;
    position: absolute;
    top: 0;
    left: 1px;
    bottom: 0;
    content: "";
}

input.cmn-toggle-round + label:before {
    right: 1px;
    background-color: #C9302C;
    -webkit-border-radius: 60px;
    -moz-border-radius: 60px;
    -ms-border-radius: 60px;
    -o-border-radius: 60px;
    border-radius: 60px;
    -webkit-transition: background 0.4s;
    -moz-transition: background 0.4s;
    -o-transition: background 0.4s;
    transition: background 0.4s;
}

input.cmn-toggle-round + label:after {
    width: 25px;
    background-color: #fff;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    -ms-border-radius: 100%;
    -o-border-radius: 100%;
    border-radius: 100%;
    -webkit-box-shadow: -1px 1px 2px 1px rgba(0, 0, 0, 0.3);
    -moz-box-shadow: -1px 1px 2px 1px rgba(0, 0, 0, 0.3);
    box-shadow: -1px 1px 2px 1px rgba(0, 0, 0, 0.3);
    -webkit-transition: all 0.4s;
    -moz-transition: all 0.4s;
    -o-transition: all 0.4s;
    transition: all 0.4s;
}

input.cmn-toggle-round:checked + label:before {
    background-color: #449d44;
}

input.cmn-toggle-round:checked + label:after {
    margin-left: 35px;
}

input.cmn-toggle-round:disabled + label {
    cursor: not-allowed;
}

/* ============================================================
SWITCH RADIO BUTTONS END
============================================================ */
.setting-section {
    display: block;
    margin: 0;
    padding: 0 15px;
}
.setting-section .setting-label {
    display: inline-block;
    position: relative;
    top: -7px;
    margin-right: 10px;
}
</style>
<div class="modal modal-success fade " id="Recordedit" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content"></div>
	</div>
</div><!-- /.modal -->
<!-- MODAL BOX WINDOW -->
<div class="modal modal-success fade " id="modal_box" tabindex="-1" role="dialog" aria-labelledby="modalBoxModelLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>
<!-- END MODAL BOX -->
<div class="row">
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
                <h1 class="pull-left"><?php echo $page_heading; ?>
                    <p class="text-muted date-time" style="padding: 4px 0px; text-transform: none;">
                        <span style="text-transform: none;"><?php echo $page_subheading; ?></span>
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

						<div class="row" id="Recordlisting">
				            <div class="col-xs-12">
								<div class="box no-box-shadow box-success">
									<div class="box-header">
										<!-- <div class="col-xs-12 col-sm-12 domain-setting">
											<div class="pull-right padright">
												<a class="btn btn-success btn-sm "> Save </a>
											</div>
										</div> -->
									</div><!-- /.box-header -->
									<div class="box-body" style="min-height: 800px;">
										<div class="setting-section">
											<label class="setting-label">OpusCast Communication: </label>
											<div class="switch">
												<input type="checkbox" class="cmn-toggle cmn-toggle-round" id="chat_capability" name="chat_capability" <?php if($chat_capability){ ?> checked="checked" <?php } ?> >
												<label for="chat_capability"></label>
											</div>
										</div>
									</div><!-- /.box-body -->
								</div><!-- /.box -->
							</div>
						</div>
					</section>
					</div>
				</div>
			</div>
		 </div>
	   </div>
	</div>
 </div>
<script type="text/javascript" >


$(function(){
	$('body').on('change', '.cmn-toggle', function(event) {
		event.preventDefault();
		var chat_capability = ($(this).prop('checked')) ? 1 : 0;
		$.ajax({
			url: $js_config.base_url + 'organisations/update_chat_capability',
			type: 'POST',
			dataType: 'json',
			data: {chat_capability: chat_capability},
			success: function(response) {
				console.log("success", response);
			}
		});

	});



})
</script>