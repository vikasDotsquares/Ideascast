<?php

if( isset($tasks) && !empty($tasks) ) {
    foreach($tasks as $key => $row) {
        $user_permissions = $row['user_permissions'];
        e($user_permissions['e_id']);
    }
}

 ?>
<br />