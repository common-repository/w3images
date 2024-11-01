<?php 
/***********************************************************************************************/
/* w3images plugin for Wordpress                                                               */
/* Plugin URI: http://www.axew3.com/                                                           */
/* Plugin author: Alessio Nanni - alias axew3                                                  */
/* =========================================================================================== */

if (strpos($_SERVER['PHP_SELF'], 'manage-images.php')) {
  die ("You can't access this file directly...");
}
// w3images Manager Panel on request


global $wpdb, $w3images_plugin_path, $w3images_plugin_url, $w3url, $sectionid, $refreshpage, $settings, $w3images_images_table, $w3images_sections_table, $w3images_options_table, $w3images_comments_table, $wp_categories_table, $w3_action, $w3_sectionid, $w3_start, $this_page;


echo '<h2 style="color:#333;text-align:center;padding-top:30px;">w3images Gallery Manager</h2>'; 

$w3strongpattern1   = "/[^a-zA-Z0-9]/";
//$w3strongpattern2 = "/[^a-zA-Z0-9- .,;:\"]/";

  if(preg_match($w3strongpattern1, $_GET['w3_action'], $matches) OR preg_match($w3strongpattern1, $_POST['w3_action'], $matches))
  {
  	$w3_action = NULL;
   }
	if(preg_match($w3strongpattern1, $_GET['w3_sectionid'], $matches) OR preg_match($w3strongpattern1, $_POST['w3_sectionid'], $matches))
	{
  	$w3_sectionid = 1;
   }
	if(preg_match($w3strongpattern1, $_GET['w3_start'], $matches) OR preg_match($w3strongpattern1, $_POST['w3_start'], $matches))
	{
  	$w3_start = 0;
   }

	
echo '<div class="wrap"><h2 style="color:#FF0000;text-align:center;">w3images future use</h2><h3>Open Settings -> w3images to set options for w3images plugin.</h3><h2><a href="http://www.axew3.com/">w3images home</a></div>';

add_action('admin_menu', 'add_w3images_options_page');

?>