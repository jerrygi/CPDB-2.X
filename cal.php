<?php
require_once("config.php"); # load configuration file
include 'class.ical.php/iCalcreator.class.php'; # load calendar creator class
$MY = array_merge($_GET, $_POST);
#$MY['PanelistID']= 242;
#$MY['RoomID']= 48 ;

#$CFG['debug']=1;



$query="select * from CPDB_Room where ConID='".$CFG['ConID']."'";
$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
print"Rooms<br>";
while ($row = mysql_fetch_assoc($sql)) {
	print "<br><a href='ical.php?Mode=Room&RoomID=".$row['RoomID']."'>".$row['RoomName']."</a>";

}
print "<br><br><a href='ical.php?Mode=All'>All Rooms</a>";

?>