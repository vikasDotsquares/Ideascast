<?php
echo $this->Html->css('projects/dropdown', array('inline' => true));
echo $this->Html->css('projects/bootstrap-input');
echo $this->Html->css('projects/task_lists', array('inline' => true));
echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));
echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true));
echo $this->Html->css('projects/bootstrap-input');

echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
echo $this->Html->script('projects/plugins/marks/jquery.mark.min', array('inline' => true));



echo $this->Html->script('canvas', array('inline' => true));
echo $this->Html->script('canvas2image', array('inline' => true));

echo $this->Html->css('projects/wiki');
echo $this->Html->script('projects/wiki', array('inline' => true));
?>
<style>
.radio-left > .form-group{ margin-bottom : 0;}

.box-header.task-list-header{

    background: #f5f5f5 none repeat scroll 0 0;
    border-color: #d2d6de #d2d6de transparent;
    border-style: solid solid none;
    border-width: 1px 1px medium;
    padding: 10px 0 8px 5px;

}

#ProjectId {
    width: 400px;
    max-width: 100%;
    margin: 0 0 0 6px;
}
.idea-wiki-top-sec{
	word-break: break-all;
    overflow-wrap: break-word;
}
</style>
<script type="text/javascript">

    $(function () {

            $(document).ajaxSend(function (e, xhr) {
				e.stopImmediatePropagation();
                window.theAJAXInterval = 1;
                // $("#ajax_overlay_text").textAnimate("..........");
                $(".ajax_overlay_preloader").hide()
				e.preventDefault();


            })
            .ajaxComplete(function (e) {
					e.stopImmediatePropagation();
                    $(".ajax_overlay_preloader").hide();
					e.preventDefault();
				e.stopPropagation();

            });
			$.ajaxSetup({
                global: false,
                headers: {
                    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                }
            })

		setTimeout(function(){
			var type = window.location.hash.substr(1);
			if( type != '' && type == 'comments' ) {
				$(".tabtype a#2").trigger('click')
				setTimeout(function(){
					if($('#comments_list .tab-content').length > 0) {
						console.clear()
						console.log( $('#page-comment-<?php echo isset($this->request->params['named']['comment'])? $this->request->params['named']['comment'] : '';?>', $('#all.tab-pane.active')) )
						$('#comments_list .tab-content').animate(
						{
							scrollTop: (  $('#page-comment-<?php echo isset($this->request->params['named']['comment'])? $this->request->params['named']['comment'] : '';?>', $('#all.tab-pane.active')).offset().top - $('#comments_list .tab-content').offset().top + $('#comments_list .tab-content').scrollTop() )
						}, "slow");
					}
				},2000 )
			}
		},2500 )


		/* ******************************** BOOTSTRAP HACK *************************************
		 * Overwrite Bootstrap Popover's hover event that hides popover when mouse move outside of the target
		 * */
		var originalLeave = $.fn.popover.Constructor.prototype.leave;
		$.fn.popover.Constructor.prototype.leave = function(obj){
			console.log(obj)
			var self = obj instanceof this.constructor ?
			obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
			var container, timeout;

			originalLeave.call(this, obj);
			if(obj.currentTarget) {
				container = $(obj.currentTarget).data('bs.popover').tip()
				timeout = self.timeout;
				container.one('mouseenter', function(){
					//We entered the actual popover – call off the dogs
					clearTimeout(timeout);
					//Let's monitor popover content instead
					container.one('mouseleave', function(){
						$.fn.popover.Constructor.prototype.leave.call(self, self);
					});
				})
			}
		};
		/* End Popover
		 * ******************************** BOOTSTRAP HACK *************************************
		 * */

        $('#todo_model').on('hidden.bs.modal', function (event) {
            $(this).find('modal-content').html("")
            $(this).removeData('bs.modal')
        })
    })

</script>
<?php
    //echo $this->Html->script(array('ckeditor/ckeditor'));
?>
<script type="text/javascript" >
    $(function () {
        var $c_status;
    })
</script>
<style>
.description.contant_selection a {
	-webkit-touch-callout: none; /* iOS Safari */
	-webkit-user-select: none;   /* Chrome/Safari/Opera */
	-khtml-user-select: none;    /* Konqueror */
	-moz-user-select: none;      /* Firefox */
	-ms-user-select: none;       /* Internet Explorer/Edge */
	user-select: none;           /* Non-prefixed version, currently
	not supported by any browser */
}
.pophover {
 float: left;
}
.popover {
 z-index: 999999 !important;
}
.popover p {
 margin-bottom: 2px !important;
}

.popover p:first-child {
 font-weight: 600 !important;
 width: 170px !important;
}
.popover p:nth-child(2) {
  font-size: 11px;
}
</style>
<?php
if(isset($project_id) &&  !is_numeric($project_id)) {
    $project_id = 0;
}
?>

<script type="text/javascript" >
    $(function () {
        $.project_id = "<?php echo $project_id; ?>";
		$.fn.modal.Constructor.prototype.enforceFocus = function() {
			modal_this = this
			$(document).on('focusin.modal', function (e) {
				if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
				&& !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select')
				&& !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
					modal_this.$element.focus()
				}
			})
		};
    })
</script>
<!-- OUTER WRAPPER	-->
<div class="row">

    <!-- INNER WRAPPER	-->
    <div class="col-xs-12">

        <!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left"><?php echo $page_heading; ?>
                    <p class="text-muted date-time" style="padding: 6px 0">
                        <span ><?php echo $page_subheading; ?></span>
                    </p>
                </h1>
            </section>
        </div>
        <!-- END HEADING AND MENUS -->

		<span id="project_header_image">
			<?php
				if( isset( $project_id ) && !empty( $project_id ) ) {
					echo $this->element('../Projects/partials/project_header_image', array('p_id' => $project_id));
				}
			?>
		</span>


        <!-- MAIN CONTENT -->
        <div class="box-content wiki">

            <div class="row ">
                <div class="col-xs-12">
                    <div class="bg-white">
                    <?php echo $this->Session->flash(); ?>
                        <!-- CONTENT HEADING -->
        <div class="box-header task-list-header" style="background: #f1f3f4 none repeat scroll 0 0;border-color: #d2d6de;border-style: solid;border-width: 1px;cursor: move;padding: 10px 0 8px 5px; border-radius: 0">

                            <!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- END MODAL BOX -->

                            <!-- FILTER BOX -->

                            <?php
                            //echo $project_id;die;
                            if (isset($project_id) && !empty($project_id)) {
                                $cky = $this->requestAction('/projects/CheckProjectType/' . $project_id . '/' . $this->Session->read('Auth.User.id'));
                                ?>
                                <script type="text/javascript" >
                                    $(function () {
                                        $c_status = '<?php echo $cky; ?>';

                                        if ($c_status == 'm_project') {
                                            $('[for=project_type_my]').trigger('click');
											$('#project_type_my').trigger('change');
                                        } else if ($c_status == 'r_project') {
                                            $('[for=project_type_rec]').trigger('click');
											$('#project_type_rec').trigger('change');
                                        } else if ($c_status == 'g_project') {
                                            $('[for=project_type_group]').trigger('click');
											$('#project_type_group').trigger('change');
                                        }

                                        $(".fancy_input").click(function (e) {
                                            $thisdata = $(this);

                                            setTimeout(function () {

                                                $("#project_report_link").attr("href", $js_config.base_url + "projects/reports/" + $("#ProjectId").val())
                                                $("#dashboard_link").attr("href", $js_config.base_url + "projects/objectives/" + $("#ProjectId").val())


                                                if ($thisdata.attr("id") == 'project_type_my') {
                                                    $c_status = 'm_project';
                                                    $("#show_resources_link").attr("href", $js_config.base_url + "users/projects/m_project:" + $("#ProjectId").val())
                                                }
                                                else if ($thisdata.attr("id") == 'project_type_rec') {
                                                    $("#show_resources_link").attr("href", $js_config.base_url + "users/projects/r_project:" + $("#ProjectId").val())
                                                    $c_status = 'r_project';
                                                }
                                                else if ($thisdata.attr("id") == 'project_type_group') {
                                                    $("#show_resources_link").attr("href", $js_config.base_url + "users/projects/g_project:" + $("#ProjectId").val())
                                                    $c_status = 'g_project';
                                                }
                                            }, 2000)
                                        })
                                    })

                                </script>
                            <?php }else{ ?>
                                <?php //echo 'hii';die;?>
                                <script type="text/javascript" >
                                    $(function () {
                                        $('#project_type_my').trigger('change');
                                    })
                                </script>
                            <?php } ?>

                            <div class="radio-left  project_selection studios-row-h">
								<label >Projects </label>
                                <label class="custom-dropdown" style="margin-top: 5px;">
										<select id="ProjectId" name="project_id" class="aqua" placeholder="Select Project">
											<?php if( isset($list_projects) && !empty($list_projects) ){
											foreach($list_projects as $key => $pval ){ ?>
											<option value="<?php echo $key?>" <?php if( $project_id == $key ){?> selected="selected"<?php } ?> ><?php echo $pval;?></option>
										<?php }
										}?>
										</select>

                                </label>
                                <!-- <span class="loader-icon fa fa-spinner fa-pulse"></span> -->
                            </div>

                        </div>
                        <div class="box noborder margin-top" id="box_body"></div>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- MODAL BOX WINDOW -->
	<div class="modal modal-success fade " id="popup_modal_profile" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>
<!-- END MODAL BOX -->

<!-- Modal Large -->
<div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal modal-success fade" id="modal_create_blogpost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>
<!-- MODAL BOX WINDOW -->
<div class="modal modal-success fade " id="popup_modal_profiles" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-lg"></div>
    </div>
</div>
<?php if(isset($this->request->params['named']['wiki_page']) && !empty($this->request->params['named']['wiki_page'])){ ?>
<script type="text/javascript" >
$(function() {
    setTimeout(function(){
        $("#page_select_default_" + <?php echo $this->request->params['named']['wiki_page'];?>).trigger("click");
    }, 5000);
})
</script>
<?php } ?>
<?php if(isset($this->request->params['named']['comment']) && !empty($this->request->params['named']['comment'])){ ?>
<script type="text/javascript" >
$(function() {
    setTimeout(function(){
        $(".tabtype").find("li #2").trigger("click");
    }, 5000);
})
</script>
<?php } ?>

<!-- END MODAL BOX -->
<script type="text/javascript" >
  $(function() {
	$('[id=ProjectId]').trigger('change');

    // $( "#comment-page-accordion-wiki" ).sortable();
    $( "#comment-page-accordion-wiki" ).disableSelection();

	  $.wiki_sorting = function() {
			console.log('test')
		  $( "#comment-page-accordion-wiki" ).sortable({
			  connectWith: ".idea-buckets",
			  scroll: true,
			  cursor: "move",
			  start: function(event, ui) {
				  ui.item.data('start_pos', ui.item.index());
			  },
			  stop: function(event, ui) {
				  var result = '',
				  total_panels = $('#comment-page-accordion-wiki .wiki-block').length,
				  details = [];
				  $('#comment-page-accordion-wiki .wiki-block').each(function(i) {
					  $(this).data('order', (i+1));

					  var detail_data = {};
					  detail_data[$(this).data('order')] = $(this).data('wid');
					  details.push(detail_data)
				  });

				  console.log(details)
				  var start_pos = ui.item.data('start_pos');
				  if (start_pos != ui.item.index()) {
                                        $("#ajax_overlay").show()
                                        if( !$.isEmptyObject(details) ) {
                                            $.post( $js_config.base_url + 'wikies/wiki_page_sorting', $.param({'sort_orders': details}), function( data ){
                                                  $("#ajax_overlay").hide()
                                            });
                                        }
				  }
			  }
		  })
	  }

  });
</script>
