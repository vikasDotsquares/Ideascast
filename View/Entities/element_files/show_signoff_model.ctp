<style type="text/css">
    #list_percentages, #list_impacts {
        font-size: 12px;
    }
    .dropdown-menu.inner {
        box-shadow: none !important;
    }
    .dropdown-menu.inner > li a {
        padding: 4px 10px !important;
    }
    .btn.dropdown-toggle.btn-default {
        background-color: #fff ;
    }
	
	.response-description{
		margin-bottom:10px;
	}
	
	.response-description label>strong{
		font-weight:550 !important;
	}

	.response-description input[type=file] {
		width: 100%;
		border: 1px #ccc solid;
	}
	textarea{
		margin-bottom:0 !important;
		/* pointer-events :none; */
	}
	 
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <h3 class="modal-title " id="createModelLabel">Task Sign Off</h3>
</div>
<div class="modal-body">
    <div class="elements-list">
        <p><strong>Signed off by:</strong> <?php echo ( isset($userDetail['UserDetail']['full_name']) && !empty($userDetail['UserDetail']['full_name']) ) ? $userDetail['UserDetail']['full_name'] : '';?> on <?php 
			//echo date('d M, Y h:i A',strtotime($comment['SignoffTask']['created']));		
			echo (isset($comment['SignoffTask']['created']) && !empty($comment['SignoffTask']['created']) )? $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',strtotime($comment['SignoffTask']['created'])),$format = 'd M, Y h:i A') : 'N/A';
		?></p>
    </div>
    <div class="" id="impact_assessment">
            
                <div class="row">
                    <div class="col-md-12">
						<div class="response-description">
                            <label><strong>Comment:</strong></label>
							<textarea class="form-control" rows="4" placeholder="Max chars allowed 250" id="signoff_comment" name="signoff_comment" readonly="readonly" style="resize: vertical; min-height:90px; background-color:#fff;"><?php 
								echo $comment['SignoffTask']['task_comment'];
							?></textarea>
                        </div>
					</div>
					<?php if( isset($comment['SignoffTask']['task_evidence']) && !empty($comment['SignoffTask']['task_evidence']) ){
						$evidence_title = ( isset($comment['SignoffTask']['evidence_title']) && !empty($comment['SignoffTask']['evidence_title']) ) ? $comment['SignoffTask']['evidence_title'] : $comment['SignoffTask']['task_evidence'];							
					?>
					<div class="col-md-12">
						<div class="response-description">
							<label><strong>Evidence:</strong></label>
							<a href="<?php echo SITEURL.'entities/download_signoff/'.$comment['SignoffTask']['id']?>"><?php echo $evidence_title; ?></a>
						</div>
					</div>
					<?php } ?>
                </div>
             
        </div>
</div>
<!-- POPUP MODAL FOOTER -->
<div class="modal-footer">    
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>