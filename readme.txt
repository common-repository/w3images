=== w3images ===
Contributors: axewww
Donate link: http://www.axew3.com/b10g/
Tags: gallery,images,thumbnails,posts,entries,comments,image,entries images,post images,images gallery,ajax slide,ajax gallery
Requires at least: 3.0.0
Tested up to: 3.0
Stable tag: trunk

The w3images, Images Gallery for WordPress.

== Description ==

W3IMAGES FEATURES

Upload single or multiples images.
Unlimited sections and subsections.
Set slide show for images to be displayed on three different ways:

   1. Simple js/html popup
   2. Slide in the same page (inline)
   3. Shadowbox ajax mode

All control over images gallery can be done from administration while any user (or specified users group you need)
can upload images on any section. Moderation provided for images (enable or disable) and more features

== Installation ==

1. Upload entire `w3images folder` to the `/wp-content/plugins/` directory and files `wp-w3images.php` and `wp-w3pup.php` to your blog root
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to - administration -> settings -> w3images - to start creating/modify sections or upload images or use any other w3images feature and modify settings
4. Open the wp.w3images.php file - if it is necessary - to modify needed template instructions and fit your WordPress theme.
5. Follow instructions and/or contribute to improve it at this address: http://www.axew3.com/b10g/w3images-for-wordpress-how-to-use-it/

== Upgrade ==

1. To upgrade the w3images plugin, simply upload all files to respective folders
   overwriting old files with latests where necessary. Remember that to let work the
   w3images plugin after 1.0.4 version with the 'ajax Shadowbox' slideshow instead of js/html popup or inline gallery,
   it is necessary to add few lines of code as reported in the 'w3images how to use page':
   http://www.axew3.com/b10g/w3images-for-wordpress-how-to-use-it/

== Frequently Asked Questions ==

Visit, ask or contribute at http://www.axew3.com/b10g/w3images-for-wordpress-how-to-use-it/

== Screenshots ==

...

== Changelog ==

= 1.1.4 =
* Updated the images-options.php file to fix the bug on batch upload (multiple images upload on w3images admin).
= 1.1.3 =
* Updated the images-options.php file to fix bug reported by Sascha on administration, single images upload. Sascha notice also about a bug on the batch images upload (multiple images upload in administration): batch upload will be fixed asap within next w3images 1.1.4 release.
* Added the w3images.css file contained in wp-content/plugins/w3images/includes/w3images.css. It is loaded by w3images to the header of the wp-w3images.php and by editing it, is possible to modify the gallery output layout.
* More fixes and little improvements.
= 1.1.2 =
* Updated all files to fix several bugs. Thumbnail mode fixed where as inline or pupup html mode was working bad.
= 1.1.1 =
* Updated the wp-w3pup.php file to fit wordpress 3 code.
* Deprecated function changed in favor of get_bloginfo();
= 1.1.0 =
* Fixed bug on plugin activation due to code elimination on version 1.0.9. Thank to user Ulil for report this.
* Added info on w3images administration about images to get with easy names and path.
* Fixed and updated some instruction on all files.
= 1.0.9 =
* Added the main administration dashboard widget for w3images to know on fly if there are images to be validate.
* Simplified the wp.w3images.php file to fit with easy any wordpress template.
* Fixed and updated instructions on all files and removed redundant code relate to wordpress versions.
= 1.0.8 =
* Added latest images validation option on w3images main options page.
* Updated to latest wordpress users rules ( wp 3.0.0 or > ) supposedly all permissions for the upload images form.
* Fixed and improved some instruction on files w3images-options.php and wp-w3images.php. To the w3images-options.php file has been added the function w3AllUp() that should incorporate time by time all others functions, and finally transormed in a class.
= 1.0.7 =
* This version fixes a security related bug and many others bugs.
* Added better captcha check for the images upload form.