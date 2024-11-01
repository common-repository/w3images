<?php
/***********************************************************************************************/
/* w3images plugin for Wordpress                                                               */
/* Plugin URI: http://www.axew3.com/                                                           */
/* Plugin author: Alessio Nanni - alias axew3                                                  */
/* =========================================================================================== */

/*
* Original code author for "class CaptchaSecurityImages": Simon Jarvis
* Requirements: PHP 4/5 with GD and FreeType libraries
* Link: http://www.white-hat-web-design.co.uk/articles/php-captcha.php
*
* Freely adapted for w3images plugin
*/

// start a session: it is not needed any check to this about security
// due to the use of the session value (used only for captcha)
// this session is killed down on wp-w3images.php file

session_start();

class CaptchaSecurityImages {

   var $font = 'includes/monofont.ttf';
 
   function generateCode($characters) {
      /* possible characters: some similar looking chars have been removed */
      $possible = '23456789abcdefghijkmnpqrstuvwxyz';
      $w3code = '';
      $i = 0;
      while ($i < $characters) { 
         $w3code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
         $i++;
      }
      return $w3code;
   }
 
   function CaptchaSecurityImages($width='100',$height='20',$characters='4') {
      $w3code = $this->generateCode($characters);
      /* font size will be 88% of the image height */
      $font_size = $height * 0.88;
      $image = imagecreate($width, $height) or die('Cannot initialize new GD image stream');
      /* set the colours */
      $background_color = imagecolorallocate($image, 217, 215, 185);
      $text_color       = imagecolorallocate($image, 0, 0, 0);
      $noise_color      = imagecolorallocate($image, 241, 187, 70);
      /* generate random dots in background */
      for( $i=0; $i<($width*$height)/3; $i++ ) {
         imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
      }
      /* generate random lines in background */
      for( $i=0; $i<($width*$height)/130; $i++ ) {
         imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
      }
      /* create textbox and add text */
      $textbox = imagettfbbox($font_size, 0, $this->font, $w3code) or die('Error in imagettfbbox function');
      $x = ($width - $textbox[4])/2;
      $y = ($height - $textbox[5])/2;
      imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $w3code) or die('Error in imagettftext function');
      /* output captcha image to browser */
      header('Content-Type: image/jpeg');
      imagejpeg($image);
      imagedestroy($image);
      $_SESSION['security_code'] = $w3code;
   }
}

$captcha = new CaptchaSecurityImages(80,30,4);
?>