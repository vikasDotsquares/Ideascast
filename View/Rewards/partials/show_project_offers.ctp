<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="createModelLabel">Project - Offers</h3>
</div>
<div class="modal-body clearfix">

<?php
$current_user_id = $this->Session->read('Auth.User.id');
if(isset($project_id) && !empty($project_id)) {
    $currency_symbol = project_currency_symbols($project_id);
    $project_offers = project_offers($project_id, true);

?>

<?php if($project_offers){ ?>
    <div class="list-group project-offers clearfix">
        <?php foreach ($project_offers as $key => $value) {
            $data = $value['RewardOffer'];
            $updated_by = 'Created by: ';
            $updated_user = user_full_name($data['creator_id']);
            $updated_by .= $updated_user;
        ?>
            <div class="list-group-item <?php if(isset($data['ended']) && !empty($data['ended'])){ ?> offer-ended <?php } ?>" data-id="<?php echo $data['id']; ?>">
                <h4 class="list-group-item-heading"><?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8"); ?></h4>
                <div class="list-group-item-text clearfix">
                    <span class="required-text">
                        <span class="req">Required: <?php echo $currency_symbol; ?><?php echo $data['amount']; ?></span>
                        <span class="buys">Buys: <?php echo offer_buys($data['id']); ?></span>
                    </span>
                </div>
                <div class="list-group-item-text clearfix">
                    <span class="required-text">
                        <?php echo $updated_by; ?> - <?php echo $this->Wiki->_displayDate(date('Y-m-d h:i:s A', strtotime($data['created'])), $format = 'd M Y g:i A'); ?>
                    </span>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<script type="text/javascript">
    $(function(){

        $('.edit-offer').on('click', function(event){
            event.preventDefault();
            var $parent = $(this).parents('.list-group-item:first'),
                data = $parent.data(),
                offer_id = data.id;

            $.project_offers_detail(offer_id).done(function(response){
                if(response.success){
                    var content = response.content;
                    $('.inp-offer-id').val(content.id);
                    $('.inp-offer-title').val(content.title);
                    $('.inp-offer-amount').val(content.amount);
                    $('.offer-updated-date').text(content.updated);
                }
            })

            var $top_parent = $parent.parents('.project-offers:first');
            $top_parent.find('.list-group-item').removeClass('edit');
            $parent.addClass('edit');
            $('.create-offer-wrap').slideDown(400);

        });

        $('.clear-offer-title').on('click', function(event){
            event.preventDefault();

            $('.inp-offer-title').val('').focus();
        });

        $('.delete-offer').on('click', function(event){
            event.preventDefault();
            var $btn_grp = $(this).parents('.btn-group:first');
            $('.offer-confirm', $btn_grp).show('slide', {direction: 'right'}, 600);
        });

        $('.confirm-no').on('click', function(event){
            event.preventDefault();
            var $btn_grp = $(this).parents('.btn-group:first');
            $('.offer-confirm', $btn_grp).hide('slide', {direction: 'right'}, 600);
        });

        $('.confirm-yes').on('click', function(event){
            event.preventDefault();
            var $btn_grp = $(this).parents('.btn-group:first')
                $list = $(this).parents('.list-group-item:first'),
                data = $list.data(),
                offer_id = data.id;

            $.ajax({
                url: $.url + 'rewards/delete_project_offer',
                type: 'POST',
                dataType: 'json',
                data: {id: offer_id},
                success: function(response) {
                    $.reload_data = true;
                    $('.cancel-offer').trigger('click', ['show']);
                    $('.offer-confirm', $btn_grp).hide('slide', {direction: 'right'}, 600, function(){
                        $list.slideUp(300, function(){
                            $(this).remove();
                        })
                    });
                }
            })
        });

    })
</script>
<?php } ?>

</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

<style type="text/css">

    .offer-ended {
        background-color: #f0f0f0;
    }

    .req, .buys {
        display: inline-block;
    }
    .req {
        min-width: 200px;
    }
    /*.buys {
        margin-left: 50px;
    }*/
    .required-text {
        /*float: left;
        width: 50%;*/
        display: inline-block;
    }
    /*.created-text {
        float: left;
        width: 50%;
    }*/
    .create-offer {
        display: block;
        padding: 15px 10px 5px 15px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }
    .project-offers {
        max-height: 302px;
        overflow-y: auto;
    }
    .project-offers .list-group-item {
        cursor: default;
    }
    .project-offers .list-group-item:hover {
        background-color: #f8f8f8;
    }
    .project-offers .list-group-item-heading {
        font-size: 14px;
        overflow: hidden;
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        line-height: normal;
    }
    .project-offers .list-group-item-heading .title-text {
        font-size: 14px;
        white-space: nowrap;
        text-overflow: ellipsis;
        width: 80%;
        display: inline-block;
        overflow: hidden;
    }
    .project-offers .list-group-item-text {
        font-size: 13px;
        margin-bottom: 3px;
        padding-top: 3px;
    }

    .clear-offer-title {
        cursor: pointer;
    }

    .list-group-item.edit, .list-group-item.edit:focus, .list-group-item.edit:hover {
        z-index: 2;
        color: #333;
        background-color: #f0f0f0;
        border-color: #f0f0f0;
    }


</style>


