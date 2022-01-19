<div class="rm-type-dd">
    <span class="selected-type"><?php echo (isset($user_risk_types) && !empty($user_risk_types)) ? 'Update Risk Type' : 'No Risk Type found'; ?></span>
    <?php if(isset($user_risk_types) && !empty($user_risk_types)) {
        asort($user_risk_types);
     ?>
    <ul class="list-group dd-data">
        <?php foreach ($user_risk_types as $id => $title) {
            $exists = custom_risk_involved($id);
        ?>
        <li class="list-group-item" data-id="<?php echo $id; ?>">
            <span class="rm-type-text edit-inline"><?php echo htmlentities($title,ENT_QUOTES, "UTF-8"); ?></span>
            <div class="pull-right controls">
                <?php if(!$exists){ ?>
                <a href="#" class="btn btn-xs  btn-change tipText" title="Update">
                    <i class="edit-icon"></i>
                </a>
                <a href="#" class="btn btn-xs  btn-trash tipText" title="Remove">
                    <i class="deleteblack"></i>
                </a>
                <?php } ?>
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