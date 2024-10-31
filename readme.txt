=== Multiple Cropped Images ===
Tags: Images, Cropping, Upload, Sizes
Tested up to: 4.9.6
Stable tag: v1.1.7
License: GPL-3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.hmtl

Upload any number of images and scale and crop them individually.

== Description ==

= Set image sizes, upload and crop images =

Multiple Cropped Images (MCI) allows you to upload an unlimited number of images to an editing page and to scale each them individually using a an easy-to-use image cropping tool and enables you to select any image section. The images are only saved in image sizes that were previously defined in a configuration page. This ensures that the images are saved in the correct image sizes. The original image and the cropped image are always available in the backend. This makes it possible to recut them at any time without having to upload the original again.

= Comfortable image management =
Using drag & drop, images can be sorted in the preferred order. This is ideal for quickly adapting a slideshow to your needs. Title and text features for each picture help to keep track. If required, these options can be displayed on the frontend.

= Shortcodes for multiple image usage =
Using shortcodes, images can be inserted into any type of post.

= Search engine friendliness included =
Additional input options such as title, text, SEO-title and SEO-alt make the MCI plugin search engine friendly.

= Equally suitable for users, developers and agencies. =
The MCI plugin is intuitive to use, and users will love its ease of use. Developers get a sophisticated solution for use in projects. Web agencies can professionally implement image management on websites while knowing that the customer can easily manage the images later. Incorrect image sizes, distorted and blurred images are a thing of the past.


== Installation ==

= Automatic Installation =
1. Log in to your WordPress dashboard and navigate to the Plugins menu
2. Search for the Plugin “Multiple Cropped Images”
3. Click the “Install Now” button

= Manual Installation =
The manual installation process requires you to upload the file manually to your server.
The WordPress Codex contains all the instructions needed on how to do this.
Visit the following link for more information:

https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation

== Changelog ==
The following change logs provide information about new features and fixed issues for all released versions.

= v1.1.7 =
* Cropper.js upgrade from 1.0 to 1.4

= v1.1.6 =
* Removed a PHP notice appearing in the backend under some circumstances

= v1.1.5 =
* Minor bug fixes

= v1.1.4 =
* Translation fixes

= v1.1.3 =
* Post ID parameter added to shortcodes

= v1.1.2 =
* Minor bug fixes and improvements

= v1.1.1 =
* Minor bug fixes and improvements

= v1.1.0 =
* Important: There has been a big change regarding the images target directory. Backup your images before updating the plugin and move them into the directory wp-content/uploads/mci/ afterwards.

= v1.0.0 =
* Initial Release

== Frequently Asked Questions ==

= I can't save images... help! =
MCI is saving all image files into a separate directory "wp-content/uploads/mci/".
Add writing permissions to the above mentioned directory to fix this problem.
If this does not resolve your issue, please contact mci@webtimal.ch for assistance.

= Uploading an image results in an error. What's happening? =
The maximum filesize set for uploads depends on your webserver's configuration.
Reduce the filesize of the image you're trying to upload or contact your web hoster for a possible change of settings.

= I have problems with saving multiple images at once =
Processing large amount of image data at once takes some time.
Typically the maximum amount of time a PHP script is allowed to run for is 30 seconds.
Exceeding this duration results in a script abortion and images may not be saved properly.
Simply lower the amount of images you are saving at once to make sure everything works fine.
