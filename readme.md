# zenphoto-on-this-day
Plugin for the Zenphoto open-source gallery that generates 'on this day' content for use in your gallery.

# Example

This plugin queries your photo gallery for photos taken 1, 2, 5, 10, 15 years ago from the current date, and returns either the oldest image, or the highest number of views if the 'hitcounter' plugin is enabled.

See the plugin in use under the 'On this day' heading at https://railgallery.wongm.com/

![image](https://user-images.githubusercontent.com/916546/40593788-1fa8e16e-626e-11e8-8a21-ac296ca941ff.png)

You can also use the plugin to generate a RSS feed - example can be found at https://railgallery.wongm.com/page/rss-on-this-day

Available data fields:

* yearsAgo
* date
* pastDateToSearch
* imageUrl
* currentDayLink
* album
* timestamp
* desc
* title

# Installation

1. Install the 'zenphoto-photostream' plugin from https://github.com/wongm/zenphoto-on-this-day
2. Copy on-this-day.php into the /plugins directory of your Zenphoto installation.
3. Rename on-this-day.php.sample-theme to on-this-day.php and copy it into your current Zenphoto theme folder.
4. Enable the 'on this day' plugin in the Zenphoto backend.
5. Navigate to yourzenphotoaddress.com/?p=on-this-day and you will now see a page displaying photos taken X days ago.

For testing purposes a custom data parameter can also be passed into the page - format is `?date=2015-11-24`.
