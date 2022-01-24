<?php 
if( isset($data) && !empty($data) ) {
	foreach($data as $key => $val) { //pr(); 
	$consal = "subpanel_".$val['id'];
	$fonid = "formManageDoList_".$val['id'];
	$fonidbutton = "button_".$val['id'];
	//echo $val['id']."<br>";
?> 
  <form id="<?php echo $fonid; ?>" method="post" enctype="multipart/form-data">	
 
<?php echo $this->Form->input("DoList.id",array("placeholder"=>"Max chars allowed 100", 'value'=>$val['id'], "class"=>"form-control", "type"=>'hidden',"div"=>false));?>		
	<div class="panel panel-default" id="<?php echo $consal; ?>">
		<div class="panel-heading">
			<h4 class="panel-title" id="">
				<a class="collepse open_sub_panel" role="button" data-toggle="collapse" href="#update_sub_panel_<?php echo $key; ?>" aria-expanded="false" aria-controls="update_sub_panel_<?php echo $key; ?>" id=""> Update: <?php echo $val['title']; ?></a>
			</h4>
		</div>
		<div class="panel-body panel-collapse collapse"   id="update_sub_panel_<?php echo $key; ?>">
				
		 
			<div class="form-group col-lg-12">
				<label for="title">Sub To-do:</label>
				<?php echo $this->Form->input("DoList.title",array("placeholder"=>"Max chars allowed 100", "id"=>"title",'value'=>$val['title'], "class"=>"form-control editor_title", "label"=>false,"div"=>false));?>
				<span id="counter_<?php echo $val['id'];?>" class="error-message error text-danger" ></span>
				
			</div>
			<div class="form-group col-lg-6">
				<label for="users">Assigned To:</label>
				<?php 
				$users = $this->requestAction(array("controller"=>"todos","action"=>"get_users",$val['project_id']));
				$users = json_decode($users, true);
				$userArr = array();
				 
				if (isset($users) && !empty($users)) {
					foreach($users as $val_u){ 
							if(isset($val_u['name']) && !empty($val_u['name'])){
								$userArr[$val_u["id"]] = $val_u["name"];
							}
					} 
				}
				
				
				$people = $this->Group->dolist_users($val['id'],false);
 
				$selected_users = $people_list = null;
				if( isset($people) && !empty($people) ) {
				    $selected_users =  Set::extract($people, '/DoListUser/user_id');					 
					foreach($people as $key => $user ) {					    
						$user_data = $this->ViewModel->get_user_data($user['DoListUser']['user_id']);
						$people_list[$user_data['UserDetail']['user_id']] = $user_data['UserDetail']['first_name'] . ' ' .$user_data['UserDetail']['last_name'];
						
					}
				}
				//pr($val); 
				?>
				
				<?php  
				echo $this->Form->input("DoListUser.user_id",array("title"=>"Select User",'type' => 'select', "multiple"=>"multiple", "id"=>"", "class"=>"form-control aqua sub_user_selection", "options"=>$userArr,"selected"=>$selected_users, "label"=>false, "div"=>false, "style"=>"display: none;", "data-width"=>"100%" ));?>
				
				<span class="error-message text-danger"></span>
				
			</div> 
			
			<div class="form-group col-lg-6">
				<label for="dateby_parent">Date From - To:</label>
				<div class="input-group"> 
					<?php 
					//pr($val);
					$dateby = '';
					if(!empty($val["start_date"]) && !empty($val["end_date"])){
						$dateby = date("d M Y",strtotime($val["start_date"])).' - '.date("d M Y",strtotime($val["end_date"]));
                     }
					 
					$ids ="datepicker_".$val["id"];				
					echo $this->Form->input("DoList.dateby",array("readonly"=>"readonly","value"=>$dateby ,"id"=>$ids,"class"=>"date-picker form-control","label"=>false,"div"=>false,"style"=>"cursor: pointer !important;"));
                                        ?>
					
					
					<div class="input-group-addon open-end-date-picker calendar-trigger">
						<i class="fa fa-calendar"></i>
					</div>
				</div>
				<span style="" class="error-message text-danger"></span>
			</div>

			<div class="form-group col-lg-12">
				<label for="doc_file">Upload:</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="uploadblackicon"></i>
					</div>
					<span title="" class="docUpload icon_btn bg-white border-radius-right tipText" data-original-title="Click to upload multiple files">
						<?php echo $this->Form->input('DoListUpload.file_name.', [ 'type' => 'file', 'label' => false, 'div' => false, 'required' => false, 'class' => 'form-control upload-input sub-dolist-uploads', 'id' => '', 'placeholder' => 'Upload Multiple Files',"multiple"=>"multiple"]); ?> <span class="text-blue" id="upText">Upload Multiple Documents</span>
					</span>
					<span class="error-message text-danger"></span>
				</div>
				
				<?php 
					$uploads = $this->Group->do_list_uploads($val['id']);
					// pr($uploads);
				?>
				<ul class="list-group" id="dolist_uploads_list">
					<?php if( isset($uploads) && !empty($uploads) ){
							foreach($uploads as $uk => $up) {
					?>
								<li class="todoimg list-group-item">
									<a href="<?php echo SITEURL.TODO;?><?php echo $up['DoListUpload']['file_name'] ?>" class="todoimglink"  download  ><?php echo $up['DoListUpload']['file_name'] ?></a>
										<span class="del-img-todo pull-right">
												<a data-id="<?php echo $up['DoListUpload']['id'] ?>" data-type="dolist" title="Click here to delete" class="text-red tipText doc_delete" href="javascript:void(0);">
														<i class="fa fa-times"></i>
												</a>
										</span>
								</li>
					<?php
							}
					} ?>
				</ul>
			</div>
			
			<div class="form-group col-lg-12">
					<?php
					$disabled = $comp = null;
					if(isset($val['sign_off']) && $val['sign_off'] == 1){
						$disabled = 'disabled';
						$comp = true;
						
					}
					?>
					<a class="btn btn-success btn-sm submit_subtodo <?php echo $disabled;?>" data-do-id="<?php echo $val['id']; ?>" data-project-id="<?php echo $this->data['DoList']['project_id'];?>" id="<?php echo $fonidbutton; ?>"> Update </a> 
					<a class="btn btn-danger btn-sm cancel_subtodo"  > Cancel </a> 
					<label class="pull-right"><?php if($comp == true){echo "Signed Off";}?></label>
			</div>

		</div>
		</div>
 
 </form>
 
	<?php } ?>
<?php } ?>