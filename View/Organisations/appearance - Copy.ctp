<style>
/* input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
   opacity: 1;
} */

input[type=number]::-webkit-inner-spin-button { 
    -webkit-appearance: block;
    cursor:pointer;
    display:block;
    width:8px;
    color: #333;
    text-align:center;
    position:relative;
	opacity: 1;
	margin-right:0;
}

input[type=number]::-webkit-inner-spin-button:before,
input[type=number]::-webkit-inner-spin-button:after {
    content: "^";
    position:absolute;
    right: 0;
	opacity: 1;
    font-family:monospace;
    line-height:
}

input[type=number]::-webkit-inner-spin-button:before {
    top:0px;
}

input[type=number]::-webkit-inner-spin-button:after {
    bottom:0px;
    -webkit-transform: rotate(180deg);
}

.password-policy .modal-header {
    background: #eee;
}
.password-policy .rows p{
    padding-bottom:10px;
}

.password-policy .pp h3{
    margin:10px 0;
}
</style>
<!-- PAGE HEADING AND DROP-DOWN MENUS OF BUTTON -->
		<div class="row">
			<section class="content-header clearfix">
           

				
            </section>
		</div>
		<!-- END HEADING AND MENUS -->
<!-- Password Policy` -->
	<div class="password-policy">
		     <div class="left-policy">
				<h1 class="pull-left" style="font-size:24px;"><?php echo $viewData['page_heading']; 
				$userdetails =  $this->Common->getOrganisationId($this->Session->read('Auth.User.id'),$this->Session->read('Auth.User.role_id'));
				?>             
                </h1>
				<div class="policy-view-edit">
                        <span class="text-muted policydate-time"  style="text-transform: none;"><?php echo $viewData['page_subheading']; ?></span> 
                    </div>	
				</div>
		
		
		
	<?php echo $this->Session->flash(); ?>
		
			<?php 		 
			
				echo $this->Form->create('OrgPassPolicy', array('type' => 'file','url'=>SITEURL.'organisations/password_policy/', 'class' => 'form-horizontal form-bordered', 'id' => 'passwordpolicyedit')); ?>
				
				
				<?php echo $this->Form->input('OrgPassPolicy.org_id', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control', 'value'=>$userdetails )); ?>
				<?php echo $this->Form->input('OrgPassPolicy.updated_by', array('type' => 'hidden', 'label' => false, 'div' => false, 'class' => 'form-control', 'value'=>$this->Session->read('Auth.User.id'))); ?>
					
 
 
			</form>
</div>

<div class="box">
	<div class="box-body">
		<form>
			<div class="form-group">
				<div class="row">
					<div class="col-md-3 col-lg-2">
						<label>Sign In Logo:</label>
					</div>
					<div class="col-md-7 col-lg-7">
						<input type="file" class="form-control">
						<p>Recommended image dimensions are 320px by 75px</p>
						<img src="<?php echo SITEURL; ?>/images/appearance-logo.jpg" alt="">
						<i class="fa fa-trash"></i>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-md-3 col-lg-2">
						<label>Sign In Color:</label>
					</div>
					<div class="col-md-3 col-lg-2">
						<input type="text"  class="form-control" placeholder="#5f9323">
					</div>
				</div>
			</div>
			<hr>
			<div class="form-group">
				<div class="row">
					<div class="col-md-3 col-lg-2">
						<label>Application Name:</label>
					</div>
					<div class="col-md-7 col-lg-7">
						<input type="text"  class="form-control" placeholder="">
					</div>
				</div>
			</div>
			<hr>
			<div class="form-group text-right">
				<button type="submit"  class="btn btn-success btn-sm">Save</button>
			</div>
		</form>
	</div>
</div>
 