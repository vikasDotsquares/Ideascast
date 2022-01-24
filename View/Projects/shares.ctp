<?php echo $this->Html->css('projects/bootstrap-input'); ?>
<!-- /.modal -->
<script type="text/javascript">
jQuery(function($) {
 
	
})
</script>
<style>
.list-acknowledge h3 { text-align:center;}
.list-acknowledge .center  { width:350px; max-width:100%; margin:15px auto;}
.list-acknowledge .center .form-control { width:250px;}
.list-acknowledge .center .select_dropdown, .list-acknowledge .center a { display:inline-block; vertical-align:top}
.acknowledge-list > ul:first-child { margin:0px; padding:0px;}
.acknowledge-list li { display:table; clear:both; width:100%}
.acknowledge-list  label { white-space:nowrap; display:table; float:left}
.list-acknowledge table th { background:#eee}
.acknowledge-list-icon { min-width:610px; float:right;   display:table;}
.acknowledge-list-icon span{ width:70px; display:inline-block; text-align:center; }
.buttons { float:right; overflow:hidden;}
</style>

<div class="row">
	<div class="col-xs-12">
  <?php echo $this->Session->flash(); ?>
		<div class="row">
			<section class="content-header clearfix"> 
				<h1 class="pull-left"><?php echo (isset($project_detail)) ? $project_detail['Project']['title'] : $page_heading; ?>
					<?php if( isset($project_detail )) { //pr($this->data); ?>
					<p class="text-muted date-time">Project: 
						<span>Created: <?php 
						//echo date('d M Y h:i:s', $project_detail['Project']['created']); 
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['created']),$format = 'd M Y h:i:s');
						?></span>
						<span>Updated: <?php 
						//echo date('d M Y h:i:s', $project_detail['Project']['modified']);
						echo $this->Wiki->_displayDate($date = date('Y-m-d h:i:s A',$project_detail['Project']['modified']),$format = 'd M Y h:i:s');						
						?></span>
					</p>
					<?php } ?>
				</h1>
				 
			</section>
		</div>

 
    <div class="box-content">
	
            <div class="row ">
                <div class="col-xs-12"> 
                    <div class="box border-top margin-top">
                        <div class="box-header" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX --> 
							 
                        </div>
						<div class="box-body clearfix list-acknowledge" style="min-height: 800px">
                             <h3>Project Name</h3>
                             <div class="center">
                             <div class="select_dropdown form-group">
                             <select class="form-control">
                                <option>Dropdown</option>
                            </select>
                            </div>
                            <a type="submit" href="http://192.168.4.32/ideascomposer/projects/lists" class="btn btn-danger">Share</a>
                            </div>
                           
                            <div class="panel panel-default">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                    	<tr>
                                            <th>Ser. Num.</th>
                                            <th>User Name</th>
                                            <th>Rank</th>
                                            <th>Propogation</th>
                                            <th>actions</th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>User Name</td>
                                            <td><a href=""><i class="fa fa-arrow-down"></i></a> <a href=""><i class="fa fa-arrow-up"></i></a></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="table-responsive">
                                    <table class="table">
                                    <tr>
                                        <th>
                                          <div class="acknowledge-list">
                                          <ul>
                                          <li>
                                        <label>Path</label> 
                                        <div class="acknowledge-list-icon">
                                            <span>Read</span>
                                            <span>Modify</span>
                                            <span>Create</span>
                                            <span>Delete</span>
                                            <span>Read ACL</span>
                                            <span>Edit ACL</span>
                                            <span>Replicate</span>
                                            <span>Datils</span>
                                        </div> 
                                        </li>
                                        </ul>
                                        </div>
                                        </th>
                                       
                                    </tr>
                                    <tr>
                                        <td>
                                        <div class="acknowledge-list">
                                            <ul>
                                                <li><label>cvcxxcv</label>
                                                  <div class="acknowledge-list-icon">
                                                    <span><input type="checkbox" /></span>
                                                    <span><input type="checkbox" /></span>
                                                    <span> <input type="checkbox" /></span>
                                                    <span><input type="checkbox" /></span>
                                                    <span><input type="checkbox" /></span>
                                                    <span><input type="checkbox" /></span>
                                                    <span><input type="checkbox" /></span>
                                                    <span><a href="">Datils</a></span>
                                                </div>
                                                    <ul>
                                                        <li><label>cvcxxcv</label> <div class="acknowledge-list-icon">
                                                    <span><input type="checkbox" /></span>
                                                    <span><input type="checkbox" /></span>
                                                    <span> <input type="checkbox" /></span>
                                                    <span><input type="checkbox" /></span>
                                                    <span><input type="checkbox" /></span>
                                                    <span><input type="checkbox" /></span>
                                                    <span><input type="checkbox" /></span>
                                                    <span><a href="">Datils</a></span>
                                                </div>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                        </td>
                                        
                                    </tr>
                                </table>
                               	
                                </div>
                                 <div class="panel-footer">
                                 <div align="right">
								<button type="submit" class="btn btn-success">Save</button>
								<a type="submit" href="http://192.168.4.32/ideascomposer/projects/lists" class="btn btn-danger">Cancel</a>
                                </div>
							</div>
                            </div>                         
                     </div>
				 
                </div>
            </div>
        </div>
	</div>
</div>

<style>
 
</style>
<script type="text/javascript" >
$(function() { 
 
}) 
</script>
