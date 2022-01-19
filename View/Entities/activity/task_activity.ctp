<div class="link_forms clearfix" id="accordion">
    <div class="list-form border bg-warning nopadding" style="display:inline-block;width:100%;">
        <a class="accordion-toggle list-group-item clearfix open_form noborder-radius" data-toggle="collapse" data-parent="#accordion" href="#task-activity">
            <span class="pull-left"><i class="fas fa-user-edit"></i> History  (Task Created: <?php echo ( isset($this->data['Element']['created']) && !empty($this->data['Element']['created'])) ? _displayDate($this->data['Element']['created'] ) : 'N/A';  ?>) </span>
            <span class="pull-right"><!--<i class="glyphicon glyphicon-plus"></i></span>-->
        </a>
        <div class="see-activity-list">
         <div id="task-activity" class="panel-collapse collapse history_update_new table-rows">
                <div class="row history_search no-padding">
                    <div class="col-sm-12">
                        <div class="form-group activt" style="margin:5px 0 0 0;">
                            <label style="margin:9px 0; display:inline-block;" for="project ">See Activity <?php //echo $type_1; ?></label>
                            <div style="display:inline-block;width: 200px; margin:  0 5px">
                                <select id="seeactivityhistory" class="form-control selectpicker">
                                    <option value="all">All</option>
                                    <option value="today">Today</option>
                                    <option value="last_7_day">Last 7 Days</option>
                                    <option value="this_month">This Month</option>
                                </select>
                            </div>
                            <div style="display:inline-block;">
                                <?php //pr($this->params);?>
                                <a id="<?php echo ucfirst($element_id); ?>" itemid="update-filter-<?php echo ucfirst($element_id); ?>" itemtype="element_tasks" href="javascript:void(0);" class="btn btn-sm btn-success filter-history-btn">
                                    Filter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row history_head">
                    <div class="col-sm-1 text-bold history_head col-activity1">User</div>
                    <div class="col-sm-1 text-bold history_head col-activity2">&nbsp;</div>
                    <div class="col-sm-3 text-bold history_head col-activity3">&nbsp;</div>
                    <div class="col-sm-4 text-bold history_head col-activity4">Message</div>
                    <div class="col-sm-3 text-bold history_head col-activity5">Updated</div>
                </div>
            <?php
            $history_lists = $this->requestAction(array("controller"=>"entities","action"=>"get_history" ,$element_id,"element_tasks"));
            ?>
            <div class="row scrolling-history table-responsive hmgo " id="update-filter-<?php echo ucfirst($element_id); ?>">
                    <?php include("update_filter_history.ctp") ?>
            </div> </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(function () {

		/* ******************************** BOOTSTRAP HACK *************************************
		 * Overwrite Bootstrap Popover's hover event that hides popover when mouse move outside of the target
		 * */
		var originalLeave = $.fn.popover.Constructor.prototype.leave;
		$.fn.popover.Constructor.prototype.leave = function(obj){
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
<script type="text/javascript">
    var selectIds = $('#task-activity');
    $(function ($) {
        selectIds.on('show.bs.collapse hidden.bs.collapse', function () {
            $(this).prev().find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
        })
    });
    $(document).ready(function () {
		$('body').delegate('#accordion', 'click', function(event){
			event.preventDefault();
			var offset = $('#accordion').offset(),
			top = offset.top - 70;
			setTimeout(function(){
				$('html, body').animate({
					scrollTop: top
				}, 700);
			}, 700);
		})
        $('.selectpicker').selectpicker();


		$('body').delegate("#accordion", 'click', function(e){
			setTimeout(function(){
				var h = $('li.selected #task-activity .hmgo').height()
				if(h > 100){

                    $('li.selected #task-activity .scrolling-history').slimScroll({
                        height:  	'500px',
                        size:		'3px'
                    });
				}
			}, 500)
		})


        //$(".filter-history-btn").click(function (event) {
        $('body').delegate(".filter-history-btn", 'click', function(e){
        //$("body").delegate(".remove_history", "click", function (event) {
            event.preventDefault()
            var id = $(this).attr("id");
            var type = $(this).attr("itemtype");
            var itemid = $(this).attr("itemid");
            var filterbyhistory = $('#filterbyhistory').val();
            var seeactivityhistory = $('#seeactivityhistory').val();
            var $row = $(this);
            var urlV = $js_config.base_url + "entities/get_filter_history/id:" + id + "/type:" + type + "/filterbyhistory:" + filterbyhistory + "/seeactivityhistory:" + seeactivityhistory;

            $.ajax({
                url: urlV,
                async: false,
                global: false,
                beforeSend: function () {
                    //$(".ajax_overlay_preloader").fadeIn();
                },
                complete: function () {
                    // $(".ajax_overlay_preloader").fadeOut();
                },
                success: function (response) {
                    $("#" + itemid).html(response).fadeIn();
                    return false;
                }
            });

        })
    })

</script>







