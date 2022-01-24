<style>
#users-table thead tr {
	background-color: #000000;
	border-top: 2px solid #2c7dac;
}
.table-fixed {
	max-height: 400px;
	overflow-y: auto;
}
</style>

<!-- POPUP MODEL BOX CONTENT HEADER -->
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h3 class="modal-title" id="createModelLabel">Participants</h3>
	</div>

	<!-- POPUP MODAL BODY -->
	<div class="modal-body">
		<?php 
		$users = $parent = null;
		if( isset($type) && !empty($type) ) {
			if( $type == 'feedback' ) {
				$parent = $viewData['Feedback'];
				$users = $viewData['FeedbackUser'];
			}
			else if( $type == 'vote' ) {
				$parent = $viewData['Vote'];
				$users = $viewData['VoteUser'];
			}
		}
		
		?>
		
		
		<div class="table-fixed" style="">
			<?php if(isset($users) && !empty($users)){ ?>
			<table class="table table-striped" id="users-table">
				<thead>
					<tr class="bg-light-blue-gradient">
						<th width="50%">Name</th>
						<th width="25%">Invited</th>
						<th width="25%">Responded</th>
					</tr>
				</thead>
				
				<tbody>
				<?php foreach($users as $user){ ?>
					<?php if(isset($user['User']['UserDetail']) && !empty($user['User']['UserDetail'])){ ?>
					<tr>
						<td>
						<?php echo $user['User']['UserDetail']['first_name'].' '.$user['User']['UserDetail']['last_name']; ?> 
						</td>
						<td>
							<?php
							$created = '';
							if(isset($user['created']) && !empty($user['created'])){ $created = $user['created']; } ?>
							<?php echo date('d M,Y', $created); ?>
						</td>
						<td>
							<?php 
							if( $type == 'feedback' ) {
								echo $this->Common->feedbackresponded($parent['id'], $user['User']['id']); 
							}
							else if( $type == 'vote' ) {
								echo $this->Common->responded($parent['id'], $user['User']['id']); 
							}
							?>
						</td>
					</tr>
					<?php } ?>
				<?php } ?>
				</tbody>
			</table>
			<?php }
				else { ?>
				<div class="">
					<div class="col-sm-12 text-center" >
						No Participants Found !!!
					</div>
				</div>
				<?php } ?>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		 
		</div>
		
		
		
		
	</div>
	
	<!-- POPUP MODAL FOOTER -->
	<div class="modal-footer">
		 <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>