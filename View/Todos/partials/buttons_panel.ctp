 
<?php
if( isset($do_list_id) && !empty($do_list_id) ) {
	$user_data = $this->ViewModel->get_user_data($data['DoList']['user_id']);

?>	
<?php
if( isset($data['DoList']) && $data['DoList']['parent_id'] > 0 ){
	$signoffmsg = " Sub To-do?";
} else {
	$signoffmsg = " To-do?";
}	
?>
<div class="tast-list-right-head-left pull-right">

		<span class="pull-right">
			<div class="todocombutton_up">
				<div class="todo_cald text-right">
					<a href="<?php echo Router::Url(array("controller" => "todos", "action" => "manage"), true); ?><?php if(isset($data['DoList']['project_id']) && !empty($data['DoList']['project_id']) ){ echo '/project:'.$data['DoList']['project_id']; } ?>" class="btn btn-sm btn-warning" id="manage_todo"><i class="fa fa-plus"></i> Create To-do</a>
				</div>
			</div>


<?php /* ?>			
			<div>
                               <!-- <a  href="javascript:void(0);" class="btn btn-xs btn-default tipText" title="Calendar">
                                    <i class="fa  fa-calendar-check-o "></i>
                                </a>-->
                                <input type="hidden" class="fillter_calender" name="fillter_calender" />
                                <a href="javascript:void(0);" class="btn btn-xs tipText btn-primary" title="Expand" id="open">
					<i class="fa fa-expand"></i>
				</a>
                                <a href="javascript:void(0);" class="btn btn-xs tipText btn-primary disabled" title="Collapse" id="close">
					<i class="fa fa-compress"></i>
				</a>
                                <a href="<?php echo Router::Url(array("controller" => "todos", "action" => "manage", $do_list_id), true); ?>" data-id="<?php echo $do_list_id ?>" class="btn btn-xs tipText btn-success <?php if(isset($data['DoList']['sign_off']) && !empty($data['DoList']['sign_off'])){ ?>disabled<?php } ?> tipText update_dolist" title="Edit">
					<i class="fa fa-pencil"></i>
				</a>

                                <button data-dotype="<?php echo $signoffmsg;?>" data-id="<?php echo $do_list_id ?>" title="Sign Off" class="btn btn-xs btn-success tipText sign_off_dolist <?php if(isset($data['DoList']['sign_off']) && !empty($data['DoList']['sign_off'])){ ?>disabled<?php } ?>">
					<i class="fa fa-flag-checkered"></i>
				</button>

                                <button title="Delete" data-dotype="<?php echo $signoffmsg;?>"  data-id="<?php echo $do_list_id ?>" class="btn btn-xs btn-danger tipText delete_dolist">
					<i class="fa fa-trash"></i>
				</button>
                                <?php

                                if(isset($data['DoList']['project_id']) && !empty($data['DoList']['project_id']) && is_numeric($data['DoList']['project_id'])){?>
                            <a href="<?php echo SITEURL;?>projects/index/<?php echo $data['DoList']['project_id'];?>" title="Open Project" class="btn btn-xs btn-default tipText">
					<i class="fa fa-folder-open"></i>
                            </a>
                            <?php } ?>
			</div>
			
<?php */ ?>			
			
			
		</span>
	</div>
<?php }else {
?>
<span class="pull-right">
    <div class="todocombutton_up">
        <div class="todo_cald text-right"><a href="<?php echo Router::Url(array("controller" => "todos", "action" => "manage"), true); ?>" class="btn btn-sm btn-warning" id="manage_todo"><i class="fa fa-plus"></i> Create To-do</a>
        </div>
    </div>

<?php /* ?>
    <div class="todocombutton_bottom">
          <!--  <a href="javascript:void(0);"  class="btn btn-xs btn-default tipText" title="Calendar"><i class="fa  fa-calendar-check-o "></i></a>-->
            <input type="hidden" class="fillter_calender" name="fillter_calender" />
            <a href="javascript:void(0);" class="btn btn-xs tipText btn-primary" title="Expand" id="open">
                <i class="fa fa-expand"></i>
            </a>
            <a href="javascript:void(0);" class="btn btn-xs tipText btn-primary disabled" title="Collapse" id="close">
                <i class="fa fa-compress"></i>
            </a>
            <a  class="btn btn-xs btn-success tipText disabled" title="Edit"><i class="fa fa-pencil"></i></a>

            <button class="btn btn-xs btn-success tipText disabled" title="Sign Off">
                    <i class="fa fa-flag-checkered"></i>
            </button>

            <button class="btn btn-xs btn-danger tipText disabled" title="Delete"><i class="fa fa-trash"></i>
            </button>
            <?php if(isset($prj_id) && !empty($prj_id) && is_numeric($prj_id)){?>
                            <a href="<?php echo SITEURL;?>projects/index/<?php echo $prj_id;?>" title="Open Project"  class="btn btn-xs btn-default tipText">
					<i class="fa fa-folder-open"></i>
                            </a>
                            <?php } ?>
    </div>
	
<?php */ ?>	
	
</span>

<?php
} ?>
<script type="text/javascript" >

    $(document).ready(function()  {
        $(".fillter_calender").daterange({
            numberOfMonths: 2,
            dateFormat: "yy-mm-dd",
            //minDate: 0,
            autoUpdateInput: false,
            onSelect: function (selected, inst) {
            },
            onClose: function (selected, inst) {
                filterdate(selected);
            },
            beforeShow: function (input, inst) {
            },
        })
        $('body').delegate(".fa-calendar-check-o", 'click', function (e) {
            e.preventDefault()
            //$(".fillter_calender").datepicker("show");
        });

    $('a[href="#"][data-toggle="modal"]').attr('href', 'javascript:;');
    $('a[href="#"][data-toggle="modals"]').attr('href', 'javascript:;');
	$('a[href=""][data-toggle="modal"]').attr('href', 'javascript:void(0);');
    });
    function filterdate(date) {
        var curUrl = window.location.href,dates = date.split(" - "),
                start_date = dates[0],
                end_date = (dates[1] !== '' && typeof dates[1] !== 'undefined') ? dates[1] : dates[0];
        if(date !== null && date !== ''){
            var view_vars = $js_config.view_vars;
            var project = (view_vars['project'] != undefined) ?  '/project:' + $.trim(view_vars['project']) : '',
                day = (view_vars['day'] != undefined) ?  '/day:' + $.trim(view_vars['day']) : '',
                startdate = (start_date != undefined) ?  '/sdate:' + $.trim(start_date) : '',
                enddate = (end_date != undefined) ? '/edate:' + $.trim(end_date) : '';
            window.location.href = $js_config.base_url+'todos/index'+project+day+startdate+enddate;
        }


    }

</script>