<?php
require_once("../config.php"); # load configuration file


print"<head><title>Convention Scheduling for ".$CFG['ConName']."</title><LINK REL=StyleSheet HREF='base.css'></head>";
;
print "<div class='main_menu'>";
print "<center><font size=5>Convention Programming Utilities for ".$CFG['ConName']."</font></center>";
require_once("GlobalMenu.php"); # load Global Menu

#if (!array_key_exists("Access",$CFG)) $CFG['Access']="DENY";
if (!(strtoupper($CFG['Access'])=='GRANT')) {
	print "<center><font color='red' size=6>Facilities Administration Access Denied</font></center>";
	exit();
}
#$CFG['debug']=1;
debug();
#array_table($CFG);


if (!array_key_exists("Action",$_POST)) $_POST['Action']="";

print "<div class='main_menu'>";
Display_Header_Options();

# Zone functions

	if ($_POST["Action"] == 'Add Zone'){
		Data_Insert_Zone();
		$_POST['ZoneID'] = 0;
		$_POST["Action"] = 'Zones';
	}

	if ($_POST["Action"] == 'Edit Zone Line'){
		$_POST["Action"] = 'Zones';
	}

	if ($_POST["Action"] == 'Edit Zone'){
		Data_Update_Zone();
		$_POST["ZoneID"] = '0';
		$_POST["Action"] = 'Zones';
	}

	if ($_POST["Action"] == 'Delete Zone'){
		Data_Delete_Zone();
		$_POST["Action"] = 'Zones';
	}

	if ($_POST["Action"] == 'Zones'){
		Display_Zones();
	}

# Rooms functions

	if ($_POST["Action"] == 'Add Room'){
		Data_Insert_Room();
		$_POST["Action"] = 'Rooms';
	}

	if ($_POST["Action"] == 'Edit Room'){
		Form_Edit_Room();
		$_POST["Action"] = 'Rooms';
	}

	if ($_POST["Action"] == 'Update_Room'){
		Data_Update_Room();
		$_POST["Action"] = 'Rooms';
	}

	if ($_POST["Action"] == 'Room Schedule'){
		Display_Room_Schedule();
	}

	if ($_POST["Action"] == 'Rooms'){
		Display_Rooms();
	}

# Sets functions

	if ($_POST["Action"] == 'Add Set'){
		Data_Insert_Set();
		$_POST['SetID'] = 0;
		$_POST["Action"] = 'Sets';
	}

	if ($_POST["Action"] == 'Edit Set Line'){
		$_POST["Action"] = 'Sets';
	}

	if ($_POST["Action"] == 'Edit Set'){
		Data_Update_Set();
		$_POST["SetID"] = '0';
		$_POST["Action"] = 'Sets';
	}

	if ($_POST["Action"] == 'Delete Set'){
		Data_Delete_Set();
		$_POST["Action"] = 'Sets';
	}

	if ($_POST["Action"] == 'Sets'){
		Display_Sets();
	}


function Display_Header_Options()
{
	global $CFG;
	print "<table width='1024px' border=1><tr>";
#	print "<td><center><form method='post'><input type='submit' name='Action' value='Facilities' Disabled></form></td>";
	print "<td><center><form method='post'><input type='submit' name='Action' value='Zones'></form></td>";
	print "<td><center><form method='post'><input type='submit' name='Action' value='Rooms'></form></td>";
	print "<td><center><form method='post'><input type='submit' name='Action' value='Sets'></form></td>";
	print "</tr></table>";

}
function Display_Sets(){
	global $CFG;
	$query = "select * from `CPDB_RoomSets` where `ConID` = '".$CFG['ConID']."' order by `SetName`";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	print "<table border=1>\r\n<tr>";
	if ($CFG['dispID'] == 1) print "<th>Set<br>ID</th>";
	print "<th>Set Name</th><th>Rooms in<br>Zone</th><td></td></tr>";
	while ($row = mysql_fetch_assoc($sql)) {
		print "\r\n<tr>";
		$query1="select count(*) as tally from `CPDB_PTR` where `SetID` = '".$row['SetID']."'";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$row1 = mysql_fetch_assoc($sql1);
		if ($CFG['dispID'] == 1) print "\r\n\t<td>".$row['SetID']."</td>";
		if ($row['SetID']==$_POST['SetID']) {
			print "	</td>
					\r\n\t<form method='post'>
					\r\n\t\t<td><input type='text' name='SetName' value='".$row['SetName']."'></td>
					\r\n\t<td>".$row1['tally']."</td>
					\r\n\t\t<td><input type='hidden' name='Action' value='Edit Set'>
					\r\n\t\t<input type='submit' name='Save' value='save'></td>
					\r\n\t\t<input type='hidden' name='SetID' value='".$_POST['SetID']."'></form>";
		} else {
			print "\r\n\t<td>".$row['SetName']."</td>
					\r\n\t<td>".$row1['tally']."</td>";
			print "\r\n\t<td><form method='Post'>\r\n\t\t<input type='hidden' name='SetID' value='".$row['SetID']."'>
					\r\n\t\t<input type='Submit' name='Submit' value='Edit'>
					\r\n\t\t<input type='hidden' name='Action' value='Edit Set Line'></form>";
			if ($row1['tally'] == 0) print "\r\n\t<form method='post'>\r\n\t\t<input type='hidden' name='SetID' value='".$row['SetID']."'>
						\r\n\t\t<input type='hidden' name='Action' value='Delete Set'>
						\r\n\t\t<input type='Submit' value='Delete' Name='Submit'></form>";
			print "</td>";
		}
		print "</tr>";
	}
	print "\r\n<tr>\r\n\t<td></td><form method='post'><td><input type='text' name='SetName'></td>
				\r\n\t<td><input type='submit' value='Add Set' name='Action'></td></form></tr>";
	print "\r\n</table>";
}

function Display_Zones(){
	global $CFG;
	$query = "select * from `CPDB_Zone` where `ConID` = '".$CFG['ConID']."' order by `ZoneName`";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	print "<table border=1>\r\n<tr>";
	if ($CFG['dispID'] == 1) print "<th>Zone<br>ID</th>";
	print "<th>Zone Name</th><th>Rooms in<br>Zone</th><td></td></tr>";
	while ($row = mysql_fetch_assoc($sql)) {
		print "\r\n<tr>";
		$query1="select count(*) as tally from `CPDB_Room` where `RoomZone` = '".$row['ZoneID']."'";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$row1 = mysql_fetch_assoc($sql1);
		if ($CFG['dispID'] == 1) print "\r\n\t<td>".$row['ZoneID']."</td>";
		if ($row['ZoneID']==$_POST['ZoneID']) {
			print "	</td>
					\r\n\t<form method='post'>
					\r\n\t\t<td><input type='text' name='ZoneName' value='".$row['ZoneName']."'></td>
					\r\n\t<td>".$row1['tally']."</td>
					\r\n\t\t<td><input type='hidden' name='Action' value='Edit Zone'>
					\r\n\t\t<input type='submit' name='Save' value='save'></td>
					\r\n\t\t<input type='hidden' name='ZoneID' value='".$_POST['ZoneID']."'></form>";
		} else {
			print "\r\n\t<td>".$row['ZoneName']."</td>
					\r\n\t<td>".$row1['tally']."</td>";
			print "\r\n\t<td><form method='Post'>\r\n\t\t<input type='hidden' name='ZoneID' value='".$row['ZoneID']."'>
					\r\n\t\t<input type='Submit' name='Submit' value='Edit'>
					\r\n\t\t<input type='hidden' name='Action' value='Edit Zone Line'></form>";
			if ($row1['tally'] == 0) print "\r\n\t<form method='post'>\r\n\t\t<input type='hidden' name='ZoneID' value='".$row['ZoneID']."'>
						\r\n\t\t<input type='hidden' name='Action' value='Delete Zone'>
						\r\n\t\t<input type='Submit' value='Delete' Name='Submit'></form>";
			print "</td>";
		}
		print "</tr>";
	}
	print "\r\n<tr>\r\n\t<td></td><form method='post'><td><input type='text' name='ZoneName'></td>
				\r\n\t<td><input type='submit' value='Add Zone' name='Action'></td></form></tr>";
	print "\r\n</table>";
}


function Display_Rooms()
{
	global $CFG;
	$RoomAray["0"]=" ";
	$query="Select * from `CPDB_Room` as R inner join `CPDB_Zone` as Z on R.`RoomZone` = Z.`ZoneID` where R.`ConID` = '".$CFG['ConID']."' order by `RoomOrder`, `RoomName`";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		if (($row["RoomChild1ID"]=='0')
			&&($row["RoomChild2ID"]=='0')
			&&($row["RoomChild3ID"]=='0')
			&&($row["RoomChild4ID"]=='0')
			&&($row["RoomChild5ID"]=='0')
			&&($row["RoomChild6ID"]=='0')
			&&($row["RoomChild7ID"]=='0')
			&&($row["RoomChild8ID"]=='0')
			&&($row["RoomChild9ID"]=='0')
			&&($row["RoomChild10ID"]=='0')){
			$RoomAray[$row["RoomID"]]=$row["RoomName"];
		}
	}


	print "<div class='datareport'>";
	print "<table><tr><td valign='top'><center><font size=4>Rooms</font></center>";
	print "<Table border=1><tr><td></td>";
	if ($CFG['dispID'] == 1) print "<td>Room<br>ID</td>";
	print "<td>Panels<br>Scheduled</td>";
	print "<td>Room Name</td>";
	print "<td>Room<br>Sqr. Ft.</td>";
	print "<td>Child 1</td>";
	print "<td>Child 2</td>";
	print "<td>Child 3</td>";
	print "<td>Child 4</td>";
	print "<td>Child 5</td>";
	print "<td>Child 6</td>";
	print "<td>Child 7</td>";
	print "<td>Child 8</td>";
	print "<td>Child 9</td>";
	print "<td>Child 10</td>";
	print "<td>Room Order</td>";
	print "<td>Room Zone</td>";
	print "<td>Hide Grid</td></tr>";

	$myrowcolor='#ffff88';
	mysql_data_seek($sql,0);
	while ($row = mysql_fetch_assoc($sql)) {
		if ($myrowcolor=='#ffff88') {
			$myrowcolor='#808080';
		} else {
			$myrowcolor='#ffff88';
		}
		print "\n\r<tr bgcolor='".$myrowcolor."'>";
		$query1="select * from `CPDB_PTR` where `RoomID` = '".$row['RoomID']."'";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$ttlrow = mysql_num_rows($sql1);
		if ($ttlrow == 0) {
			#print "<form method='post'><td rowspan=2><input type='submit' name='submit' value='Remove Room'><input type='hidden' name='Action' value='Remove Room'><input type='hidden' name='RoomID' value='".$row["RoomID"]."'></td></form>";
			print "<td rowspan=2>&nbsp;</td>";
		} else {
			print "<form method='post'><td rowspan=2><input type='submit' name='submit' value='Room Schedule'></td><input type='hidden' name='Action' value='Room Schedule'><input type='hidden' name='RoomID' value='".$row["RoomID"]."'></form>";
		}
		$query2="select count(*) as Tally from `CPDB_PTR` where `RoomID` = '".$row['RoomID']."' ";
		$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		if ($CFG['dispID'] == 1) print "<td>".$row["RoomID"]."</td>";
		print "<td>".$row2['Tally']."</td>";
		print "<td>".$row["RoomName"]."</td>";
		print "<td>".$row["RoomSqr"]."</td>";
		print "<td>".$RoomAray[$row["RoomChild1ID"]]."</td>";
		print "<td>".$RoomAray[$row["RoomChild2ID"]]."</td>";
		print "<td>".$RoomAray[$row["RoomChild3ID"]]."</td>";
		print "<td>".$RoomAray[$row["RoomChild4ID"]]."</td>";
		print "<td>".$RoomAray[$row["RoomChild5ID"]]."</td>";
		print "<td>".$RoomAray[$row["RoomChild6ID"]]."</td>";
		print "<td>".$RoomAray[$row["RoomChild7ID"]]."</td>";
		print "<td>".$RoomAray[$row["RoomChild8ID"]]."</td>";
		print "<td>".$RoomAray[$row["RoomChild9ID"]]."</td>";
		print "<td>".$RoomAray[$row["RoomChild10ID"]]."</td>";
		print "<td>".$row["RoomOrder"]."</td>";
		print "<td>".$row['ZoneName']."</td>";
		print "<td>".$row['RoomHideGrid']."</td>";
		print "</tr>\n\r";
		if ($CFG['dispID'] == 1) {
			print "<tr bgcolor='".$myrowcolor."'><td colspan=17>".$row['RoomNotes']."</td></tr>";
		} else {
			print "<tr bgcolor='".$myrowcolor."'><td colspan=16>".$row['RoomNotes']."</td></tr>";
		}
		print "\n\r";
	}
	print "</table>";
	print "<div class='secform'>";
	if ($CFG['dispID'] == 1) print "<table><tr><td colspan=4><form method='post'><input type='submit' name='submit' value='Edit Room'><input type='hidden' name='Action' value='Edit Room'><input type='text' name='RoomID' size=3></form></td></tr>";
	print "</table>";


	print "</td><td valign='top'>";
	print "<center><font size=4>Insert new Room</font></center>";
	$options = "";
	$RoomAray["0"]="-no child room-";
	foreach ($RoomAray as $key => $value){
		/*<*/
		$options .= "<option value='".$key."'>".$value."</option>\r\n";
	}
	Form_Add_Room( $options);

	print "</td></tr></table>";
}

function Form_Add_Room ($options)
{
global $CFG;


	$query="Select * from `CPDB_Zone` where `ConID` = '".$CFG['ConID']."' order by `ZoneName`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$zone = '';
	while ($row = mysql_fetch_assoc($sql)) {
		$zone .= "<option value='".$row['ZoneID']."'>".$row['ZoneName']."</option>";
	}
	print "<div class='MainForm'>";
	print "<table border=1><form method='post'>";
	print "<tr><td>Room Name</td><td><input type='text' name='RoomName'></td></tr>";
	print "<tr><td>Room Sqr. Ft.</td><td><input type='text' name='RoomSqr'></td></tr>";
	print "<tr><td>Room Order</td><td><input type='text' name='RoomOrder'></td></tr>";
	print "<tr><td>Child Room 1</td><td><select name='ChildRoom1ID'>".$options."</select></td></tr>";
	print "<tr><td>Child Room 2</td><td><select name='ChildRoom2ID'>".$options."</select></td></tr>";
	print "<tr><td>Child Room 3</td><td><select name='ChildRoom3ID'>".$options."</select></td></tr>";
	print "<tr><td>Child Room 4</td><td><select name='ChildRoom4ID'>".$options."</select></td></tr>";
	print "<tr><td>Child Room 5</td><td><select name='ChildRoom5ID'>".$options."</select></td></tr>";
	print "<tr><td>Child Room 6</td><td><select name='ChildRoom6ID'>".$options."</select></td></tr>";
	print "<tr><td>Child Room 7</td><td><select name='ChildRoom7ID'>".$options."</select></td></tr>";
	print "<tr><td>Child Room 8</td><td><select name='ChildRoom8ID'>".$options."</select></td></tr>";
	print "<tr><td>Child Room 9</td><td><select name='ChildRoom9ID'>".$options."</select></td></tr>";
	print "<tr><td>Child Room 10</td><td><select name='ChildRoom10ID'>".$options."</select></td></tr>";
	print "<tr><td>Notes</td><td><textarea name='RoomNotes'></textarea></td></tr>";
	print "<tr><td>Room Zone</td><td><select name='RoomZone'>".$zone."</select></td></tr>";
	print "<tr><td>Hide on Grid</td><td><input type='radio' name='RoomHideGrid' value='0' Checked>NO<br><input type='radio' name='RoomHideGrid' value='1'>Yes</td></tr>";
	print "<tr><td colspan=2><center><input type='submit' name='Add Room' value='Add Room'><input type='hidden' name='Action' value='Add Room'></td></form></tr>";
	print "</table>";
}

function Form_Edit_Room()
{
	global $CFG;
	print "<div class='MainForm'>";
	$query= "Select * from `CPDB_Room` where `ConID` = '".$CFG['ConID']."'";
	$query1= "Select * from `CPDB_Room` where RoomID = '".$_POST['RoomID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	$row1 = mysql_fetch_assoc($sql1);

	$query2="Select * from `CPDB_Zone` where `ConID` = '".$CFG['ConID']."' order by `ZoneName`";
	$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
	$zone = '';
	while ($row2 = mysql_fetch_assoc($sql2)) {
		$zone .= "<option value='".$row2['ZoneID']."'";
		if ($row1['RoomZone']==$row2['ZoneID']) $zone .= " selected ";
		$zone .= ">".$row2['ZoneName']."</option>";
	}

	print "<table border=1><form method='post'><tr><td>";
	print "<table><tr><td>Room Name</td><td><input type='text' name='RoomName' value='".$row1['RoomName']."'></td></tr>";
	print "<tr><td>Room Sqr Ft.</td><td><input type='text' name='RoomSqr' value='".$row1['RoomSqr']."'></td></tr>";
	print "<tr><td>Room Notes</td><td><textarea name='RoomNotes' rows=7 cols=30>".$row1['RoomNotes']."</textarea></td></tr>";
	print "<tr><td>Room Order</td><td><input type='text' name='RoomOrder' value='".$row1['RoomOrder']."'></td></tr>";
	print "<tr><td>Room Zone</td><td><select name='RoomZone'>".$zone."</select></td></tr>";
	print "<tr><td>Hide from grid</td><td><input type='radio' name='RoomHideGrid' value='0'";
	if ($row1['RoomHideGrid']=='0') print " Checked ";
	print "                                >No<br><input type='radio' name='RoomHideGrid' value='1'";
	if ($row1['RoomHideGrid']=='1') print " Checked ";
	print"								   >Yes</td></tr>";
	print "</table></td><td><table border=1>";
	for ($i = 1; $i <= 10; $i ++ ){
		$rowname= "RoomChild".$i."ID";
		#print $rowname;
		print "<tr><td> Child Room ".$i."</td><td><select name='ChildRoom".$i."ID'>";
		print "<option value='0' selected></option>";
		while ($row = mysql_fetch_assoc($sql)) {
			print "<option value='".$row['RoomID']."'";

			if ($row['RoomID'] == $row1[$rowname]) print " selected ";
			print ">".$row['RoomName']."</option>\r\n";
		}
		print "</select></td></tr>";
		mysql_data_seek($sql,0);
	}
	print "</table></tr><tr><td colspan=2>";
	print "<input type='submit' name='Save Changes' value='Save Changes'><input type='hidden' name='Action' value='Update_Room'><input type='hidden' name='RoomID' value='".$_POST['RoomID']."'></form>";
	print "</td></tr></table>";

}

function 	Display_Room_Schedule()
{
	global $CFG;
	######
	# Display Room Alocation
	######
	$query="select * from `CPDB_Room` as R left outer join `CPDB_Zone` as Z on Z.ZoneID = R.RoomZone where `RoomID` = '".$_POST['RoomID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	print "<table border=1><tr><th colspan=3><center>".$row['RoomName']."<br>".$row['ZoneName']."</th></tr>";
	$startDTS = strtotime($CFG['constartdate']);
	for ($i = $startDTS; $i <= ($startDTS +(60*60*24* $CFG['conrundays'] )); $i=$i+(60*30)) {
		$curDate = date("Y-m-d G:i:s",$i);
		$dispDate = date("Y-m-d ".$CFG['TimeFormat'],$i);
		print "<tr><td>".$dispDate."</td>";
		$query="select * from `CPDB_PTR` as P inner join `CPDB_Panels` as E on P.`PanelID` = E.`PanelID` left outer join `CPDB_RoomSets` as S on S.`SetID` = P.`SetID` where `RoomID` = '".$_POST['RoomID']."' and P.`Start` = '".$curDate."' order by `Start`, `End`";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_assoc($sql)) {
			$rowsp = (strtotime($row['End']) - strtotime($row['Start']))/1800;
			print"<td rowspan='".$rowsp."' bgcolor='#bbbbbb'>".$row['PanelTitle']."<br><font color='blue'>Set = ".$row['SetName']."</font></td></form>";
		}
		print "</tr>";
	}
	print "</table>";
}




function Data_Update_Room()
{
	global $CFG;
	$query="Update `CPDB_Room`
			set `ConID` = 		'".$CFG['ConID']."',
			`RoomName` = 		'".$_POST['RoomName']."',
			`RoomSqr` = 		'".$_POST['RoomSqr']."',
			`RoomChild1ID` = 	'".$_POST['ChildRoom1ID']."',
			`RoomChild2ID` = 	'".$_POST['ChildRoom2ID']."',
			`RoomChild3ID` = 	'".$_POST['ChildRoom3ID']."',
			`RoomChild4ID` = 	'".$_POST['ChildRoom4ID']."',
			`RoomChild5ID` = 	'".$_POST['ChildRoom5ID']."',
			`RoomChild6ID` = 	'".$_POST['ChildRoom6ID']."',
			`RoomChild7ID` = 	'".$_POST['ChildRoom7ID']."',
			`RoomChild8ID` = 	'".$_POST['ChildRoom8ID']."',
			`RoomChild9ID` = 	'".$_POST['ChildRoom9ID']."',
			`RoomChild10ID` =	'".$_POST['ChildRoom10ID']."',
			`RoomNotes` =		'".$_POST['RoomNotes']."',
			`RoomOrder` = 		'".$_POST['RoomOrder']."',
			`RoomHideGrid` = 	'".$_POST['RoomHideGrid']."',
			`RoomZone` = 		'".$_POST['RoomZone']."'
			where `RoomID` = '".$_POST['RoomID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Insert_Room()
{
	global $CFG;
	$query="insert into `CPDB_Room`
		(
		`ConID` ,
		`RoomName` ,
		`RoomSqr` ,
		`RoomChild1ID` ,
		`RoomChild2ID` ,
		`RoomChild3ID` ,
		`RoomChild4ID` ,
		`RoomChild5ID` ,
		`RoomChild6ID` ,
		`RoomChild7ID` ,
		`RoomChild8ID` ,
		`RoomChild9ID` ,
		`RoomChild10ID`,
		`RoomNotes` ,
		`RoomOrder` ,
		`RoomHideGrid` ,
		`RoomZone`
		) values (
		'".$CFG["ConID"]."',
		'".$_POST["RoomName"]."',
		'".$_POST["RoomSqr"]."',
		'".$_POST["RoomChild1ID"]."',
		'".$_POST["RoomChild2ID"]."',
		'".$_POST["RoomChild3ID"]."',
		'".$_POST["RoomChild4ID"]."',
		'".$_POST["RoomChild5ID"]."',
		'".$_POST["RoomChild6ID"]."',
		'".$_POST["RoomChild7ID"]."',
		'".$_POST["RoomChild8ID"]."',
		'".$_POST["RoomChild9ID"]."',
		'".$_POST["RoomChild10ID"]."',
		'".$_POST["RoomNotes"]."',
		'".$_POST["RoomOrder"]."',
		'".$_POST["RoomHideGrid"]."',
		'".$_POST["RoomZone"]."'
		)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['RoomID'] = mysql_insert_id();
}

function Data_Delete_Room(){
	$query = "delete from `CPDB_Room` where `RoomID` = '".$_POST['RoomID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Update_Zone(){
	global $CFG;
	$query="update `CPDB_Zone`
			set `ZoneName` = '".$_POST['ZoneName']."'
			where `ZoneID` = '".$_POST['ZoneID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Insert_Zone(){
	global $CFG;
	$query = "insert into `CPDB_Zone`
				(
				`ZoneName`,
				`ConID`
				) values (
				'".$_POST['ZoneName']."',
				'".$CFG['ConID']."'
				)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['ZoneID'] = mysql_insert_id();
}

function Data_Delete_Zone(){
	global $CFG;
	$query="select * from `CPDB_Room` where `RoomZone` = '".$_POST['ZoneID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	if (mysql_num_rows($sql) == 0){
		$query = "delete from `CPDB_Zone` where `ZoneID` = '".$_POST['ZoneID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	} else {
		print "<font color='red'>Can not delete that Zone, rooms are still assigned to it</font><br>";
	}
}

function Data_Update_Set(){
	global $CFG;
	$query="update `CPDB_RoomSets`
			set `SetName` = '".$_POST['SetName']."'
			where `SetID` = '".$_POST['SetID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Insert_Set(){
	global $CFG;
	$query = "insert into `CPDB_RoomSets`
				(
				`SetName`,
				`ConID`
				) values (
				'".$_POST['SetName']."',
				'".$CFG['ConID']."'
				)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['SetID'] = mysql_insert_id();
}

function Data_Delete_Set(){
	global $CFG;
	$query="select * from `CPDB_PTR` where `SetID` = '".$_POST['SetID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	if (mysql_num_rows($sql) == 0){
		$query = "delete from `CPDB_RoomSets` where `SetID` = '".$_POST['SetID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	} else {
		print "<font color='red'>Can not delete that Set, There are still panels using it</font><br>";
	}
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

function Display_Query($query){
	global $CFG;
	if ($CFG['print_query']==1) {
		print "<br><font color='green'>".$query."</font><br><br>";
	}
}

?>