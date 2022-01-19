<?php echo $this->Html->css('projects/bootstrap-input'); ?>
<!-- /.modal -->
<script type="text/javascript">
jQuery(function($) {
 
	
	$('body').delegate('label.permissions', 'click', function(event) {
		// $('label.permissions').on('click', function (event) {
			var e = $(this);
			
			var $input = $(this).find('input[type=checkbox]'),
				iName = $input.attr('name'),
				$options = $('.propogate-options');
			
			$input.prop("checked", !$input.prop("checked"));
			
			if($input.prop("checked")) {
				$(this).addClass('active') 
			}
			else {
				$(this).removeClass('active')
			}
	}) 
			 
	var tpl = $('#propogate_options > .options-inner').html();
	
	$('.propogation').popover({
		trigger: 'manual',
		content: '<form>test</form>',
		placement: 'right',
		container: 'body',
		html : true,
		title: function() {
			return 'Propogate Permissions <span onclick="$(this).parent().parent().hide();" class="close">&times;</span>';
		}
	})
	// $('.propogation').popover('show ')
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
<style>

label.permissions, label.propogation {
	background: #cecece none repeat scroll 0 0;
    color: #939393;
	display: inline-block;
    height: 25px;
    margin: 5px;
    padding: 2px 0 0;
    text-align: center;
	cursor: pointer;
	/* position: relative; */
}
label.permissions > input[type=checkbox], label.propogation > input[type=checkbox] {
	display:none;
}
label.permissions.active, label.propogation.active {
	color: #ffffff;
}
/* blue */
label.permissions.active.permit_read {
	background: #1695ff none repeat scroll 0 0;
}
label.permissions.active.permit_read:hover {
	background: #0080ff  none repeat scroll 0 0;
}
/* red */
label.permissions.active.permit_delete {
	background: #DD4B39  none repeat scroll 0 0;
}
label.permissions.active.permit_delete:hover {
	background: #D73925  none repeat scroll 0 0;
}
/* teal */
label.permissions.active.permit_edit {
	background: #F39C12  none repeat scroll 0 0;
}
label.permissions.active.permit_edit:hover {
	background: #DB8B0B  none repeat scroll 0 0;
}
/* purple */
label.permissions.active.permit_copy {
	background: #00A65A  none repeat scroll 0 0;
}
label.permissions.active.permit_copy:hover {
	background: #008D4C  none repeat scroll 0 0;
}
/* green */
label.permissions.active.permit_move {
	background: #605CA8  none repeat scroll 0 0;
}
label.permissions.active.permit_move:hover {
	background: #555299  none repeat scroll 0 0;
}
/* orange */
label.propogation.active.perm_propogate {
	background: #59D6F5  none repeat scroll 0 0;
}
label.propogation.active.perm_propogate:hover {
	background: #00C0EF  none repeat scroll 0 0;
}
.propogation.perm_propogate {
	padding: 5px 0 !important;
}



.btn-circle {
  width: 30px;
  height: 30px;
  text-align: center;
  padding: 6px 0;
  font-size: 12px;
  line-height: 1.428571429;
  border-radius: 15px;
}
.btn-circle.btn-xs {
    border-radius: 50%;
    font-size: 12px;
    height: 24px;
    line-height: 1.33;
    padding: 3px 7px;
    width: 25px;
}
.btn-circle.btn-lg {
  width: 50px;
  height: 50px;
  padding: 10px 16px;
  font-size: 18px;
  line-height: 1.33;
  border-radius: 25px;
}
.btn-circle.btn-xl {
  width: 70px;
  height: 70px;
  padding: 10px 16px;
  font-size: 24px;
  line-height: 1.33;
  border-radius: 35px;
}


.propogate-option {
	background-color: rgba(0, 0, 0, 0.7);
    border-radius: 6px;
    display: none; 
    min-width: unset;
    padding: 0; 
    width: 115px;
	
}
/* .permissions.permit_propogate.active > .propogate-options {
	display: block;
}  */
/* .propogate-options  */
.options-arrow.bottom {
    border-left: 5px solid rgba(0, 0, 0, 0);
    border-right: 5px solid rgba(0, 0, 0, 0);
    border-top: 5px solid rgba(0, 0, 0, 0.7);
    bottom: -5px;
    height: 0;
    left: 50%;
    margin-left: -5px;
    position: absolute;
    width: 0;
}
.options-arrow.right {
    border-bottom: 5px solid rgba(0, 0, 0, 0);
    border-right: 8px solid rgba(0, 0, 0, 0.7);
    border-top: 5px solid rgba(0, 0, 0, 0);
    height: 0;
    left: -3px;
    margin-left: -5px;
    position: absolute;
    top: 33%;
}

</style>



<div class="row">
	<div class="col-xs-12">
  <?php echo $this->Session->flash(); ?>
		<div class="row">
			<section class="content-header clearfix"> 
				<h1 class="pull-left"> 
				</h1>
				 
			</section>
		</div>

 
    <div class="box-content">
	
            <div class="row ">
                <div class="col-xs-12"> 
                    <div class="box border-top margin-top">
                        <div class="box-header" style="">
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="popup_model_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
