<?php
require_once __DIR__ . '/common/config.php';
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/common/CSV.class.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>TCAT :: Export gap data</title>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <link rel="stylesheet" href="css/main.css" type="text/css" />

        <script type="text/javascript" language="javascript">



        </script>

    </head>

    <body>

        <h1>TCAT :: Export gap data</h1>

        <?php
        validate_all_variables();
        // make filename and open file for write
        $module = "gapData";
        $sql = "SELECT querybin, `type` FROM tcat_query_bins WHERE querybin = '" . mysql_real_escape_string($_GET['dataset']) . "'";
        $sqlresults = mysql_query($sql);
        if ($res = mysql_fetch_assoc($sqlresults)) {
            $bintype = $res['type'];
        } else {
            $bintype = 'other';
        }
        $exportSettings = array();
        if (isset($_GET['exportSettings']) && $_GET['exportSettings'] != "")
            $exportSettings = explode(",", $_GET['exportSettings']);
        if ((isset($_GET['location']) && $_GET['location'] == 1))
            $module = "geoTweets";
        $filename = get_filename_for_export($module, implode("_", $exportSettings));
        $csv = new CSV($filename, $outputformat);
        // write header
        $header = "start,end";
        $csv->writeheader(explode(',', $header));

        // make query
        $sql = "SELECT * FROM tcat_error_gap WHERE type = '" . mysql_real_escape_string($bintype) . "' and start >= '" . mysql_real_escape_string($_GET['startdate']) . "' and end <= '" . mysql_real_escape_string($_GET['enddate']) . "'";
        // loop over results and write to file
        $sqlresults = mysql_query($sql);
        if ($sqlresults) {
            while ($data = mysql_fetch_assoc($sqlresults)) {
                $csv->newrow();
                $csv->addfield($data["start"]);
                $csv->addfield($data["end"]);
                $csv->writerow();
            }
        }
        $csv->close();

        echo '<fieldset class="if_parameters">';
        echo '<legend>Your File</legend>';
        echo '<p><a href="' . filename_to_url($filename) . '">' . $filename . '</a></p>';
        echo '</fieldset>';
        ?>

    </body>
</html>