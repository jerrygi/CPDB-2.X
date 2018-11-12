<?php
require_once("../config.php"); # load configuration file


print"<head><title>Convention Scheduling for ".$CFG['ConName']."</title><LINK REL=StyleSheet HREF='base.css'></head>";
;
print "<div class='main_menu'>";
print "<center><font size=5>Convention Programming Utilities for ".$CFG['ConName']."</font></center>";
require_once("GlobalMenu.php"); # load Global Menu

#if (!array_key_exists("Access",$CFG)) $CFG['Access']="DENY";
if (!(strtoupper($CFG['Access'])=='GRANT')) {
	print "<center><font color='red' size=6>Admin Access Denied</font></center>";
	exit();
}
#$CFG['debug']=1;
debug();
#array_table($CFG);

if (!array_key_exists("Action",$_POST)) $_POST['Action']="";

print "<div class='main_menu'>";
Display_Header_Options();

# Report functions

	if ($_POST["Action"] == 'Panels'){
		Display_Panel_Report();
	}

	if ($_POST["Action"] == 'Bio`s'){
		Display_Bio_Report();
	}

	if ($_POST["Action"] == 'Update Panel'){
		Data_Update_Panel();
		$_POST['Action'] = 'Edit Panels';
	}

	if ($_POST['Action'] == 'Edit Panel'){
		Display_Form_Edit_Panel();
		$_POST['Action'] = 'Edit Panels';
	}

	if ($_POST['Action'] == 'Edit Panels'){
		Display_Panels_to_Edit();
	}

function Data_Update_Panel(){
	global $CFG;
	$query = "select * from `CPDB_Panels` where `PanelID` = '".$_POST['PanelID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$query = "update `CPDB_Panels`
				set `PanelTitle` = '".$_POST['PanelTitle']."',
				`PanelDescription` = '".$_POST['PanelDescription']."'
				where `PanelID` = '".$_POST['PanelID']."'";
	$query1 = "insert into `CPDB_PanelEdits`
				(`EditBy`,
				`EditTime`,
				`PanelID`,
				`Paneltitle`,
				`PanelDescription`,
				`PanelNotes`,
				`CatID`
				) values (
				'".$CFG['USERNAME']."',
				now(),
				'".$_POST['PanelID']."',
				'".$_POST['PanelTitle']."',
				'".$_POST['PanelDescription']."',
				'".$row['PanelNotes']."',
				'".$row['CatID']."'
				)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$sql=mysql_query($query1) or die('Query failed: ' . mysql_error());
}

function Display_Header_Options()
{
	global $CFG;
	print "<table width='1024px' border=1><tr>";
	print "<td><center><form method='post'><input type='submit' name='Action' value='Panels'></form></td>";
	print "<td><center><form method='post'><input type='submit' name='Action' value='Bio`s'></form></td>";
	print "<td><center><form method='post'><input type='submit' name='Action' value='Edit Panels'></form></td>";
	print "</tr></table>";

}

function Display_Form_Edit_Panel(){
	global $CFG;
	print "<div class='MainForm'>";
	print "<table border=1 width='1024px'>";
	$query = "Select * from `CPDB_Panels` as P inner join `CPDB_Category` as C on C.`CatId` = P.`CatID` where `PanelID` = '".$_POST['PanelID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	print "<form method='post'>
			<input type='hidden' name='Action' value='Update Panel'>
			<input type='hidden' name='PanelID' value='".$_POST['PanelID']."'>
	<tr><th>Panel Title</th><td><input type='text' name='PanelTitle' value='".$row['PanelTitle']."' size=50></td></tr>
	<tr><th>Panel Description</th><td><TEXTAREA NAME='PanelDescription' COLS=100 ROWS=20>".$row['PanelDescription']."</TEXTAREA></td></tr>
	<tr><td colspan=2><input type='submit' value='save'></td></th></form></tr>
	</table>";
}

function Display_Panels_to_Edit(){
	global $CFG;
	print "<div class='MainForm'>";
	print "<table border=1 width='1024px'>";
	$query = "Select * from `CPDB_Panels` as P inner join `CPDB_Category` as C on P.`CatID` = C.`CatID` left outer join `CPDB_PTR` as V on V.PanelId = P.PanelID where P.`ConID` = '".$CFG['ConID'].  "' and V.RoomID >=0 and P.PanelHidePublic = 0 order by `Category`,`PanelTitle`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$query1="Select max(EditTime) as EditTime, EditBy, now() as CurTime from `CPDB_PanelEdits` where `PanelID` = '".$row['PanelID']."'";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$row1 = mysql_fetch_assoc($sql1);
		$x = strtotime($row1['CurTime']) - strtotime($row1['EditTime']);
		if ($x <= $CFG['edit time 1']*60*60) {
			$rowcolor=$CFG['edit color 1'];
		} else {
			if ($x <= $CFG['edit time 2']*60*60){
				$rowcolor=$CFG['edit color 2'];
			} else {
				if ($x <= $CFG['edit time 3']*60*60){
					$rowcolor=$CFG['edit color 3'];
				} else {
					$rowcolor=$CFG['edit color base'];
				}
			}
		}


		$editbycolor=$CFG['edit color base'];
		if ($CFG['USERNAME'] == $row1['EditBy']) $editbycolor=$CFG['edit color highlight'];
		print "<tr bgcolor='".$rowcolor."'><form method='post'><input type='hidden' name='Action' value='Edit Panel'><input type='hidden' name='PanelID' value='".$row['PanelID']."'><td><input type='submit' value='edit'></td></form>
				<td bgcolor='".$editbycolor."'>".$row1['EditBy']."</td>
				<td>".$row['Category']."</td>
				<td>".$row['PanelTitle']."</td>
				<td>".$row['PanelDescription']."</td>
				</tr>";
	}
	print "</table>";
}

function Display_Bio_Report(){
	global $CFG;
	print"<head><title>Panelist Bio's for ".$CFG['ConName']."</title></head>";

	$imagescale = 75;

	if (array_key_exists("scale",$_GET)) $imagescale = $_GET['scale'];
	$query = "SELECT distinct P.`PanelistID`, `PanelistPubName`,`PanelistFirstName`, `PanelistLastName`, I.`image_id`, `Biography`
				FROM `CPDB_Panelist` as P
				inner join `CPDB_P2P` as J
				on P.PanelistID = J.PanelistID
				inner join `CPDB_PanelistCon` as C
				on C.`PanelistID` = P.`PanelistID`
				left outer join `CPDB_Image` as I
				on I.PanelistId = P.PanelistID
				where J.`ConID` = '".$CFG['ConID']."'
				and P.IsEquip=0
				order by `PanelistLastName`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	print "<table width='30%' border=1>";
	$panelist_list="";
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><th align='left'>";
		if ($row['PanelistPubName']=='') {
			print $row['PanelistFirstName'] ." " . $row['PanelistLastName'];
			$panelist_list.="<b>".$row['PanelistFirstName'] ." " . $row['PanelistLastName']."</b>";
		} else {
			print $row['PanelistPubName'];
			$panelist_list.="<b>".$row['PanelistPubName']."</b>";
		}

		print "</th></tr>";
		print "<tr><td valign='top'>";
		if ($row['image_id'] > 0) {
			#<
			print "<img src=".$CFG['webpath']."tn_img.php?pid=".$row["PanelistID"]."&scale=".$imagescale." class='floatLeft'>";
		}
		$panelist_list.="<br>";
		if ($row['Biography'] == '') {
			print "<I>No Biography Provided</i>";
			$panelist_list.="<I>No Biography Provided</i>";
		} else {
			print $row['Biography'];
			$panelist_list.=$row['Biography'];
		}
		print "</td></tr><tr><td>&nbsp</td></tr>";
		$panelist_list.="<br><br>";
	}
	print "</table>";
	print $panelist_list;
}

function Display_Panel_Report(){
	global $CFG;
	$index=array('0');
	$dumplist="";
	$query="select * from `CPDB_Panels` as P
			inner join `CPDB_PTR` as S
			on P.`PanelID` = S.`PanelID`
			inner join `CPDB_Room`
			as R on R.`RoomID` = S.`RoomID`
			where P.ConID = '".$CFG['ConID']."' and `PanelHidePublic` = 0 and `CatID` not in (16,28) and PanelTitle <> 'Virtual Masquerade Photo Shoot' order by `Start`, `RoomName`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	print "<table border=0><font size=2>";
	#print "<tr><th>Panel ID</th><th>Duration</th><th>Room</th><th>Title</th><th>Description</th><th>Panelists</th></tr>";
	$lastStart = '';
	while ($row = mysql_fetch_assoc($sql)) {
		array_push($index,$row['PanelID']);
		$reverse = array_flip($index);
		$duration = ( (strtotime($row['End']) - strtotime($row['Start'])) /60/60);
		$PanStart = date("l g:i a",strtotime($row['Start']));
		if (!($PanStart == $lastStart)) {
			print "<tr><td colspan=3 bgcolor='#cccccc'><font size=2>".$PanStart."</td></tr>";
		}else {
			#print "<tr><td colspan=3><hr></td></tr>";
			print "<tr><td colspan=3></td></th>";
		}
		$lastStart = $PanStart;
		print "<tr><td><font size=2><i>".$duration." hours</td><td align='center'><font size=2><I>".$row['RoomName']."</td><td align='right'><font size=2><i>".$reverse[$row['PanelID']]."</td></tr>";
		print "<tr><td colspan=3><font size=2><B>".$row['PanelTitle']."</b></td></tr>";
		if (!($row['PanelDescription']=='')) print "<tr><td colspan=3><font size=2>".$row['PanelDescription']."</td></tr>";
		$Panelists = '';
		$query1 = "select * from `CPDB_P2P` as O inner join `CPDB_Panelist` as P on O.`PanelistID` = P.`PanelistID` where O.`PanelID` = '".$row['PanelID']."' and P.`IsEquip` = 0 order by `Moderator` desc";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		while ($row1 = mysql_fetch_assoc($sql1)) {
			$bestname=$row1['PanelistPubName'];
			if ($bestname=='') {
				$bestname=$row1['PanelistFirstName'] ." ". $row1['PanelistFirstName'];
			}
			$Panelists .= $bestname.", ";
		}
		if (!($Panelists=='')) 	print "<tr><td colspan=3><font size=2>".$Panelists."</td></tr>";
		$dumplist.="<b>".$PanStart." - ".$row['RoomName']." - #".$reverse[$row['PanelID']]."</b><br>";
		$dumplist.="<b>".$row['PanelTitle']."</b><br>";
		$dumplist.=$row['PanelDescription']."<br>";
		$dumplist.=$Panelists."<br><br>";

	}
	print "</table>";
	print "<br><br>";
	print $dumplist;

	print "<br>";
	print "<table border=1>";
	print "<tr><th>Panelist</th><th>Panel`s</th></tr>";
	$query="Select * from `CPDB_Panelist` as P
			inner join `CPDB_PanelistCon` as C
			on P.`PanelistID` = C.`PanelistID`
			inner join CPDB_Invite as I
			on I.PanelistId = P.PanelistID
			where `IsEquip` = 0
			and C.ConID = '".$CFG['ConID']."'
			and I.ConID = '".$CFG['ConID']."'
			and I.InviteState <> 'Unavailable'
			order by `PanelistLastName`";
	$reverse = array_flip($index);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$temp = "";
		$query1="select A.`PanelID` as PanelID1 from `CPDB_P2P` as A inner join `CPDB_PTR` as B on A.`PanelID` = B.`PanelID` where `PanelistID` = '".$row['PanelistID']."' order by `Start`";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		while ($row1 = mysql_fetch_assoc($sql1)) {
			$temp .= $reverse[$row1['PanelID1']].", ";
		}
		$temp1=str_replace(", , ",", ",$temp);
		$temp2=str_replace(", , ",", ",$temp1);
		$temp3=trim($temp2,", ");
		if ($temp3==''){
		} else {
			print "<tr><td>";

			if ($row['PanelistPubName'] == '') {
				print $row['PanelistFirstName']. " ".$row['PanelistLastName'];
			} else {
				print $row['PanelistPubName'];
			}
			print "</td><td>".$temp3."</td></tr>";
		}
	}
	print "</table>";
	print "<br><br>";

	# Readings#
	print "<table border=1>";
	$query = "Select * from `CPDB_Panels` as P
	inner join `CPDB_PTR` as S on P.`PanelID` = S.`PanelID`
	inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID`
	inner join `CPDB_Category` as C on P.`CatId` = C.`CatID`
	where `Category` = 'Reading'
	and P.`ConID` = '".$CFG['ConID']."'
	order by `Start`, `RoomName`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['PanelTitle']."</td><td>".date("l g:i a",strtotime($row['Start']))."</td><td>".$row['RoomName']."</td></tr>";
	}

	print "</table>";
	print "<br><br>";
	# Autograph Sessions #
	print "<table border=1>";
	$query = "Select * from `CPDB_Panels` as P
	inner join `CPDB_PTR` as S on P.`PanelID` = S.`PanelID`
	inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID`
	inner join `CPDB_Category` as C on P.`CatId` = C.`CatID`
	where `Category` = 'Autograph'
	and P.`ConID` = '".$CFG['ConID']."'
	order by `Start`, `RoomName`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['PanelTitle']."</td><td>".date("l g:i a",strtotime($row['Start']))."</td><td>".$row['RoomName']."</td></tr>";
	}

	print "</table>";
	print "<br><br>";
	# Concerts #
	print "<table border=1>";
	$query = "Select * from `CPDB_Panels` as P
	inner join `CPDB_PTR` as S on P.`PanelID` = S.`PanelID`
	inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID`
	inner join `CPDB_Category` as C on P.`CatId` = C.`CatID`
	where `Category` = 'Concert'
	and P.`ConID` = '".$CFG['ConID']."'
	order by `Start`, `RoomName`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['PanelTitle']."</td><td>".date("l g:i a",strtotime($row['Start']))."</td><td>".$row['RoomName']."</td></tr>";
	}
	print "</table>";
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