<?php
require_once("config.php"); # load configuration file

if (array_key_exists('ConID',$_GET)){
	$CFG['ConID'] = $_GET['ConID'];
}

if (!(array_key_exists('mode',$_GET))){
	exit();
}
if ($_GET['mode']=='usage'){
	usage();
	exit();
}
header ("Content-Type:text/xml");
$stream=new XMLWriter();
$stream->openURI('php://output');
$stream->setIndent(true);
$stream->setIndentString("     ");
$stream->startDocument('1.0','UTF-8');

$stream->startElement("CPDB-1");

if ($_GET['mode']=='FullReport'){
	condetails();
	listpanels();
	listpresentors();
	listrooms();
	$stream->endElement(); #CPDB-1
	return $stream->outputMemory(true);
}

############################################
if ($_GET['mode']=='Presentor'){
	if(!(array_key_exists('ID',$_GET))){
		$_GET['mode']='exit';
	} else {
		presentor($_GET['ID'],1);
		$stream->endElement(); #CPDB-1
		return $stream->outputMemory(true);
	}
}

if ($_GET['mode']=='ListPresentors'){
	listpresentors();
	$stream->endElement(); #CPDB-1
	return $stream->outputMemory(true);
}
############################################
if ($_GET['mode']=='Panel'){
	if(!(array_key_exists('ID',$_GET))){
		$_GET['mode']='exit';
	} else {
		panel($_GET['ID'],1);
		$stream->endElement(); #CPDB-1
		return $stream->outputMemory(true);
	}
}

if ($_GET['mode']=='ListPanels'){
	listpanels();
	$stream->endElement(); #CPDB-1
	return $stream->outputMemory(true);
}
##############################################
if ($_GET['mode']=='Highlite'){
	highlite(1);
	$stream->endElement(); #CPDB-1
	return $stream->outputMemory(true);
}

if ($_GET['mode']=='ListHighlite'){
	highlite(0);
	$stream->endElement(); #CPDB-1
	return $stream->outputMemory(true);
}


if ($_GET['mode']=='exit'){
	exit();
}

function condetails(){
	global $stream;
	global $CFG;

	$query="select * from CPDB_Convention where ConID = '".$CFG['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);

	$stream->startElement("Convention");

	$stream->startElement("Name");
	$stream->text($row['ConName']);
	$stream->endElement();

	$stream->startElement("StartDate");
	$stream->text($row['ConDate']);
	$stream->endElement();

	$stream->startElement("RunDays");
	$stream->text($row['ConDays']);
	$stream->endElement();

	$stream->startElement("FirstHour");
	$stream->text($row['ConStartHour']);
	$stream->endElement();

	$stream->startElement("LastHour");
	$stream->text($row['ConEndHour']);
	$stream->endElement();

	$stream->startElement("FirstDailyHour");
	$stream->text($row['FirstDailyHour']);
	$stream->endElement();

	$stream->startElement("LastDailyHour");
	$stream->text($row['LastDailyHour']);
	$stream->endElement();

	$stream->endElement();#Convention
}

function listrooms(){
	global $stream;
	global $CFG;

	$query="select *
		from CPDB_Room as R
		inner join CPDB_Zone as Z
		on R.RoomZone = Z.ZoneID
		where R.ConID = '".$CFG['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$stream->startElement("Rooms");

	while($row = mysql_fetch_assoc($sql)){
		$stream->startElement("Room");

			$stream->startElement("Name");
			$stream->text($row['RoomName']);
			$stream->endElement();

			$stream->startElement("Order");
			$stream->text($row['RoomOrder']);
			$stream->endElement();

			$stream->startElement("Hide");
			$stream->text($row['RoomHideGrid']);
			$stream->endElement();

			$stream->startElement("Zone");
			$stream->text($row['ZoneNAme']);
			$stream->endElement();

			$stream->startElement("ID");
			$stream->text($row['']);
			$stream->endElement();

			$stream->startElement("Child1");
			$stream->text($row['RoomChild1ID']);
			$stream->endElement();

			$stream->startElement("Child2");
			$stream->text($row['RoomChild2ID']);
			$stream->endElement();

			$stream->startElement("Child3");
			$stream->text($row['RoomChild3ID']);
			$stream->endElement();

			$stream->startElement("Child4");
			$stream->text($row['RoomChild4ID']);
			$stream->endElement();

			$stream->startElement("Child5");
			$stream->text($row['RoomChild5ID']);
			$stream->endElement();

			$stream->startElement("Child6");
			$stream->text($row['RoomChild6ID']);
			$stream->endElement();

			$stream->startElement("Child7");
			$stream->text($row['RoomChild7ID']);
			$stream->endElement();

			$stream->startElement("Child8");
			$stream->text($row['RoomChild8ID']);
			$stream->endElement();

			$stream->startElement("Child9");
			$stream->text($row['RoomChild9ID']);
			$stream->endElement();

			$stream->startElement("Child10");
			$stream->text($row['RoomChild10ID']);
			$stream->endElement();

			$stream->startElement("SqrFt");
			$stream->text($row['RoomSqr']);
			$stream->endElement();

		$stream->endElement();#Room

	}
	$stream->endElement();#Rooms

}
function highlite($type=1){
	global $stream;
	global $CFG;


	$query="select PanelID from CPDB_Panels where ConID='".$CFG['ConID']."' and PanelHighlited = 1";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$rowcount = mysql_num_rows($sql);
	if ($rowcount==0) {
		return;
	}

	$stream->startElement("PanelList");
	if ($type==1){
		$record = mt_rand(0,$rowcount - 1);

		mysql_data_seek($sql,$record);
		$row=mysql_fetch_assoc($sql);
		$query="select PanelID from CPDB_Panels where PanelID = '".$row['PanelID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	}
	while ($row=mysql_fetch_assoc($sql)){
		panel($row['PanelID'],1);
	}
	$stream->endElement();
}


function listpanels(){
	global $stream;
	global $CFG;
	$stream->startElement("PanelList");

	$query="select PanelID from CPDB_Panels where ConID = '".$CFG['ConID']."' order by PanelID";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		panel($row['PanelID'],1);

	}
	$stream->endElement();
}

function listpresentors(){
	global $stream;
	global $CFG;
	$stream->startElement("PresentorList");

	$query="select PanelistID from CPDB_Panelist order by PanelistID";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		presentor($row['PanelistID'],1);

	}
	$stream->endElement();
}

function panel($panelID, $sub=0){
	global $CFG;
	global $stream;

	$query="select *
			from CPDB_Panels as P
			inner join CPDB_Category as C
			on P.CatID = C.CatID
			inner join CPDB_PTR as T
			on P.PanelID = T.PanelID
			inner join CPDB_Room as R
			on T.RoomID = R.RoomID
			where P.PanelID = '".$panelID."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	if (mysql_num_rows($sql) == 0){
		return;
	}
	$row = mysql_fetch_assoc($sql);
	$query1 = "select P.PanelistID as ID
				from CPDB_Panelist as P
				inner join CPDB_P2P as L
				on L.PanelistID = P.PanelistID
				where L.PanelID = '".$panelID."'";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());

	$stream->startElement("Panel");
		$stream->startElement("PanelID");
		$stream->text($panelID);
		$stream->endElement();

		$stream->startElement("HidePublic");
		$stream->text($row['PanelHidePublic']);
		$stream->endElement();

		$stream->startElement("Title");
		$stream->writeCdata($row['PanelTitle']);
		$stream->endElement();

		$stream->startElement("Category");
		$stream->text($row['Category']);
		$stream->endElement();

		$stream->startElement("Room");
		$stream->text($row['RoomName']);
		$stream->endElement();

		$stream->startElement("StartTime");
		$stream->text($row['Start']);
		$stream->endElement();

		$stream->startElement("EndTime");
		$stream->text($row['End']);
		$stream->endElement();

	if ($sub==1){
			$stream->startElement("Description");
			$stream->writeCdata($row['PanelDescription']);
			$stream->endElement();

			$stream->startElement("Presentors");

		while ($row1 = mysql_fetch_assoc($sql1)) {
			presentor($row1['ID'],0);
		}
		$stream->endElement();
	} else {
			$stream->startElement("FullLink");
			$stream->writeCdata($CFG['webpath']."xml_iface.php?mode=Panel&ConID=".$CFG['ConID']."&ID=".$panelID);
			$stream->endElement();

			$stream->startElement("ShortLink");
			$stream->writeCdata("xml_iface.php?mode=Panel&ConID=".$CFG['ConID']."&ID=".$panelID);
			$stream->endElement();

	}
	$stream->endElement();

}

function presentor($panelistID, $sub=0){
	global $CFG;
	global $stream;

	$query="select * from CPDB_Panelist as P
			inner join CPDB_PanelistCon as C
			on P.PanelistID = C.PanelistID
			where P.PanelistID = '".$panelistID."'
			and C.ConID = '".$CFG['ConID']."'
			and P.IsEquip = 0";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	if (mysql_num_rows($sql) == 0){
		return;
	}
	$row = mysql_fetch_assoc($sql);
	$query1="select P.PanelID,C.Moderator, P.PanelTitle, T.Category, P.PanelHidePublic
			from CPDB_P2P as C
			inner join CPDB_Panels as P
			on C.PanelID = P.PanelID
			inner join CPDB_Category as T
			on P.CatID = T.CatID
			where C.PanelistID = '".$panelistID."'
			and C.ConID = '".$CFG['ConID']."'";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	$stream->startElement("Presentor");
		$stream->startElement("PresentorID");
		$stream->text($panelistID);
		$stream->endElement();

		$stream->startElement("ListMePublicly");
		$stream->text($row['listme']);
		$stream->endElement();

		$stream->startElement("LastName");
		$stream->writeCdata($row['PanelistLastName']);
		$stream->endElement();

		$stream->startElement("FirstName");
		$stream->writeCdata($row['PanelistFirstName']);
		$stream->endElement();

		$stream->startElement("PubName");
		$stream->writeCdata($row['PanelistPubName']);
		$stream->endElement();

		$stream->startElement("BadgeName");
		$stream->writeCdata($row['PanelistBadgeName']);
		$stream->endElement();

	if ($sub==1){

		$stream->startElement("Bio");
		$stream->writeCdata($row['Biography']);
		$stream->endElement();

		$stream->startElement("FullImageLink");
		$stream->text($CFG['webpath']."img.php?pid=".$row['PanelistID']);
		$stream->endElement();

		$stream->startElement("ShortImageLink");
		$stream->text("img.php?pid=".$row['PanelistID']);
		$stream->endElement();

		$stream->startElement("Panels");
		while ($row1 = mysql_fetch_assoc($sql1)) {
			panel($row1['PanelID'],0);
		}
		$stream->endElement(); #Panels
	} else {
			$stream->startElement("FullLink");
			$stream->text($CFG['webpath']."xml_iface.php?mode=Presentor&ConID=".$CFG['ConID']."&ID=".$panelistID);
			$stream->endElement();

			$stream->startElement("ShortLink");
			$stream->text("xml_iface.php?mode=Presentor&ConID=".$CFG['ConID']."&ID=".$panelistID);
			$stream->endElement();

	}
	$stream->endElement(); #Presentor
}

function usage(){
	print"<h1><center>Usage</center></h1>
			<h3>xml_iface.php&mode=<i>XXXXXX</i>&ConID=<i>XX</i>&ID=<i>XXXX</i></h3>
					<h2>required paramaters</h2>
					<h3>mode</h3>
						values<ul>
						<li><b>ListPanels</b> full list of panels for the specified Convention</li>
						<li><b>ListPresentors</b> full list of precentors for the specified Con</li>
						<li><b>ListHighlite</b> Full list of Highlited panels for the specified Con</li>
						<li><b>Panel</b> Details on a specific panel<ul><li><b>ID</b> is required</li></ul></li>
						<li><b>Presentor</b> Details on a specific presentor<ul><li><b>ID</b> is required</li></ul></li>
						<li><b>Highlite</b> Details on a Highlited panel for the specified Con.<ul><li>Randomly selected</li></ul></li>

						</ul>
					<h3>ID</h3>
					required on
					<ul>
						<li><B>Panel</b> specific PanelID for the chosen panel<ul><li><b>ConID</b> has no effect</li></ul></li>
						<li><B>Presentor</b> specific PanelistID for the chosen Presentor<ul><li><b>ConID</b> determines which conventions panels are displayed for this Presentor</li></ul></li>
					</ul>
					Ignored on
					<ul>
						<li>ListPanels</li>
						<li>ListPresentors</li>
					</ul>


					<h3>ConID</h3>
					Defaults to current Convention
					";
}

function charconvert($text){
	return htmlentities($text,ENT_QUOTES,'cp1252');
}
