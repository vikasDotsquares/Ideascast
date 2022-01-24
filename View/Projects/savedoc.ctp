
<style>
	body{  font-family: "Calibri" }
	table{	border-left: 1px solid #ccc;border-top: 1px solid #ccc;	border-spacing:0;border-collapse: collapse; }
	table td {	float: left;padding: 2mm;vertical-align:top;}
	table.heading{	height:50mm;}		
	h1.heading	{ color: #000;	font-size: 14pt;font-weight: normal;padding: 0 10px 0 0;text-align: right;	margin:0px;	}
	h2.heading	{font-size:14px;color:#000;	font-weight:normal;	padding: 0 5px 0 0;	text-align: right;	margin:0px;	}
	#invoice_body , #invoice_total{	width:100%;	}
	#invoice_body table , #invoice_total table{	  border: 1px solid #ccc;border-collapse: collapse;	border-spacing: 0;	display: block;	float: left;min-height: 360px;	width: 100%;}
	#invoice_body table td , #invoice_total table td{text-align:center; padding: 6px 1px; border:solid 1px #999;}
	li {font-weight: bold;	margin-left: 38px;	width: auto;}
	tr {float: left;width: 100%;}
	.wrapper {	margin: 0 auto;	width: 650px;}
	table tr.title td {	float:left; border-bottom-width:2px; background:#cccccc; text-transform:uppercase; font-size:15px; color:#000;}
	
	
	
	.forty{ font-size:32px; color:navy; text-align: center ;  margin-right:auto;
    margin-left:auto;width: 100%; margin: 50px auto 0; padding:0px; font-weight:bold ; background : #D9A602;color : #fff;
     font-family: "Calibri" ;}
	
	.thiry-six{ font-size:24px;color:#71B22B;text-align: center;  margin-right:auto;
    margin-left:auto;margin: 0px auto ;padding:0;  width: 100%; font-family: "Calibri" ;}
	
	.twenty-four{ font-size:20px;color:#000;text-align: center; margin-right:auto;
     margin-left:auto;margin: 0px auto ;padding:0; width: 100%; font-family: "Calibri" ;}
	.black{color: #1C1C1C;}
	
	.dark-blue{color: #006A6A;  font-family: "Calibri" ;}
	
	 .inner-heading { color : #006A6A; font-size:26px; font-weight:bold ; font-style : italic ; border-bottom : solid 1px #006a6a; padding: 0 0 10px;float:left;  font-family: "Calibri" ; page-break-before: always;}	
	
	.inner-heading-break{page-break-after: always;}
	
	.area-heading { color : #FFC000; font-size:22px; font-weight:bold ; font-style : italic ;  border-bottom : solid 1px #FFC000; padding: 0 0 10px; margin: 0; float:left;  }
	
	.area-headings { color : #C04830; font-size:20px; font-weight:bold ;  padding: 0 0 10px;float:left;  font-family: "Calibri" ; }
	
	.area-headingss { color : #006A6A; font-size:17px; font-weight:bold ;  padding: 0 0 10px;float:left;   font-family: "Calibri" ; }
	
	.desc{ padding: 10px 0; float:left;  font-family: "Calibri" ;}	 
	 
	.link { color : #76B531; font-size:13px;  font-family: "Calibri" ; margin: 0 0 0 20px;  float:left; width:100%;}
	
	.main_pads{
    margin: 0 50px;
	}
	
     /* .area-heading-bot {border-bottom : solid 1px #FFC000; margin: 0 0 20px 0;} */
	
	</style>




<div class="main_pads">
	<div class="col-xs-12">
 
		<div class="row">
			<section class="content-header clearfix"> 
					<aside class="box-title pull-left">
						<div class="text-muted date-time">
							<h1 class="forty"><?php echo strip_tags($projects['0']['Project']['title']);
							$data =$this->Common->userDetail($projects['0']['User']['id']);
							?>

							</h1><br>
							<p class="thiry-six"><span class="black">Created by :</span> <?php echo $data['UserDetail']['first_name']." ".$data['UserDetail']['last_name']; ?>
							</p><br>
							<p class="twenty-four"><span class="black">Date : </span><?php echo date('d-M-Y',$projects['0']['Project']['created']);  ?>
							</p>
						</div>
					</aside>
					<div class="box-tools pull-right">
						<div id="viewcontrols" class="btn-group tipText" title="<?php tipText('change-layout' ); ?>"> 
							<a class="gridview active btn btn-success btn-sm" id="grid_view" data-limit="140"><i class="fa fa-th-large"></i></a>
							<a class="listview btn btn-success btn-sm" id="list_view" data-limit="470"><i class="fa fa-bars"></i></a>
							
						</div> 
					</div>
			</section>
		</div>

 
    <div class="box-content ">
	
            <div class="row ">
                <div class="col-xs-12">
					
					
				<?php 
				
				if( isset($projects) && !empty($projects)) { 
				?>
                    <div class="box border-top margin-top">
                        <div class="box-header" style=" ">
                            
							 
							
							
						
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
						
                        </div>
						<?php echo $this->Session->flash(); ?>
						<div class="box-body clearfix list-acknowledge">
							<div id="list_grid_containers" class="">
							
			
								<div class="grid clearfix filetree" id="browser" >	
									<?php									
									$project_counter = ( isset($projects) && !empty($projects) ) ? count($projects) : 0;
									foreach( $projects as $key => $val ) {
										$item = $val['Project'];
									    $comments = $this->requestAction('/users/workspace/'.$item['id']);
									?> 
									<p> 											
										  
										<div>
										
										<?php
										 
										if(!empty($comments)){
										$row_counter = 0;
										
										foreach($comments as $com1){
										
										$com = $com1['ProjectWorkspace'];
											
										if(in_array($com['workspace_id'],$wps_id)){
										
										echo "<pagebreak page-break-before=' always;'></pagebreak>";
										
										$d =	$this->ViewModel->countAreaElements($com['workspace_id']);
										
										$wsp =	$this->Common->workspace($com['workspace_id']);
										$wspDes = $wsp['Workspace']['description'];
										
										
							//	if( $d['active_element_count'] > 0 && (( isset($d['assets_count']['docs']) && !empty($d['assets_count']['docs']) ) || ( isset($d['assets_count']['links']) && !empty($d['assets_count']['links']) )) ) {
								
								
										echo '
										<div class="inner-heading-break">
<h2 class="inner-heading">Workspace : '.strip_tags($this->Common->workspaceName($com['workspace_id'])).'</h2>'; //die;
										
												//$allArea = $this->Common->area($com['workspace_id']);
												$allArea = $this->requestAction('/users/area/'.$com['workspace_id']);
										
										echo '<p class="desc">Description : '.$wspDes.'</p>';
										
										echo "
										
										<div class='area-heading-bot'>";
										
												foreach($allArea as $area){
													$areaAssets =	$this->ViewModel->countAreaElements(null, $area['Area']['id']);
													if( $areaAssets['element_count'] > 0 && (( isset($areaAssets['assets_count']['docs']) && !empty($areaAssets['assets_count']['docs']) ) || ( isset($areaAssets['assets_count']['links']) && !empty($areaAssets['assets_count']['links']) ) )) {
													
												echo "<p class='closed'><h2 class='area-heading'>&#9733; Area : ".strip_tags($area['Area']['title'])."</h2>";
												
												//echo "<p class='desc'>Description : ".$area['Area']['description']."</p>";
												
												
												echo "<div>";
												

	
													foreach($area['Elements'] as $elem){
														$elemAssets =	$this->ViewModel->countAreaElements(null, null, $elem['id']);
														if( ( isset($elemAssets['assets_count']['docs']) && !empty($elemAssets['assets_count']['docs'] ) ) || ( isset($elemAssets['assets_count']['links']) && !empty($elemAssets['assets_count']['links'] ) ) ) {	
															
													if(empty($elem['title'])){
													$etl = "N/A";
													}else{
													$etl = $elem['title'];
													}
													
													//pr($elem); die;
													
													  echo "<p class='closed'>
														<h2 class='area-headings'>&#x27a4; Element : ".strip_tags($etl)."</h2>";
													  
													  	echo '<p class="desc">Description : '.@$elem['description'].'</p>';


												
												
if( ( isset($elemAssets['assets_count']['links']) && !empty($elemAssets['assets_count']['links'] ) ) ) {												
										
													echo "<div>
												

														<p class='closed'>
														<h2 class='area-headingss'>&#xbb; Links</h2>
														<div>";
														$alldoc = $this->requestAction('/users/links/'.$elem['id']);
															foreach($alldoc as $doc){
															
															$chkLinks = explode("//",$doc['ElementLink']['references']);
															
															//pr($chkLinks); 
															
															
															if(isset($chkLinks['1']) && !empty($chkLinks['1'])){
															 echo "<p><i class='fa fa-link'></i> <span class='files'>"."<a target='_blank' class='link' href=".$doc['ElementLink']['references'].">". $doc['ElementLink']['title']."</a>";
															}else{
															 
															 echo "<p><i class='fa fa-link'></i> <span class='files'>"."<a target='_blank' class='link' href="."http://".$doc['ElementLink']['references'].">". $doc['ElementLink']['title']."</a>";
															}
									
													?>
									
															
													<?php	echo 	" </span></p>";
															}
															
													echo "
												</div>
												</p>
												</div>";

												}
												
												echo "</p>";		
															
													}
													
												}	
													
												echo "
												</div>
												
												</p>";
											//	echo '<br><p class="area-heading"> </p>';
												}
								}
										
									
									 
										echo "
										
										</div>
										</div>";		
										
										?>	
										<?php //pr($com);
									//	}
										
										
										
										}
									     
										}
										
										
										//die;
										}
										
									 ?>
									 
								
									</div>
									
									 								 
										</p>
										
									<?php 
									
									} 
									?>
									
											 
										</div>
									
						
							</div> 
					    </div> 
                    </div>
				<?php }else {
					echo $this->element('../Projects/partials/error_data', array(
					'error_data' => [
					'message' => "You have not created any project yet.",
					'html' => "Click<a class='' href='".Router::Url(array('controller' => 'projects', 'action' => 'manage_project', 'admin' => FALSE ), TRUE)."'> here </a>to create project now."
					]
					));
				}
				?>
                </div>
            </div>
        </div>
	</div>
</div>



<style>
ul.grid li .panel .panel-body .list-textcontents {
    min-height: 160px;
}

.error-inline {
	display: inline-block;
	padding-left: 5px;
	vertical-align: middle;
	font-size: 11px;
	color: #cc0000; 
	margin-top: -5px;
}
textarea { resize: none; }
</style>

