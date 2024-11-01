<?php
/***********************************************************************************************/
/* w3images plugin for Wordpress                                                               */
/* Plugin URI: http://www.axew3.com/                                                           */
/* Plugin author: Alessio Nanni - alias axew3                                                  */
/* =========================================================================================== */

// GO TO the follow LINE in this file to start edit template and fit your: 
// <!-- START WordPress html and code like any other template page -->

// START COMMON WORDPRESS CODE & w3images CODE

define('WP_USE_THEMES', true);

/** WordPress wakeup */
require( dirname(__FILE__) . '/wp-load.php' );

////////////////////////////////////////////////////////////////////////////////////////////////////
// w3images code                                                                                  //
////////////////////////////////////////////////////////////////////////////////////////////////////

 require_once("".$w3images_plugin_path."w3images-includes.php");
 require_once("".$w3images_plugin_path."w3images-functions.php");
 require_once("".$w3images_plugin_path."includes/w3lang.php");

get_header(); 

// END -> COMMON WORDPRESS CODE & w3images CODE

?>

<!-- START WordPress html and code like any other template page -->


<body <?php body_class(); ?>>

 <div id="container">
		<div id="content" role="main">

<!-- END WordPress html and code like any other template page -->

<!-- START w3images gallery wrapper -->

<div class="w3gallerywrap">

<!-- START call w3images gallery output -->

<?php

if($w3_action != 'w3SubmitImage' OR !isset($_REQUEST['w3_imageid'])){

echo '<div class="w3spacelements">';
  // output the w3images navigation menu
  w3_GetMenu($w3_sectionid, $w3_sectionid);
echo '</div>';

  // output the w3images gallery
  wp_w3_displayImages($w3_sectionid, $w3_start);
  	
} 

	echo '<div class="submitwrap">';
  	
    // output the w3images submit img button
  wp_w3_uploadPerm($w3getPermValue,$w3_action, $w3_sectionid);
  	echo '</div>';

?>

<!-- / END call w3images gallery output  -->

        </div><!-- END w3images gallery wrapper -->
 
 
 <!-- START WordPress html and code like any other template page -->
 
			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar();
      get_footer(); ?>

<!-- END WordPress html and code like any other template page -->

<?php
// kill the session to avoid sniff of the gallery captcha value
// session value need to be different every time page reload
unset($_SESSION['security_code']);
?>