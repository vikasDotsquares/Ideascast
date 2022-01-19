<?php
echo $this->Html->css('projects/bs-selectbox/bootstrap-multiselect');
echo $this->Html->script('projects/plugins/selectbox/bootstrap-multiselect', array('inline' => true));

echo $this->Html->css('projects/appassets');
echo $this->Html->script('projects/appassets');
?>
<style type="text/css">
	.multiselect.dropdown-toggle.btn .arrow {
		padding: 6px 5px !important;
	}

	ul.multiselect-container.dropdown-menu {
		max-height: 300px;
		overflow: auto;
	}

	.multiselect-container.dropdown-menu label {
		width: 100%;
	}
	input.form-control.multiselect-search {
		font-weight: normal;
	}
	.radio input[type="radio"] {
	     opacity: 1;
	    z-index: 1;
		display:none;
	}
	
	.multiselect-container>li>a>label {
		padding: 3px 20px 3px 10px;
	}

	.form-control:focus {
		border-color: none !important;
	}

	table.table.table-bordered.projectdetail {
		margin-bottom: 2px !important;
	}

	select#api_type:focus {
		border-color: #00c0ef !important;
	}
	select.aqua:focus {
		border-color: #00c0ef !important;
	}

	.createriskapi .loader-icon {
		position: absolute;
		right: -18px;
		top: 23%;
		display: none;
	}

	.create_elementapi .loader-icon {
		position: absolute;
		right: -18px;
		top: 23%;
		display: none;
	}

	#element_api_elelist,#create_element_api_arealist,#create_element_api_elelist,#create_risk_api_type,#create_risk_api_elelist,#todo_api_users_list,#project_todo_api_list {
		display:block;
	}

	.fa-info {
		background: #00aff0 none repeat scroll 0 0;
		border-radius: 50%;
		color: #fff;
		font-size: 13px;
		height: 22px;
		line-height: 24px;
		width: 22px;
	}

	#appassests .arrow, #Recordlisting .arrow{
		display:none;
	}

</style>
<!-- END MODAL BOX -->
<div class="row">
	<div class="col-xs-12">

		<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
                <h1 class="pull-left">Assets
                    <p class="text-muted date-time" style="padding: 4px 0px;">
                        <span style="text-transform: none;">View Project Assets</span>
                    </p>
                </h1>
            </section>
		</div>
		<!-- END HEADING AND MENUS -->

	<!-- Content Header (Page header) -->
	 <div class="box-content">
		<div class="row">
			<div class="col-xs-12 inner-wrapper">
				<div class="box  noborder-top">

				<?php echo $this->Session->flash(); ?>

		<section class="box-body no-padding mainsections">
		<div class="row" id="Recordlisting">
            <div class="col-xs-12">
				<div class="box no-box-shadow box-success">
					<div class="box-header panel-user-header">
							<div class="col-sm-custom">
                            	<label class="custom-dropdown" style="width:100%;">
									<select class="form-control aqua" name="user_projects" id="assets_user_projects">
										<?php if( isset($allProjects) && !empty($allProjects) ){
											foreach($allProjects as $key => $myProjectlist){

												if( !empty($myProjectlist['Project']['id']) ){
										?>
										<option value="<?php echo $myProjectlist['Project']['id'];?>"><?php echo strip_tags($myProjectlist['Project']['title']); ?></option>
										<?php	}
											}
										}
										?>
									</select>
								</label>
                            </div>
					</div><!-- /.box-header -->

					<div class="box-body" id="appassests">
						<div class="blank_assests">Please select project</div>
					</div><!-- /.box-body -->

				</div><!-- /.box -->
			</div>
		</div>
    </section>
					</div>
				</div>
			</div>
		 </div>
	   </div>
	</div>
 </div>

 <script type="text/javascript">
    $(function(){
		$("body").delegate("[name=allusers]", "click", function(){

			if( $(this).prop('checked') == true ){
				$(".projectDetailrow").hide();
				$("#api_ov_charity").attr('disabled',true);
			} else {
				$(".projectDetailrow").show();
				$("#api_ov_charity").attr('disabled',false);
			}

		})

        $('#assets_user_projects').multiselect({
            enableUserIcon: false,
            enableFiltering: true,
            filterPlaceholder: 'Search Project',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%',
        })


		$( "#assets_user_projects" ).trigger( "change" );

		$('body').delegate("#accordianshowhide", "click", function(event) {
	        event.preventDefault();
	        var $this = $(this);

			setTimeout(function(){
				console.log($("#collapse1").hasClass('in'));
				if( $("#collapse1").hasClass('in') ){
					$this.attr('data-original-title', 'Collapse')
		                .tooltip('fixTitle')
		                .tooltip('show');
					// $this.attr('data-original-title','Collapse');
				} else {
					$this.attr('data-original-title', 'Expand')
		                .tooltip('fixTitle')
		                .tooltip('show');
					// $this.attr('data-original-title','Expand');
				}
			},500)


	    })
    })
</script>