<?php
/**
 * Image Helper class file.
 *
 * Generate an image with a specific size.
 *
 *
 * @package       Cake.View.Helper
 * @since         CakePHP(tm) v 1.1
 */

App::uses('AppHelper', 'View/Helper');

/**
 * Image Helper class for generate an image with a specific size.
 *
 * ImageHelper encloses 2 method needed while resizing images.
 *
 * @package       View.Helper
 */
class ImageHelper extends AppHelper{

    public $helpers = array('Html','Form');

    /**
     * Generate an image with a specific size
     * @param  string   $image   Path of the image (from the webroot directory)
     * @param  int      $width
     * @param  int      $height
     * @param  array    $options Options (same that HtmlHelper::image)
     * @param  int      $quality
     * @return string   <img> tag
     */
    public function resize($image, $width, $height, $options = array(), $quality = 100){
        $options['width'] = $width;
        $options['height'] = $height;
        return $this->Html->image(SITEURL.PROJECT_IMAGE_PATH.$this->resizedUrl($image, $width, $height, $quality), $options);
    }    
	
	public function resizeBlog($image, $width, $height, $options = array(), $quality = 100){
        $options['width'] = $width;
        $options['height'] = $height;
        return $this->Html->image(SITEURL.POST_PIC_PATH.$this->resizedUrl($image, $width, $height, $quality,POST_PIC_PATH), $options);
    }


    /**
     * Create an image with a specific size
     * @param  string   $file   Path of the image (from the webroot directory)
     * @param  int      $width
     * @param  int      $height
     * @param  array    $options Options (same that HtmlHelper::image)
     * @param  int      $quality
     * @return string   image path
     */
    public function resizedUrl($file, $width, $height, $quality = 100 , $imageDir = PROJECT_IMAGE_PATH){
	
 
 
        # We define the image dir include Theme support
        //$imageDir = PROJECT_IMAGE_PATH;//(!isset($this->theme)) ? IMAGES : APP.'View'.DS.'Themed'.DS.$this->theme.DS.'webroot'.DS.'img'.DS;

        # We find the right file
        $pathinfo   = pathinfo(trim($file, '/'));
        $file       = $imageDir . trim($file, '/');
		// $output     = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_' . $width . 'x' . $height . '.' . $pathinfo['extension'];
		$output     =  $pathinfo['filename'] . '_' . $width . 'x' . $height . '.' . $pathinfo['extension'];
        // echo $pathinfo['dirname'];

        if (!file_exists($imageDir . $output)) {

            # Setting defaults and meta
            $info                         = getimagesize($file);
            list($width_old, $height_old) = $info;

            # Create image ressource
            switch ( $info[2] ) {
                case IMAGETYPE_GIF:   $image = imagecreatefromgif($file);   break;
                case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($file);  break;
                case IMAGETYPE_PNG:   $image = imagecreatefrompng($file);   break;
                default: return false;
            }

            # We find the right ratio to resize the image before cropping
            $heightRatio = $height_old / $height;
            $widthRatio  = $width_old /  $width;

            $optimalRatio = $widthRatio;
            if ($heightRatio < $widthRatio) {
                $optimalRatio = $heightRatio;
            }
            $height_crop = ($height_old / $optimalRatio);
            $width_crop  = ($width_old  / $optimalRatio);

            # The two image ressources needed (image resized with the good aspect ratio, and the one with the exact good dimensions)
            $image_crop = imagecreatetruecolor( $width_crop, $height_crop );
            $image_resized = imagecreatetruecolor($width, $height);

            # This is the resizing/resampling/transparency-preserving magic
            if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
                $transparency = imagecolortransparent($image);
                if ($transparency >= 0) {
                    $transparent_color  = imagecolorsforindex($image, $trnprt_indx);
                    $transparency       = imagecolorallocate($image_crop, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                    imagefill($image_crop, 0, 0, $transparency);
                    imagecolortransparent($image_crop, $transparency);
                    imagefill($image_resized, 0, 0, $transparency);
                    imagecolortransparent($image_resized, $transparency);
                }elseif ($info[2] == IMAGETYPE_PNG) {
                    imagealphablending($image_crop, false);
                    imagealphablending($image_resized, false);
                    $color = imagecolorallocatealpha($image_crop, 0, 0, 0, 127);
                    imagefill($image_crop, 0, 0, $color);
                    imagesavealpha($image_crop, true);
                    imagefill($image_resized, 0, 0, $color);
                    imagesavealpha($image_resized, true);
                }
            }

            imagecopyresampled($image_crop, $image, 0, 0, 0, 0, $width_crop, $height_crop, $width_old, $height_old);
            imagecopyresampled($image_resized, $image_crop, 0, 0, ($width_crop - $width) / 2, ($height_crop - $height) / 2, $width, $height, $width, $height);


            # Writing image according to type to the output destination and image quality
            switch ( $info[2] ) {
              case IMAGETYPE_GIF:   imagegif($image_resized, $imageDir . $output, $quality);    break;
              case IMAGETYPE_JPEG:  imagejpeg($image_resized, $imageDir . $output, $quality);   break;
              case IMAGETYPE_PNG:   imagepng($image_resized, $imageDir . $output, 9);    break;
              default: return false;
            }
        } 
        return $output;
    }

}
