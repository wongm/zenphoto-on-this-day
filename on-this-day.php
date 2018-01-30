<?php
/**
 * On this day
 *
 * Generates 'on this day' content for use on your gallery
 *
 * @author Marcus Wong (wongm)
 * @package plugins
 */

$plugin_description = gettext("Generates 'on this day' content for use in your gallery.");
$plugin_author = "Marcus Wong (wongm)";
$plugin_version = '1.0.0'; 
$plugin_URL = "https://github.com/wongm/zenphoto-on-this-day/";

function getSummaryForCurrentDay($customDate, $offsetHours = 0) {

    if (!function_exists('setCustomPhotostream')) {
    	exit();
    }
    
    $now = time();
    if (strlen($customDate) > 0)
    {
        $now = strtotime($customDate . ' 22:00:00');
    }
    $oneDay = new DateInterval('P1D');
    $melbournetimezone = new DateTimeZone('Australia/Melbourne');
    $dateToSearch = new DateTime();
    $dateToSearch->setTimestamp($now);
    $dateToSearch->setTimezone($melbournetimezone);
    
    $currentHour = $dateToSearch->format('H');
    if ($currentHour < (int)$offsetHours)
    {
        $dateToSearch->sub($oneDay);
    }
    $currentDayLink = $dateToSearch->format('Y-m-d');
    
    $maxHitcounter = 0;
    foreach (array(1, 2, 5, 10, 15) AS $year)
    {
        if ($year == 1)
        {
            $suffix = " year ago";
        }
        else
        {
            $suffix = " years ago";
        }
    
        $pastDateToSearch = clone $dateToSearch;
        $pastDateToSearch->sub(new DateInterval('P' . $year . 'Y'));
        $dayLink = $pastDateToSearch->format('Y-m-d');
    
        // run the query
        setCustomPhotostream("i.date >= '$dayLink' AND i.date < '$dayLink' + INTERVAL 1 DAY AND a.folder NOT LIKE '%bus%' AND a.folder NOT LIKE '%bits%' AND a.folder != 'road-coaches' AND a.folder != 'photoshop' AND a.folder NOT LIKE 'wagons%'", "", "i.hitcounter DESC");
    
        // validate we have photos to show
        $photocount = getNumPhotostreamImages();
        if ($photocount > 0)
        {
            next_photostream_image();
            
            global $_zp_current_image;
            //$hitcounter = (getHitcounter($_zp_current_image) / $year);
            $hitcounter = getHitcounter($_zp_current_image);
            if ($hitcounter > $maxHitcounter)
            {
                $candidate = new stdClass;
                $candidate->yearsAgo = $year . $suffix;
                $candidate->date = getImageDate();
                $candidate->pastDateToSearch = $pastDateToSearch;
                $candidate->imageUrl = getDefaultSizedImage();
                $candidate->currentDayLink = $currentDayLink;
                $candidate->album = getAlbumTitleForPhotostreamImage();
                $candidate->timestamp = $dateToSearch->getTimestamp();
                $candidate->desc = getImageTitle() . ". " . getImageDesc();
                $candidate->title = "$candidate->yearsAgo, " . $candidate->pastDateToSearch->format('d F Y');
                $maxHitcounter = $hitcounter;
            }
        }
    }
    
    return $candidate;
}

?>