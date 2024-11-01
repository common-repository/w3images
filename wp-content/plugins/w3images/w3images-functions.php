<?php

/*
Plugin Name: w3images gallery for WordPress
Plugin URI: http://www.axew3.com/
Description: This plugin allows you to add an image gallery to your WordPress.
Author: Alessio Nanni - axew3
Author URI: http://www.axew3.com/
Version: 1.1.4
    Copyright (c) 2007/2010 by Alessio Nanni
    License: the w3images plugin for WordPress is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by the Free Software Foundation,
    either version 3 of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    http://www.gnu.org/licenses/
*/

if (stristr(htmlentities($_SERVER['PHP_SELF']), "w3images-functions.php")) {
	die ("You can't access this file directly.");
}

// this session is killed down on wp-w3images.php file
// session is also started on file w3images-captcha.php
// to keep alive it and control it against the user's passed value

// session is used just for captcha, no security measures needed

session_start();

global $wpdb, $PHP_SELF, $w3images_plugin_path, $w3images_plugin_url, $w3url,$table_prefix, $sectionid, $refreshpage, $settings, $w3images_images_table, $w3images_sections_table, $w3images_options_table, $w3images_comments_table, $wp_categories_table, $w3_action, $w3_sectionid, $w3_start, $this_page;

$PHP_SELF = get_home_url(); // get_home_url() // (WP 3.0+)

$w3images_images_table   = $table_prefix.'w3images';
$w3images_sections_table = $table_prefix.'w3images_sections';
$w3images_options_table  = $table_prefix.'w3images_options';
$w3images_comments_table = $table_prefix.'w3images_comments';
$wp_categories_table     = $table_prefix.'categories';
$w3images_plugin_path  = ABSPATH . '/wp-content/plugins/w3images/';
$w3images_plugin_url   = $PHP_SELF . '/wp-content/plugins/w3images/';

 require_once($w3images_plugin_path."includes/w3lang.php");

   $w3options        = $wpdb->get_results("SELECT value FROM $w3images_options_table");
   $maxcol           = $w3options[0]->value; 
   $limit            = $w3options[3]->value;
   $w3getPermValue   = $w3options[5]->value;
   $w3navLinksValue  = $w3options[6]->value;
   $w3slideSet       = $w3options[7]->value;
   
function w3images_add_manage_page() {
	add_management_page('w3images manager', 'w3images', 8, 'w3images/manage-images.php');
}

function w3images_add_options_page() {
  add_options_page('w3images Options', 'w3images', 8, 'w3images/images-options.php');
}

function add_w3images_manage_page() {
	add_images_management_page('w3images Manager', 'w3images', 8, 'w3images/manage-images.php');
}

function add_w3images_options_page() {
  add_images_options_page('w3images Options', 'w3images', 8, 'w3images/images-options.php');
}

function w3images_dashboard_widget() {
     wp_add_dashboard_widget('w3images_render_dashboard_widget', __('w3images Gallery', 'w3images'), 'w3images_render_dashboard_widget');
} 

// check if w3images table exist

function w3_init() {
 global $w3images_images_table, $w3images_sections_table, $w3images_options_table, $w3images_comments_table;
	
	function w3_table_exists($table_name) {
	  global $wpdb;
	  foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
	    if ($table == $table_name) return true;
    }
	  return false;
	}
	
function w3_create_table($table_name, $query) {
	  global $wpdb;
	  $result = $wpdb->query($query);
	  return w3_table_exists($table_name);
	}

  $w3images_table_query = "CREATE TABLE $w3images_images_table (
                imageid       INT(10)      UNSIGNED NOT NULL              AUTO_INCREMENT,
                sectionid     INT(10)      UNSIGNED NOT NULL DEFAULT '0',
                activated     TINYINT(1)            NOT NULL DEFAULT '0',
                filename      VARCHAR(32)           NOT NULL DEFAULT '',
                showauthor    TINYINT(1)            NOT NULL DEFAULT '0',
                author        VARCHAR(64)           NOT NULL DEFAULT '',
                title         VARCHAR(128)          NOT NULL DEFAULT '',
                description   TEXT,
                height        smallint(6)           DEFAULT NULL,
                width         smallint(6)           DEFAULT NULL,
                PRIMARY KEY(imageid)
                ) TYPE = MyISAM";

 $w3images_sections_table_query = "CREATE TABLE $w3images_sections_table (
                sectionid   INT(10)      UNSIGNED NOT NULL              AUTO_INCREMENT,
                parentid    INT(10)      UNSIGNED NOT NULL DEFAULT '0',
                activated   TINYINT(1)            NOT NULL DEFAULT '0',
                name        VARCHAR(128)          NOT NULL DEFAULT '',
                description TEXT                  NOT NULL,
                sorting     VARCHAR(32)           NOT NULL DEFAULT '',
                PRIMARY KEY(sectionid)
                ) TYPE = MyISAM";  
                
      
  $w3images_options_table_query = "CREATE TABLE $w3images_options_table ( 
                settingid  INT(10)         UNSIGNED NOT NULL             AUTO_INCREMENT,
                title  VARCHAR(100)                 NOT NULL, 
                description  MEDIUMTEXT             NOT NULL, 
                input   MEDIUMTEXT                  NOT NULL, 
                value MEDIUMTEXT                    NOT NULL, 
                displayorder INT(10)                NOT NULL DEFAULT '0',
                PRIMARY KEY(settingid)
                ) TYPE = MyISAM";
                
               
  if(!w3_table_exists($w3images_images_table)) {
	  w3_create_table($w3images_images_table, $w3images_table_query);
	}
	
	if(!w3_table_exists($w3images_sections_table)) {
	  w3_create_table($w3images_sections_table, $w3images_sections_table_query); 
	}

	if(!w3_table_exists($w3images_options_table)) {
	  w3_create_table($w3images_options_table, $w3images_options_table_query);
	}
}

w3_init();

$get_def_val_info = $wpdb->get_row("SELECT sectionid FROM $w3images_sections_table WHERE sectionid='1'");
 
if(! $get_def_val_info) {

 $wpdb->query("INSERT INTO $w3images_images_table (imageid, sectionid, activated, filename, showauthor, author, title, description, height, width) 
            VALUES ('1', '1', '1', '1.jpg', '0', 'axew3', 'the wp w3images gallery plugin default image', 'Umbria Sunset - Lake Trasimeno - Italy', '500', '375')");

 $wpdb->query("INSERT INTO $w3images_sections_table (sectionid, parentid, activated, name, description, sorting)
					  VALUES (NULL, '0', '1', 'w3images - rename it as you like', '', 'Newest First')");
 
 $wpdb->query("INSERT INTO  $w3images_options_table (settingid, title, description, input, value, displayorder) 
					  VALUES (NULL, 'Thumbnails per Row', 'Enter the number of thumbnails to display per row:', 'text', '5', '1'), (NULL, 'Max Thumbnail Width', 'Max width thumbnail resize:', 'text', '100', '2'), (NULL, 'Max Thumbnail Height', 'Max height thumbnail resize:', 'text', '100', '3'), (NULL, 'Thumbnails Per Page', 'Number of thumbnails images that a section should display per page:', 'text', '30', '4'), (NULL, 'Image Resizing', 'Resize images to thumbnails using:', '<select name=\\\\\"settings[\$setting[settingid]]\\\\\">\r\n<option value=\\\\\"2\\\\\"    \".w3se(\$settings[value]==\"2\",       \"selected\", \"\").\">GD2</option>\r\n<option value=\\\\\"1\\\\\"    \".w3se(\$settings[value]==\"1\",       \"selected\", \"\").\">GD1</option>\r\n<option value=\\\\\"0\\\\\"    \".w3se(\$settings[value]==\"0\", \"selected\", \"\").\">Submit Thumbnails</option>\r\n</select>', '2', 6), (NULL, 'Users Upload Permission', 'Choose what level is required to upload images on your gallery (notice: with or without moderation)', '<select name=\\\\\"settings[\$setting[settingid]]\\\\\">\r\n<option value=\\\\\"5\\\\\"    \".w3se(\$settings[value]==\"5\",       \"selected\", \"\").\">Enable Images upload Only For Admins</option>\r\n<option value=\\\\\"4\\\\\"    \".w3se(\$settings[value]==\"4\",       \"selected\", \"\").\">Enable Images Upload For All Admins, Editors and Contributors</option>\r\n<option value=\\\\\"3\\\\\"    \".w3se(\$settings[value]==\"3\",       \"selected\", \"\").\">Enable Images upload For Subscribers (registered) With Moderation</option>\r\n<option value=\\\\\"2\\\\\"    \".w3se(\$settings[value]==\"2\",       \"selected\", \"\").\">Enable Images upload For Subscribers (registered) Without Moderation</option>\r\n<option value=\\\\\"1\\\\\"    \".w3se(\$settings[value]==\"1\",       \"selected\", \"\").\">Enable Images Upload For All With Moderation</option>\r\n<option value=\\\\\"0\\\\\"    \".w3se(\$settings[value]==\"0\", \"selected\", \"\").\">Enable Images Upload For All Without Moderation</option>\r\n</select>', '1', 6), (NULL, 'Next-Previous link mode', 'Next or Previous link mode. Set how to navigate images: clickable thumbnail image link or simple text link. Note: this is applicable only for js/html popup mode or page inline gallery mode', '<select name=\\\\\"settings[\$setting[settingid]]\\\\\">\r\n<option value=\\\\\"1\\\\\"    \".w3se(\$settings[value]==\"1\",       \"selected\", \"\").\">Clickable Thumbnail</option>\r\n<option value=\\\\\"0\\\\\"    \".w3se(\$settings[value]==\"0\", \"selected\", \"\").\">Text Link</option>\r\n</select>', '1', 7), (NULL, 'Images navigation', 'Slideshow: set mode to navigate images.', '<select name=\\\\\"settings[\$setting[settingid]]\\\\\">\r\n<option value=\\\\\"2\\\\\"    \".w3se(\$settings[value]==\"2\",       \"selected\", \"\").\">Display slideshow in the same page</option>\r\n<option value=\\\\\"1\\\\\"    \".w3se(\$settings[value]==\"1\",       \"selected\", \"\").\">Simple js/html popup-like slideshow</option>\r\n<option value=\\\\\"0\\\\\"    \".w3se(\$settings[value]==\"0\", \"selected\", \"\").\">Ajax Shadowbox Slideshow</option>\r\n</select>', '1', 8)");
 }

function &w3_sanitization($string) {

//php 5 > 6 >
//filter_var($subject, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
//filter_var($subject, FILTER_SANITIZE_URL, FILTER_FLAG_STRIP_HIGH);
// $string = mb_convert_encoding($string, "UTF-8", "auto"); // only php5>
// $string = preg_replace("/[^a-zA-Z0-9]/", "", $string);

$string = preg_replace("/[^a-zA-Z0-9- .,;:\"]/", "", $string);

  return $string;

}

 // Check GD Setting

$getgdselected = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE title = 'Image Resizing'");

$gdselected = $getgdselected->value;    // 2 = GD2
                                        // 1 = GD1
                                        // 0 = Thumbnail

function w3se($expression, $returntrue, $returnfalse = '')
{
  if($expression == 0)
  {
    return $returnfalse;
  }
  else
  {
    return $returntrue;
  }
}

function w3_GetMenu($sectionid, $currsectionid)
{
  global $wpdb, $categoryid, $w3images_sections_table, $PHP_SELF;

  $getsections = $wpdb->get_results("SELECT sectionid, parentid, name FROM $w3images_sections_table WHERE sectionid = '$sectionid'");

   if(!$getsections){

  	 // if a not existent section ID has been passed, here we force to get the root section ID 1
     // avoid some loop problem
  	 $getsections   = $wpdb->get_results("SELECT sectionid, parentid, name FROM $w3images_sections_table WHERE sectionid = 1");

    // ... set default sectionid
     $sectionid = 1;
     echo W3SECTIONDONOTEXIST;
       
    }
 	
   foreach ($getsections as $getsection) {
	   $section  = $getsection->sectionid;
	   $parentid = $getsection->parentid;
	   $catname  = $getsection->name;
   }
  
      while($sectionid != 1){
          $sectionid = w3_GetMenu($parentid, $currsectionid);
       }

     if($section == $currsectionid)
     { 
       echo '<b class="w3menunav"><a href="'.$PHP_SELF.'/wp-w3images.php?w3_sectionid=' . $section  . '">'.$catname.'</a></b>&nbsp;';
     }
       else { 
              echo '<b class="w3menunav"><a href="'.$PHP_SELF.'/wp-w3images.php?w3_sectionid=' . $section  . '">'.$catname.'</a>&nbsp;<span class="w3sep">&nbsp; - &nbsp;</span></b>';
            }

   return $sectionid;
  
}



function w3InsertImage($sectionid, $activated)
{

global $wpdb, $PHP_SELF, $w3images_images_table, $w3images_options_table, $w3images_plugin_url, $w3images_plugin_path,  $refreshpage, $image;


  $image         = $_FILES['w3_image'];
  $filesize      = $_FILES['w3_image']['size'];
  $thumbnail     = $_FILES['w3_thumbnail'];
  $thumbnailsize = $_FILES['w3_thumbnail']['size'];
  $MAX_FILE_SIZE = $_POST['MAX_FILE_SIZE'];
  $author        = $_POST['w3_author'];
  $title         = $_POST['w3_title'];
  $description   = $_POST['w3_description'];
  $w3randG       = $_POST['w3randG'];
  $showauthor    = 1; // future use
  
  
  
  
  // last check for size limit
 if($filesize > $MAX_FILE_SIZE){
 	  echo '<h3>The file you are try to upload is too big. Here some info:</h3>';
  	echo '<b>size limit (in bytes): '.$MAX_FILE_SIZE.' byte<br />size of your image (in bytes): '.$filesize.'</b>';
    return 0;
 	}


$uploadErrors = array(
    UPLOAD_ERR_INI_SIZE   => 'w3images info: The uploaded file exceeds the upload_max_filesize directive in php.ini.',
    UPLOAD_ERR_FORM_SIZE  => 'w3images info: The uploaded file exceeds the allowed dimesion that is '.$MAX_FILE_SIZE.' kb. Submitted image dimension: '.$filesize.'.',
    UPLOAD_ERR_PARTIAL    => 'w3images info: The uploaded file was only partially uploaded.',
    UPLOAD_ERR_NO_FILE    => 'w3images info: No file where loaded.',
    UPLOAD_ERR_NO_TMP_DIR => 'w3images info: Missing temporary folder.',
    UPLOAD_ERR_CANT_WRITE => 'w3images info: Failed to write file to disk. Please, check CHMOD value for the folder "images", which is inside the w3images plugin folder. If you are not the adminstrator of this site, you may can inform him about this issue.',
    UPLOAD_ERR_EXTENSION  => 'w3images info: File upload stopped by extension.',
);

   $w3errorCode = $_FILES['w3_image']['error'];

   if($w3errorCode !== UPLOAD_ERR_OK)
   {
    if(isset($uploadErrors[$w3errorCode])){
        echo "<h4>" . $uploadErrors[$w3errorCode] . "</h4>";
        PrintRedirect2($gotopage, $sectionid, 4);
        return;
      }
}

  // sanitize any input
 $image['name']      =&  w3_sanitization($image['name']);
 $thumbnail['name']  =&  w3_sanitization($thumbnail['name']);
 $author             =&  w3_sanitization($author);
 $title              =&  w3_sanitization($title);
 $description        =&  w3_sanitization($description);
 $w3randG            =&  w3_sanitization($w3randG);

 // GD lib setting
  $imageresizing = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE title = 'Image Resizing'");
  $gdselected = $imageresizing->value;

	// also filename can be empty if the file size exceed
  if ( $image['name'] == '' ) {
  	$w3error =  'The file name is empty due to file size exceed, or just because the field contain invalid characters or nothing.';
  }
  
  elseif ( $_SESSION['security_code'] != $w3randG ) {
  
  	       $w3error = 'Security code mismatch';
  }
  
  elseif ( $thumbnail['name'] == '' && $gdselected == 0 ) {
    $w3error = 'The thumbnail field contain a value that isn\'t correct or invalid characters';
  }
  
  elseif ( empty($thumbnailsize) && $gdselected == 0  ) {
  	$w3error = 'You need to provide a thumbnail for your image';
  }
  
  elseif ( $author == '' ) {
  	$w3error = 'The author field contain an incorrect value or invalid characters';
  }
  
  elseif ( $title == '' ) {
  	$w3error = 'The title field contain an incorrect value or invalid characters';
  }
  
  elseif ( $description == '' ) {
  	$w3error = 'The description field contain an incorrect value or invalid characters';
  }
  
    if($w3error)
  {

      
      echo '<h3 class="w3errormsg">'.$w3error.'</h3>';
      
      PrintRedirect2($gotopage, $sectionid, 4);
   
    return;

  }
  

  
  $imagesdir  = $w3images_plugin_path . 'images';

  $known_photo_types = array(
                         'image/pjpeg' => 'jpg',
                         'image/jpeg'  => 'jpg',
                         'image/gif'   => 'gif',
                         'image/bmp'   => 'bmp',
                         'image/x-png' => 'png',
                         'image/png'   => 'png'
                       );

  $gd_function_suffix = array(
                          'image/pjpeg' => 'JPEG',
                          'image/jpeg'  => 'JPEG',
                          'image/gif'   => 'GIF',
                          'image/bmp'   => 'WBMP',
                          'image/x-png' => 'PNG',
                          'image/png'   => 'PNG'
                        );

  $valid_image_types = array('image/pjpeg',
                             'image/jpeg',
                             'image/gif',
                             'image/bmp',
                             'image/x-png',
                             'image/png');
  
  if(!in_array($image['type'], $valid_image_types))
    $w3errors[] = 'It\'s not a valid image type.';

  if(isset($_FILES['w3_thumbnail']) AND !in_array($thumbnail['type'], $valid_image_types))
    $w3errors[] = 'It is not a valid thumbnail type';

  if(!strlen($title))
    $w3errors[] = 'You must enter a title for the image';
 
  if(!strlen($author))
    $w3errors[] = 'Please insert a name for the author field';

  if($image['size'] == 0)
    $w3errors[] = 'You have not select an image or your image is too big. Max dimension for images upload: '.intval($MAX_FILE_SIZE).'<br />Your file size: '.$sizeToLoad.' kb';

  if($gdselected == 0 AND $thumbnail['size'] == 0)
    $w3errors[] = 'Please select a thumbnail';

 
  if(!strlen($description))
    $w3errors[] = 'Enter a description';

  if($w3errors)
  {
    foreach($w3errors as $key => $value)
    {
      echo '<h3 class="w3errormsg">'.$value.'</h3>';
    }


    return 0;

  }
  
  $imgidv  = $wpdb->get_row("SELECT MAX(imageid) AS imageidw FROM $w3images_images_table");
  $imageid .= $imgidv->imageidw; 
  $imageid = $imageid + 1;
  
    //  ID for image name and definitive store

  $filetype  = $image['type'];
  $extention = $known_photo_types[$filetype];
  $filename  = $imageid . '.' . $extention;

  $wpdb->query("INSERT INTO $w3images_images_table(imageid, sectionid, activated, filename, showauthor, author, title, description, height, width)
                VALUES('$imageid', '$sectionid', '$activated', '$filename', '$showauthor', '$author', '$title', '$description', '0', '0') ");

  copy($image['tmp_name'], $imagesdir."/".$filename);
  
  $size = GetImageSize($imagesdir . '/' . $filename );

  $wpdb->query("UPDATE $w3images_images_table SET filename = '$filename', height = '$size[1]', width = '$size[0]' WHERE imageid = '$imageid' ");


  if($gdselected)
  {
  	
    $w3opt     = $wpdb->get_results("SELECT value FROM $w3images_options_table");
    $maxwidth  = $w3opt[1]->value;
    $maxheight = $w3opt[2]->value; 


    if($size[0] > $size[1])
    {
      $thumbnail_width  = $maxwidth;
      $thumbnail_height = (int)($maxwidth * $size[1] / $size[0]);
    }
    else
    {
      $thumbnail_height = $maxheight;
      $thumbnail_width  = (int)($maxheight * $size[0] / $size[1]);
    }

    $function_suffix    = $gd_function_suffix[$filetype];
    $function_to_read   = "ImageCreateFrom".$function_suffix;
    $function_to_write  = "Image".$function_suffix;

    $source_handle      = $function_to_read ( $imagesdir."/".$filename);

    if($gdselected == 2)
    {
      $destination_handle = ImageCreateTrueColor($thumbnail_width, $thumbnail_height);

      ImageCopyResampled($destination_handle, $source_handle, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $size[0], $size[1]);
    }
    else if($gdselected == 1)
    {
      $destination_handle = ImageCreate($thumbnail_width, $thumbnail_height);

      ImageCopyResized($destination_handle, $source_handle, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $size[0], $size[1]);
    }

    $function_to_write($destination_handle, $imagesdir."/tb_".$filename);
    ImageDestroy($destination_handle);

  }
  else
  {

    $thumbtype = $thumbnail['type'];
    $extention = $known_photo_types[$thumbtype];
    $thumbname = 'tb_' . $imageid . '.' . $extention;

    copy($thumbnail['tmp_name'], $imagesdir . '/' . $thumbname);
  }
  
  // Display appropriate answer to the $activated value

  if($activated == 0) {
  	      PrintRedirect2($PHP_SELF, $sectionid, $timeout = 4);
      echo'<h3 class="w3h3">Your image Will be checked out as soon and then published online. Thank for your image entry!</h3>';
      
 
    }
    else {
    	   	PrintRedirect2($PHP_SELF, $sectionid, $timeout = 4);
    	echo'<h3 class="w3h3">Your image has been saved!</h3>';
 
    
    	}

 return;
}



function w3SubmitImage($sectionid,$activated)
{
  global $wpdb, $w3images_options_table, $w3images_sections_table, $current_user, $PHP_SELF,$w3images_plugin_path;

// if passed section ID do not exist, do not display upload form
 $gsi = $wpdb->get_results("SELECT sectionid,name FROM $w3images_sections_table WHERE sectionid = '$sectionid'");

   if(!$gsi){
   	   return;
    } else{
    	
    	foreach($gsi as $gsid){
    		$sname = $gsid->name;
    	}
    	
    }

 // wp default global users info
 
get_currentuserinfo();

  echo '<div class="w3wrap2">'.W3SUBMITIMG.'&nbsp;&nbsp;<strong>'.$sname.'</strong><br /><br />';
 // w3_GetMenu($sectionid, $sectionid);
  echo '</div>'; 
  echo '<div class="w3wrap2">';
      
       $maxsize = ini_get('upload_max_filesize');
       
    if (strpos($maxsize, 'M')) {
        substr($maxsize, 0, -1);  
        $maxsize = intval($maxsize)*1024*1024;
      }
    elseif (strpos($maxsize, 'K')) {
    	substr($maxsize, 0, -1);
        $maxsize = intval($maxsize)*1024;
      }
    elseif (strpos($maxsize, 'G')) {
    	substr($maxsize, 0, -1);
        $maxsize = intval($maxsize)*1024*1024*1024;
      }
      
     else { unset($maxsize); }
     
     if(!$maxsize) { $maxsize = 1000000; } // ini_get have return nothing: give to $maxsize an arbitrary medium value 
 
 echo '<form method="post" enctype="multipart/form-data" action="'.$PHP_SELF.'/wp-w3images.php?w3_action=insertimage&amp;w3_sectionid='.$sectionid.'' . '">';
 echo '<table width="100%" border="0" cellspacing="10" cellpadding="0">';
 echo '<tr><td valign="top">'.W3AUTHOR.'</td>
       <td style="padding-left: 8px; padding-bottom: 8px;"><input size="30%" type="text" name="w3_author" value="'.$current_user->user_login.'" /></td></tr>';  
 echo '<tr>
          <td valign="top">'.W3ITITLE.'</td>
          <td style="padding-left: 8px; padding-bottom: 8px;"><input size="30%" type="text" name="w3_title" /></td>
             </tr>';

// GD option

$getgdselected = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE title = 'Image Resizing'", ARRAY_A);

$gdselected = $getgdselected['value'];  // 2 = GD2
                                        // 1 = GD1
                                        // 0 = User will submit thumbnail

 if($gdselected == 0)
{
   echo '<tr>
            <td valign="top">'.W3THUMB.'</td>
            <td style="padding-left: 8px; padding-bottom: 8px;"><input name="w3_thumbnail" type="file" size="30%" /></td>
          </tr>';
  }
  echo '<tr>
          <td valign="top">'.W3DESCRIPTION.'</td>
          <td style="padding-left: 8px; padding-bottom: 8px;"><textarea name="w3_description" rows="5" cols="40%"></textarea></td>
        </tr>
        <tr>
        
          <td style="padding-bottom: 8px;"><img src="./wp-content/plugins/w3images/w3images-captcha.php" /> &nbsp;<input style="font-size:xx-small;text-align:center;" value="insert code" onblur="if(this.value==\'\')this.value=\'insert code\';" onfocus="if(this.value==\'insert code\')this.value=\'\';" name="w3randG" size="12" /></td>
          <td style="padding-left: 8px; padding-bottom: 8px;"><input type="hidden" name="w3_sectionid" value="' . $sectionid . '" /><input type="hidden" name="w3randS" value="' . $w3rand . '" /><input type="hidden" name="MAX_FILE_SIZE" value="'.$maxsize.'" /><input name="w3_image" type="file" /><input type="submit" value="'.W3SUBMIT.'" />';
       
      if($activated == 0){ 
         echo '<div>'.W3MODERATEIMG.'</div></td>
               </tr></table></form></div>';
       } else {
       	        echo '</tr>
                      </table>
                      </form></div>';
    }


}

function w3_GetSectionImageCount($sectionid, $imagecount)
{
  global $wpdb, $w3images_images_table, $w3images_sections_table;

  $getimagecount = $wpdb->get_results("SELECT *  FROM $w3images_images_table WHERE sectionid = '$sectionid' AND activated = '1'");

  $imagecount = sizeof($getimagecount);

  $getsubsections = $wpdb->get_results("SELECT sectionid FROM $w3images_sections_table WHERE parentid = '$sectionid'");

if ($getsubsections) {
	
  foreach($getsubsections as $subsection)
  {
    $imagecount = w3_GetSectionImageCount($subsection->sectionid, $imagecount);
  }
}
  return sizeof($getimagecount);
}

// w3_GetSubTotalSectionImageCount is a recursive function:                             //
// it count total images on each directory and all related subdirectories if there are. //
// axew3 07/01/2007 00.31.06  $rev 14/06/2010                                           // 

function w3_GetSubTotalSectionImageCount($sectionid, $imagecount) 
{
   global $wpdb, $w3images_images_table, $w3images_sections_table;

  $getimagecount  = $wpdb->get_results("SELECT * FROM $w3images_images_table WHERE sectionid = '$sectionid' AND activated = '1'");

  $getsubsections = $wpdb->get_results("SELECT sectionid FROM $w3images_sections_table WHERE parentid = '$sectionid'");

if ($getsubsections) {
	
  foreach($getsubsections as $subsection)
  {
    $imagecount = w3_GetSubTotalSectionImageCount($subsection->sectionid, $imagecount);
    
    $getsubsubsections = $wpdb->get_results("SELECT sectionid FROM $w3images_sections_table WHERE parentid = '".$subsection->sectionid."'");
   
      while ($countsub = $getsubsubsections->sectionid){
    	
       $getimagecount = w3_GetSubTotalSectionImageCount($countsub, $imagecount);
     
      }
   }
}
  return sizeof($getimagecount) + $imagecount;
}


// function w3_SectionsSubNames($sectionid)
// it return all links to sub sections for each passed parent section
// $date     01/08/2010
// $rev.date 16/08/2010

function w3_SectionsSubNames($sectionid)
{
   global $wpdb, $w3images_sections_table, $PHP_SELF;

   $purl0 = $PHP_SELF . "/wp-w3images.php?w3_sectionid=";

     $getsubsections = $wpdb->get_results("SELECT sectionid, parentid, name FROM $w3images_sections_table WHERE parentid = '$sectionid' AND activated = 1 ORDER BY sectionid ASC");
    	 
    	 $b = sizeof($getsubsections);
    	 $c = 0;
    	 
         foreach( $getsubsections as $getsubin ){

          $c++;
     
           if( $getsubin->sectionid == 1 ) {  continue; }

      	    echo "<small><a href=\"".$purl0.$getsubin->sectionid."\" title=\"".$getsubin->name."\">".$getsubin->name."</a></small> ";
      	   
      	      if( $c == $b ){ echo "<br />"; }
      	      
      	       $getsubde = $wpdb->get_results("SELECT sectionid, parentid, name FROM $w3images_sections_table WHERE parentid = '$getsubin->sectionid' AND activated = 1 ORDER BY sectionid ASC");
             
                 $d = sizeof($getsubde);
    	           $e = 0;  
    	        
                   foreach( $getsubde as $getsubdew ){
                  	
                  	  $e++;
         
                      
                    echo  "<small><a href=\"".$purl0.$getsubdew->sectionid."\" title=\"".$getsubdew->name."\">".$getsubdew->name."</a></small> ";
                     
                      if( $d == $e ){ echo "<br />"; }
                 
                    }    
            
                        w3_SectionsSubNames($getsubdew->sectionid);
             }
     
 	 return;
}




function PrintRedirect2($gotopage, $sectionid, $timeout = 0)
{
	
	// todo -> $gotopage need to be removed as argument
	global $PHP_SELF;

 $PHP_SELF = $PHP_SELF.'/wp-w3images.php?w3_sectionid=' . $sectionid;


  echo '<table width="100%" style="border:0" cellpadding="0" cellspacing="0">
        <tr>
          <td style="width:100%;text-align:center;">';
  echo '<tr><td style="width:100%;text-align:center;vertical-align:bottom">Updating...</td>
        </tr>
        <tr>
          <td style="width:100%;text-align:center;vertical-align:bottom">';

  echo '<a href="'.$PHP_SELF.'" onclick="javascript:clearTimeout(timerID);">Click here if your browser don\'t redirect you.</a>';

  echo '</td></tr></table>';

  echo '<script type="text/javascript">';
  if($timeout == 0)
  {
    echo 'window.location="'.$PHP_SELF.'"';
  }
  else
  {
    echo 'timeout = '.($timeout*5).';

          function Refresh()
          {
            timerID = setTimeout("Refresh();", 100);

            if (timeout > 0)
            {
              timeout -= 1;
            }
            else
            {
              clearTimeout(timerID);
              window.location="'.$PHP_SELF.'";
            }
          }

          Refresh();';
  }

  echo '</script>';

}


// w3 random thumb function

// $randSectFrom: section from where images are token: 0 get images from any available section. Another integer value specify the specific section from where images are token.
// $randImgNum: number of images to be displayed
// $w3xrow: number of images displayed per row
 
// w3images_random_thumbs($a,$b,$c);
 
function w3images_random_thumbs($randSectFrom=0, $randImgNum=9, $w3xrow=3) {
	
	global $wpdb, $PHP_SELF, $w3images_images_table, $w3images_plugin_url;
	
	
	$getpopmode = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE title = 'Images navigation'");

  $w3popmode = $getpopmode->value;  // get the popup mode to be used
  
  

  if($randSectFrom == 0) {  

	   $randfilenames = $wpdb->get_results("SELECT * FROM $w3images_images_table WHERE activated = 1 ORDER BY RAND() LIMIT ".$randImgNum."");
   }
     else {
 	          $randfilenames = $wpdb->get_results("SELECT * FROM $w3images_images_table WHERE sectionid = ".$randSectFrom." AND activated = 1 ORDER BY RAND() LIMIT ".$randImgNum."");
         }

if (!$randfilenames) { 

	   echo '<h3 style="color:#FF0000">Selected images section do not exist or there aren\'t uploaded images yet.</h3>';
	   return;
	}



  $w3curcol = 0; 
  $w3totimg = $randImgNum;
  $w3Rows   = $w3totimg;
  
  
	 echo '<table class="w3tablecont" cellpadding="0" cellspacing="0"><tbody><tr>';
	
	 foreach ($randfilenames as $randfn) {

		 if($w3popmode == 1){
		 	
		 	  $sizew = $randfn->width;
		   	$sizeh = $randfn->height;
		 	
		  echo "<td class=\"w3randtd\"><a href='' onclick=\"window.open('".$PHP_SELF."/wp-w3pup.php?w3_sectionid=" . $randfn->sectionid . "&amp;w3_imageid=" . $randfn->imageid . "', '', 'width=" . ($sizew+100) . ",height=" . ($sizeh+300) . ",directories=no,location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,resizable=yes');return false\" target=\"_blank\"><img style=\"border:1px solid #000\" src=\"" . $w3images_plugin_url . "images/tb_" . $randfn->filename . "\" alt=\"" . $randfn->filename . "\" /></a></td>";
	   } else {
         echo "<td class=\"w3randtd\"><a href=\"".$w3images_plugin_url."images/".$randfn->filename."\" title=\"".$randfn->title." - ".$randfn->description."\" rel=\"shadowbox[w3images]\"><img src=\"".$w3images_plugin_url."images/tb_".$randfn->filename."\" alt=\"".$randfn->title."\" /></a></td>";
        }
        
  	 $w3curcol++;
	
	     if($w3curcol == $w3xrow)
        {
          echo '</tr>';
          $w3curcol = 0;
        }
     }
  
   echo '</tbody></table>'; 
	
}


function wp_w3_uploadPerm($w3getPermValue,$w3_action,$w3_sectionid) {

global $PHP_SELF,$for_user_id;
 // wp default global users info

if ( '' == $for_user_id )
		$user = wp_get_current_user();
	else
		$user = new WP_User($for_user_id);
		
		$user_level = (int) isset($user->user_level) ? $user->user_level : 0;
		
  // wp users default level values
 
  // for reference see: http://codex.wordpress.org/Roles_and_Capabilities  // //
  
  // set permission for each group as request for w3images:
 
   // 5) Enable Images Upload For Admins (Only)
   // 4) Enable Images Upload For All Admins, Editors and Contributors (Only)
   // 3) Enable Images upload For Subscribers (all registered) With Moderation 
   // 2) Enable Images upload For Subscribers (all registered) Without Moderation 
   // 1) Enable Images upload For All (also unregistered) With Moderation 
   // 0) Enable Images upload For All (also unregistered) Without Moderation 


 if($w3getPermValue == 0){ // images uploads allowed for all with no moderation
    if($w3_action == 'insertimage')  { w3InsertImage($w3_sectionid, 1); }
    if($w3_action == 'w3SubmitImage'){ w3SubmitImage($w3_sectionid, 0);     }      
    
   if(!isset($w3_action)){
	  echo '<form action="'.$PHP_SELF.'/wp-w3images.php" method="post">
	        <input type="hidden" name="w3_action" value="w3SubmitImage" />
	        <input type="hidden" name="w3_sectionid" value="'.$w3_sectionid.'" />
         	<input type="submit" value="'.W3SUBMIT.'" class="w3imagesInput" />
          </form>';
   }

 }


 if($w3getPermValue == 1){  // images uploads allowed for all with moderation (except admin)
 	 
 	    if ($user_level > 8)
 	    {
 	    	if($w3_action == 'insertimage')  { w3InsertImage($w3_sectionid, 1); }
 	  	
 	  	}	else {
               if($w3_action == 'insertimage')  { w3InsertImage($w3_sectionid, 0); }
             }
             
    if($w3_action == 'w3SubmitImage' && $user_level > 9 )
      { 
      	 w3SubmitImage($w3_sectionid, 1); 
      	 
      } else {
      	        if($w3_action == 'w3SubmitImage') { w3SubmitImage($w3_sectionid, 0); }
      	     }
      	           
     
       if(!isset($w3_action)){
          
	        echo '<form action="'.$PHP_SELF.'/wp-w3images.php" method="post">
	           <input type="hidden" name="w3_action" value="w3SubmitImage" />
	           <input type="hidden" name="w3_sectionid" value="'.$w3_sectionid.'" />
             <input type="submit" value="'.W3SUBMIT.'" class="w3imagesInput" />
            </form>';
       }
      
 }


 if($w3getPermValue == 2){  // images uploads allowed for all registered with no moderation
   if ($user_level > 0) { // this user seem to be logged in
   
       if($w3_action == 'insertimage')  { w3InsertImage($w3_sectionid, 1); }
       if($w3_action == 'w3SubmitImage'){ w3SubmitImage($w3_sectionid);    }      
   
       if(!isset($w3_action)){
	  echo '<form action="'.$PHP_SELF.'/wp-w3images.php" method="post">
	        <input type="hidden" name="w3_action" value="w3SubmitImage" />
	        <input type="hidden" name="w3_sectionid" value="'.$w3_sectionid.'" />
         	<input type="submit" value="'.W3SUBMIT.'" class="w3imagesInput" />
          </form>';
     }
   }

}


 if($w3getPermValue == 3){  // images uploads allowed for all with moderation (exclude admin) 
   if ($user_level > 0) {  // seem to be logged in
         
         if ($user_level > 9) 
 	       {
 	  	     if($w3_action == 'insertimage')  { w3InsertImage($w3_sectionid, 1); }
 	  	   } 
 	  	    else { 	                               
                 if($w3_action == 'insertimage')  { w3InsertImage($w3_sectionid, 0); }
               }
           
       if($w3_action == 'w3SubmitImage'){ w3SubmitImage($w3_sectionid);    }      
   
       if(!isset($w3_action)){
	  echo '<form action="'.$PHP_SELF.'/wp-w3images.php" method="post">
	        <input type="hidden" name="w3_action" value="w3SubmitImage" />
	        <input type="hidden" name="w3_sectionid" value="'.$w3_sectionid.'" />
         	<input type="submit" value="'.W3SUBMIT.'" class="w3imagesInput" />
          </form>';
        }
  }

}

 if($w3getPermValue == 4){

   if ($user_level > 5) {  // user seem to be logged in and is more than subscriber
   	                                     // see: http://codex.wordpress.org/User_Levels#User_Level_6 
   	                                     // for references and additions

       if($w3_action == 'insertimage')  { w3InsertImage($w3_sectionid, 1); }
       if($w3_action == 'w3SubmitImage'){ w3SubmitImage($w3_sectionid);    }      
   
       if(!isset($w3_action)){
	  echo '<form action="'.$PHP_SELF.'/wp-w3images.php" method="post">
	        <input type="hidden" name="w3_action" value="w3SubmitImage" />
	        <input type="hidden" name="w3_sectionid" value="'.$w3_sectionid.'" />
         	<input type="submit" value="'.W3SUBMIT.'" class="w3imagesInput" />
          </form>';
       }
  }

}
    
 if($w3getPermValue == 5){  // // images uploads allowed only for admin

  if ($user_level > 9) {
         	
       if($w3_action == 'insertimage')  { w3InsertImage($w3_sectionid, 1); }
       if($w3_action == 'w3SubmitImage'){ w3SubmitImage($w3_sectionid);    }      
   
       if(!isset($w3_action)){
	  echo '<form action="'.$PHP_SELF.'/wp-w3images.php" method="post">
	        <input type="hidden" name="w3_action" value="w3SubmitImage" />
	        <input type="hidden" name="w3_sectionid" value="'.$w3_sectionid.'" />
         	<input type="submit" value="'.W3SUBMIT.'" class="w3imagesInput" />
          </form>';
       }
   } 

}   
            
   return;
   
} // End wp_w3_uploadPerm


// START function wp_w3_displayImages($sectionid, $start)

function wp_w3_displayImages($sectionid, $start){

  global $wpdb, $PHP_SELF, $w3_action, $w3_imageid, $w3_sectionid, $userdata, $w3images_images_table, $w3images_options_table, $w3images_sections_table, $w3images_plugin_url, $w3slideSet, $maxcol, $limit, $w3navLinksValue;

if( $w3_action == 'insertimage' OR $w3_action == 'w3SubmitImage' ){ return; }
 

 if( empty($start))
 { 
 	 $start = 0;
 }

// -> update this mysql

   $popup_set = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE settingid = 8");
   $popup_set = $popup_set->value;
   
   $maxcol = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE settingid = 1");
   $maxcol = $maxcol->value;
   
   $limit  = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE settingid = 4");
   $limit = $limit->value; 

  $getsubsections = $wpdb->get_results("SELECT * FROM $w3images_sections_table WHERE parentid = '$sectionid' AND activated = 1");
  
  foreach ($getsubsections as $subsection) {
  	
  	// Section images count
        $numsectionimages       = w3_GetSectionImageCount($subsection->sectionid, 0);
    
    // Total directory images count
     
        $mainfoldertotnumimages = w3_GetSubTotalSectionImageCount($subsection->sectionid, 0);
  
        $mainfoldertotnumimages = '<strong> '.$mainfoldertotnumimages .= '</strong> ' . W3TOTALSUBCOUNT;
  
    echo '<div style="text-align:left"><strong style="padding-left:10px;"><a href="'.$PHP_SELF.'/wp-w3images.php?w3_sectionid=' . $subsection->sectionid . '">'.$subsection->name.'</a></strong> ('.$numsectionimages.W3IMAGES.')' .$mainfoldertotnumimages.'<br />';
    
     if(strlen($subsection->description)){
     	
        echo '<span style="padding-left:20px;font-size:70%;">' . $subsection->description . '</span>';
        
     }
     
     echo'</div>';
     
      // get subsections list for each parent section
     
   w3_SectionsSubNames($subsection->sectionid);

  }

   // get single image 

   if(is_numeric($w3_imageid))
    {
    	
       $image = $wpdb->get_row("SELECT * FROM $w3images_images_table WHERE imageid = '$w3_imageid'"); 

     } else {
              $image->title = $image->author = $image->imageid = NULL;
            }

  $common_set = $wpdb->get_row("SELECT sorting FROM $w3images_sections_table WHERE sectionid = '$sectionid'");
  $sorttype   = $common_set->sorting;

  switch($sorttype)
  {
    case 'Alphabetically A-Z':
      $order = 'title ASC';
      $navprevioussql = 'title < \'' . $image->title . '\' ORDER BY title DESC';
      $navnextsql     = 'title > \'' . $image->title . '\' ORDER BY title ASC';
    break;

    case 'Alphabetically Z-A':
      $order = 'title DESC';
      $navprevioussql = 'title > \'' . $image->title . '\' ORDER BY title ASC';
      $navnextsql     = 'title < \'' . $image->title . '\' ORDER BY title DESC';
    break;

    case 'Author Name A-Z':
      $order = 'author ASC';
      $navprevioussql = 'author < \'' . $image->author . '\' ORDER BY author DESC';
      $navnextsql     = 'author > \'' . $image->author . '\' ORDER BY author ASC';
    break;

    case 'Author Name Z-A':
      $order = 'author DESC';
      $navprevioussql = 'author > \'' . $image->author . '\' ORDER BY author ASC';
      $navnextsql     = 'author < \'' . $image->author . '\' ORDER BY author DESC';
    break;

    case 'Oldest First':
      $order = 'imageid ASC';
      $navprevioussql = 'imageid < ' . $image->imageid . ' ORDER BY imageid DESC';
      $navnextsql     = 'imageid > ' . $image->imageid . ' ORDER BY imageid ASC';
    break;

    case 'Newest First':
      $order = 'imageid DESC';
      $navprevioussql = 'imageid > ' . $image->imageid . ' ORDER BY imageid ASC';
      $navnextsql     = 'imageid < ' . $image->imageid . ' ORDER BY imageid DESC';
    break;
  }

 if (isset($_REQUEST['w3_imageid']))
 {
 	 // prev/next navigation
 	 
  	 if ($w3navLinksValue == 0) // text link mode navigation
  	 { 
  	    if($previousimage = $wpdb->get_row("SELECT imageid, filename, title FROM $w3images_images_table WHERE sectionid = '$sectionid' AND activated = 1 AND $navprevioussql LIMIT 1"))
        {
           $previousimagelink = '<h4><a href="'.$PHP_SELF.'/wp-w3images.php?w3_sectionid=' . $image->sectionid . '&w3_imageid=' . $previousimage->imageid . '">'.W3NEXT.'</a></h4>';
         }
    
        if($nextimage = $wpdb->get_row("SELECT imageid, filename FROM $w3images_images_table WHERE sectionid = $sectionid AND activated = 1 AND $navnextsql LIMIT 1"))
        {
          $nextimagelink = '<h4><a href="'.$PHP_SELF.'/wp-w3images.php?w3_sectionid=' . $image->sectionid . '&w3_imageid=' . $nextimage->imageid . '">'.W3PREVIOUS.'</a></h4>';
        }
        
     } else 
            { // thumbnail link mode navigation
              
              if($previousimage = $wpdb->get_row("SELECT imageid, filename, title FROM $w3images_images_table WHERE sectionid = '$sectionid' AND activated = 1 AND $navprevioussql LIMIT 1"))
               {
                 $previousimagelink = '<div style="float:left;"><a href="'.$PHP_SELF.'/wp-w3images.php?w3_sectionid=' . $image->sectionid . '&amp;w3_imageid=' . $previousimage->imageid . '"><img src="./wp-content/plugins/w3images/images/tb_'.$previousimage->filename.'" alt="'.$previousimage->title.'" /></a><br /><div style="font-size:xx-small;text-align:center;">'.W3NEXT.'</div></div>';
                }
    
               if($nextimage = $wpdb->get_row("SELECT imageid, filename, title FROM $w3images_images_table WHERE sectionid = $sectionid AND activated = 1 AND $navnextsql LIMIT 1"))
               {
           	    $nextimagelink = '<div style="float:right;"><a href="'.$PHP_SELF.'/wp-w3images.php?w3_sectionid=' . $image->sectionid . '&amp;w3_imageid=' . $nextimage->imageid . '"><img src="./wp-content/plugins/w3images/images/tb_'.$nextimage->filename.'" alt="'.$nextimage->title.'" /></a><br /><div style="font-size:xx-small;text-align:center;">'.W3PREVIOUS.'</div></div>';
               }
  
             } // CLOSE // thumb mode
  
  // display inline gallery
    
      echo '<div class="w3gallerywrap"><div class="w3centertit">' . $image->title . '</div>';
  
    if($image->showauthor == 1)
    {
      echo '<div class="w3spacelements">'.W3AUTHOR.' ' . $image->author . '</div>';
    }

      echo '<table class="w3tablecont" cellpadding="0" cellspacing="0"><tbody><tr>
            <td colspan="2" style="text-align:center"><img src="' . $w3images_plugin_url . 'images/' . $image->filename . '" alt="" /></td>
          </tr>'; 
          if (strlen($image->description)){
         echo '<tr>
            <td colspan="2"><div class="w3idesc">'.nl2br($image->description, true) . '</div></td>
            </tr>';
          }
          echo '<tr>
            <td class="w3nav_l" style="text-align:left;">'  . w3se(isset($previousimagelink), $previousimagelink) . '</td>
            <td class="w3nav_r" style="text-align:right;">' . w3se(isset($nextimagelink),     $nextimagelink)     . '</td>
          </tr></tbody></table>';
          
     } else { // display gallery thumbnails
 	
         echo '<table class="w3tablecont" cellpadding="0" cellspacing="0"><tr><tbody>';
 	 
      	 $getimages = $wpdb->get_results("SELECT * FROM $w3images_images_table WHERE sectionid = '$sectionid' AND activated = 1 ORDER BY $order LIMIT $start, $limit");
     
         $rows   = sizeof($getimages);
         $i      = 0;
         $curcol = 0; // Initialize current column
     
         foreach ($getimages as $getimage) {
         $getimageid      = $getimage->imageid;
         $getsectionid    = $getimage->sectionid;
         $getactivated    = $getimage->activated;
         $getfilename     = $getimage->filename;
         $getallowsmilies = $getimage->allowsmilies;
         $getshowauthor   = $getimage->showauthor;
         $getauthor       = $getimage->author;
         $gettitle        = $getimage->title;
         $getdescription  = $getimage->description;
         $getheight       = $getimage->height;
         $getwidth        = $getimage->width;
     
      	  if($getheight == 0 OR empty($getheight)){
      	   $size = @GetImageSize($w3images_plugin_url . 'images/' . $getfilename);
           $sizew = $size[0];
           $sizeh = $size[1];
      	   } else {
      	 	           $sizew = $getwidth;
                     $sizeh = $getheight;
                 }
      
     // w3images slideshow mode
      
     	if($w3slideSet == 0) // shadowbox
      { 
         echo '<td style="padding: 0 5px 5px 5px;border:0px;"><a href="'. $w3images_plugin_url. 'images/'.$getfilename.'" title="'.$gettitle.' - '.$getdescription.'" rel="shadowbox[w3images]"><img src="' . $w3images_plugin_url . 'images/tb_'.$getfilename.'" alt="'.$gettitle.'" /></a></td>';
      }
      
      if($w3slideSet == 1) // simple js/html popup mode
      { 
         echo "<td style=\"padding: 0 5px 5px 5px;border:0px;\"><a href='' onclick=\"window.open('".$PHP_SELF."/wp-w3pup.php?w3_sectionid=".$sectionid."&amp;w3_imageid=".$getimageid."', '', 'width=" . ($sizew+100) . ",height=" . ($sizeh+300) . ",directories=no,location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,resizable=yes');return false\"><img src=\"" . $w3images_plugin_url . "images/tb_".$getfilename."\" alt=\"\" /></a></td>\r\n";
      }
      
      if($w3slideSet == 2) // slide images inline the same page
      { 
        echo '<td style="padding: 0 5px 5px 5px;border:0px;"><a href="' . $PHP_SELF . '/wp-w3images.php?w3_sectionid=' . $getsectionid . '&amp;w3_imageid=' . $getimageid . '"><img src="' . $w3images_plugin_url . 'images/tb_'.$getfilename.'" alt="'.$gettitle.'" /></a></td>';
      }
       
        $curcol++;
        
     if($curcol == $maxcol)
      {
        echo '</tr><tr>';
        $curcol = 0;
      }
    
  } // end foreach 
    
  echo '</tr></tbody></table>'; 

    if(($start > 0) || ($rows > $limit))
    {
      echo '<table class="w3tablecont" cellpadding="0" cellspacing="0"><tbody><tr>';

      if($start > 0)
      {
        echo '<td align="center"><a href="' .$PHP_SELF. '/wp-w3images.php?w3_sectionid=' . $getsectionid . '&amp;w3_start=' . ($start - $limit) . '">Previous images</a></td>';
      }

     if($rows > $limit)
      {
         $start += $limit;
         
        if ( $rows > $start ) {
            echo '<td align="center"><a href="' .$PHP_SELF. '/wp-w3images.php?w3_sectionid=' . $getsectionid . '&amp;w3_start=' . $start . '">More images</a></td>';
          }
      }

        echo '</tr></tbody></table></div><!-- / div w3gallerycont -->';
      
      }

   }

  return;
 
} // END function wp_w3_displayImages($sectionid, $start)


////////////////////////////////////////////////////////////////////////////////////////////////////
// END w3images function
/**
 * @package WordPress
 * w3images function - w3images plugin
 */
////////////////////////////////////////////////////////////////////////////////////////////////////

/**
* w3images dashboard widget
*/



function w3images_render_dashboard_widget()
{
    global $wpdb,$w3images_images_table,$refreshpage;
  
  if ( $n = $wpdb->get_var("SELECT count(*) FROM $w3images_images_table WHERE activated = '0'") )
  {
  	
  	echo '<form action="options-general.php?page=w3images/images-options.php" method="POST">
        
        <input type="hidden" name="action" value="w3DisplayImages" />
        <input type="hidden" name="sectionid" value="OfflineImages" />';
  
      if($n > 1){  echo "There are $n images to be validate&nbsp;&nbsp;"; }
      
             else { 
       	              echo "There is one image to be validate&nbsp;&nbsp;";
       	           }   
       	           
       	 echo "<input type=\"submit\" value=\"Validate Offline Images\" /></form>";
 
  } else {
  	       echo "There are no images to be validate.";
  	    }
  	   
  	   
 return;
}

/**
*  end w3images dashboard widget.
*/

function w3images_css(){
	// w3images header style
	global $w3images_plugin_url;
	echo "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".$w3images_plugin_url."includes/w3images.css\" />\n";
}

// adding style to the w3images gallery when needed

if (stristr($_SERVER['PHP_SELF'], "wp-w3images.php")){
	add_action('wp_head', 'w3images_css');
}

  add_action('admin_menu', 'w3images_add_manage_page');
  add_action('admin_menu', 'w3images_add_options_page');
  add_action('wp_dashboard_setup', 'w3images_dashboard_widget');

?>