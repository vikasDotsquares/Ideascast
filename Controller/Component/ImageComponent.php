<?php
/**
 * Component for working with image.
 
 */

class ImageComponent extends Component {

	function resize_png_image($img,$newWidth,$newHeight,$target) {
		$srcImage=imagecreatefrompng($img);
		if($srcImage==''){
			return FALSE;
		}
		$srcWidth=imagesx($srcImage);
		$srcHeight=imagesy($srcImage);
		$percentage=(double)$newWidth/$srcWidth;
		$destHeight=round($srcHeight*$percentage)+1;
		$destWidth=round($srcWidth*$percentage)+1;

		$destImage=imagecreatetruecolor($destWidth-1,$destHeight-1);
		if(!imagealphablending($destImage,FALSE)){
			return FALSE;
		}
		if(!imagesavealpha($destImage,TRUE)){
			return FALSE;
		}
		if(!imagecopyresampled($destImage,$srcImage,0,0,0,0,$destWidth,$destHeight,$srcWidth,$srcHeight)){
			return FALSE;
		}
		if(!imagepng($destImage,$target)){
			return FALSE;
		}
		imagedestroy($destImage);
		imagedestroy($srcImage);
		return TRUE;
	}
		

	function resize_image($source_file_path,$newWidth,$newHeight,$target) {
		list($source_width, $source_height, $source_type) = getimagesize($source_file_path);

		if ($source_type === NULL) {
			return false;
		}
		
			switch ($source_type) {

			case IMAGETYPE_GIF:

				$srcImage = imagecreatefromgif($source_file_path);

				break;

			case IMAGETYPE_JPEG:
			   
				$srcImage = imagecreatefromjpeg($source_file_path);
			   
				break;
		   
			case IMAGETYPE_PNG:
			
				$srcImage = imagecreatefrompng($source_file_path);
			  
				break;
		   
			default:
				return false;
		}
				
				
		//$srcImage=imagecreatefrompng($img);
		if($srcImage==''){
			return FALSE;
		}
		$srcWidth=imagesx($srcImage);
		$srcHeight=imagesy($srcImage);
		$percentage=(double)$newWidth/$srcWidth;
		$percentageH=(double)$newHeight/$srcHeight;
		
		$destHeight=round($srcHeight*$percentageH)+1;
		$destWidth=round($srcWidth*$percentage)+1;

		$destImage=imagecreatetruecolor($destWidth-1,$destHeight-1);
		if(!imagealphablending($destImage,FALSE)){
			return FALSE;
		}
		if(!imagesavealpha($destImage,TRUE)){
			return FALSE;
		}
		if(!imagecopyresampled($destImage,$srcImage,0,0,0,0,$destWidth,$destHeight,$srcWidth,$srcHeight)){
			return FALSE;
		}
		if(!(imagepng($destImage,$target) || imagegif($destImage,$target) || imagejpeg($destImage,$target))){
			return FALSE;
		}
		imagedestroy($destImage);
		imagedestroy($srcImage);
		return TRUE;
	}
		
	function upload_image($upload_image,$tmp_path,$uploaddir){
	
		//code of upload image for category
		$filename	=	strtolower(substr($upload_image,0, (strpos($upload_image,'.')-1) )).md5(time());
		$ext 		=	strtolower(substr($upload_image, (strpos($upload_image,'.')+1) ));
		$filename	=	str_replace(' ','_',$filename).'.'.$ext;	

		if(move_uploaded_file($tmp_path,$uploaddir.$filename)){
			return $filename;
		}else{
			return false;
		}		
	}
	
}