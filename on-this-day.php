<?php
/**
 * On this day
 *
 * Generates 'on this day' content for use on your gallery
 *
 * You can add custom filter to the results using the 'on_this_day_additional_where' filter.
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
        echo "Please enable 'zenphoto-photostream' plugin";
        exit();
    }

    $now = time();
    if (strlen($customDate) > 0)
    {
        $now = strtotime($customDate . ' 22:00:00');
    }
    $oneDay = new DateInterval('P1D');
    $localTimezone = new DateTimeZone(getOption('time_zone'));
    $dateToSearch = new DateTime();
    $dateToSearch->setTimestamp($now);
    $dateToSearch->setTimezone($localTimezone);

    $currentHour = $dateToSearch->format('H');
    if ($currentHour < (int)$offsetHours)
    {
        $dateToSearch->sub($oneDay);
    }
    $currentDayLink = $dateToSearch->format('Y-m-d');

    $maxHitcounter = 0;
    $candidate = new stdClass;
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

        $sqlWhere = "i.date >= '$dayLink' AND i.date < '$dayLink' + INTERVAL 1 DAY";
        $additionalSqlWhere = zp_apply_filter('on_this_day_additional_where');
        if (strlen($additionalSqlWhere) > 0)
        {
            $sqlWhere = "$sqlWhere AND $additionalSqlWhere";
        }
        
        $sqlOrder = "i.hitcounter DESC";
        if (function_exists('NewDailySummary')) {
            $sqlOrder = "i.daily_score DESC, " . $sqlOrder;
        }

        // run the query
        setCustomPhotostream($sqlWhere, "", $sqlOrder);

        // validate we have photos to show
        $photocount = getNumPhotostreamImages();
        if ($photocount > 0)
        {
            next_photostream_image();

            global $_zp_current_image;

            $hitcounter = 1;
            if (function_exists('getHitcounter')) {
                $hitcounter = getHitcounter($_zp_current_image);
            }
            if ($hitcounter > $maxHitcounter)
            {
                $candidate = new stdClass;
                $candidate->yearsAgo = $year . $suffix;
                $candidate->date = getImageDate();
                $candidate->pastDateToSearch = $pastDateToSearch;
                $candidate->imageUrl = getDefaultSizedImage();
                $candidate->imagePageUrl = getImageURL();
                $candidate->currentDayLink = $currentDayLink;
                $candidate->album = getAlbumTitleForPhotostreamImage();
                $candidate->desc = getImageTitle() . ". " . getImageDesc();
                $candidate->title = "$candidate->yearsAgo, " . $candidate->pastDateToSearch->format('j F Y');
                $candidate->image = $_zp_current_image;
                $maxHitcounter = $hitcounter;
            }
        }
    }

    // reset current image to the one we were on
    if (property_exists($candidate, 'image'))
    {
         global $_zp_current_image;
        $_zp_current_image = $candidate->image;
    }

    $candidate->timestamp = $dateToSearch->getTimestamp();
    return $candidate;
}

/*
 * Example implementation of the 'on_this_day_additional_where' filter:
 *
 
zp_register_filter('on_this_day_additional_where', 'on_this_day_additional_where');

function on_this_day_additional_where() {
    return "a.folder NOT LIKE '%stations%' AND a.id NOT IN (SELECT `objectid` FROM ". prefix('obj_to_tag') ." ott INNER JOIN ". prefix('tags') ." t ON ott.`tagid` = t.`id` WHERE ott.`type` = 'albums' AND t.`name` = 'buses')";
}

 *
 */

?>