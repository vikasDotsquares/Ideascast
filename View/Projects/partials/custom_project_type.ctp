<div class="rm-type-dd"  lister="lister">
    <span class="selected-type"><?php
	echo (isset($projects_types) && !empty($projects_types)) ? 'Edit Task Types' : 'No Type found'; ?></span>
    <?php if(isset($projects_types) && !empty($projects_types)) { ?>
    <ul class="list-group dd-data">
        <?php
		foreach ($projects_types as $id => $title) {
            $exists = custom_type_involved($id,$project_id);
        ?>
        <li class="list-group-item" data-id="<?php echo $id; ?>" data-projectids="<?php echo $project_id; ?>" >
            <span class="rm-type-text edit-inline"><?php echo htmlentities($title, ENT_QUOTES, "UTF-8"); ?></span>
            <div class="pull-right controls">
                <?php if(!$exists){ ?>
				<div class="task-type-controls">
					<a href="#" class="btn-change tipText" title="Edit">
						<i class="edit-icon"></i>
					</a>
					<div class="btn btn-xs offer-confirm">
						<span class="confirm-yes tipText" title="Yes"><i class="activegreen"></i></span>
						<span class="confirm-no tipText" title="No"><i class="inactivered"></i></span>
					</div>
					<a href="#" class="btn-trash-old delete-task-type tipText" title="Delete">
						<i class="deleteblack"></i>
					</a>

				</div>
                <?php } else { echo "(In Use)&nbsp;"; ?>
						 <a href="#" class="btn-change tipText" title="Edit">
						 	<i class="edit-icon"></i>
						</a>
						 <a href="#" class=" tipText typereassign <?php if($type_count <= 1 ){ ?>disabled<?php } ?>" data-typeid="<?php echo $id; ?>" data-project_id="<?php echo $project_id; ?>" title="Reassign">
							<i class="reassignblackicon"></i>
						</a>
					<?php
				} ?>
            </div>
			<?php if($exists){ ?>
				<div class="elementtypelist ">
					<select name="data[ElementType][type_id]" data-projectid="<?php echo $project_id; ?>"  data-eletypeid="<?php echo $id; ?>" class="form-control col-md-9 element_typeid" >
						<option value="">Select Task Type</option>
					</select>
					<a class="btn btn-success btn-sm assing_element" style="display:inline-block;">Reassign</a>
					<a class="btn btn-danger btn-sm cancel_assignment" style="display:inline-block;">Cancel</a>
				</div>
			<?php } ?>
        </li>
        <?php } ?>
    </ul>
    <?php } ?>
</div>

<script type="text/javascript">
    $(function(){
        var $triggered = $('.rm-type-dd');
        var $list = $triggered.find('ul.dd-data');
        $('.rm-type-dd').data('list', $list);
        $list.data('triggered', $triggered);
    })
</script>