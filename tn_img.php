<?php
// image.php - by Hermawan Haryanto hermawan@dmonster.com
// Example PHP Script, demonstrating Storing Image in Database
// Detailed Information can be found at http://www.codewalkers.com

// database connection
// database connection
require_once("config.php");

#$connection = mysql_pconnect("$dbhost","$dbusername","$dbpasswd")
#	or die ("Couldn't connect to server.");

#$db = mysql_select_db("$database_name", $connection) or die("Couldn't select database.");

$query    = "SELECT * FROM `CPDB_Image` WHERE PanelistID =".$_GET["pid"];
$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
if (mysql_num_rows ($sql)>0) {
  $row = mysql_fetch_assoc($sql);
  $image_type = $row["image_type"];
  $image = $row["image"];
  Header ("Content-type: $image_type");
        $img = imagecreatefromstring($image);

        $x = ImageSX($img);
	    $y = ImageSY($img);

        $newwidth = 200;
        if (array_key_exists("scale",$_GET)) $newwidth = $_GET['scale'];
        $newheight = $newwidth * $y / $x;

        $thumb = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresized($thumb, $img, 0, 0, 0, 0, $newwidth, $newheight, $x, $y);
        imagejpeg($thumb);

  #print $oDestinationImage;
}
?>
