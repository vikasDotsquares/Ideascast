<div class="rm-type-dd" lister="lister">
    <span class="selected-type"><?php
	echo (isset($projects_types) && !empty($projects_types)) ? 'Edit Task Types' : 'No Type found'; ?></span>
    <?php if(isset($projects_types) && !empty($projects_types)) { ?>
    <ul class="list-group dd-data">
        <?php
		foreach ($projects_types as $id => $title) {
            $exists = custom_type_involved($id,$project_id);
        ?>
        <li class="list-group-item" data-id="<?php echo $id; ?>" data-projectids="<?php echo $project_id; ?>" >
            <span class="rm-type-text edit-inline"><?php   echo htmlentities($title, ENT_QUOTES, "UTF-8"); ?></span>
            <div class="pull-right controls">
                <?php if(!$exists){ ?>
                <a href="#" class="btn-change tipText" title="Edit">
                    <i class="edit-icon"></i>
                </a>
                <a href="#" class="btn-trash tipText" title="Delete">
                    <i class="deleteblack"></i>
                </a>
                <?php } else {
					/* if( $title == 'General' ){
						echo "(Cannot Edit/Remove)";
					} */
				} ?>
            </div>
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