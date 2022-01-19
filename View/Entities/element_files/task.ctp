<style>
	.template_info {
		background: #00aff0;
		border-radius: 50%;
		color: #ffffff;
		font-size: 10px;
		height: 20px;
		line-height: 18px;
		padding: 0 8px;
		width: 20px;
		display: inline-block;
	}

	.popover .popover-content .template_create  {
		font-size: 12px;
		font-weight: normal;
	}
</style>

<?php
$pointer_event = '';
if( $ele_signoff == true ){
	$pointer_event = 'signoffpointer';
}
echo $this->Form->create('Element', array('url' => array('controller' => 'entities', 'action' => 'update_element', $element_id), 'class' => "form-bordered  padding-top $pointer_event", 'id' => 'formUpdateElement')); ?>

<?php echo $this->Form->input('Element.id', [ 'type' => 'hidden']); ?>
<?php
echo $this->Form->input('Element.area_id', [ 'type' => 'hidden']);

echo $this->Form->input('Element.create_activity', [ 'type' => 'hidden','value'=>true]);

if ((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($is_editaa) && $is_editaa > 0)) {
    $dsss = 'data-new';
} else {
   // $dsss = 'disabled';
    $dsss = '';
}
?>
<style>
.task-type-select {
    position: relative;
    display: block;
    min-height: 40px;
    z-index: 1;
}
.task-type-select .form-control{
	    position: absolute;
}

</style>

<div class="form-group clearfix">

    <label class="col-sm-12 nopadding-left " for="txa_title">Task Type:</label>
    <div class="col-sm-5 clearfix nopadding-left">
    <div class="task-type-select">
	<?php
		$projectEleType = $this->ViewModel->project_element_type($project_id);

		if( !empty($projectEleType) && count($projectEleType) >= 14 ){
			echo $this->Form->select('ElementType.type_id', $projectEleType, array('escape' => false, 'class' => 'form-control','empty'=>false, 'id' => 'project_ele_types', 'default'=>$selElementType,"onfocus"=>"this.size=14;", "onblur"=>"this.size=1;", "onchange"=>"this.size=1;", "size"=>"1" ));
		}else if( !empty($projectEleType) && count($projectEleType) < 14 ){

			echo $this->Form->select('ElementType.type_id', $projectEleType, array('escape' => false, 'class' => 'form-control','empty'=>false, 'id' => 'project_ele_types', 'default'=>$selElementType));
		} else {
			echo $this->Form->select('ElementType.type_id', $projectEleType, array('escape' => false, 'class' => 'form-control','empty'=>false, 'id' => 'project_ele_types', 'empty'=>'Select Task Type' ));
		}

		echo $this->Form->input('ElementType.project_id', array('escape' => false, 'class' => 'form-control', 'value'=>$project_id,'type'=>'hidden' ));
		echo $this->Form->input('ElementType.element_id', array('escape' => false, 'class' => 'form-control', 'value'=>$element_id,'type'=>'hidden' ));

		//if( empty($selElementType['ElementType']['type_id']) ){
		echo $this->Form->input('ElementType.id', array('escape' => false, 'class' => 'form-control', 'type'=>'hidden', 'value'=>$updateElementType ));
		/*} else {
			echo $this->Form->input('ElementType.id', array('escape' => false, 'class' => 'form-control', 'type'=>'hidden', 'value'=>$selElementType['ElementType']['id'] ));
		}*/

	?>
    </div>
    <span style="" class="error-messagess text-danger"> <?php
        if (isset($errors) && isset($errors['title']) && isset($errors['title'][0])) {
            echo $errors['title'][0];
        } else {

        }
        ?>
	</span>

</div>
</div>

<div class="form-group">

    <label class=" " for="txa_title">Title:</label>

    <?php
	$this->request->data['Element']['title'] = $this->request->data['Element']['title'];
	echo $this->Form->text('Element.title', [ 'class' => 'form-control', 'id' => 'txa_title', 'required' => false, 'escape' => true,  $dsss, 'placeholder' => 'Max chars allowed 50']); ?>
    <span class="error text-red chars_left" ></span>
    <span style="" class="error-messagess text-danger"> <?php
        if (isset($errors) && isset($errors['title']) && isset($errors['title'][0])) {
            echo $errors['title'][0];
        } else {

        }
        ?>
	</span>

</div>

<div class="form-group">

    <label class=" " for="txa_description">Description:</label>

    <?php
	$this->request->data['Element']['description'] = $this->request->data['Element']['description'];
	echo $this->Form->textarea('Element.description', [ 'class' => 'form-control', 'id' => 'txa_description', 'required' => false, 'escape' => true, 'rows' => 2, $dsss, 'placeholder' => 'Max chars allowed 750', 'style'=>'height: 70px']); ?>
    <span class="error text-danger text-red chars_left" ></span>
    <span style="" class="error-messagess  text-danger"> <?php
        if (isset($errors) && isset($errors['description']) && isset($errors['description'][0])) {
            echo $errors['description'][0];
        } else {

        }
        ?></span>

</div>

<div class="form-group clearfix">

    <label for="ElementComments">Outcome: </label>

    <?php
	$this->request->data['Element']['comments'] = $this->request->data['Element']['comments'];
	echo $this->Form->textarea('Element.comments', [ 'class' => 'form-control', 'id' => 'txa_comments', 'escape' => true, 'required' => false, 'rows' => 7, $dsss, 'placeholder' => 'Max chars allowed 2000']); ?>
    <span class="error text-red text-danger chars_left" ></span>

<!--<label id="ElementCommentsTotalChars" class="chars_left">2000 Chars, <span>2000</span> left</label>-->
</div>


<div class="form-group clearfix nopadding">
    <label class="nopadding pull-left cokor-sceme" >Color Theme:</label>
    <div class="col-md-8 nopadding">
        <div class="nopadding-left ">
            <?php echo $this->Form->input('Element.color_code', [ 'type' => 'hidden', 'id' => 'color_code']); ?>

            <div class="form-control noborder cokor-sceme-box cokor-sceme-box-height" >
				<!--new-color theme-->
				<ul class="color-ul">
                	<li class="color-items">
						<a href="#" data-color="panel-color-lightred" data-preview-color="bg-color-lightred" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Light Red"><i class="square-color panel-text-lightred"></i></a>
						<a href="#" data-color="panel-color-red" data-preview-color="bg-color-red" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Red"><i class="square-color panel-text-red"></i></a>
						<a href="#" data-color="panel-color-maroon" data-preview-color="bg-color-maroon" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Maroon"><i class="square-color panel-text-maroon"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightorange" data-preview-color="bg-color-lightorange" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Light Orange"><i class="square-color panel-text-lightorange"></i></a>
						<a href="#" data-color="panel-color-orange" data-preview-color="bg-color-orange" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Orange"><i class="square-color panel-text-orange"></i></a>
						<a href="#" data-color="panel-color-darkorange" data-preview-color="bg-color-darkorange" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Dark Orange"><i class="square-color panel-text-darkorange"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightyellow" data-preview-color="bg-color-lightyellow" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Light Yellow"><i class="square-color panel-text-lightyellow"></i></a>
						<a href="#" data-color="panel-color-yellow" data-preview-color="bg-color-yellow" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Yellow"><i class="square-color panel-text-yellow"></i></a>
						<a href="#" data-color="panel-color-darkyellow" data-preview-color="bg-color-darkyellow" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Dark Yellow"><i class="square-color panel-text-darkyellow"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightgreen" data-preview-color="bg-color-lightgreen" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Light Green"><i class="square-color panel-text-lightgreen"></i></a>
						<a href="#" data-color="panel-color-green" data-preview-color="bg-color-green" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Green"><i class="square-color panel-text-green"></i></a>
						<a href="#" data-color="panel-color-darkgreen" data-preview-color="bg-color-darkgreen" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Dark Green"><i class="square-color panel-text-darkgreen"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightteal" data-preview-color="bg-color-lightteal" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Light Teal"><i class="square-color panel-text-lightteal"></i></a>
						<a href="#" data-color="panel-color-teal" data-preview-color="bg-color-teal" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Teal"><i class="square-color panel-text-teal"></i></a>
						<a href="#" data-color="panel-color-darkteal" data-preview-color="bg-color-darkteal" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Dark Teal"><i class="square-color panel-text-darkteal"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightaqua" data-preview-color="bg-color-lightaqua" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Light Aqua"><i class="square-color panel-text-lightaqua"></i></a>
						<a href="#" data-color="panel-color-aqua" data-preview-color="bg-color-aqua" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Aqua"><i class="square-color panel-text-aqua"></i></a>
						<a href="#" data-color="panel-color-darkaqua" data-preview-color="bg-color-darkaqua" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Dark Aqua"><i class="square-color panel-text-darkaqua"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightblue" data-preview-color="bg-color-lightblue" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Light Blue"><i class="square-color panel-text-lightblue"></i></a>
						<a href="#" data-color="panel-color-blue" data-preview-color="bg-color-blue" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Blue"><i class="square-color panel-text-blue"></i></a>
						 <a href="#" data-color="panel-color-navy" data-preview-color="bg-color-navy" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Navy"><i class="square-color panel-text-navy"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightpurple" data-preview-color="bg-color-lightpurple" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Light Purple"><i class="square-color panel-text-lightpurple"></i></a>
						<a href="#" data-color="panel-color-purple" data-preview-color="bg-color-purple" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Purple"><i class="square-color panel-text-purple"></i></a>
						<a href="#" data-color="panel-color-darkpurple" data-preview-color="bg-color-darkpurple" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Dark Purple"><i class="square-color panel-text-darkpurple"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightmagenta" data-preview-color="bg-color-lightmagenta" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Light Magenta"><i class="square-color panel-text-lightmagenta"></i></a>
						<a href="#" data-color="panel-color-magenta" data-preview-color="bg-color-magenta" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Magenta"><i class="square-color panel-text-magenta"></i></a>
						<a href="#" data-color="panel-color-darkmagenta" data-preview-color="bg-color-darkmagenta" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Dark Magenta"><i class="square-color panel-text-darkmagenta"></i></a>
					</li>
					<li class="color-items">
						<a href="#" data-color="panel-color-lightgray" data-preview-color="bg-color-lightgray" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Light Gray"><i class="square-color panel-text-lightgray"></i></a>
						<a href="#" data-color="panel-color-gray" data-preview-color="bg-color-gray" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Gray"><i class="square-color panel-text-gray"></i></a>
						<a href="#" data-color="panel-color-darkgray" data-preview-color="bg-color-darkgray" class="squares squares-default squares-xs el_color_box <?php echo $dsss; ?>  tipText" title="Dark Gray"><i class="square-color panel-text-darkgray"></i></a>
					</li>
				</ul>

            </div>
        </div>
    </div>
    <div class="col-md-2 preview" style="text-align: center; display: none;">
        <span style="margin-top: 8px; display: none;">Color preview</span>
    </div>
</div>

<?php

$feedbackcnt = $this->Common->ele_feed_count($this->request->data['Element']['id'], 'feedback');
$votecnt = $this->Common->ele_feed_count($this->request->data['Element']['id'], 'vote');
$decisioncnt = $this->Common->ele_feed_count($this->request->data['Element']['id'], 'decision');

$dfv_progress = false;
/* if(isset($element_decisions) && !empty($element_decisions)){
	if($element_decisions['decision_short_term'] == 'PRG'){
		$dfv_progress = true;
	}
}
if(isset($progressing_feed) && !empty($progressing_feed)){
	$dfv_progress = true;
}
if(isset($not_started_feed) && !empty($not_started_feed)){
	$dfv_progress = true;
}
if(isset($progressing_vote) && !empty($progressing_vote)){
	$dfv_progress = true;
}
if(isset($not_started_vote) && !empty($not_started_vote)){
	$dfv_progress = true;
} */


if(isset($feedbackcnt) && !empty($feedbackcnt)){
	$dfv_progress = true;
}
if(isset($votecnt) && !empty($votecnt)){
	$dfv_progress = true;
}
if(isset($decisioncnt) && !empty($decisioncnt)){
	$dfv_progress = true;
}

$dfv_prg_class = '';
if( isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date']) && isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date']) ){
	if($dfv_progress){
		$dfv_prg_class = 'dfv_prg_class';
	}
}

?>
<div class="form-group clearfix <?php //echo $dfv_prg_class;?>">
<span class="<?php echo $dfv_prg_class;?>">
    <label class="nopadding " style="display: inline-block;float: left;margin: 2px 10px 0 0;position: none;">Date Schedule: </label>

    <input type="radio" <?php echo $dsss; ?> id="el_dc_yes" name="data[Element][date_constraints]" class="fancy_input" value="1"   />
    <label class="fancy_label <?php echo $dsss; ?>" for="el_dc_yes">Yes</label>

    <input <?php echo $dsss; ?>  type="radio" id="el_dc_no" name="data[Element][date_constraints]" class="fancy_input <?php echo $dfv_prg_class; ?>"  value="0" checked />
	<label class="fancy_label <?php echo $dsss; ?>" for="el_dc_no">No</label></span>
	<?php
	if( isset($dfv_prg_class) && !empty($dfv_prg_class) ){
	?>
	<i class="fa fa-info template_info prophover" data-placement="top" data-content="<div class='template_create'>You cannot remove the date schedule for this Task because it contains either Decisions, Feedback or Votes.</div>" data-original-title="" title=""></i><?php } ?>

</div>

<div class="form-group clearfix <?php //echo $dfv_prg_class; ?>" id="date_constraints_dates" >

    <div class="form-group input-daterange">

        <div class="row date-row">
        <div class="col-sm-6 create-edit-date-f">
			<label class="control-label" for="start_date">Start date:</label>
            <div class="input-group">
                <?php
				$start_date_w = isset($this->request->data['Element']['start_date']) && !empty($this->request->data['Element']['start_date']) ? date("d M Y", strtotime($this->request->data['Element']['start_date'])) : '';

                $end_date_w = isset($this->request->data['Element']['end_date']) && !empty($this->request->data['Element']['end_date']) ? date("d M Y", strtotime($this->request->data['Element']['end_date'])) :'';

                echo $this->Form->input('Element.start_date', [ 'type' => 'text', 'label' => false, "value" => $start_date_w, 'div' => false, 'error' => false, 'id' => 'start_date_elm', 'required' => false, $dsss, 'readonly' => 'readonly', 'class' => 'form-control dates input-small']);

                ?>
                <div class="input-group-addon <?php echo $dsss; ?> open-start-date-picker calendar-trigger">
                    <i class="fa fa-calendar"></i>
                </div>

            </div>
			<?php echo $this->Form->error('Element.start_date', null, array('class' => 'error-message')); ?>
        </div>


        <div class="col-sm-6 create-edit-date-f">
			<label class="control-label" for="end_date">End date:</label>
            <div class="input-group">
<?php echo $this->Form->input('Element.end_date', [ 'type' => 'text', 'label' => false, 'error' => false, 'div' => false, 'id' => 'end_date_elm', 'required' => false, 'readonly' => 'readonly', $dsss, "value" => $end_date_w, 'class' => 'form-control dates input-small']); ?>
                <div class="input-group-addon <?php echo $dsss; ?>  open-end-date-picker calendar-trigger">
                    <i class="fa fa-calendar"></i>
                </div>


            </div>
<?php echo $this->Form->error('Element.end_date', null, array('class' => 'error-message')); ?>

        </div>
	</div>

    </div>
</div>
<?php
$is_owner = $this->Common->userproject($project_id, $this->Session->read('Auth.User.id'));

$p_permission = $this->Common->project_permission_details($project_id, $this->Session->read('Auth.User.id'));

$is_editaa = $this->Common->element_manage_edit($this->data['Element']['id'], $project_id, $this->Session->read('Auth.User.id'));


if($ele_signoff == false){

if ((isset($is_owner) && !empty($is_owner)) || (isset($project_level) && $project_level == 1) || (isset($p_permission['ProjectPermission']['project_level']) && $p_permission['ProjectPermission']['project_level'] == 1) || (isset($is_editaa) && $is_editaa > 0) ) {
    ?>
    <div class="form-group clearfix">
        <input class="btn btn-sm btn-success save_element" value="Save" type="submit"/>
        <a href="<?php echo SITEURL . 'projects/manage_elements/' . element_project($this->request->data['Element']['id']).'/'.element_workspace($this->request->data['Element']['id']); ?>" class="btn btn-danger btn-sm cancel hide_ele" >Cancel</a>

    </div>
<?php } else { ?>
    <div class="form-group clearfix">
        <div class="btn btn-success disabled"  >Save</div>
        <a href="<?php echo SITEURL . 'projects/manage_elements/' . element_project($this->request->data['Element']['id']).'/'.element_workspace($this->request->data['Element']['id']); ?>" class="btn btn-danger btn-sm cancel hide_ele" >Cancel</a>
    </div>
<?php }
} else {
 ?>
	<div class="form-group clearfix">
        <div class="btn btn-success disabled"  >Save</div>
        <a href="<?php echo SITEURL . 'projects/manage_elements/' . element_project($this->request->data['Element']['id']).'/'.element_workspace($this->request->data['Element']['id']); ?>" class="btn btn-danger btn-sm cancel hide_ele" >Cancel</a>
    </div>
<?php } ?>

<?php
echo $this->Form->end();

$strpos1 = explode('-', $this->request->data['Element']['color_code']);
$row_color = 'panel-color-gray';
if(isset($strpos1) && !empty($strpos1)) {
	$sz = count($strpos1) - 1;
	$row_color = 'panel-color-'.$strpos1[$sz];
}
?>
<script>
$(function(){
	var color_code = '<?php echo $row_color; ?>';
	console.log('color_code', color_code);
	$('.el_color_box i').removeClass('fa-check');
	$('.el_color_box[data-color="'+color_code+'"] i').addClass('fa-check');
	$('.prophover').popover({
		placement : 'right',
		trigger : 'hover',
		html : true,
		container: 'body',
		delay: {show: 50, hide: 400}
	})

	var date_constraints = '<?php echo $this->data["Element"]["date_constraints"]; ?>';
	if(date_constraints == 1) {
		$("#el_dc_yes").trigger('click')
	}
  $('.icon_center_option').click(function(){

	var hr = $(this).attr('href');
	 location.reload(hr);
	//alert(hr);
	//window.location.href = hr;
  })


	$('.reopen-signoff').tooltip({

		 container: 'body', placement: 'auto', 'template': '<div class="tooltip reopen-signoffer" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
	})

})
</script>

<style type="text/css">
	textarea {
		resize: vertical;
	}
	.dfv_prg_class{
		pointer-events: none;
	}
</style>