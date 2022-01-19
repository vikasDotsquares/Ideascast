<?php
$current_user_id = $this->Session->read('Auth.User.id');

if(isset($project_id) && !empty($project_id)) {
$currency_symbol = project_currency_symbols($project_id);
$project_offers = project_offers($project_id);

?>

<?php if($project_offers){ ?>
    <div class="list-group project-offers clearfix">
        <?php foreach ($project_offers as $key => $value) {
            $data = $value['RewardOffer'];
            $updated_by = 'Created by: ';
            $updated_user = user_full_name($data['creator_id']);
            $updated_by .= $updated_user;// . ': ';
            $is_offer_redeemed = is_offer_redeemed($data['id']);
        ?>
            <div class="list-group-item <?php if(isset($data['ended']) && !empty($data['ended'])){ ?> offer-ended <?php } ?>" data-id="<?php echo $data['id']; ?>" >
                <h4 class="list-group-item-heading"><?php echo htmlentities($data['title'], ENT_QUOTES, "UTF-8"); ?></h4>
                <div class="list-group-item-text">
                    <span class="pull-left1">
                        <span class="req">Required (<?php echo $currency_symbol; ?>): <?php echo $data['amount']; ?></span>
                        <span class="buys">Buys: <?php echo offer_buys($data['id']); ?></span>
                    </span>
                    <span class="pull-right">
                        <?php if(!isset($data['ended']) || empty($data['ended'])){  ?>
                        <div class="btn-group">
                            <?php if($current_user_id == $data['creator_id']) { ?>
                                <?php if( $is_offer_redeemed ) { ?>
                                    <span class="btn btn-xs btn-success edit-offer tipText" title="Edit"><i class="fa fa-pencil"></i></span>
                                    <div class="btn btn-xs offer-confirm">
                                        <span class="btn-confirm confirm-yes btn-success tipText" title="Yes"><i class="fa fa-check"></i></span>
                                        <span class="btn-confirm confirm-no btn-danger tipText" title="No"><i class="fa fa-times"></i></span>
                                    </div>
                                    <span class="btn btn-xs btn-danger delete-offer tipText" title="Delete"><i class="fa fa-trash"></i></span>
                                <?php } ?>
                                    <span class="btn btn-xs btn-danger end-offer " >End Offer</span>
                                <?php } ?>
                        </div>
                        <?php } ?>
                            <span class="btn btn-xs btn-default btn-offer-end">Offer Ended</span>
                    </span>
                </div>
                <?php
                $created = $this->Wiki->_displayDate(date('Y-m-d h:i:s A', strtotime($data['created'])), $format = 'd M Y g:i A');
                    ?>
                    <div class="list-group-item-text"><?php echo $updated_by.' - '.$created; ?></div>
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
                offer_id = data.id,
                project_id = $('#sel_offers_project').val();

            $.project_offers_detail(offer_id).done(function(response){
                if(response.success){
                    var content = response.content;
                    $('.inp-offer-id').val(content.id);
                    $('.inp-offer-title').val(content.title);
                    $('.inp-offer-amount').val(content.amount);
                    $('.offer-updated-date').text(content.updated);
                }
                else{
                    $('#sel_offers_project option[value=""]').prop('selected', true);
                    $('#sel_offers_project option[value="'+project_id+'"]').prop('selected', true);
                    $('#sel_offers_project').trigger('change');
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
            var $btn_grp = $(this).parents('.btn-group:first'),
                $list = $(this).parents('.list-group-item:first'),
                data = $list.data(),
                offer_id = data.id,
                project_id = $('#sel_offers_project').val();
            $.reload_data = true;
                $.project_offers_detail(offer_id).done(function(response){
                    if(response.success){
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
                    }
                    else{
                        $('#sel_offers_project option[value=""]').prop('selected', true);
                        $('#sel_offers_project option[value="'+project_id+'"]').prop('selected', true);
                        $('#sel_offers_project').trigger('change');
                    }
                })
        });

        $('.end-offer').click(function(event) {
            var $this = $(this),
                $btn_grp = $(this).parents('.btn-group:first')
                $list = $(this).parents('.list-group-item:first'),
                data = $list.data(),
                offer_id = data.id,
                project_id = $('#sel_offers_project').val();
            $.reload_data = true;
            $.ajax({
                url: $js_config.base_url + 'rewards/end_project_offer',
                type: 'POST',
                dataType: 'json',
                data: {id: offer_id, project_id: project_id},
                success: function(response) {
                    if($('.offer-confirm', $btn_grp).length > 0){
                        $('.offer-confirm', $btn_grp).hide('slide', {direction: 'right'}, 600, function(){
                            $list.addClass('offer-ended');
                        });
                    }
                    else{
                        $list.addClass('offer-ended');
                    }
                }
            });
        });

    })
</script>

<style type="text/css">
    .list-group-item.offer-ended {
        pointer-events: none;
        background-color: #f0f0f0;
    }
    .offer-ended .edit-offer, .offer-ended .delete-offer, .offer-ended .end-offer {
        opacity: 0.6;
    }
    .offer-ended .btn-group {
        display: none;
    }
    .list-group-item .btn-offer-end {
        display: none;
    }
    .list-group-item.offer-ended .btn-offer-end {
        display: inline-block;
    }
    .req, .buys {
        display: inline-block;
    }
    .req {
        min-width: 150px;
    }
    .buys {
        margin-left: 50px;
    }
</style>
<?php } ?>


