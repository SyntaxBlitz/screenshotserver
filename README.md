ScreenShotServer
================
This is a simple script I wrote awhile ago in PHP that allows for image uploading from Greenshot.  
The idea behind it is that most of the time, when you need to quickly send a screenshot to someone, you're probably going to send the screenshot and then forget about it the next day. Most of the time, you don't actually need these screenshots to be permanent. This server will automatically overwrite uploaded images for every 62 images uploaded (each temporary image has a one-character alphanumeric filename that matches), unless they're explicitly made permanent. This concept is obviously more suitable for only a single user (or a small group of users) so that images are not overwritten too quickly. This is a simple script, so it doesn't do any authentication for image uploads or for requests to the permanent page. For that reason, I'd not recommend actually using it. :)

How it works
------------
The actual image uploader involved is [Greenshot](http://getgreenshot.org/). Greenshot has a spiffy feature that allows us to automatically upload an image to imgur after screenshotting it. We're going to rip that functionality out of the software and use it for our own purposes. This script responds to imgur API requests as though it's actually the imgur server, so all we need to do is change the Greenshot plugin so that it uses our script as the API entry point (which the imgur plugin developer made very easy to do; it's part of the configuration <3).

To make an image permanent, we navigate to /permanent/image_id, which will redirect us to a permanent version of the image that won't be overwritten by later images. I've also included a simple userscript that will, upon pressing 'P', automatically navigate to this URL when you're viewing an image.

Installation
------------
This needs to be installed on an operating system with case-sensitive filenames.

Plop these files into a directory, **which should be the root folder of either the domain or a subdomain**. You can modify this code to work inside a directory, of course, but it's not currently written to work like that.

Create two MySQL tables in a database, called 'temporaryImages' and 'storedImages' in my instance (though you can call them whatever you want and just modify the config).
This is the table creation code:

    CREATE TABLE temporaryImages id INT NOT NULL AUTO_INCREMENT, singleByteName CHAR(1) NOT NULL, timestamp BIGINT
    CREATE TABLE storedImages id INT NOT NULL AUTO_INCREMENT, longName VARCHAR(16) NOT NULL, timestamp BIGINT

Feel free to set longName's length to a different value.

Then modify `configuration.php` with the correct values.

Now, just set up Greenshot to use your base URL as the imgur API url. You'll find this in the configuration section for the imgur plugin. There should not be a trailing slash on the value in the field. While you're at it, make sure the image type is set to png; this script is written with the assumption that you're uploading pngs. To change that, just change the content type headers and filename extensions.

Rights
------
This work, excluding the derivative base62.php, is released under the MIT license. base62.php was given to the StackOverflow community by [Baishampayan Ghose](http://stackoverflow.com/users/8024/baishampayan-ghose).