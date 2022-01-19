<span class="workinfo"><strong>Unavailable Days (Next 3 Mth):</strong> </span>
	<?php

	//if( $_SERVER['REMOTE_ADDR'] == '192.168.4.176' || $_SERVER['REMOTE_ADDR'] == '192.168.4.175'  || $_SERVER['REMOTE_ADDR'] == '192.168.4.218' || $_SERVER['REMOTE_ADDR'] == '192.168.4.192' ) {
 
	$noAvailDates = $this->ViewModel->not_available_dates($user_id);
	
	//$dates = $this->ViewModel->threeMonthsDates();
	//pr($dates );
	 
	if( isset($noAvailDates) && !empty($noAvailDates) ){

		$datelists =  $this->ViewModel->check_continuous_avail_dates($noAvailDates,$user_id);
		if( isset($datelists) && !empty($datelists) ){
			echo $datelists;
		} else {
			echo '<span class="workinfo">None</span>';
		}
	} else {
		echo '<span class="workinfo">None</span>';
	}

//} ?>