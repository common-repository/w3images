<?php
/***********************************************************************************************/
/* w3images plugin for Wordpress                                                               */
/* Plugin URI: http://www.axew3.com/                                                           */
/* Plugin author: Alessio Nanni - alias axew3                                                  */
/* =========================================================================================== */

if (stristr(htmlentities($_SERVER['PHP_SELF']), "images-options.php")) {
	die ("<h3>You can't access this file directly.</h3>");
}

$PHP_SELF = get_home_url(); // get_home_url() // (WP 3.0+)

$w3images_images_table   = $table_prefix.'w3images';
$w3images_sections_table = $table_prefix.'w3images_sections';
$w3images_options_table  = $table_prefix.'w3images_options';
$w3images_plugin_path    = ABSPATH.'/wp-content/plugins/w3images/';
$w3images_plugin_url     = $PHP_SELF . '/wp-content/plugins/w3images/';

$refreshpage = $_SERVER['PHP_SELF'].'?page='.$_GET['page'];

// Check GD Support 

$getgdselected = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE title = 'Image Resizing'", ARRAY_A);

$gdselected = $getgdselected['value'];  // 2 = GD2
                                        // 1 = GD1
                                        // 0 = User will submit thumbnail

if($getgdselected)
{
  // clear array
  $gdsupport = array(1 => 0,
                     2 => 0);

  // check for gd1 support
  if(function_exists('imagecreatefromjpeg'))
  {
    $gdsupport[1] = 1;
  }

  // check for gd2 support
  if(function_exists('imagecreatetruecolor'))
  {
    $gdsupport[2] = 1;
  }

  // if gd is not supported
  if($gdsupport[$gdselected] == 1)
  {
    $gdenabled = 1;
  }
  else
  {
    $gderror = '<b>GD ' . $gdselected . ' is not supported on your sever.</b>';
  }
}

// function unhtmalspecialchars
// Php > 4.3.0 or 5+

function unhtmlspecialchars($string)
{
  $trans_table = get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);

  // some versions of PHP match single quotes to &#39;
  if($trans_table["'"] != '&#039;')
  {
    $trans_table["'"] = '&#039;';
  }

  return (strtr($string, array_flip($trans_table)));
}

// FUNCTION CLEAN FORM

function CleanFormValue($value)
{
  return htmlspecialchars(unhtmlspecialchars($value), ENT_QUOTES);
}

// FUNCTION PRINT SECTION

function w3PrintSection($sectionname)
{
  echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="15" height="30"></td>
          <td width="100%" valign="bottom" align="center">
            <div><b>'.$sectionname.'</b></div>
          </td>
          <td width="1" height="30">&nbsp;</td>
        </tr>
        <tr>
          <td width="15" height="4">&nbsp;</td>
          <td>&nbsp;</td>
          <td width="15" height="4">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3">

          <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="7">&nbsp;</td>
            <td>';
}


// FUNCTION END SECTION 

function w3EndSection()
{
  echo '    </td>
            <td width="7">&nbsp;</td>
          </tr>
          </table>

          </td>
        </tr>
        </table>

        <br /><br />';
}

// PAGE REDIRECT 

function PrintRedirect($gotopage, $timeout = 0)
{

  w3PrintSection('Update');
  echo '<table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td colspan="2">Updating...</td>
        </tr>
        <tr>
          <td width="70%">';

  echo '<a href="'.$gotopage.'" onclick="javascript:clearTimeout(timerID);">
        Settings Updated! Click here if your browser don\'t redirect you.</a>';

  echo '    </font>
          </td>
        </tr>
        </table>';

  echo '<script type="text/javascript">';
  if($timeout == 0)
  {
    echo 'window.location="'.$gotopage.'"';
  }
  else
  {
    echo 'timeout = '.($timeout*10).';

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
              window.location="'.$gotopage.'";
            }
          }

          Refresh();';
  }

  echo '</script>';

  w3EndSection();

  exit();
}

function w3UpdatePlugSettings($settings, $refreshpage)
{
  global $wpdb, $w3images_options_table, $refreshpage, $settings;
  $settings = $_POST['settings'];
  
  while(list($key,$val) = each($settings))
  {
  	
    $wpdb->query("UPDATE $w3images_options_table SET value='$val' WHERE settingid='$key'");
  }

  PrintRedirect($refreshpage, 1);
}


function w3_display_sect_ids(){
	
   global $wpdb, $w3images_images_table, $w3images_sections_table;

  $getsubsections = $wpdb->get_results("SELECT sectionid, name FROM $w3images_sections_table");

echo '<hr /><h4>Info for random thumbnails function on your pages.<br /><span style="color:#FF0000">Available Images Sections ID:</span></h4>';
echo '<div style="background-color:#F1F2F3;padding:8px;">';
  foreach($getsubsections as $subsection)
  {
  	 
  	echo '<strong><i>'.$subsection->name . '</i> = ID ' . $subsection->sectionid . '</strong><br />';
  
     
      }
      
   echo '</div><hr />';
 }
      

function w3_PrintPluginSettings($refreshpage)
{
  global $wpdb, $w3images_options_table, $refreshpage;

$pluginsettings = $wpdb->get_results("SELECT * FROM $w3images_options_table", ARRAY_A);

  echo '<div class="wrap"><h2>w3images settings options</h2>';

  echo '<form method="post" action="'.$refreshpage.'&amp;action=w3updateplugin">

        <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <input type="hidden" name="refreshpage" value="'.$refreshpage.'" />';


foreach ($pluginsettings as $settings) {

    if(strlen($settings['title']))
    {
    	
      echo '<tr>
            <td width="70%" style="padding:20px 0;"><b style="font-size:110%">'. $settings['title'] . '</b><br /><strong>' . $settings['description'].'</strong></td>
            <td>';
    }

    if($settings['input']=="text")
    {
    	
      echo '<input type="text" size="40" name="settings['.$settings[settingid].']" value="'.htmlspecialchars($settings['value']).'">';
    }
    elseif($settings['input']=="yesno")
    {
    	
      echo "Yes<input type=\"radio\" name=\"settings[$settings[settingid]]\"  ".w3se($settings['value']==1,"checked","")." value=\"1\"> No <input type=\"radio\" name=\"settings[$settings[settingid]]\" ".w3se($settings['value']==0,"checked","")." value=\"0\">";
    }
    elseif($settings['input']=="textarea")
     {
     echo "<textarea name=\"settings[$settings[settingid]]\" rows=\"4\" cols=\"30\">".$settings['value']."</textarea>";
     }
    else
    {
     eval("echo \"$settings[input]\";");
    }

    echo '<br /><br /></td></tr>';
  }


  echo '<tr><td bgcolor="#FCFCFC" colspan="2" align="center">
           <input type="hidden" name="action" value="w3updateplugin" />
           <input type="submit" value="Save Settings" />
          </td>
        </tr>
        </table>
        </form>';
        
        
        
  w3EndSection();
  
echo '<div style="padding:20px;">'.w3_display_sect_ids().'</div>';

  echo '</div>';

} 

function DeleteSection($sectionid)
{

  global $wpdb, $w3images_sections_table, $refreshpage;
  
     if($sectionid==1){ 
  	
  	    echo'<h4>You can not delete the root images section!</h4>';
        PrintRedirect($refreshpage, 1);
    
         exit();
      }
  
  $wpdb->query("DELETE FROM $w3images_sections_table WHERE sectionid = '$sectionid' LIMIT 1");

  PrintRedirect($refreshpage, 1);
}

function DeleteSectionImages($sectionid)
{
  global $wpdb, $w3images_images_table, $refreshpage;
  $sectionid = $_POST['sectionid'];
  
  $wpdb->query("DELETE FROM $w3images_images_table WHERE sectionid = '$sectionid'");

  PrintRedirect($refreshpage, 1);
}

function w3InsertSection($parentid, $activated, $name, $description, $sorting)
{
  global $wpdb, $w3images_sections_table, $refreshpage;
  $sectionid   = '';
  $parentid    = $_POST['parentid'];
  $activated   = $_POST['activated'];
  $name        = $_POST['name'];
  $description = $_POST['description'];
  $sorting     = $_POST['sorting'];
  
    if ($activated == '')
  {
    $activated = intval($activated);
  }
 
  $wpdb->query("INSERT INTO $w3images_sections_table (parentid, activated, name, description, sorting)
              VALUES('$parentid', '$activated', '$name', '$description', '$sorting')");

  PrintRedirect($refreshpage, 1);
}

function w3UpdateSection($sectionid, $parentid, $activated, $name, $description, $sorting)
{
  global $wpdb, $w3images_sections_table, $refreshpage;
  
  $sectionid   = $_POST['sectionid'];
  $parentid    = $_POST['parentid'];
  $activated   = $_POST['activated'];
  $name        = $_POST['name'];
  $description = $_POST['description'];
  $sorting     = $_POST['sorting'];
  
      if($sectionid == 1)
    {
       $parentid = 0;
    }
  
      if ($activated == '')
    {
      $activated = intval($activated);
    }
  
  if($_POST['deletesection'] == 1)
  {
    DeleteSection($sectionid);
  }

  if($_POST['deletesectionimages'] == 1)
  {
    DeleteSectionImages($sectionid);
  }

  $wpdb->query("UPDATE $w3images_sections_table SET parentid = '$parentid',
                                                 activated   = '$activated',
                                                 name        = '$name',
                                                 description = '$description',
                                                 sorting     = '$sorting'
                                           WHERE sectionid   = '$sectionid'");

  PrintRedirect($refreshpage, 1);
}

function w3DisplaySectionForm($sectionid)
{
  global $wpdb, $w3images_sections_table, $refreshpage;
  $sectionid = $_POST['sectionid'];

  if(isset($sectionid))
  {
    $section = $wpdb->get_row("SELECT * FROM $w3images_sections_table WHERE sectionid = '$sectionid'", ARRAY_A);

    w3PrintSection('Edit Section');
  }
  else
  {
    $section = array("parentid"    => '1',
                     "activated"   => 1,
                     "name"        => '',
                     "description" => '',
                     "sorting"     => 'Newest First' );

  w3PrintSection('Create New Section');
  }

  echo '<form method="post" action="'.$refreshpage.'">
        <input type="hidden" name="sectionid"  value="'.$sectionid.'" />

        <table width="100%" border="0" cellpadding="5" cellspacing="0">';

  if(isset($sectionid))
  {
    echo '<tr>
            <td width="15%" valign="top"><b>Delete Section:</b></td>
            <td width="75%" valign="top">';

    if($sectionid == 1)
      echo 'The Root Section can not be deleted.';
    else
      echo '<input type="checkbox" name="deletesection" value="1"> Delete this section?<br/>';

    echo '  </td>
          </tr>';

    echo '<tr>
            <td width="15%" valign="top"><b>Delete Images:</b></td>
            <td width="75%" valign="top">
              <input type="checkbox" name="deletesectionimages" value="1"> Delete all of the images in this section?
            </td>
          </tr>';
  }

  echo '<tr>
          <td width="15%"><b>Sub Section Of:</b></td>
          <td width="75%" valign="top">';

  if($sectionid == 1)
  {
    echo 'The <b>Root Section</b> is the <b>Parent</b> of all sections. You can rename it. Can\'t be a subsection and can\'t be deleted.';
  }
  else
  {
    $getsections = $wpdb->get_results("SELECT sectionid, name FROM $w3images_sections_table ORDER BY name", ARRAY_A);
    echo '<select name="parentid">';

   foreach ($getsections as $sections) {

      if($sectionid != $sections['sectionid'])
        echo "<option value=\"$sections[sectionid]\" ".w3se($section[parentid] == $sections[sectionid],"selected","").">$sections[name]</option>";
    }
    echo '</select>';
  }

  echo '  </td>
        </tr>
        <tr>
          <td width="15%" valign="top"><b>Sort Images By:</b></td>
          <td width="75%" valign="top">
                          <select name="sorting">
                          <option '.w3se($section[sorting] == "Newest First",       "selected", "") .'>Newest First</option>
              <option '.w3se($section[sorting] == "Oldest First",       "selected", "") .'>Oldest First</option>
                          <option '.w3se($section[sorting] == "Alphabetically A-Z", "selected", "") .'>Alphabetically A-Z</option>
                          <option '.w3se($section[sorting] == "Alphabetically Z-A", "selected", "") .'>Alphabetically Z-A</option>
                          <option '.w3se($section[sorting] == "Author Name A-Z",    "selected", "") .'>Author Name A-Z</option>
                          <option '.w3se($section[sorting] == "Author Name Z-A",    "selected", "") .'>Author Name Z-A</option>
                        </select>
          </td>
        </tr>
        <tr>
          <td width="15%"><b>Section Name:</b></td>
          <td width="75%" valign="top">
            <input type="text" name="name" value="'.CleanFormValue($section['name']).'" />
          </td>
        </tr>';

  if($sectionid != 1)
  {
    echo '<tr>
            <td width="15%" valign="top"><b>Description:</b></td>
            <td width="75%" valign="top">
              <textarea name="description" cols="54" rows="5">'.$section['description'].'</textarea>
            </td>
          </tr>
          <tr>
            <td width="15%" valign="top"><b>Options:</b></td>
            <td width="75%" valign="top">
              <input type="checkbox" name="activated" value="1" '.w3se($section['activated'] == 1, "CHECKED", "").'><b>Active:</b> Do you want this section online?
            </td>
          </tr>';
  }

  echo '<tr>
          <td colspan="2" align="center">';

  if($sectionid)
  {
    echo '<input type="hidden" name="action" value="w3updatesection" />
          <input type="submit" value="Update Section" />';  }
  else
  {
    echo '<input type="hidden" name="action" value="w3insertsection" />
          <input type="submit" value="Create Section" />';
  }

  echo '  </td>
        </tr>
        </table>
        </form>';
  w3EndSection();
}


function w3DeleteImage($imageid)
{
  global $wpdb, $w3images_images_table, $refreshpage, $w3images_plugin_path;
  
  $getfilename = $wpdb->get_row("SELECT filename FROM $w3images_images_table WHERE imageid = '$imageid'");

  $filename    = $getfilename->filename;

  $image       = $w3images_plugin_path . '/images/'    . $filename;
  $thumbnail   = $w3images_plugin_path . '/images/tb_' . $filename;

  @unlink($image);
  @unlink($thumbnail);
  $wpdb->query("DELETE FROM $w3images_images_table WHERE imageid = '$imageid'");
  
  PrintRedirect($refreshpage, 1);
}

function w3AllUp($imageidsapprove=0,$imageids=0)
{
  global $wpdb, $w3images_images_table, $refreshpage, $w3images_plugin_path;

    // approve images if there are
  if (count($imageidsapprove) > 0){
  
     for($i = 0; $i < count($imageidsapprove); $i++)
     {
    	 // remove all chars except int numbers (imageid) from passed values to get working the query
    	 // ?? The: someone can help?
    	 // don't know why the $_POST var on w3DeleteImages() and so passed here to w3AllUp(), contain values as do. No time to investigate
        $imageidsapprove[$i] = preg_replace("/[^0-9]/", "", $imageidsapprove[$i]);
        $wpdb->query("UPDATE $w3images_images_table SET activated = 1 WHERE imageid = $imageidsapprove[$i]");

      }
   }

   return;

} 

function w3UpdateImage($deleteimage, $imageid, $sectionid, $activated, $showauthor, $author, $title, $description)
{
  global $wpdb, $w3images_images_table, $refreshpage;

  $deleteimage   = $_POST['deleteimage'];
  $imageid       = $_POST['imageid'];
  $sectionid     = $_POST['sectionid'];
  $activated     = $_POST['activated'];
  $showauthor    = $_POST['showauthor'];
  $author        = $_POST['author'];
  $title         = $_POST['title'];
  $description   = $_POST['description'];
  
    if ($activated == '')
  {
    $activated = intval($activated);
  }
   if ($showauthor == '')
  {
    $showauthor = intval($showauthor);
  }

  if($deleteimage == 1)
  {
  	
    w3DeleteImage($imageid);
  }

  if(strlen($title) == 0)
  {
    $title = '(untitled)';
  }

  $wpdb->query("UPDATE $w3images_images_table SET sectionid     = '$sectionid',
                                    activated     = '$activated',
                                    showauthor    = '$showauthor',
                                    author        = '$author',
                                    title         = '$title',
                                    description   = '$description'
                              WHERE imageid       = '$imageid'");

  PrintRedirect($refreshpage, 1);
} 

function w3_1_InsertImage()
{
   global $wpdb, $w3images_images_table, $w3images_options_table, $refreshpage, $w3images_plugin_path, $gdenabled, $gdselected;

  $image     = '';
  $thumbnail = '';

  $image         = $_FILES['image'];
  $thumbnail     = $_FILES['thumbnail'];
  $sectionid     = $_POST['sectionid'];
  $activated     = $_POST['activated'];
  $showauthor    = $_POST['showauthor'];
  $author        = $_POST['author'];
  $title         = $_POST['title'];
  $description   = $_POST['description'];
  
  if ($activated == '')
  {
    $activated = intval($activated);
  }
  if ($showauthor == '')
  {
    $showauthor = intval($showauthor);
  }
 
  $valid_image_types = array('image/pjpeg',
                             'image/jpeg',
                             'image/gif',
                             'image/bmp',
                             'image/x-png',
                             'image/png');
                             
  if($image['size'] == 0)
  {
    $error = 'Please select an image.';
  }

  if(!in_array($image['type'], $valid_image_types))
  {
    $error = 'Invalid image type.';
  }

  if(isset($_FILES['thumbnail']) AND !in_array($thumbnail['type'], $valid_image_types))
  {
    $error = 'Invalid thumbnail type.';
  }

  if(isset($error))
  {
    echo ' <h2>Uploading Errors:</h2>' . $error;
    PrintRedirect($refreshpage, 3);
    return;
  }
  
  
  // initialization
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
                        
  $wpdb->query("INSERT INTO $w3images_images_table (sectionid, activated, filename, showauthor, author, title, description)
              VALUES('$sectionid', '$activated', 0, '$showauthor', '$author', '$title', '$description') ");

  $imgidv    = $wpdb->get_row("SELECT MAX(imageid) AS imageid FROM $w3images_images_table");
  $imageid   = $imgidv->imageid;
  $filetype  = $image['type'];
  $extention = $known_photo_types[$filetype];
  $filename  = $imageid . '.' . $extention;

  $wpdb->query("UPDATE $w3images_images_table SET filename  = '$filename' WHERE imageid = '$imageid'");

  copy($image['tmp_name'], $imagesdir."/".$filename);



  if($gdenabled)
  {

     if ($_POST['w3thumbwidth'] > 2  && $_POST['w3thumbheight'] > 2){
     
	        $maxwidth  = $_POST['w3thumbwidth'];
	        $maxheight = $_POST['w3thumbheight'];

      }

     else {
   	     
          $getwidth = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE title = 'Max Thumbnail Width'", ARRAY_A);
          $maxwidth = $getwidth['value'];
          
          $getheight = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE title = 'Max Thumbnail Height'", ARRAY_A);
          $maxheight = $getheight['value'];

     }
	
	
	
    $size = GetImageSize($imagesdir . '/' . $filename ); 

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

    $function_suffix   = $gd_function_suffix[$filetype];
    $function_to_read  = "ImageCreateFrom".$function_suffix;
    $function_to_write = "Image".$function_suffix;

    $source_handle = $function_to_read ( $imagesdir."/".$filename );

    if($gdselected == 2)
    {
      $destination_handle = ImageCreateTrueColor($thumbnail_width, $thumbnail_height );

      ImageCopyResampled($destination_handle, $source_handle, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $size[0], $size[1] );
    }
    else if($gdselected == 1)
    {

      $destination_handle = ImageCreate($thumbnail_width, $thumbnail_height );

      ImageCopyResized($destination_handle, $source_handle, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $size[0], $size[1] );
    }


    $function_to_write( $destination_handle, $imagesdir."/tb_".$filename );
    ImageDestroy($destination_handle );

  }
   else {

    $thumbtype = $thumbnail['type'];
    $extention = $known_photo_types[$thumbtype];
    $thumbname = 'tb_' . $imageid . '.' . $extention;

    copy($thumbnail['tmp_name'], $imagesdir . '/' . $thumbname);
  }

  PrintRedirect($refreshpage, 1);
}


    function w3DisplayImageForm($imageid)
{
   global $wpdb, $refreshpage, $w3images_images_table, $w3images_plugin_url, $gdenabled;
  $imageid = $_GET['imageid'];
  
  if(is_numeric($imageid))
  {
    $image = $wpdb->get_row("SELECT * FROM $w3images_images_table WHERE imageid = '$imageid'", ARRAY_A);
    echo '<div class="wrap"><h3>Edit Image</h3>';
  }
  else if(isset($_POST['SubmitImage']))
  {
    $image = array("sectionid"     => $_POST['sectionid'],
                   "author"        => $userinfo['username'],
                   "title"         => $_POST['title'],
                   "description"   => $_POST['description'],
                   "activated"     => $_POST['activated'],
                   "showauthor"    => $_POST['showauthor']);

  }
  else
  {
    $image = array("sectionid"     => '1',
                   "author"        => $userinfo['username'],
                   "title"         => "",
                   "description"   => "",
                   "activated"     => 1,
                   "showauthor"    => 1);
                   
 echo '<div class="wrap"><h3>Insert new Image</h3>';
 
  }

  echo '<form enctype="multipart/form-data" action="'.$refreshpage.'" method="post" name="upload_form">
        <input type="hidden" name="imageid"  value="'.$imageid.'" />

        <table width="100%" border="0" cellpadding="5" cellspacing="10">';

  if(isset($imageid))
  {
    echo '<tr>
            <td width="15%"><b>Delete Image:</b></td>
            <td width="75%" valign="top">
              <input type="checkbox" name="deleteimage"  value="1"> Delete this image?
            </td>
          </tr>';
  }

  echo '<tr>
          <td width="15%"><b>Image:</b></td>
          <td width="75%" valign="top">';

  if(isset($imageid))
  { 
          echo '<a href="'.$w3images_plugin_url.'images/'.$image['filename'].'" target="_blank"><img src="'.$w3images_plugin_url.'images/tb_'.$image['filename'].'" /></a>'; 
          echo "<br /><br />relative url for picture's thumbnail: ".$w3images_plugin_url."images/<strong>tb_".$image['filename']."</strong>";
          echo "<br /><br />relative url for full picture: ".$w3images_plugin_url."images/<strong>".$image['filename']."</strong><br /><br />";
       
  }
  else
  {
    echo '<input name="image" type="file" />';
  }

  echo '  </td>
        </tr><tr>
          <td width="15%" valign="top"><b>Options:</b></td>
          <td width="75%" valign="top">
            <input type="checkbox" name="activated"      value="1" '.w3se($image['activated']     == 1, "CHECKED", "").'> <b>Publish:</b> Activate this image?<br />
            <input type="checkbox" name="showauthor"     value="1" '.w3se($image['showauthor']    == 1, "CHECKED", "").'> <b>Display Author Name:</b> Would you like display author\'s name?<br />
           <br /><br /></td>
        </tr>';

  if(!isset($imageid))
  {
    echo '<tr>
            <td width="15%"><b>Thumbnail:</b></td>
            <td width="75%" valign="top">';

    if($gdenabled){
        echo 'Thumbnail will be created automatically';
   } else {
            echo '<input name="thumbnail" type="file" />';
        }
    echo '  </td>
          </tr>';
          
          echo '<tr>
          <td width="15%" valign="middle"><b>Thumbnail dimension in pixel</b> (default value will be used if not set):</td>
          <td width="75%" valign="middle">
            <input type="text" name="w3thumbwidth" value="Thumbnail width" onblur="if(this.value==\'\')this.value=\'Thumbnail width\';" onfocus="if(this.value==\'Thumbnail width\')this.value=\'\';" />
            <input type="text" name="w3thumbheight" value="Thumbnail height" onblur="if(this.value==\'\')this.value=\'Thumbnail height\';" onfocus="if(this.value==\'Thumbnail height\')this.value=\'\';" />
          </td>
        </tr>';
  }

  echo '<tr>
          <td width="15%"><b>Section:</b></td>
          <td width="75%" valign="top">';

 w3PrintSectionSelection($image['sectionid']);

  echo '  </td>
        </tr>
        <tr>
          <td width="15%"><b>Author:</b></td>
          <td width="75%" valign="top">
            <input type="text" name="author" value="'.CleanFormValue($image['author']).'" />
          </td>
        </tr>
        <tr>
          <td width="15%"><b>Title:</b></td>
          <td width="75%" valign="top">
            <input type="text" name="title" value="'.CleanFormValue($image['title']).'" />
          </td>
        </tr>
        <tr>
          <td width="15%" valign="top"><b>Description:</b></td>
          <td width="75%" valign="top">
            <textarea name="description" cols="50" rows="10">'.CleanFormValue($image['description']).'</textarea>
          </td>
        </tr>
        
        <tr>
          <td colspan="2" align="center">';

  if($imageid)
  {
    echo '<input type="hidden" name="action" value="w3updateimage" />
          <input type="submit" value="Update Image" />';  }
  else
  {
    echo '<input type="hidden" name="action" value="w3_1_insertimage" />
          <input type="submit" name="SubmitImage" value="Submit Image" />';
  }

  echo '  </td>
        </tr>
        </table>

        </form>';
  w3EndSection();
  
  echo '</div>';
  
}

function w3DisplaySettings()
{
  global $refreshpage;

  w3_PrintPluginSettings($refreshpage);
}


function w3DeleteImages()
{
  global $wpdb, $refreshpage, $w3images_images_table, $w3images_plugin_path;

  $imageids = $_POST['imageids'];

 if(count($_POST['imageids']) > 0)
  {
     for($i = 0; $i < count($imageids); $i++)
     {
  	
      $getfilename = $wpdb->get_row("SELECT filename FROM $w3images_images_table WHERE imageid = '".$imageids[$i]."'");
     
      $image     = $w3images_plugin_path . 'images/'    . $getfilename->filename;
      $thumbnail = $w3images_plugin_path . 'images/tb_' . $getfilename->filename;

      @unlink($image);
      @unlink($thumbnail);

      $wpdb->query("DELETE FROM $w3images_images_table WHERE imageid = '$imageids[$i]'");
    
     }
  }

// As the w3DeleteImages() is called when updating last inserted images
// that are listed on the main w3images option page, we add also the approve checkbox
// here to activate images if necessary, and not only to delete
// See function w3DisplayImages($viewtype) the "action" of the "form"

  if(count($_POST['imageidsapprove']) > 0)
  {
  	$app = $_POST['imageidsapprove'];
  	// call w3AllUp to approve images
  	w3AllUp($app,0);
 
  }
  

  PrintRedirect($refreshpage, 3);
  
}

function w3DisplayImages($viewtype)
{
global $wpdb, $w3images_images_table, $w3images_sections_table, $refreshpage, $w3images_plugin_url;
	
	$sectionid = $_POST['sectionid'];
	
	if ($sectionid == 'OfflineImages')
	{
	$viewtype = 'OfflineImages';
	}
	
  switch($viewtype)
  {
  	// latest images list on w3images main options page
    case 'Latest Images':   
      $getimages = $wpdb->get_results("SELECT * FROM $w3images_images_table ORDER BY imageid DESC LIMIT 0,20");
    break;

    case 'OfflineImages':  
      $getimages = $wpdb->get_results("SELECT * FROM $w3images_images_table WHERE activated = 0 ORDER BY imageid DESC");
      $viewtype  = 'Offline Images';
    break;

    default:
      $getimages = $wpdb->get_results("SELECT * FROM $w3images_images_table WHERE sectionid = '$sectionid' ORDER BY imageid DESC");
      $viewtype  = 'Images';
  }

  echo '<div class="wrap"><h3>'.$viewtype.'</h3>';

  echo '<form action="'.$refreshpage.'" method="POST">
        <input type="hidden" name="action" value="w3deleteimages" />
        
        
        <table width="100%" border="0" cellpadding="5" cellspacing="8">
        <tr>
          <td>Image</td>
          <td>Filename</td>
          <td>Section</td>
          <td>Author</td>
          <td>Status</td>
          <td>Delete</td>
          <td>Approve</td>
        </tr>';

foreach ($getimages as $imageitem) {
	
	$section = $wpdb->get_row("SELECT name FROM $w3images_sections_table WHERE sectionid = '$imageitem->sectionid'");

    echo '<tr>
            <td>&nbsp;<a href="'.$refreshpage.'&action=w3displayimageform&imageid='.$imageitem->imageid.'"><img src="'.$w3images_plugin_url.'images/tb_' . $imageitem->filename .'" alt="Click to modify" /></a></td>
            <td>&nbsp;'.$imageitem->filename.'</td>
            <td>&nbsp;'.$section->name.'</td>
            <td>&nbsp;'.$imageitem->author.'</td>
            <td>&nbsp;'.w3se($imageitem->activated=="1","<div style=\"color:green\">Online</div>","<div style=\"color:red\"><b>Offline</b></div>").'</td>
            <td>&nbsp;<input type="checkbox" name="imageids[]" value="'.$imageitem->imageid.'" /></td>
           <td>&nbsp;'.w3se($imageitem->activated=="1","&nbsp;","<input type=\"checkbox\" name=\"imageidsapprove[]\" value=\"'.$imageitem->imageid.'\" />").'</td>
          </tr>';
}

  echo '<tr>
          <td bgcolor="#FCFCFC" colspan="5" align="right" style="padding-right: 20px;">
           <input type="submit" value="Update Images" />
          </td>
        </tr>
        </table>

        </form>';

  w3EndSection();
  
  echo '</div>';

}

function w3PrintSectionSelection($sectionid)
{
 global $wpdb, $w3images_sections_table;

  $getsections = $wpdb->get_results("SELECT sectionid, name FROM $w3images_sections_table ORDER BY name", OBJECT);

  echo '<select name="sectionid">';

foreach ($getsections as $section) {
	
    echo "<option value=\"$section->sectionid\" ".w3se($sectionid == $section->sectionid,"selected","").">$section->name".w3se($section->sectionid == 1," (root)","")."</option>";
  }
  echo '</select>';
}

function w3PrintSectionSelectionEx()
{
  global $wpdb, $w3images_images_table, $w3images_sections_table;

  $getsections = $wpdb->get_results("SELECT sectionid, name FROM $w3images_sections_table");

  echo '<select name="sectionid">';

 foreach ($getsections as $section) {

   $getimagecount = $wpdb->get_results("SELECT imageid FROM $w3images_images_table WHERE activated = 1 AND sectionid = '$section->sectionid'");
   $imagecount    = sizeof($getimagecount);
    
   echo '<option value="' . $section->sectionid . '">'.$section->name.' ('.$imagecount.')</option>';
  }

  $getofflineimages = $wpdb->get_results("SELECT imageid FROM $w3images_images_table WHERE activated = 0");
  $offline_images    = sizeof($getofflineimages);

  echo '<option value="OfflineImages">Offline Images (' . $offline_images . ')</option>
        </select>';
 
}


function w3BatchUpload()
{
    global $wpdb, $w3images_images_table, $w3images_options_table, $refreshpage, $gdselected, $w3images_plugin_path, $w3images_plugin_url;

  $sectionid     = $_POST['sectionid'];
  $activated     = $_POST['activated'];
  $showauthor    = $_POST['showauthor'];
  $author        = $_POST['author'];
  $description   = $_POST['description'];
  $uploadlimit   = $_POST['uploadlimit'];

  // error checking
  if(!is_numeric($uploadlimit)){
    $uploadlimit = 10; // default
  }

  // init vars
  $imagesmoved = 0;
  $errors      = '';

  $uploaddir = $w3images_plugin_path . 'upload/';
  $imagedir  = $w3images_plugin_path . 'images/';

  $getwidth  = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE title = 'Max Thumbnail Width'");
  $maxwidth  = $getwidth->value;

  $getheight = $wpdb->get_row("SELECT value FROM $w3images_options_table WHERE title = 'Max Thumbnail Height'");
  $maxheight = $getheight->value;

  $known_photo_types = array(
                         'image/pjpeg' => 'jpg',
                         'image/jpeg'  => 'jpg',
                         'image/gif'   => 'gif',
                         'image/bmp'   => 'bmp',
                         'image/x-png' => 'png',
                         'image/png'   => 'png'
                       );

  // GD Function List
  $gd_function_suffix = array(
                          'image/pjpeg' => 'JPEG',
                          'image/jpeg'  => 'JPEG',
                          'image/gif'   => 'GIF',
                          'image/bmp'   => 'WBMP',
                          'image/x-png' => 'PNG',
                          'image/png'   => 'PNG'
                        );

  $d = dir($uploaddir);

  for($i = 0; ($entry = $d->read()) && ($i < $uploadlimit); $i++)
  {

    if(substr($entry, -4) == 'jpeg'){
      $title = substr($entry, 0, -5);
      $title = preg_replace("/[^\.a-zA-Z0-9_-]/", "", $title);
    } else {
            $title = substr($entry, 0, -4);
            $title = preg_replace("/[^\.a-zA-Z0-9_-]/", "", $title);
         }
    
    
    if( ($size = @GetImageSize($uploaddir . $entry)))
    {
    	
      switch($size[2])
      {
        case '1':
          $filetype = 'image/gif';
        break;

        case '2':
          $filetype = 'image/jpeg';
        break;

        case '3':
          $filetype = 'image/x-png';
        break;

        case '4':
          $filetype = 'image/bmp';
        break;
      }

      $imgidv  = $wpdb->get_row("SELECT MAX(imageid) AS imageid FROM $w3images_images_table");
      $imageid = $imgidv->imageid + 1; 
  
      $extention = $known_photo_types[$filetype];
      $filename  = $imageid . '.' . $extention;
     
      $wpdb->query("INSERT INTO $w3images_images_table(imageid, sectionid, activated, filename, showauthor, author, title, description, height, width)
                  VALUES('$imageid', '$sectionid', '$activated', '$filename', '$showauthor', '$author', '$title', '$description', '$size[1]', '$size[0]')");
 
      $extention = $known_photo_types[$filetype];
      $filename  = $imageid . '.' . $extention;

      $wpdb->query("UPDATE $w3images_images_table SET filename = '$filename' WHERE imageid = '$imageid' ");

      if(rename($uploaddir . $entry, $imagedir . $filename))
      {
        if($size[0] > $size[1])
        {
          $thumbnail_width = $maxwidth;
          $thumbnail_height = (int)($maxwidth * $size[1] / $size[0]);
        }
        else
        {
          $thumbnail_height = $maxheight;
          $thumbnail_width = (int)($maxheight * $size[0] / $size[1]);
        }

        $function_suffix   = $gd_function_suffix[$filetype];
        $function_to_read  = "ImageCreateFrom".$function_suffix;
        $function_to_write = "Image".$function_suffix;

        $source_handle = $function_to_read($imagedir . $filename);

        if($gdselected == 2)
        {
          $destination_handle = ImageCreateTrueColor($thumbnail_width, $thumbnail_height );

          ImageCopyResampled($destination_handle, $source_handle, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $size[0], $size[1] );
        }
        else if($gdselected == 1)
        {
          $destination_handle = ImageCreate($thumbnail_width, $thumbnail_height );

          ImageCopyResized($destination_handle, $source_handle, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $size[0], $size[1] );
        }

        $function_to_write($destination_handle, $imagedir . 'tb_' . $filename);
        ImageDestroy($destination_handle );

        $imagesmoved++;
      }
      else
      {
        $wpdb->query("DELETE FROM $w3images_images_table WHERE imageid = $imageid");
        $errors .= $entry . 'An error occurs, image was not copied successfully.<br />';
      }
    }
    else
    {
      $i--;  
    }  

  } 

  echo'<h3 style="padding-left:30px;">Batch Upload Results</h3>';
  echo '<table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td align="center">
            Total of ' . $imagesmoved . ' images were uploaded successfully.<br /><br />

            <b>'.$errors.'</b><br /><br />

            <a href="'.$refreshpage.'">Return to the Image Gallery.</a>
          </td>
        </tr>
        </table>';
  w3EndSection();

}

function w3BatchUploadForm()
{
 global $refreshpage, $gdenabled, $w3images_plugin_url;

 echo '<div class="wrap">';

  if(!$gdenabled)
  {
    echo '<h3>Please enable GD</h3>';
    echo '<table width="100%" border="0" cellpadding="5" cellspacing="0">
          <tr>
            <td align="center">
              <b>Batch Uploading requires GD enabled on your server and selected
              within the Image Gallery\'s settings page.</b>
            </td>
          </tr>
          </table>';
    w3EndSection();

    return(0);
  }

  echo '<h3>Batch Uploading</h3>';
  echo '<form action="'.$refreshpage.'" method="post">
        <input type="hidden" name="action" value="w3batchupload" />

        <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td width="15%" valign="top"><b>Instructions:</b></td>
          <td width="75%" valign="top">
            Upload all of your images via ftp to this folder: <b><i>wp-content/plugins/w3images/upload/</i></b><br />
          </td>
        </tr>
        <tr>
          <td width="15%"><b>Number of images to upload:</b></td>
          <td width="75%" valign="top">
            <input type="text" name="uploadlimit" value="10" />
          </td>
        </tr>
        <tr>
          <td width="15%"><b>Section to upload images in:</b></td>
          <td width="75%" valign="top">';

  w3PrintSectionSelection($image['sectionid']);

  echo '  </td>
        </tr>
        <tr>
          <td width="15%"><b>Author of the images:</b></td>
          <td width="75%" valign="top">
            <input type="text" name="author" value="'.CleanFormValue($image['author']).'" />
          </td>
        </tr>
        <tr>
          <td width="15%" valign="top"><b>Options:</b></td>
          <td width="75%" valign="top">
            <input type="checkbox" name="activated"      value="1" "CHECKED"><b>Publish:</b> Are you ready to publish all of the images?<br />
            <input type="checkbox" name="showauthor"     value="1" "CHECKED"><b>Display Author Name:</b> Would you like the author\'s name (of the submission) under the title of the images?<br />
            </td>
        </tr>
        <tr>
          <td colspan="2" align="center">
          <input type="submit" value="Start Uploading" />
          </td>
        </tr>
        </table>
        </form>';
  w3EndSection();
  echo '</div>';

}

function w3DisplayDefault()
{
  global $refreshpage;
  
  echo '<div class="wrap"><h2>w3images options</h2>';

  echo '<h3>Settings</h3>';
  echo '<table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr><td width="70%">View and change your image gallery settings:</td>
            <td style="padding-left: 40px;">
            <form method="post" action="'.$refreshpage.'">
            <input type="hidden" name="action" value="w3displaysettings" />
            <input type="submit" value="Display Settings" />
            </form>
            </td></tr>
        </table>
        <hr />';
  w3EndSection();

  echo '<h3>Images</h3>';
  echo '<table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr><td width="70%"><b>Add Image</b><br />Add a new image to your blog:</td>
            <td style="padding-left: 40px;">
            <form method="post" action="'.$refreshpage.'">
            <input type="hidden" name="action" value="w3displayimageform" />
            <input type="submit" value="New Image" />
            </form>
            </td></tr>
        <tr><td width="70%"><br /><br /><b>Batch Upload</b><br />Add many images all at once:</td>
            <td style="padding-left: 40px;"><br /><br />
            <form method="post" action="'.$refreshpage.'">
            <input type="hidden" name="action" value="w3batchuploadform" />
            <input type="submit" value="Batch Upload" />
            </form>
            </td></tr>
        <tr><td width="70%"><br /><br /><b>Manage Images</b><br />Browse through and edit your images:</td>
            <td style="padding-left: 40px;"><br /><br />
            <form method="post" action="'.$refreshpage.'">';

  w3PrintSectionSelectionEx();

  echo '    <input type="hidden" name="action" value="w3displayimages" />
            <input type="submit" value="View Images" />
            </form>
            </td></tr>
        </table>
        <hr />';
  w3EndSection();

  echo '<h3>Sections</h3>
        <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td width="70%"><b>Add Section</b><br />You can organize your images into many directories by creating new sections:<br />
          <i>The default section is root and it can not be deleted, but you can name it as you like.</i>
          </td>
            <td style="padding-left: 40px;">
            <form method="post" action="'.$refreshpage.'">
            <input type="hidden" name="action" value="w3displaysectionform" />
            <input type="submit" value="New Section" />
            </form>
            </td></tr>
        <tr><td width="70%"><br /><br /><b>Edit Section</b><br />Select and edit section here:</td>
            <td style="padding-left: 40px;"><br /><br />
            <form method="post" action="'.$refreshpage.'">';

       w3PrintSectionSelection('');

  echo '    <input type="hidden" name="action" value="w3displaysectionform" />
            <input type="submit" value="Edit Section" />
            </form>
            </td></tr>
        </table>
        <hr />';
  w3EndSection();


  w3DisplayImages('Latest Images');
  
  echo '</div>';
  
}
 
// switch functions

switch($action)
{
	case 'w3updateplugin':
	w3UpdatePlugSettings($settings, $refreshpage);
	break;
	
  case 'w3batchupload':
    w3BatchUpload();
  break;

  case 'w3batchuploadform':
    w3BatchUploadForm();
  break;

  case 'w3_1_insertimage':
    w3_1_InsertImage();
  break;

  case 'w3updateimage':
    w3UpdateImage($deleteimage, $imageid, $sectionid, $activated, $showauthor, $author, $title, $description);
  break;

  case 'w3deleteimages':
    w3DeleteImages();
  break;

  case 'w3deleteimage':
    w3DeleteImage($imageid);
  break;

  case 'w3insertsection':
    w3InsertSection($parentid, $activated, $name, $description, $sorting);
  break;

  case 'w3updatesection':
    w3UpdateSection($sectionid, $parentid, $activated, $name, $description, $sorting);
  break;

  case 'w3displayimageform':
    w3DisplayImageForm($imageid);
  break;

  case 'w3displaysectionform':
    w3DisplaySectionForm($sectionid);
  break;

  case 'w3displayimages':
    w3DisplayImages($sectionid);
  break;

  case 'w3displaysettings':
    w3DisplaySettings();
  break;

  default:
      // instead to display the main settings options page,
      // if coming from main widget dashboard, we like to see
      // only list of all offline images, so:
     if($_POST['sectionid']=="OfflineImages")
     {
     	 w3DisplayImages("OfflineImages");
  	    
      } else {
               w3DisplayDefault();
             }
}


?>