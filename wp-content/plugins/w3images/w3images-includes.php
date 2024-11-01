<?php 
/***********************************************************************************************/
/* w3images plugin for Wordpress                                                               */
/* Plugin URI: http://www.axew3.com/                                                           */
/* Plugin author: Alessio Nanni - alias axew3                                                  */
/* =========================================================================================== */
 
// common check

$w3strongpattern1 = "/[^a-zA-Z0-9_-]/";  // strong pattern

  if( isset($_GET['w3_action']) && preg_match($w3strongpattern1, $_GET['w3_action'], $matches)      OR isset($_POST['w3_action']) && preg_match($w3strongpattern1, $_POST['w3_action'], $matches))
  {
  	
  	$w3_action    = NULL;
  	
   } else { 

   	        $w3_action = (empty($_POST['w3_action'])) ? $_GET['w3_action'] : $_POST['w3_action']; 
   	      }
   
	if( isset($_GET['w3_sectionid']) && preg_match($w3strongpattern1, $_GET['w3_sectionid'], $matches) OR isset($_POST['w3_sectionid']) && preg_match($w3strongpattern1, $_POST['w3_sectionid'], $matches))
	{ 
  	$w3_sectionid = 1;
      
   } else { 
   	        $w3_sectionid = (empty($_POST['w3_sectionid'])) ? $_GET['w3_sectionid'] : $_POST['w3_sectionid']; 
   	      }
   	     
	if( isset($_GET['w3_start']) && preg_match($w3strongpattern1, $_GET['w3_start'], $matches)         OR isset($_POST['w3_start']) && preg_match($w3strongpattern1, $_POST['w3_start'], $matches))
	{
  	$w3_start     = 1;
  	
   } else { 
   	        $w3_start = (empty($_POST['w3_start'])) ? $_GET['w3_start'] : $_POST['w3_start']; 
   	      }
   	      
  if( isset($_GET['w3_imageid']) && preg_match($w3strongpattern1, $_GET['w3_imageid'], $matches)     OR isset($_POST['w3_imageid']) && preg_match($w3strongpattern1, $_POST['w3_imageid'], $matches))
	{
  	$w3_imageid   = 1;
  	
   } else { 
   	        $w3_imageid = (empty($_POST['w3_imageid'])) ? $_GET['w3_imageid'] : $_POST['w3_imageid']; 
   	     }

 if( empty($w3_sectionid) OR $w3_sectionid == 0 )
 {
 	$w3_sectionid    = 1;
 }

// define common vars

$PHP_SELF = get_bloginfo('url');

$w3images_images_table   = $table_prefix.'w3images';
$w3images_sections_table = $table_prefix.'w3images_sections';
$w3images_options_table  = $table_prefix.'w3images_options';
$w3images_comments_table = $table_prefix.'w3images_comments';
$w3images_plugin_path    = ABSPATH  . '/wp-content/plugins/w3images/';
$w3images_plugin_url     = $PHP_SELF.'/wp-content/plugins/w3images/';
?>