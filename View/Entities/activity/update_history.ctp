<?php
//echo $this->Html->css(array('styles-inner'));
echo $this->Html->css('projects/bs-selectbox/bootstrap-select.min');
echo $this->Html->css('projects/bootstrap-input');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-select', array('inline' => true));
?>
<script type="text/javascript" src="<?php echo SITEURL; ?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
	   var  dd = '0px';
	    setTimeout(function(){
	     dd = $('li.selected .bg-warning .hmgo').height()

        $('.selectpicker').selectpicker();

		setTimeout(function(){
		if(dd > 500){

        $('li.selected .bg-warning .scrolling-history').slimScroll({
            height:  '800px',
            size:'3px'
        });
		}
		},1000)

		},1500)
    })

</script>
<div class="row list-form border bg-warning nopadding col-sm-12">
    <a class="list-group-item clearfix open_form noborder-radius" href="javascript:void(0);">
        <span class="pull-left">
            <?php
            $folder1 = 'fa';
            if(isset($type_1) && $type_1 == 'element_links'){
               $folder = 'link';
            }else if(isset($type_1) && $type_1 == 'element_notes'){
                $folder = 'file-text';
            }else if(isset($type_1) && $type_1 == 'element_documents'){
                $folder = 'folder-o';
            }else if(isset($type_1) && $type_1 == 'element_mindmaps'){
                $folder = 'sitemap';
            }else if(isset($type_1) && $type_1 == 'element_decisions'){
                $folder = 'user-edit';
                $folder1 = 'fas';
            }else if(isset($type_1) && $type_1 == 'feedback'){
                $folder = 'bullhorn';
            }else if(isset($type_1) && $type_1 == 'votes'){
                $folder = 'inbox';
            }
			$folder = 'user-edit';
                $folder1 = 'fas';
            ?>
            <i class="<?php echo $folder1;?> fa-<?php echo $folder;?>"></i>
            &nbsp;<span class="updatetitle"><?php //echo ucfirst($type); ?> History</span>
        </span>

    </a>
    <div class="row history_search no-padding">

        <div class="col-sm-12">
            <div class="form-group activt">
                <label style="margin:9px 0; display:inline-block;" for="project ">See Activity <?php //echo $type_1;?></label>
                <div style="display:inline-block;width: 200px; margin:  0 5px">
                    <select id="seeactivityhistory-value"  class="form-control selectpicker">
                        <option value="all">All</option>
                        <option value="today">Today</option>
                        <option value="last_7_day">Last 7 Days</option>
                        <option value="this_month">This Month</option>
                    </select>
                </div>
                <div style="display:inline-block;">
                    <a id="<?php echo ucfirst($id); ?>" itemid="update-filter-<?php echo ucfirst($id); ?>" itemtype="<?php echo ucfirst($type_1); ?>" href="javascript:void(0);" class="btn btn-sm btn-success filter-history-btn">
                        Filter
                    </a>
                </div>
            </div>


        </div>


    </div>
    <div class="row history_head" style="/* border-bottom:none */">
            <div class="col-sm-1 text-bold history_head col-activity1">Member</div>
            <div class="col-sm-1 text-bold history_head col-activity2">&nbsp;</div>
            <div class="col-sm-3 text-bold history_head col-activity3">&nbsp;</div>
            <div class="col-sm-4 text-bold history_head col-activity4">Message</div>
            <div class="col-sm-3 text-bold history_head col-activity5">Updated</div>
<!--            <div class="col-sm-1 text-bold history_head">Action</div>-->
   </div>
    <div class="scrolling-history table-responsive hmgo" id="update-filter-<?php echo ucfirst($id); ?>">
        <?php include("update_filter_history.ctp")?>
    </div>
</div>

<style>
    .view_profile {
	/* background: #ed5b49 none repeat scroll 0 0;
	border: 1px solid transparent;
	border-radius: 3px; */
	color: #fff;
	margin: 0px;
	padding: 0 4px;
	float:left;
}
.view_profile:hover, .view_profile:focus {
/* 	background: #dd4b39 none repeat scroll 0 0;
	border: 1px solid #dd4b39; */
	color: #fff;
}
</style>
<script type="text/javascript">
    $(function () {
         $('#popup_modal').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal')//.find(".modal-content").html('<img src="../images/ajax-loader-1.gif" style="margin: auto;">');
        });


        $("body").delegate(".remove_history", "click", function (event) {
            event.preventDefault()
            //console.clear()
            var $that = $(this),
              data = $that.data(),
              dataId = data.id,
              dataAction = data.action,
              $row = $that.parents(".row:first"),
              remove_url = data.remote;
              //alert($row.html());
            $.when($.confirm({message: 'Are you sure you want to delete this history?', title: 'Delete confirmation'})).then(
              function () {

               $.ajax({
                type: 'POST',
                data: $.param({link_id: dataId}),
                dataType: 'JSON',
                url: remove_url,
                global: false,
                success: function (response, statusText, jxhr) {
                 if (response.success) { //checks
                  //bg-danger
                  $row.effect("size", {
                   to: {height: 0, backgroundColor: '#E82B70', border: '#d73925'}
                  }, 2000, function () {
                   $row.remove()
                  });
                 }
                },
                complete: function (data) {
                    }
                })

                },function (){});
            })


        $('body').delegate(".filter-history-btn", 'click', function(e){
        //$("body").delegate(".remove_history", "click", function (event) {
            event.preventDefault()
            var id = $(this).attr("id");
            var type = $(this).attr("itemtype");
            var itemid = $(this).attr("itemid");
            var seeactivityhistory = $('#seeactivityhistory-value option:selected').val();

            var $row = $(this);
            var urlV = $js_config.base_url + "entities/get_filter_history/id:" + id + "/type:" + type + "/seeactivityhistory:" + seeactivityhistory;

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
    });

</script>
<style>.history_gap{ padding-right:0 !important ;}</style>
