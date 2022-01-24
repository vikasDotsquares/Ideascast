<?php

if( isset($row) && !empty($row) ) {
 ?>


				<div class="input-group has-loader margin-tb">
					<i class="fa loader" id="icon"></i>
					<input type="text" id="inpAreaTitle" name="data[Area][title]" value="<?php echo htmlentities($row['title']); ?>" class="form-control input_holder" style="padding: 5px; min-width: 240px;" placeholder="Max 100 chars" />
				</div>
				<input type="hidden" id="inpAreaId" name="data[Area][id]" value="<?php echo $row['id'] ?>" />


		<div class="popover-footer text-right ">
			<div class="form-group">
				<button type="submit" id="popover_submit" class="btn btn-sm btn-success" data-remote="<?php echo Router::Url(array('controller' => 'workspaces', 'action' => 'update_area', $row['id'], 'admin' => FALSE ), TRUE); ?>">Save</button>
				<button type="reset" id="popover_close" class="btn btn-sm btn-danger margin-left">Cancel</button>
			</div>
		</div>



<?php }else { ?>
<div id="myPopoverModal" class="popover popover-default">
	<div class="popover-content">
	</div>
	<div class="popover-footer">
		<button type="submit" class="btn btn-sm btn-primary">Submit</button><button type="reset" class="btn btn-sm btn-default">Reset</button>
	</div>
</div>
<?php } ?>

<style>

@keyframes loader {
    to {transform: rotate(360deg);}
}

@-webkit-keyframes loader {
    to {-webkit-transform: rotate(360deg);}
}

.loader {
    min-width: 12px;
    min-height: 14px;
}

.loader:before {
    content: 'Loading…';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin-top: -5px;
    margin-left: -8px;
}
.loader:not(:required):before {
    content: '';
    border-radius: 50%;
    /* border: 2px solid rgba(0, 0, 0, .3);
    border-top-color: rgba(0, 0, 0, .6);  */
    border: 2px solid rgba(22, 159, 40, .0);
    border-top-color: rgba(22, 159, 40, 1);
}

.has-loader.active .loader:not(:required):before {
    animation: loader .6s linear infinite;
    -webkit-animation: loader .6s linear infinite;
}
.has-loader i#icon {
    left: auto;
    position: absolute;
    right: 7px;
    top: 8px;
    z-index: 10;
	opacity: 0
}
.has-loader.active i#icon {
	opacity: 1;
}
</style>