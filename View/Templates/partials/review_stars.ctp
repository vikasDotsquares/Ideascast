<?php 
$item_id = $template_id;

$review_count = template_reviews($item_id, 1);
$sum_template_reviews = sum_template_reviews($item_id);
 
$average = 0;
if( (isset($sum_template_reviews[0][0]['total']) && !empty($sum_template_reviews[0][0]['total'])) && (isset($review_count) && !empty($review_count)) ) {
	$average = $sum_template_reviews[0][0]['total'] / $review_count;
	
	$whole = floor($average);      // 1
	$fraction = $average - $whole; // .25
	 
	if($fraction > 0.5 || $fraction < 0.5){								
	$average = round($average);
	 
	}else{
	$average = $average;	
	}
	
}

?>
<input id="star5" name="rating_<?php echo $item_id ?>" value="5" type="radio" <?php if($average == 5){ ?>checked="checked" <?php } ?>>
<label class="full lbl" for="star5" title="Awesome - 5 stars"></label>

<input id="star4half" name="rating_<?php echo $item_id ?>" value="4 and a half" type="radio" <?php if($average == 4.5){ ?>checked="checked" <?php } ?>>
<label class="half lbl" for="star4half" title="Pretty good - 4.5 stars"></label>

<input id="star4" name="rating_<?php echo $item_id ?>" value="4" type="radio" <?php if($average == 4){ ?>checked="checked" <?php } ?>>
<label class="full lbl" for="star4" title="Pretty good - 4 stars"></label>

<input id="star3half" name="rating_<?php echo $item_id ?>" value="3 and a half" type="radio" <?php if($average == 3.5){ ?>checked="checked" <?php } ?>>
<label class="half lbl" for="star3half" title="Meh - 3.5 stars"></label>

<input id="star3" name="rating_<?php echo $item_id ?>" value="3" type="radio" <?php if($average == 3){ ?>checked="checked" <?php } ?>>
<label class="full lbl" for="star3" title="Meh - 3 stars"></label>

<input id="star2half" name="rating_<?php echo $item_id ?>" value="2 and a half" type="radio" <?php if($average == 2.5){ ?>checked="checked" <?php } ?>>
<label class="half lbl" for="star2half" title="Kinda bad - 2.5 stars"></label>

<input id="star2" name="rating_<?php echo $item_id ?>" value="2" type="radio" <?php if($average == 2){ ?>checked="checked" <?php } ?>>
<label class="full lbl" for="star2" title="Kinda bad - 2 stars"></label>

<input id="star1half" name="rating_<?php echo $item_id ?>" value="1 and a half" type="radio" <?php if($average == 1.5){ ?>checked="checked" <?php } ?>>
<label class="half lbl" for="star1half" title="Meh - 1.5 stars"></label>

<input id="star1" name="rating_<?php echo $item_id ?>" value="1" type="radio"  <?php if($average == 1){ ?>checked="checked" <?php } ?> >
<label class="full lbl" for="star1" title="Sucks big time - 1 star"></label>

<input id="starhalf" name="rating_<?php echo $item_id ?>" value="half"type="radio" <?php if($average == 0.5){ ?>checked="checked" <?php } ?>>
<label class="half lbl" for="starhalf" title="Sucks big time - 0.5 stars"></label>