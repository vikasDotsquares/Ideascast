<script src="<?php echo SITEURL; ?>js/jquery.canvasjs.min.js" type="text/javascript"></script>

	<div class="panel-dafult vote_result_box">
	
	
		<div class="panel-header bg-green pawan">
			
			<h4 class="modal-title">
			 Vote Details
			</h4>
			
		</div>
		<!---->
		<div class="panel-body">
		<div class="row">
		
		<div class="graph_datils">
			<?php 
			$vote_id = '';

		if(isset($vote_details['Vote']['id'])){ $vote_id =  $vote_details['Vote']['id']; } ?>
		<div class="modal-body">
			<div class="row vote_detail_left">
			
			<?php 
			$ArrVoteResult = array();
			if(isset($vote_details) && !empty($vote_details)){ ?>
				
				<div class="form-group clearfix">
				<label  class="control-label col-md-4 col-xs-6">Vote Reason:</label>
				<div class="control-label col-md-8 col-xs-6"><?php if(isset($vote_details['Vote']['reason']))   echo htmlentities($vote_details['Vote']['reason'], ENT_QUOTES, "UTF-8");;  ?></div>
				</div>
				
				<div class="form-group clearfix">
				<label  class="control-label col-md-4 col-xs-6">Voting Method:</label>
				<p class="control-label col-md-8 col-xs-6"><?php if(isset($vote_details['VoteQuestion']['VoteType']['title'])) echo $vote_details['VoteQuestion']['VoteType']['title'];				
				$typeDesc = $this->Common->VoteTypeDescription($vote_details['VoteQuestion']['VoteType']['id']);
				?>&nbsp;<a  tabindex="0" data-placement="top" class="btn toltipover secondButton" role="button" data-toggle="popover" data-trigger="hover" title="" data-content="<?php echo $typeDesc;?>"><i class="fa fa-info"></i></a>
				<?php /* <a class="todolist_links" title="<?php echo $typeDesc;?>" style="cursor:pointer;" ><i id="typeDescIcon" class="fa fa-info-circle" ></i></a>*/ ?></p>
				</div>
				
				<div class="form-group clearfix">
				<label  class="control-label col-md-4 col-xs-6">Voting For:</label>
				<p class="control-label col-md-8 col-xs-6"><?php if(isset($vote_details['VoteQuestion']['title']))   
				
				echo htmlentities($vote_details['VoteQuestion']['title'], ENT_QUOTES, "UTF-8");
				?></p>
				</div>
				
				<?php if(isset($vote_details['VoteQuestion']['VoteQuestionOption']) && !empty($vote_details['VoteQuestion']['VoteQuestionOption'])){ 
					
					$i=0;
					$options = array();
					$start = '';
					$end = '';
					//pr($vote_details);
					foreach($vote_details['VoteQuestion']['VoteQuestionOption'] as $options){
						if(isset($vote_details['VoteQuestion']['vote_type_id']) && !empty($vote_details['VoteQuestion']['vote_type_id']) && $vote_details['VoteQuestion']['vote_type_id'] != '6' && $vote_details['VoteQuestion']['vote_type_id'] != '5' && $vote_details['VoteQuestion']['vote_type_id'] != '2'){
							//$ArrVoteResult[$i]['x'] = $this->Common->getVoteCount($options['id'], $vote_details['Vote']['id']);
							$ArrVoteResult[$i]['x'] = $i+5;
							//$ArrVoteResult[$i]['y'] = $options['option'];
							$ArrVoteResult[$i]['y'] = $this->Common->getVoteCount($options['id'], $vote_details['Vote']['id']);
							$ArrVoteResult[$i]['label'] = $options['option'];
						}else if(isset($vote_details['VoteQuestion']['vote_type_id']) && $vote_details['VoteQuestion']['vote_type_id'] == '2'){
							if(empty($start) && $start != '0'){
								$start = $options['option'];
							}else{
								$end = $options['option'];								
							}						
						}else if(isset($vote_details['VoteQuestion']['vote_type_id']) && ($vote_details['VoteQuestion']['vote_type_id'] == '5' || $vote_details['VoteQuestion']['vote_type_id'] == '6')){
							/* $ArrVoteResult[$i]['x'] = $this->Common->getVoteCountCumulative($options['id'], $vote_details['Vote']['id']);
							$ArrVoteResult[$i]['y'] = $options['option'];
							$options[$i] =  $options['option']; */
							$ArrVoteResult[$i]['x'] = $i+5;
							$ArrVoteResult[$i]['y'] = $this->Common->getVoteCountCumulative($options['id'], $vote_details['Vote']['id']);
							$ArrVoteResult[$i]['label'] = $options['option'];
						}
				
					$i++;
					}
					if(isset($start) && isset($end) && !empty($end)){
						for($j=$start; $j<=$end; $j++){
							$ArrVoteResult[$j]['x'] = $j+5;
							$ArrVoteResult[$j]['y'] = $this->Common->getVoteCountScore($j, $vote_details['Vote']['id']);
							$ArrVoteResult[$j]['label'] = $j;
						}
					}
				}
				?>
				</div>
				<?php if(isset($vote_id) && !empty($vote_id)){ ?>
			<div class="participants">
				<div class="row">
					<div class="form-group clearfix  col-sm-6">
						<label class="control-label">Total Invites:</label>
						<div class="colum-data"><?php $total = $this->Common->totalinvites($vote_id); echo $total; ?></div>
					</div>
					
					<div class="form-group clearfix col-sm-6">
						<label  class="control-label ">Participants:</label>
						<div class="colum-data"><?php $totalparticipants = $this->Common->totalparticipants($vote_id); echo $totalparticipants; ?></div>
					</div>
				</div>
				
				<div class="row">
					<div class="form-group clearfix col-sm-6">
						<label  class="control-label ">Request Declined:</label>
						<div class="colum-data"><?php $totaldeclined = $this->Common->totaldeclined($vote_id); echo $totaldeclined; ?></div>
					</div>
					
					<div class="form-group clearfix col-sm-6">
						<label  class="control-label">No Response:</label>
						<div class="colum-data"><?php echo ($total - ($totalparticipants + $totaldeclined)); ?></div>
					</div>
				</div>
			</div>
			<?php } ?>
				
			<?php } ?>				
		</div>
		</div>
		
		<div class="graph_view">	

			<div class="chartContainer" id="chartContainer<?php echo $vote_id; ?>"></div>
			
		</div>
		
		</div>
		</div>
		
		
	</div><!-- /.modal-content -->


<style>
.fa-info {
    background: #00aff0 none repeat scroll 0 0;
    border-radius: 50%;
    color: #fff;
    font-size: 13px;
    height: 22px;
    line-height: 21px !important;
    width: 22px;
}

.vote_detail_left .btn{
	padding:6px 4px;
} 


.bg-green-active, .modal-success .modal-header, .modal-header, .modal-success .modal-footer, .panel-primarys > .panel-heading {
  background-color: #5f9323 !important;
  color: #ffffff;
}

.modal-dialog .modal-content .modal-footer {
    background: #eeeeee none repeat scroll 0 0 !important;
    border-top-color: #aaaaaa;
    color: #333333;
}

.canvasjs-chart-credit {
  display: none;
}
.chartContainer{min-height:300px;}
.participants { border-top:2px solid #00ACD6; padding-top:15px;}
.participants .control-label { float:left; width:120px;}
.participants .colum-data { display: block; overflow:hidden; text-align:center;}
.participants .row { border:none;}
.vote_result_box { margin-bottom:15px}
.vote_result_box .panel-header {    background: #67a028 !important;}
.vote_result_box .row { border:none; margin-left: -15px;margin-right: -15px;}
.vote_result_box .panel-body {max-width: 100%;   overflow:initial;}
.vote_result_box .vote_detail_left { padding-top:20px;}
.vote_result_box h4.modal-title { padding-left:12px;}
.vote_result_box .panel-body .col-sm-6 { display:table-cell; vertical-align:top;}
.control-label{ word-break : keep-all;}
.control-label{ word-break : keep-all;}
</style>

<?php

 if(isset($ArrVoteResult) && !empty($ArrVoteResult)){ 



$ArrVoteResult_new = array_values($ArrVoteResult);
 

 ?>
	<script type="text/javascript">
 	$(function(){
			var chart = new CanvasJS.Chart("chartContainer<?php echo $vote_id; ?>",
			{
				title:{
					text: "Vote Results"
				},
				axisX: {
					title:"Vote Options"
				},
				axisY:{
					title: "Votes",
					interval: 5,
					minimum: 0,
				},

				data: [
				{
					type: "column",

					dataPoints: <?php echo json_encode($ArrVoteResult_new); ?>,
				}
				]
			});

			chart.render();
		})  
	</script>
<?php } ?>


<script type="text/javascript" >
$(function(){
		
		$('.secondButton').popover({
		  container: "body",
		  placement: "left",
		  html: true,
		  content: function () {
			return '<div class="popover-message">' + $(this).data("content") + '</div>';
		  }
		});
		
		$('.todolist_links').tooltip({
            template: '<div class="tooltip CUSTOM-CLASS" style="text-transform:none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="text-transform:none !important;"> </div></div>'
            , 
			'container': 'body', 
			'placement': 'top',
        })
		
		 $('.todolist_links').on('mouseleave', function (e) {
            var $tooltip = $(this).data('bs.tooltip'),
                    $tip = $tooltip.$tip;

            $tip.hide()

        })
	})
</script>

<style>

</style>