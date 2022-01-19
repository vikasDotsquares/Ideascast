<?php
echo $this->Html->css('projects/todo');
echo $this->Html->script('projects/todo', array('inline' => true));

echo $this->Html->css('projects/dropdown');



echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));
?>

<style>
input.aqua {
  border-color: #00c0ef;
}
label {
   font-weight: normal;
}
	.table-rows {
		
	}	
.to-text-ellipsis {
    text-overflow: ellipsis;
    overflow: hidden;
	white-space: nowrap;
	display: block;
}
.box-body .table-rows:not(.table-catcher) [class*=" col-sm-"], .box-body .table-rows:not(.table-catcher) [class^=col-sm-] {
    display: inline-block;
}
	
	
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left">
                    <?php echo $page_heading; ?>
                    <p class="text-muted date-time">
                        <span style="text-transform: none;"><?php echo $page_subheading; ?></span>
                    </p>
                </h1>
            </section>
        </div>
        <div class="row">
            <div class="col-xs-12 msg_box">
                <?php echo $this->Session->flash();  ?>
            </div>
        </div>
        <div class="box-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header todos-select-requests" style="background: #f1f3f4 none repeat scroll 0 0; border-color: #d2d6de #d2d6de transparent; border-style: solid solid none; border-width: 1px 1px medium; cursor: move; padding: 12px 0 2px;" >
                            <div class="col-sm-10">
                                <form class="form-inline  " method="get" action="<?php echo SITEURL; ?>todos/requests/<?php echo $type;?>" id="todofilter" role="form">
                                    <div class="form-group todos-select" style="margin-bottom: 5px; margin-right: 15px">
                                        <label for="filters">Filters:</label>
                                        <label style=" " class="custom-dropdown">
                                        <select name="status" class="form-control aqua">
                                            <option value="">All</option>
                                            <option <?php echo isset($status) && $status == "OPN" ? "selected='selected'" : '' ?> value="OPN">
                                                Open
                                            </option>
                                            <option <?php echo isset($status) && $status == "RJCT" ? "selected='selected'" : '' ?> value="RJCT">
                                                Declined
                                            </option>
                                            <option <?php echo isset($status) && $status == "PND" ? "selected='selected'" : '' ?> value="PND">
                                                Not Started
                                            </option>
                                            <option <?php echo isset($status) && $status == "PRG" ? "selected='selected'" : '' ?> value="PRG">
                                                In Process
                                            </option>
                                            <option <?php echo isset($status) && $status == "OVD" ? "selected='selected'" : '' ?> value="OVD">
                                                Overdue
                                            </option>
                                            <option <?php echo isset($status) && $status == "CMP" ? "selected='selected'" : '' ?> value="CMP">
                                                Completed
                                            </option>
                                        </select>
                                        </label>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 5px; vertical-align: top;">
                                        <label for="keywords">Keywords:</label>
                                        <input type="text" name="keywords" value="<?php echo isset($keywords) && $keywords != "" ? $keywords : '' ?>" class="form-control aqua" placeholder="Keywords" id="keywords">
                                    </div>
                                    <div class="form-group" style="margin-bottom: 5px;vertical-align: top;margin-top: 3px;">
                                        <button type="submit" class="btn btn-success todofilter btn-sm">Filter</button>
                                        <a  class="btn btn-danger btn-sm" href="<?php echo SITEURL; ?>todos/requests/<?php echo $type;?>">Reset</a>
                                    </div>

                                </form>
                            </div>
                            <div class="col-sm-2">
                                <?php
                                if(isset($type) && $type == 'sub'){
                                ?>
                                <a href="<?php echo SITEURL;?>todos/requests/main" class="btn btn-success btn-sm pull-right">
                                    To-do
                                    <?php
                                    $countMain = $this->requestAction(array("action"=>"getMainTodoRequestCount"));
                                    echo '('.$countMain.')';
                                    ?>
                                </a>
                                <?php
                                }else{
                                ?>
                                <a href="<?php echo SITEURL;?>todos/requests/sub" class="btn btn-success btn-sm pull-right">
                                    Sub To-do
                                    <?php
                                    $countSub = $this->requestAction(array("action"=>"getSubTodoRequestCount"));
                                    echo '('.$countSub.')';
                                    ?>
                                </a>
                                <?php
                                }
                                ?>
                            </div>
                        </div>

                        <div class="box-body" style="min-height: 500px;">
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="" id="todotab">
                                    <div class="table_wrapper clearfix">
                                        <div class="table_head">
                                            <div class="row">
                                                <div class="col-sm-3 col-md-4 col-lg-3 resp">
                                                    <h5>  Title
                                                    </h5>
                                                </div>
                                                <div class="col-sm-2 resp">
                                                    <h5>Start Date
                                                    </h5>
                                                </div>
                                                <div class="col-sm-2 resp">
                                                    <h5>End Date
                                                    </h5>
                                                </div>
                                                <div class="col-sm-3 col-md-2 col-lg-3 resp">
                                                    <h5><?php  if($type && $type == 'sub'){echo 'Sub ';}?> To-do </h5>
                                                </div>
                                                <div class="col-sm-2 resp text-center">
                                                    <h5>Action</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-rows data_catcher" id="main_todo_request">
                                            <?php   $Mtype = $type;
                                            //pr($data);
                                            if(isset($data) && !empty($data)){
                                                foreach($data as $todo){
                                                //pr($todo['DoListUser']['approved']);

                           ?>

                                                <div class="row">
                                                    <div class="col-sm-3 col-md-4 col-lg-3 resp" >
                                                        <span class="tipText to-text-ellipsis" title="<?php echo Sanitize::html($todo['DoList']['title']);?>">
                                                        <?php

                                                        echo $this->text->truncate(
                                                            Sanitize::html($todo['DoList']['title']),
                                                            250,
                                                            array(
                                                                'ending' => '...',
                                                                'exact' => false,
                                                                'html' => true
                                                            )
                                                        );
                                                        ?>
                                                        </span>
                                                    </div>
                                                    <div class="col-sm-2 resp ">
                                                        <?php
                                                        if(isset($todo['DoList']['start_date']) && !empty($todo['DoList']['start_date'])){
                                                            //echo date("d M Y", strtotime($todo['DoList']['start_date']));
															echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($todo['DoList']['start_date'])),$format = 'd M Y');
                                                        }else{
                                                            echo 'N/A';
                                                        }
                                                        ?>

                                                    </div>
                                                    <div class="col-sm-2 resp  ">
                                                        <?php
                                                        if(isset($todo['DoList']['end_date']) && !empty($todo['DoList']['end_date'])){
                                                            //echo date("d M Y", strtotime($todo['DoList']['end_date']));
															echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($todo['DoList']['end_date'])),$format = 'd M Y');
                                                        }else{
                                                            echo 'N/A';
                                                        }
                                                        ?>

                                                    </div>
                                                    <div class="col-sm-3 col-md-2 col-lg-3 resp ">
                                                        <?php
                                                        echo $this->requestAction(array("action"=>"get_status",$todo['DoList']['id']));
                                                        ?>
                                                    </div>
                                                    <div class="col-sm-2 resp text-center">
                                                       <a data-original-title="<?php echo ($Mtype == 'sub') ? 'Sub' : ''; ?> To-do Details" class="bredd btn btn-xs btn-info tipText" href="<?php echo SITEURL ?>todos/tododetails/<?php echo $todo['DoList']['id']; ?>" >
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <?php
                                                        $open = $this->Todocomon->get_status($todo['DoList']['id'], $Mtype );
                                                                                        // pr($open);
                                                        if(isset($open) && $todo['DoListUser']['approved'] == 1 && ($open == 'completed' || $open == 'Progressing' || $open == 'Overdue' || $open == 'Not Started')){
                                                            $link_project = '';

                                                            if(isset($todo['DoList']['project_id']) && !empty($todo['DoList']['project_id'])) {
                                                                    $link_project = '/project:'.$todo['DoList']['project_id'];
                                                            }

                                                            $link_todo = '';
                                                            if(isset($todo['DoList']['id']) && !empty($todo['DoList']['id'])) {
                                                                    $link_todo = '/dolist_id:'.$todo['DoList']['id'];
                                                            }

                                                        ?>
                                                        <a href="<?php echo SITEURL.'todos/index'.$link_project.$link_todo ?>"  data-original-title="Open <?php echo ($Mtype == 'sub') ? 'Sub' : ''; ?> To-do" class=" bredd btn btn-xs tipText" >
                                                            <div class="btn btn-xs btn-default" style=""><i class="fa fa-folder-open"></i></div>
                                                        </a>
                                                        <?php

                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>

                                            <p>
                                            <?php
                                            //echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')));
                                            ?>
                                            </p>
                                            <?php
                                            /*
                                            if ($this->Paginator->hasPage(null, 2)) {

                                            ?>
                                            <ul class="pagination pull-right">
                                            <?php
                                                echo $this->Paginator->options(array('url' => $this->passedArgs));
                                                echo $this->Paginator->prev('&laquo;', array('tag' => 'li', 'escape' => false), '<a href="#">&laquo;</a>', array('class' => 'prev disabled', 'tag' => 'li', 'escape' => false));
                                                echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentLink' => true, 'currentClass' => 'active', 'currentTag' => 'a'));
                                                echo $this->Paginator->next('&raquo;', array('tag' => 'li', 'escape' => false), '<a href="#">&raquo;</a>', array('class' => 'prev disabled', 'tag' => 'li', 'escape' => false));
                                            ?>
                                            </ul>
                                            <?php
                                            }*/
                                            ?>
                                            <?php
                                            }else{
                                            ?>
                                                <div class="row">
                                                    <div class="col-lg-12 text-center" style="padding: 20px 0px;">
                                                        No Requests.
                                                    </div>
                                                </div>

                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
