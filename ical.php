<?php
require_once("config.php"); # load configuration file
include 'class.ical.php/iCalcreator.class.php'; # load calendar creator class
$MY = array_merge($_GET, $_POST);
#$MY['PanelistID']= 242;
#$MY['RoomID']= 48 ;

$CFG['debug']=1;
#debug();

$v=create_header();


#if ($MY['Mode']=='Room') enumerate_room_schedule();
#if ($MY['Mode']=='Panelist') enumerate_panelist_schedule();
#if ($MY['Mode']=='All') enumerate_all();

if ($MY['Mode']=='Room') room_schedule();
if ($MY['Mode']=='Panelist') panelist_schedule();
if ($MY['Mode']=='All') all();

#all();
$v->returnCalendar();


function all() {
	global $CFG;
	global $MY;
	global $v;
	$rowlist = array();
	$query="select * from `CPDB_PTR` as L
			inner join `CPDB_Room` as R on R.RoomID = L.RoomID
			where L.ConID = '".$CFG['ConID']."' and
			R.RoomHideGrid = 0";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		array_push($rowlist,$row['RoomID']);
	}
	$MY['RoomID'] = implode(",",$rowlist);

	room_schedule();
}

function room_schedule(){
	global $CFG;
	global $MY;
	global $v;
#	$query1="select * from `CPDB_PTR` as L
#			inner join `CPDB_Panels` as P on P.PanelID = L.PanelID
#			inner join `CPDB_Category` as C on C.CatID = P.CatID
#			inner join `CPDB_Room` as R on R.RoomID = L.RoomID
#			where L.RoomID in (".$MY['RoomID'].") and L.ConID = '".$CFG['ConID']."'";
	$query1 = "select * from `CPDB_PTR` as P
				inner join `CPDB_Room` as R on R.RoomID = P.RoomID
				where P.ConID = '".$CFG['ConID']."' and
						P.RoomID in (".$MY['RoomID'].") and
						RoomHideGrid = 0";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	while ($row1 = mysql_fetch_assoc($sql1)) {
		enumerate_panel($row1['PanelID']);
	}
}

function panelist_schedule(){
	global $CFG;
	global $MY;
	$query1="select * from `CPDB_P2P`
			where PanelistID = '".$MY['PanelistID']."' and
			ConID = '".$CFG['ConID']."'";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	while ($row1 = mysql_fetch_assoc($sql1)) {
		enumerate_panel($row1['PanelID']);
	}
}


function enumerate_all() {
	global $CFG;
	global $MY;
	$rowlist = array();
	$query="select * from `CPDB_PTR` as L
			inner join `CPDB_Room` as R on R.RoomID = L.RoomID
			where L.ConID = '".$CFG['ConID']."' and
			R.RoomHideGrid = 0";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		array_push($rowlist,$row['RoomID']);
	}
	$MY['RoomID'] = implode(",",$rowlist);

	enumerate_room_schedule();
}
function create_header(){
	global $CFG;
	$config = array( 'unique_id' => 'CPDB' );
	  // set Your unique id
	$v = new vcalendar( $config );
	  // create a new calendar instance
	$v = new vcalendar( $config );
	  // create a new calendar instance
	$v->setProperty( 'method', 'PUBLISH' );
	  // required of some calendar software
	$v->setProperty( "x-wr-calname", $CFG['ConName']);
	  // required of some calendar software
	$v->setProperty( "X-WR-CALDESC", $CFG['ConName']);
	  // required of some calendar software
	$v->setProperty( "X-WR-TIMEZONE", $CFG['TimeZone'] );

	return $v;
}

function enumerate_panel($PanelID){
	global $CFG;
	global $MY;
	global $v;
	$query="Select * from `CPDB_Panels` as P
			inner join `CPDB_PTR` as L on P.PanelID = L.PanelID
			inner join `CPDB_Room` as R on R.RoomID = L.RoomID
			inner join `CPDB_Category` as C on P.CatID = C.CatID
			where P.PanelID = '".$PanelID."'
			and P.PanelHidePublic = 0";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$vevent = & $v->newComponent( 'vevent' );
		$vevent->setProperty( 'dtstart', timestamp_to_array($row['Start']) );
		$vevent->setProperty( 'dtend', timestamp_to_array($row['End']) );
		$vevent->setProperty( 'LOCATION', $row['RoomName'] );
		$vevent->setProperty( 'summary', $row['PanelTitle'] );
		$vevent->setProperty( 'description', $row['PanelDescription']);
		$vevent->setProperty( 'X-ALT-DESC;FMTTYPE=text/html',  $row['PanelDescription']."<br><br>".panelists_as_bullets($row['PanelID']) );
		#$vevent->setProperty( 'comment', 'This is a comment' );
		$vevent->setProperty( 'CATEGORIES', 'scifi');
		$vevent->setProperty( 'CATEGORIES', 'convention');
		$vevent->setProperty( 'CATEGORIES', $CFG['ConName']);
		$vevent->setProperty( 'CATEGORIES', $row['Category']);
	}
}

function enumerate_room_schedule(){
	global $MY;
	global $CFG;
	$query="select * from `CPDB_Room` where `RoomID` in (".$MY['RoomID'].")";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$config = array( 'unique_id' => 'CPDB' );
	  // set Your unique id
	$v = new vcalendar( $config );
	  // create a new calendar instance
	$v = new vcalendar( $config );
	  // create a new calendar instance
	$v->setProperty( 'method', 'PUBLISH' );
	  // required of some calendar software
	$v->setProperty( "x-wr-calname", $CFG['ConName']." schedule for ".$row['RoomName'] );
	  // required of some calendar software
	$v->setProperty( "X-WR-CALDESC", $CFG['ConName']." schedule for ".$row['RoomName'] );
	  // required of some calendar software
	$v->setProperty( "X-WR-TIMEZONE", "PST" );
	$query1="select * from `CPDB_PTR` as L
			inner join `CPDB_Panels` as P on P.PanelID = L.PanelID
			inner join `CPDB_Category` as C on C.CatID = P.CatID
			inner join `CPDB_Room` as R on R.RoomID = L.RoomID
			where L.RoomID in (".$MY['RoomID'].") and L.ConID = '".$CFG['ConID']."'";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	while ($row1 = mysql_fetch_assoc($sql1)) {
		$vevent = & $v->newComponent( 'vevent' );
		// create an event calendar component
		$vevent->setProperty( 'dtstart', timestamp_to_array($row1['Start']) );
		$vevent->setProperty( 'dtend', timestamp_to_array($row1['End']) );
		$vevent->setProperty( 'LOCATION', $row1['RoomName'] );
		$vevent->setProperty( 'summary', $row1['PanelTitle'] );
		$vevent->setProperty( 'description', $row1['PanelDescription']);
		$vevent->setProperty( 'X-ALT-DESC;FMTTYPE=text/html',  $row1['PanelDescription']."<br><br>".panelists_as_bullets($row1['PanelID']) );
		$vevent->setProperty( 'comment', 'This is a comment' );
		$vevent->setProperty( 'CATEGORIES', 'scifi');
		$vevent->setProperty( 'CATEGORIES', 'convention');
		$vevent->setProperty( 'CATEGORIES', $CFG['ConName']);
		$vevent->setProperty( 'CATEGORIES', $row1['Category']);
#		$query2="select PanelistName, PanelistPubName, S.shareemail from `CPDB_P2P` as L
#				inner join `CPDB_Panelist` as P on L.PanelistId = P.PanelistID
#				inner join `CPDB_PanelistCon` as S on S.PanelistId = P.PanelistID
#				where L.PanelID = '".$row1['PanelID']."'";
#		$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
#		while ($row2 = mysql_fetch_assoc($sql2)) {
#			if ($row2['shareemail']==1){
#				$vevent->setProperty( 'attendee', $row2[PanelistPubName].'<'.$row2['PanelistEmail'] .'>');
#			} else {
#				$vevent->setProperty( 'attendee', $row2[PanelistPubName].'<'.$row2['PanelistPubName'] .'@example.com>');
#			}
#		}
	}
	$v->returnCalendar();
}

function enumerate_panelist_schedule(){
	global $MY;
	global $CFG;
	$query="select * from `CPDB_Panelist` where `PanelistID` = '".$MY['PanelistID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	if ($row['PanelistPubname'] == '') {
		$pname=$row['PanelistName'];
	} else {
		$pname=$row['PanelistPubName'];
	}


	$config = array( 'unique_id' => 'CPDB' );
	  // set Your unique id
	$v = new vcalendar( $config );
	  // create a new calendar instance
	$v = new vcalendar( $config );
	  // create a new calendar instance
	$v->setProperty( 'method', 'PUBLISH' );
	  // required of some calendar software
	$v->setProperty( "x-wr-calname", $CFG['ConName']." schedule for ".$pname );
	  // required of some calendar software
	$v->setProperty( "X-WR-CALDESC", $CFG['ConName']." schedule for ".$pname  );
	  // required of some calendar software
	$v->setProperty( "X-WR-TIMEZONE", "PST" );


	$query="select * from `CPDB_P2P` as L
	inner join `CPDB_Panels` as P 	on P.PanelID = L.PanelID
	inner join `CPDB_PTR` as T 		on P.PanelID = T.PanelID
	inner join `CPDB_Room` as R 	on T.RoomID = R.RoomID
	inner join `CPDB_Category` as C on P.CatID = C.CatID
	where L.PanelistID = '".$MY['PanelistID']."'
	and L.ConID = '".$CFG['ConID']."'";

	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {


#		array_table(timestamp_to_array($row['Start']) , $bgcolor="ff9999", $cols=3,$note="");
#		array_table(timestamp_to_array($row['End']) , $bgcolor="ff9999", $cols=3,$note="");
		$vevent = & $v->newComponent( 'vevent' );
		// create an event calendar component
		$vevent->setProperty( 'dtstart', timestamp_to_array($row['Start']) );
		$vevent->setProperty( 'dtend', timestamp_to_array($row['End']) );
		$vevent->setProperty( 'LOCATION', $row['RoomName'] );
		$vevent->setProperty( 'summary', $row['PanelTitle'] );
		$vevent->setProperty( 'description', $row['PanelDescription'] );
		$vevent->setProperty( 'X-ALT-DESC;FMTTYPE=text/html',  $row['PanelDescription']."<br><br>".panelists_as_bullets($row['PanelID']) );
		$vevent->setProperty( 'comment', 'This is a comment' );
		$vevent->setProperty( 'CATEGORIES', 'scifi');
		$vevent->setProperty( 'CATEGORIES', 'convention');
		$vevent->setProperty( 'CATEGORIES', $CFG['ConName']);
		$vevent->setProperty( 'CATEGORIES', $row['Category']);
#		$query1="select PanelistName, PanelistPubName, S.shareemail from `CPDB_P2P` as L
#				inner join `CPDB_Panelist` as P on L.PanelistId = P.PanelistID
#				inner join `CPDB_PanelistCon` as S on S.PanelistId = P.PanelistID
#				where L.PanelID = '".$row['PanelID']."'";
#				$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
#				while ($row1 = mysql_fetch_assoc($sql1)) {
#					if ($row1['shareemail']==1){
#						$vevent->setProperty( 'attendee', $row1[PanelistPubName].'<'.$row1['PanelistEmail'] .'>');
#					} else {
#						$vevent->setProperty( 'attendee', $row1[PanelistPubName].'<'.$row1['PanelistPubName'] .'@example.com>');
#					}
#
#				}
	}
	$v->returnCalendar();

}

function panelists_as_bullets($PanelID){
	#<
	global $CFG;
	global $MY;

	$myoutput = "Panelists:<ul>";

	$queryx="select L.PanelistID, P.PanelistPubName from CPDB_P2P as L
			inner join CPDB_Panelist as P on P.PanelistID = L.PanelistID
			where PanelID = '".$PanelID."'";
	$sqlx=mysql_query($queryx) or die('Query failed: ' . mysql_error());
#print $queryx."<br><br>";
	while ($rowx = mysql_fetch_assoc($sqlx)) {
		$myoutput .= "<li><a target='matrix' href='".$CFG['webpath']."\matrix.php?mode=panelist&ID=".$rowx['PanelistID']."'>".$rowx['PanelistPubName']."</a>";
	}
	$myoutput .="</ul>";

	return $myoutput;

}



function timestamp_to_array($timestamp){
	## explode timestamp on Space results in Date Part and Time Part
		$ar = explode(" ",$timestamp);
	## explode datepart on - gives year, month and date
		$ard = explode("-",$ar[0]);
	## explode timepart on : and get Hour, minute and second
		$art = explode(":",$ar[1]);

	$out = array('year'=>$ard[0], 'month'=>$ard[1], 'day'=>$ard[2], 'hour'=>$art[0] ,'min'=>$art[1] ,'sec'=>$art[2] );
#	array_table($out);
	return $out;
	}

function debug() {
	global $CFG;
	if ($CFG['debug']==1){

#			print_r (array_keys($_POST));
#			print"<br>\r\n";
#			print_r (array_values($_POST));
#			print"<br>\r\n";
		$message = "User ID = ".$_SERVER['PHP_AUTH_USER']."<br>Exploding '$ _POST'";
		array_table($_POST, "99ff99",1,$message);
		print "</font><br>";
	}
}


function array_table($myarray, $bgcolor="ff9999", $cols=3,$note="")
{
	print "<br><font color='red'><B>Begin Debug Info</B><br>";
	print $note."<br>";
	$ttlrows=count($myarray);
	$wrkrows=$ttlrows-($ttlrows%$cols);
	$rows=($wrkrows/$cols)+2;
	print "<table border=1 bgcolor='#".$bgcolor."'><tr>";

	$row=1;
	uksort($myarray, "cmp");
	foreach($myarray as $key => $value ) {
		#<
		if ($row==1) {
			print "<td valign='top'><table border=1><tr><th>KEY</th><th>VALUE</TH></tr>";
		}
		print "<TR><td>".$key."</td><td>";
		if (count($value)==1){
			print $value;
		} else {
			array_table($value);
		}
		print "</td></tr>";
		$row++;
		if ($row==$rows){
			$row=1;
			print "</table></td>";
		}
	}
	if (!($row==$rows)){
		print "</tr></table>";
	}
	print "</tr></table>";
	print "<B>End Debug Info</B></font>";
}
function cmp($a, $b)
{
    return strcasecmp($a, $b);
}

function Display_Query($query, $def=''){
	global $CFG;
	if ($CFG['print_query']==1) {
		print "<br><font color='green'>".$query."</font><br><font color='olive'>".$def."</font><br>";
	}
}
?>