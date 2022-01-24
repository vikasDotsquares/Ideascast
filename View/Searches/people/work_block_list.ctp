<?php $block_count = (isset($data) && !empty($data)) ? count($data) : 0; ?>
<div class="workblock-col-header">
    <div class="workblock-col workblock-col-1">
        Blocks <span class="total-people">(<?php echo $block_count; ?>)</span>
    </div>
    <div class="workblock-col workblock-col-2">
        To Date
    </div>
    <div class="workblock-col workblock-col-3">
        Comment
    </div>
    <div class="workblock-col workblock-col-4">
        Actions
    </div>
</div>

<div class="workblock-data-list">
<?php if(isset($data) && !empty($data)){ ?>
    <?php foreach ($data as $key => $value) {
        $detail = $value['UserBlocks'];
    ?>
    <div class="workblock-data-row" data-id="<?php echo $detail['id']; ?>">
        <div class="workblock-col workblock-col-1">
            <?php echo ( isset($detail['work_start_date']) && !empty($detail['work_start_date']) ) ? date('d M Y', strtotime($detail['work_start_date'])) : ''; ?>
        </div>
        <div class="workblock-col workblock-col-2">
            <?php echo ( isset($detail['work_end_date']) && !empty($detail['work_end_date']) ) ? date('d M Y', strtotime($detail['work_end_date'])) : ''; ?>
        </div>
        <div class="workblock-col workblock-col-3">
           <span class="wb-comment-text"> <?php echo $detail['comments']; ?> </span>
        </div>
        <div class="workblock-col workblock-col-4 workblock-actions">

            <a class="tipText delete-work" href="#" title="Delete"> <i class="deleteblack"></i></a>
        </div>
    </div>
    <?php } ?>
<?php }else{ ?>
<div class="no-sec-data-found">No work blocks</div>
<?php } ?>
</div>
