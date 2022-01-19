
<?php echo "vikas";		
$arr = array(array('item' => 'Banana Cream Cheesecake',
                   'male' => 3,
                   'female' => 1),
             array('item' => 'Banana Cream Cheesecake',
                   'male' => 3,
                   'female' => 2),
             array('item' => 'Banana Cream Cheesecake',
                   'male' => 3,
                   'female' => 1),
             array('item' => 'Banana Cream Cheesecake',
                   'male' => 3,
                   'female' => 2),
             array('item' => 'Milk',
                   'male' => 2,
                   'female' => 1),
             array('item' => 'Banana Cream Cheesecake',
                   'male' => 3,
                   'female' => 1));
				   
				 
			 
				   
		   $counted['M'] = array_sum(array_map(function($value)  { if($value['item']=='Banana Cream Cheesecake') return $value['male'];} , $arr));
		   $counted['F'] = array_sum(array_map(function($value){if($value['item']=='Banana Cream Cheesecake') return $value['female'];}, $arr));
		   
		   print_r($counted);
		   echo "<br>";
		  
//echo $counted['Banana Cream Cheesecake'];

?>