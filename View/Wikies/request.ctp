<?php
echo $this->Html->css('projects/wiki');

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
                <?php echo $this->Session->flash(); ?>
            </div>
        </div>
        <div class="box-content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header wiki-requests-h1" style="background-color: #f1f3f4; border-color: #ddd; border-image: none; border-style: solid; border-width: 1px; padding: 10px 10px 0 10px;" >
                            <div class="col-sm-12">
                                <form class="form-inline  " method="get" action="<?php echo SITEURL; ?>wikies/request" id="todofilter" role="form">
                                    <div class="form-group" style="margin: 0; margin-right: 15px">
                                        <label for="filters">Filters:</label>
                                        <label style=" " class="custom-dropdown">
                                        <select name="status" class="form-control aqua" style=" width: 200px; max-width: 100%; background: #E6E6E6" >
                                            <option value="">All</option>
                                            <option <?php echo isset($status) && $status == "accept" ? "selected='selected'" : '' ?> value="accept">
                                                Accepted
                                            </option>
                                            <option <?php echo isset($status) && $status == "decline" ? "selected='selected'" : '' ?> value="decline">
                                                Declined
                                            </option>
                                            <option <?php echo isset($status) && $status == "pending" ? "selected='selected'" : '' ?> value="pending">
                                                Pending
                                            </option>

                                        </select>
                                        </label>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 5px">
                                        <label for="keywords">Keywords:</label>
                                        <input type="text" name="keywords" value="<?php echo isset($keywords) && $keywords != "" ? $keywords : '' ?>" class="form-control aqua" placeholder="Keywords" id="keywords">
                                    </div>
                                    <div class="form-group" style="margin-bottom: 5px">
                                        <button type="submit" class="btn btn-success todofilter btn-sm">Filter</button>
                                        <a  class="btn btn-danger btn-sm" href="<?php echo SITEURL; ?>wikies/request">Reset</a>
                                    </div>

                                </form>
                            </div>
                            <div class="col-sm-2">

                            </div>
                        </div>

                        <div class="box-body" style="min-height: 500px;">
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="wiki-requests-table" id="todotab">
                                    <div class="table_wrapper clearfix">
                                        <div class="table_head">
                                            <div class="row">
                                                <div class="col-sm-3 resp">
                                                    <h5> Title </h5>
                                                </div>
                                                <div class="col-sm-2 resp wiki-requests-deta">
                                                    <h5>Requested Date </h5>
                                                </div>
                                                <div class="col-sm-2 resp">
                                                    <h5>Accepted Date </h5>
                                                </div>
                                                <div class="col-sm-2 resp">
                                                    <h5> Status </h5>
                                                </div>
												<div class="col-sm-2 resp">
                                                    <h5> Requested By </h5>
                                                </div>
                                                <div class="col-sm-1 resp text-center">
                                                    <h5>Action</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-rows data_catcher" id="main_todo_request">
                                            <?php
                                            $all_data = [];

                                            if(isset($data) && !empty($data)){
                                                foreach($data as $wiki){
                                                    if(dbExists('Wiki', $wiki['WikiUser']['wiki_id'])){
                                                        $all_data[] = $wiki;
                                                    }
                                                }
                                            }
                                            if(isset($all_data) && !empty($all_data)){
                                                foreach($all_data as $wiki){
                                                    // if(dbExists('Wiki', $wiki['WikiUser']['wiki_id'])){
                                            ?>

                                                <div class="row">
                                                    <div class="col-sm-3 resp" >
                                                        <span class="tipText" title="<?php echo Sanitize::html($wiki['Wiki']['title']);?>">
                                                        <?php

                                                        echo $this->text->truncate(
                                                            Sanitize::html($wiki['Wiki']['title']),
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
                                                        if(isset($wiki['WikiUser']['created']) && !empty($wiki['WikiUser']['created'])){
                                                            //echo date("d M Y", $wiki['WikiUser']['created']);
															echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$wiki['WikiUser']['created']),$format = 'd M Y');
                                                        }else{
                                                            echo 'N/A';
                                                        }
                                                        ?>

                                                    </div>
                                                    <div class="col-sm-2 resp  ">
                                                        <?php
                                                        if(isset($wiki['WikiUser']['updated']) && !empty($wiki['WikiUser']['updated']) && ($wiki['WikiUser']['approved'] == 1 || $wiki['WikiUser']['approved'] == 2)){
                                                            //echo date("d M Y", $wiki['WikiUser']['updated']);
															echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$wiki['WikiUser']['updated']),$format = 'd M Y');
                                                        }else{
                                                            echo 'N/A';
                                                        }
                                                        ?>

                                                    </div>
                                                    <div class="col-sm-2 resp ">
                                                        <?php

                                                        if(isset($wiki['WikiUser']['approved']) && $wiki['WikiUser']['approved'] == 0){
                                                            echo 'Requested';
                                                        }else if(isset($wiki['WikiUser']['approved']) && $wiki['WikiUser']['approved'] == 1){
                                                            echo 'Accepted';
                                                        }else if(isset($wiki['WikiUser']['approved']) && $wiki['WikiUser']['approved'] == 2){
                                                            echo 'Declined';
                                                        }
                                                        ?>
                                                    </div>
													<div class="col-sm-2 resp ">
                                                        <?php if (isset($wiki['WikiUser']['user_id']) && !empty($wiki['WikiUser']['user_id'])) {
                                                        ?>
                                                        <a href="#" style="float: none;" data-remote="<?php echo SITEURL ?>shares/show_profile/<?php echo $wiki['WikiUser']['user_id']; ?>"  data-target="#popup_modal"  data-toggle="modal" class="view_profile text-maroon" >
                                                            <i class="fa fa-user"></i>
                                                        </a>
                                                        <?php
                                                        echo $this->Common->userFullname($wiki['WikiUser']['user_id']);
														}
														?>
                                                    </div>

                                                    <div class="col-sm-1 resp text-center">
                                                        <a data-original-title="Wiki Details" class="bredd btn btn-xs btn-info tipText" href="<?php echo SITEURL ?>wikies/wikidetails/<?php echo $wiki['WikiUser']['id']; ?>" >
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php
                                            // }
                                        }
                                            ?>
                                            <?php
                                            /*if ($this->Paginator->hasPage(null, 2)) {
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

<div class="modal modal-success fade " id="popup_modal_profile" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<script type="text/javascript" >
$(function(){

$('body').on('hidden.bs.modal', '.modal', function () {
    $(this).removeData('bs.modal');
    $(this).find('.modal-content').html('');
	console.log("removed");
    $('.tooltip').hide()
});

})
</script>

