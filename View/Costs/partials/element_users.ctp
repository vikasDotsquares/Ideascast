<option value="">Select</option>
<?php
    if(isset($element_user_rates) && !empty($element_user_rates)){
        foreach ($element_user_rates as $key => $value) {
            $data_work = $value['upc']['day_rate'];
            $data_hour = $value['upc']['hour_rate'];
            $data_work_rates = 'data-day="'.$data_work.'"';
            $data_hour_rates = 'data-hour="'.$data_hour.'"';
            echo '<option value="'.$value['user_details']['user_id'].'" '.$data_work_rates.' '.$data_hour_rates.'>'.htmlentities($value[0]['fullname']).'</option>';
        }
    }
?>