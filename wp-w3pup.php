<?php
/***********************************************************************************************/
/* w3images plugin for Wordpress                                                               */
/* Plugin URI: http://www.axew3.com/                                                           */
/* Plugin author: Alessio Nanni - alias axew3                                                  */
/* =========================================================================================== */

 define('WP_USE_THEMES', false);

/** WordPress wakeup */
 require( dirname(__FILE__) . '/wp-load.php' );

$PHP_SELF = get_bloginfo('url');

$w3images_plugin_path    = ABSPATH . '/wp-content/plugins/w3images/';
$w3images_plugin_url     = $PHP_SELF.'/wp-content/plugins/w3images/';
 
 require_once($w3images_plugin_path."includes/w3lang.php");
  
 if(!isset($_GET['w3_sectionid']) OR !isset($_GET['w3_imageid']))
  {
  	echo "<a href=\"javascript:self.close();\">Close this window</a>";
    die('<h2>Nothing to do with images here</h2>');
  }


// sanitize $_GET

if (is_numeric($_GET['w3_sectionid'])){ $sectionid  = $_GET['w3_sectionid']; } else { $sectionid  = 1; }
   
if (is_numeric($_GET['w3_imageid']))  { $w3_imageid = $_GET['w3_imageid'];   } else { $w3_imageid = 1; }


if(!$image = $wpdb->get_row("SELECT * FROM $w3images_images_table WHERE imageid = '$w3_imageid' AND sectionid = '$sectionid' AND activated = 1"))
{
  die('<h2>Nothing to do with images here</h2>');
}

 $common_set = $wpdb->get_row("SELECT sorting FROM $w3images_sections_table WHERE sectionid = '$sectionid'");
 $sorttype = $common_set->sorting;


switch($sorttype)
{
  case 'Alphabetically A-Z':
    $navprevioussql = 'title < \'' . $image->title . '\' ORDER BY title DESC';
    $navnextsql     = 'title > \'' . $image->title . '\' ORDER BY title ASC';
  break;

  case 'Alphabetically Z-A':
    $navprevioussql = 'title > \'' . $image->title . '\' ORDER BY title ASC';
    $navnextsql     = 'title < \'' . $image->title . '\' ORDER BY title DESC';
  break;

  case 'Author Name A-Z':
    $navprevioussql = 'author < \'' . $image->author . '\' ORDER BY author DESC';
    $navnextsql     = 'author > \'' . $image->author . '\' ORDER BY author ASC';
  break;

  case 'Author Name Z-A':
    $navprevioussql = 'author > \'' . $image->author . '\' ORDER BY author ASC';
    $navnextsql     = 'author < \'' . $image->author . '\' ORDER BY author DESC';
  break;

  case 'Oldest First':
    $navprevioussql = 'imageid < ' . $image->imageid . ' ORDER BY imageid DESC';
    $navnextsql     = 'imageid > ' . $image->imageid . ' ORDER BY imageid ASC';
  break;

  case 'Newest First':
    $navprevioussql = 'imageid > ' . $image->imageid . ' ORDER BY imageid ASC';
    $navnextsql     = 'imageid < ' . $image->imageid . ' ORDER BY imageid DESC';
  break;
}


   $w3options        = $wpdb->get_results("SELECT value FROM $w3images_options_table");
   $w3navLinksValue  = $w3options[7]->value;


if ($w3navLinksValue == 0) // text link mode
  	 { 
  	    if($previousimage = $wpdb->get_row("SELECT imageid, filename, title FROM $w3images_images_table WHERE sectionid = '$sectionid' AND activated = 1 AND $navprevioussql LIMIT 1"))
        {
           $previousimagelink = '<h4><a href="'.$PHP_SELF.'/wp-w3pup.php?w3_sectionid=' . $image->sectionid . '&amp;w3_imageid=' . $previousimage->imageid . '">'.W3NEXT.'</a></h4>';
         }
    
        if($nextimage = $wpdb->get_row("SELECT imageid, filename FROM $w3images_images_table WHERE sectionid = $sectionid AND activated = 1 AND $navnextsql LIMIT 1"))
        {
          $nextimagelink = '<h4><a href="'.$PHP_SELF.'/wp-w3pup.php?w3_sectionid=' . $image->sectionid . '&amp;w3_imageid=' . $nextimage->imageid . '">'.W3PREVIOUS.'</a></h4>';
        }
        
     } else 
            { // thumbnail link mode
              
              if($previousimage = $wpdb->get_row("SELECT imageid, filename, title FROM $w3images_images_table WHERE sectionid = '$sectionid' AND activated = 1 AND $navprevioussql LIMIT 1"))
               {
                 $previousimagelink = '<div style="float:left;"><a href="'.$PHP_SELF.'/wp-w3pup.php?w3_sectionid=' . $image->sectionid . '&amp;w3_imageid=' . $previousimage->imageid . '"><img src="./wp-content/plugins/w3images/images/tb_'.$previousimage->filename.'" alt="'.$previousimage->title.'" /></a><br /><div style="font-size:xx-small;text-align:center;">'.W3NEXT.'</div></div>';
                }
    
               if($nextimage = $wpdb->get_row("SELECT imageid, filename, title FROM $w3images_images_table WHERE sectionid = $sectionid AND activated = 1 AND $navnextsql LIMIT 1"))
               {
           	    $nextimagelink = '<div style="float:right;"><a href="'.$PHP_SELF.'/wp-w3pup.php?w3_sectionid=' . $image->sectionid . '&amp;w3_imageid=' . $nextimage->imageid . '"><img src="./wp-content/plugins/w3images/images/tb_'.$nextimage->filename.'" alt="'.$nextimage->title.'" /></a><br /><div style="font-size:xx-small;text-align:center;">'.W3PREVIOUS.'</div></div>';
               }
  
             } // CLOSE // thumb mode


// set here w3pup popup slideshow style

echo '<html>
      <head>
        <title>' . $image->title . '</title>
        <style type="text/css">
       body {
       padding:0px;
       margin:15px;
	font-size: small;
	font-family: Tahoma, Verdana, Arial, Sans-Serif;
	color: #000;
	}
	
	a:link {font-weight:bold; color: #f15622; text-decoration: none;}
  a:active {font-weight:bold; color: #f15622; text-decoration: none;}
  a:visited {font-weight:bold; color: #f15622; text-decoration: none; }
  a:hover {font-weight:bold; color:#29709B; text-decoration: underline;}
  
  img{ border:0; }
  
	.w3imgpup {
	align:center;
	text-align:center;
	}
	.tdpad {
	padding:20px;
	}
	.divsub {
	font-weight:bold;
	padding:0px 20px;
	}
        </style>
      </head>
      <body style="background-color:#FFF">
      <table cellpadding="0" cellspacing="0" border="0" width="100%"> 
      <tr><td colspan="2" class="tdpad"><strong>' . $image->title . '</strong><br /><br /></td></tr>
      <tr><td colspan="2" class="w3imgpup" align="center"><a href="javascript:self.close();"><img src="'.$w3images_plugin_url.'images/' . $image->filename . '" style="border:0" /></a>
        </td></tr></table>
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
      <tr>
        <td class="tdpad style="padding-top: 7px;" align="left" width="50%">'  . w3se(isset($previousimagelink), $previousimagelink) . '</td>
        <td class="tdpad style="padding-top: 7px;" align="right" width="50%">' . w3se(isset($nextimagelink),     $nextimagelink)     . '</td>
      </tr>
       <tr><td colspan="2">
      <div class="divsub">';

echo nl2br($image->description) . '<br /><br />';

if($image->showauthor)
{
  echo '<span style="font-size:80%">Submitted by ' . $image->author . '</span><br /><br />';
}
echo '</div></td></tr></table></body>
      </html>';
 
?>