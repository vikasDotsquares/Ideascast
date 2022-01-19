
<style>
.table-wrapper {
	border: 1px solid #a9a9a9;
	border-radius: 4px;
	padding: 1px;
	width: 100%;
	max-width:120px;
	display:table;
}
.table-wrapper .table {
	margin: 0;
	width: 120px;
	height:82px;
}
.table-wrapper .table td {
	border: 1px solid #fff;
	/* border-radius: 5px; */
	padding: 0px 0;
	vertical-align: middle;
}

 
.btn-group .aqua {
    border-color: #00c0ef;
	background:#fff;
	border-radius: 0;
}
.open ul.dropdown-menu > li{
border: medium none !important;
}

ul.multiselect-container.dropdown-menu {
  border-radius: 0 !important;
  box-shadow: none !important;
  height: auto;
  max-height: 300px;
  overflow: auto;
  width: 100%;
}

.caret {    
    border-top-color: #b7b7b7;
}

.open ul.dropdown-menu > li a {
    border: medium none !important;
    padding: 0 0 0 2px;
	color:#000;
}

.open ul.dropdown-menu > li > a > label {
  cursor: pointer;
  font-weight: 400;
  height: 100%;
  margin: 0;
  padding: 0 !important;
}

.multiselect-container > li > a > label {
    cursor: pointer;
    font-weight: 400;
    height: 100%;
    margin: 0;
    padding: 0 !important;
}

.open ul.dropdown-menu > li a:hover {
    background: #3399FF;
	color:#fff;    
}

.dropdown-menu > .active > a, .dropdown-menu > .active > a:focus, .dropdown-menu > .active > a:hover {
    background: #3399FF !important;
  color: #fff !important;
  outline: 0 none;
  text-decoration: none;
}

.dropdown-menu > li > a:hover, .dropdown-menu > li > a:focus, .dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus {
   background: #3399FF !important;
  color: #fff !important;
  background-image: linear-gradient(to bottom, #428bca 0%, #357ebd 100%);
  background-repeat: repeat-x;
}  
    
.btn-group.open .dropdown-toggle {
  box-shadow: none;
} 
</style>


<script type="text/javascript" >
$(function(){
	var $c_status;
})
</script>
<?php 
echo $this->Html->css('projects/dropdown', array('inline' => true));
echo $this->Html->css('projects/bootstrap-input');
echo $this->Html->css('projects/task_lists', array('inline' => true));
echo $this->Html->css('projects/bs-selectbox/bootstrap-multi', array('inline' => true));
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multi', array('inline' => true));
echo $this->Html->script('projects/plugins/jquery.dot', array('inline' => true));
echo $this->Html->script('projects/plugins/bootstrap-checkbox', array('inline' => true));
echo $this->Html->css('projects/bootstrap-input'); 
?>
<script type="text/javascript" >
$(function(){
    $c_status = null;
	$(".checkbox_on_off").checkboxpicker({
		style: true,
		defaultClass: 'tick_default',
		disabledCursor: 'not-allowed',
		offClass: 'tick_off',
		onClass: 'tick_on',
		offTitle: "Off",
		onTitle: "On",
		offLabel: 'Off',
		onLabel: 'On',
	})


	$.element_status = $('[name=element_status]').multiselect({
		buttonClass					: 'btn aqua',
		buttonWidth					: '100%',
		checkboxName				: 'element_status_val',
		includeSelectAllOption		: true,
		nonSelectedText				: 'No Status Selected',
		onChange : function(option, checked, select) { 
			// console.log('onChange: ' )
		}
	});

	$.strip_tags = function(value) {
		var body = value || '';
		var regex = /(<([^>]+)>)/ig
		return body.replace(regex, "");
	}
	
	$.project_id = $('[name=project_id]').multiselect({
		buttonClass					: 'btn aqua',
		buttonWidth					: '100%',
		checkboxName				: 'project_id',
		includeSelectAllOption		: true,
                selected		: '63',
		nonSelectedText				: 'No Project Selected',
		onChange : function(option, checked, select) { 
			var project_id = option.val();
			//$.get_wsp_area(project_id)
			
		},
		
	});

	$('body').delegate('[name=project_type]', 'change', function(e) {
		e.preventDefault();
		console.log($(this))
		
		var $that 			= $(this),
			value 			= $that.val(),
			params 			= {type: value},
			$prj_spiner 	= $('#ProjectId').parent().find('.loader-icon');
			//$wsp_spiner 	= $('#WorkspaceId').parent().find('.loader-icon'),
			//$area_spiner 	= $('#AreaId').parent().find('.loader-icon');
		
		$prj_spiner.show() 
		
		$.when(
		
					$.ajax({
						url: $js_config.base_url + 'team_talks/wiki_list_projects',
						type: "POST",
						data: $.param(params),
						dataType: "JSON",
						global: false,
						success: function (response) {
							
							var pathname = window.location.pathname.split("/");
							var filename = pathname[pathname.length-1];
							var named_project = filename.split(':');
							
							if(named_project.length > 0){
								filename = named_project[1];	
							}
							
							if(pathname[pathname.length-1] == ''){
								var filename = pathname[pathname.length-2];
							}else if(pathname[pathname.length-1] != '' && $.isNumeric(pathname[pathname.length-1])){
								var filename = pathname[pathname.length-1];
							}
							
							var selectValues = response.content;
							$('#ProjectId').empty();
							
							if( selectValues != null ) {
								$('#ProjectId').append(function() {
									var output = '';
									
									$.each(selectValues, function(key, value) {
										var sel = '';
										if(key == filename){
											sel = 'selected="selected"';
										}
										if($.strip_tags(value) != '')
											output += '<option '+sel+' value="' + key + '">' + $.strip_tags(value) + '</option>';
									});
									return output;
								});
							}
							
							$('#ProjectId').multiselect('rebuild');
							
						}
					})
					
				).then(function( data, textStatus, jqXHR ) {
					$prj_spiner.hide()
					  
					var project_id = $("#ProjectId option:first").val();
					$( "#ProjectId" ).trigger( "change" );
					 
				});
		})
		
		$('body').delegate('[id=ProjectId]', 'change', function(e) {
			e.preventDefault();
			console.log($(this).val())
			
			var $that 			= $(this),
			value 			= $that.val(),
			params 			= {project_id: value},
			$prj_spiner 	= $('#ProjectId').parent().find('.loader-icon');
		
			$prj_spiner.show()
		
		$.when(
		
			$.ajax({
				url: $js_config.base_url + 'team_talks/projects_wiki_list',
				type: "POST",
				data: $.param(params),
				dataType: "HTML",
				global: false,
				success: function (response) {					
					$('#box_body').html(response);
				}
			})
			
		).then(function( data, textStatus, jqXHR ) {
			$prj_spiner.hide()
			  
			var project_id = $("#ProjectId option:first").val();
			 
		});
			
			
		})
		
	$('#project_type_my').trigger('change');
	$('[for=project_type_my]').trigger('click');
	
	/* $('#modal_medium').on('show.bs.modal', function (e) {

	 $(this).find('.modal-content').css({
		  width: $(e.relatedTarget).data('modal-width'), //probably not needed
	 });
	});

	$('#modal_medium').on('hidden.bs.modal', function () {
	 $(this).removeData('bs.modal');
	}); */
	
})	
	
</script>
<style>

</style>
<!-- OUTER WRAPPER	-->
<div class="row">
	
	<!-- INNER WRAPPER	-->
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
                <h1 class="pull-left"><?php   echo $page_heading; ?>
                    <p class="text-muted date-time">
                        <span><?php echo $page_subheading; ?></span>
                    </p>
                </h1>  
            </section>
		</div>
		<!-- END HEADING AND MENUS -->
	 
	 
		<!-- MAIN CONTENT -->
		<div class="box-content">
	
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box ">
						<?php echo $this->Session->flash(); ?>
						<!-- CONTENT HEADING -->
                        <div class="box-header" style="background: #efefef none repeat scroll 0 0;">
						 
							<!-- MODAL BOX WINDOW -->
                            <div class="modal modal-success fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
							<!-- END MODAL BOX -->
							
							<!-- FILTER BOX -->
                                                        
							<?php 
							if(isset($project_id) && !empty($project_id)) {
								$cky = $this->requestAction('/projects/CheckProjectType/'.$project_id.'/'.$this->Session->read('Auth.User.id')); 
							
							?>
							<script type="text/javascript" >
							$(function(){
								$c_status ='<?php echo $cky; ?>';
								
								if($c_status == 'm_project'){
									$('[for=project_type_my]').trigger('click');
								}else if($c_status == 'r_project'){
									$('[for=project_type_rec]').trigger('click');
								}else if($c_status == 'g_project'){
									$('[for=project_type_group]').trigger('click');
								} 
							   
								$(".fancy_input").click(function(e) {
									$thisdata = $(this);
									
									setTimeout(function(){
									$("#project_report_link").attr("href", $js_config.base_url +"projects/reports/"+$("#ProjectId").val())
									$("#dashboard_link").attr("href", $js_config.base_url +"projects/objectives/"+$("#ProjectId").val())
									
									
									if($thisdata.attr("id") == 'project_type_my'){
										$c_status = 'm_project';
										$("#show_resources_link").attr("href", $js_config.base_url +"users/projects/m_project:"+$("#ProjectId").val())
									}
									else if($thisdata.attr("id") == 'project_type_rec'){
										$("#show_resources_link").attr("href", $js_config.base_url +"users/projects/r_project:"+$("#ProjectId").val())
										 $c_status = 'r_project';
									}
									else if($thisdata.attr("id") == 'project_type_group'){
										$("#show_resources_link").attr("href", $js_config.base_url +"users/projects/g_project:"+$("#ProjectId").val())
										$c_status = 'g_project';
									}
									},2000)
								}) 
							})
						
							</script>
							<?php } ?>
							
							<div class="col-sm-12 col-md-12 col-lg-12 row-first">
								<div class="form-group clearfix">
								<div class="radio radio-warning">
                                    <input type="radio" id="project_type_my" name="project_type" class="fancy_input" value="1"   />
									<label class="fancy_labels" for="project_type_my">My Projects</label>
								</div>
								<div class="radio radio-warning">	
									<input type="radio"   id="project_type_rec" name="project_type" class="fancy_input"  value="2" checked />
									<label class="fancy_labels" for="project_type_rec">Received Projects</label>
								</div>
								<div class="radio radio-warning">	
									<input type="radio"   id="project_type_group" name="project_type" class="fancy_input"  value="3" checked />
									<label class="fancy_labels" for="project_type_group">Group Received Projects</label>
								</div>
                                  
								
								</div>
                                                           
							</div>
							
							
								<div class="col-sm-12 col-md-6 col-lg-3 project_selection"> 
									<select id="ProjectId"  name="project_id" class="project_select"  placeholder="Select Project"  ></select>
									<span class="loader-icon fa fa-spinner fa-pulse"></span>
								</div>							
								
							</div>														
							<div class="box-body" id="box_body"></div>
				
						</div>
										   
					</div>							
				</div>   
				
				</div>
										   
			</div>
		</div>	

<!-- Modal Large -->
     				   <div class="modal modal-success fade" id="modal_medium" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     					<div class="modal-dialog modal-md">
     					     <div class="modal-content"></div>
     					</div>
     				   </div>					   
<!-- /.modal -->
		