<?php
require_once("../config.php"); # load configuration file

print"<head>
<title>Convention Scheduling for ".$CFG['ConName']."</title>
<LINK REL=StyleSheet HREF='".$CFG['CSS']."'>
	<script type='text/javascript' src='javascript/common.js'></script>
	<script type='text/javascript' src='javascript/css.js'></script>
	<script type='text/javascript' src='javascript/standardista-table-sorting.js'></script>
	<script type='text/javascript' src='../tinymce/js/tinymce/tinymce.min.js'></script>
	<script type='text/javascript'>
	tinymce.init({
	    selector: 'textarea',
	    entity_encoding : 'named',
	    browser_spellcheck : true,
	    menubar : false,
	    statusbar : true,
	    toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect | cut copy paste | link'

	 });
</script>
	<!--<script src='sorttable.js'></script>-->

</head>";
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

	if ($_POST["Action"] == 'Reports'){
		Display_Reports_Options();
	}

	if ($_POST['Action']=='Clear Report'){
		# Do not print the Buttons on the top of the page
		if (($_POST['submit']=='Panelist Itineraries') or ($_POST['SubAction']=='Print Panelist Schedule')){
			$temp = Report_Panelist_Itinerary ($_POST['PanelistID'], $_POST['DisplayContact']);
			print $temp;
		}
		if ($_POST['submit']=='Room Itineraries'){
			Report_Room_Itinerary($_POST['RoomID']);
		}
		if ($_POST['SubAction']=='Email Panelist Schedule') {
			Are_You_Sure();
		}

	} else {
		Display_Refresh_Button();
		print "</div>";
	}

	if ($_POST["Action"] == 'Bulk Email Itineraries'){
		if (strtolower($_POST['confirm'])=='yes') {
			Send_Bulk_Email_Itineraries();
		}
	}
# Panel Functions

	if ($_POST['Action'] == 'PanelLockToggle'){
		Data_Togle_Lock_Panel();
		$_POST['Action'] = 'Panel Description';
	}

	if ($_POST['Action'] == 'NextPanel'){
		Next_Panel();
		$_POST['Action'] = 'Panel Description';
	}

	if ($_POST['Action'] == 'Update Panel'){
		Data_Update_Panels();
		$_POST['Action'] = 'Panel Description';
	}

	if ($_POST['Action'] == 'Clone Panel'){
		Data_Clone_Panel();
		$_POST['Action'] = 'Panel Description';
	}

	if ($_POST['Action'] == 'View Panel Edits'){
		View_Panel_Edits();
		$_POST['Action'] = 'Panel Description';
	}

	if ($_POST['Action'] == 'Delete Panel') {
		Data_Delete_Panels();
		Data_Delete_Ptr_by_PanelID();
		$_POST["Action"] = 'Panels';
	}

	if ($_POST['Action'] == 'Add Panel'){
		$_POST["PanelSolo"] = 0;
		$_POST["PanelApproved"] = 0;
		$_POST["PanelTech"] = 0;
		Data_Insert_Panels();
		$_POST['Action'] = 'Panel Description';
	}

	if ($_POST['Action'] == "Update Schedule"){
		Data_Generate_Ptr();
		$_POST['Action'] = 'Panel Description';
	}

	if ($_POST['Action'] == "Unschedule"){
		Data_Delete_Ptr();
		$_POST['Action'] = 'Panel Description';
	}

	if ($_POST['Action'] == 'Make Moderator'){
		Data_Toggle_Moderator();
		$_POST["Action"] = 'Panel By ID';
	}

	if ($_POST["Action"] == 'Panel By ID'){
		Display_Panel_Description();
	}

	if ($_POST['Action'] == 'Panel Description'){
		Display_Panel_Description();
	}
	if ($_POST["Action"] == 'Panels'){
		Form_Add_Panel();
		Display_Panel_Key();
		Display_Panel_List();
	}

# Panelist functions

	if ($_POST['Action'] == 'Update Panelist'){
		Data_Update_Panelist();
		Data_Update_Panelistcon();
		Data_Generate_Availability();
		Data_Generate_Maxpanels();
		$_POST['Action']="Panelist Detail";
	}

	if ($_POST['Action'] == 'Update Image'){
		save_image();
		$_POST["Action"] = 'Panelist Detail';
	}

	if ($_POST['Action'] == 'Insert Panelist Ranking'){
		Data_Insert_Panelranking();
		$_POST["Action"] = 'Panelist Detail';
	}

	if ($_POST['Action'] == 'Add Panelist 2 Panel'){
		if (!array_key_exists("Moderator",$_POST)) $_POST['Moderator']='0';
		Data_Insert_P2P();
		Display_Panel_Description();
	}

	if ($_POST['Action']== 'Create and Add Panelist'){
		Data_Insert_Panelist_Short();
		$_POST['Action']='Add Panelist 2 Panel and Rank';
	}

	if ($_POST['Action'] == 'Add Panelist 2 Panel and Rank'){
		if (!array_key_exists("Moderator",$_POST)) {
			$_POST['Moderator']='0';
			$_POST['Moderate']='0';
		}
		if (!array_key_exists("Rank",$_POST)) $_POST['Rank']='6';
		Data_Insert_P2P();
		Data_Insert_Panelranking();
		Display_Panel_Description();
	}

	if ($_POST['Action'] == 'Remove Panelist From Panel'){
		Data_Delete_P2P();
		Display_Panel_Description();
	}

	if ($_POST["Action"] == 'Panelist By ID'){
		Display_Panelist_Detail();
	}

	if ($_POST["Action"] == 'Add Panelist'){
##
	}

	if ($_POST["Action"] == 'Panelist'){
		Form_Add_Panelist();
		Display_Panelist_List();
		Display_Panelist_Key();
	}

	if ($_POST["Action"] == 'Panelist Detail'){
		Display_Panelist_Detail();
	}

# Grid functions
	if ($_POST["Action"] == 'Display Grid'){
		Display_Grid();
	}

	if ($_POST["Action"] == 'Schedule Conflict Detail'){
		Display_Schedule_Conflict_Detail();
	}
# Equipment based actions begin here
	if ($_POST['Action']=='Add Equipment'){
		Data_Add_Equipment();
		$_POST['Action']='Equipment';
		$_POST['PanelistID'] = 0;
	}
	if ($_POST['Action']=='Activate Equipment'){
		Data_Insert_Panelistcon();
		$_POST['Action']='Equipment';
		$_POST['PanelistID'] = 0;
	}

	if ($_POST['Action']=='Edit Equipment'){
		$_POST['Action']='Equipment';
	}

	if ($_POST['Action']=='Disable Equipment'){
		Data_Disable_Equipment();
		$_POST['Action']='Equipment';
		$_POST['PanelistID'] = 0;
	}

	if ($_POST['Action']=='Update Equipment'){
		Data_Update_Equipment();
		$_POST['Action']='Equipment';
		$_POST['PanelistID'] = 0;
	}

	if ($_POST['Action']=='Equipment'){
		Display_Equipment();
	}


# Availability Matrix Functions Begin Here
	if ($_POST['Action']=="Availability Matrix"){
		AvailabilityMatrix();
	}



# Facilities Based Actions begin here
#	if ($_POST["Action"] == 'Add Room'){
#		Data_Insert_Room();
#		$_POST["Action"] = 'Facilities';
#	}
#
#	if ($_POST["Action"] == 'Edit Room'){
#		Form_Edit_Room();
#		$_POST["Action"] = 'Facilities';
#	}
#
#	if ($_POST["Action"] == 'Update_Room'){
#		Data_Update_Room();
#		$_POST["Action"] = 'Facilities';
#	}
#
#	if ($_POST["Action"] == 'Facilities'){
#		Display_Facilities();
#	}

print "<br><br><br>";




function Send_Bulk_Email_Itineraries()
{
	print "</tr></table>";
	global $CFG;
	## Select only Scheduled Panelists who have emails;
	$query="select * from `CPDB_Panelist`
			where PanelistID in (
				select Distinct PanelistID from `CPDB_P2P` where ConID = '".$CFG['ConID']."')
			and IsEquip=0
			and PanelistEmail <> ''
			order by PanelistName";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$letter = file_get_contents('../text/'.$CFG['Itinerarie_Letter']);
	while ($row = mysql_fetch_assoc($sql)){

		$eol="\r\n";

		# Common Headers
		$headers = "From: ".$CFG['ConName']." Programming Department <".$CFG['srcEmail'].">".$eol;
		$headers .= "BCC:".$CFG['BCC_Address']."".$eol;
		$headers .= "Reply-To:".$CFG['ConName']." Programming Department <".$CFG['replyToEmail'].">".$eol;
		$headers .= "Return-Path: ".$CFG['srcEmail'].$eol;
		$headers .= "Message-ID: <".$now."TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
		$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";

		$wrkltr = $letter;
		$pos=strpos($wrkltr,"[SUBJECT]");
		$pos1=strpos($wrkltr,"[/SUBJECT]");
		$subject = substr($wrkltr,$pos + 9,$pos1-$pos - 9);
		$wrkltr = str_replace("[InviteName]", $row['PanelistFirstName'], $wrkltr);
		$wrkltr = str_replace("[SUBJECT]".$subject."[/SUBJECT]","",$wrkltr);

		#print $row['PanelistEmail']. $subject. "<head></head><body>".$wrkltr. $headers. '-f'.$CFG['srcEmail']."<br><br>";
		print "<br>Sending Itinerarie to".$row['PanelistName']." at ".$row['PanelistEmail']."<br>";

		$wrkltr .= Report_Panelist_Itinerary ($row['PanelistID'], $_POST['DisplayContact']);

		mail ($row['PanelistEmail'], $subject, $wrkltr, $headers, "-f".$CFG['ConName']." Programming Department <".$CFG['srcEmail'].">");

		#imap_mail($row['PanelistEmail'], $subject, $wrkltr, $headers);
		##imap_mail($row['PanelistEmail'], $subject, $wrkltr, $headers, "-f ".$CFG['ConName']." Programming Department <".$CFG['srcEmail'].">");

		print "Mail Sent to  ".$row['PanelistName']." at ".$row['PanelistEmail']."<br>";
	}

	$query="select * from `CPDB_Panelist`
			where PanelistID in (
				select Distinct PanelistID from `CPDB_P2P` where ConID = '".$CFG['ConID']."')
			and IsEquip=0
			and PanelistEmail = ''
			order by PanelistName";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)){
		print "<br>Unable to send Itinerarie to ".$row['PanelistName']." No email listed";
	}

}

function save_image()
{
	global $CFG;
	if ($_FILES)
	{
		$image_types = Array ("image/bmp",
		"image/jpeg",
		"image/pjpeg",
		"image/gif",
		"image/x-png");
		if (is_uploaded_file ($_FILES["userfile"]["tmp_name"]))
		{
			$userfile  = addslashes (fread
			(fopen ($_FILES["userfile"]["tmp_name"], "r"),
			filesize ($_FILES["userfile"]["tmp_name"])));
			$file_name = $_FILES["userfile"]["name"];
			$file_size = $_FILES["userfile"]["size"];
			$file_type = $_FILES["userfile"]["type"];
			if ($debug==1)
			{
				print $file_name ."<br>";
				print $file_size ."<br>";
				print $file_type ."<br>";
				print $userfile ."<br>";
			}
			if (in_array (strtolower ($file_type), $image_types))
			{
				$query="select * from CPDB_Image where PanelistID = '".$_POST['PanelistID']."'";
				$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
				if (mysql_num_rows($sql)==0)
				{
					$query = "INSERT INTO `CPDB_Image` "
					. "(`PanelistID`,`image_type`, `image`, `image_size`, `image_name`, `image_date`) ";
					$query.= "VALUES (";
					$query.= "'".$_POST['PanelistID']."',";
					$query.= "'{$file_type}', '{$userfile}', '{$file_size}', "
					. "'{$file_name}', NOW())";
				} else {
					$row = mysql_fetch_assoc($sql);
					$query="update CPDB_Image
					Set`image_type` = '".$file_type."',
					`image` = '".$userfile."',
					`image_size` = '".$file_size."',
					`image_name`= '".$file_name."',
					`image_date`= NOW()
					where image_id = '".$row['image_id']."'";
				}
				$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			}
		}
	}
}

function Display_Equipment(){
	global $CFG;
	$query="select * from `CPDB_Panelist` where `IsEquip` = 1";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	print "<div class='MainForm'>";
	print "<table border=1>";
	print "<tr><td></td>";
	if ($CFG['dispID']==1) print "<th>Equipment<br>ID</th>";
	print "<th>Item</th><th>Owner</th><th>Panels<br>Scheduled</th></tr>";
	while ($row = mysql_fetch_assoc($sql)){
		$query1="Select * from `CPDB_PanelistCon` where `PanelistID` = '".$row['PanelistID']."' and `ConID` = '".$CFG['ConID']."'";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$tally = mysql_num_rows($sql1);
		$query1="select * from `CPDB_P2P` where `PanelistID` = '".$row['PanelistID']."' and `ConID` = '".$CFG['ConID']."'";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$tally1 = mysql_num_rows($sql1);
		print "<tr><form method='post'>
				<input type='hidden' name='Action' value='Equipment Details'>
				<td><input type='submit' value='Details'></td>
				<input type='hidden' name='PanelistID' value='".$row['PanelistID']."'>
				</form>";
		if ($CFG['dispID']==1) print "<td>".$row['PanelistID']."</td>";
		if ($_POST['PanelistID'] == $row['PanelistID']) {
			print "<form method='Post'>
					<input type='hidden' name='Action' value='Update Equipment'>
					<input type='hidden' name='PanelistID' value='".$_POST['PanelistID']."'>
					<td><input type='text' name='PanelistName' value='".$row['PanelistName']."'></td>
					<td><input type='text' name='PanelistBadgeName' value='".$row['PanelistBadgeName']."'></td>
					<td>".$tally1."</td>
					<td><input type='submit' value='Save'></td></form>";
		} else {
			print "<td>".$row['PanelistName']."</td>
					<td>".$row['PanelistBadgeName']."</td>
					<td>".$tally1."</td>";
			print "<form method='post'>
					<input type='hidden' name='Action' value='Edit Equipment'>
					<input type='hidden' name='PanelistID' value='".$row['PanelistID']."'>
					<td><input type='submit' value='Edit'></td></form>";
		}
		if ($tally==0) {
			print"<form method='post'>
					<input type='hidden' name='Action' value='Activate Equipment'>
					<input type='hidden' name='PanelistID' value='".$row['PanelistID']."'>
					<td bgcolor='#ff0000'><input type='submit' value='Activate'></td></form>";
		} else {
			if ($tally1==0) {
				print"<form method='post'>
					<input type='hidden' name='Action' value='Disable Equipment'>
					<input type='hidden' name='PanelistID' value='".$row['PanelistID']."'>
					<td bgcolor='#00ff00'><input type='submit' value='Disable'></td></form>";
			} else {
				print "<td bgcolor='#00ff00'></td>";
			}
		}
		print "<form method='post'>
				<input type='hidden' name='Action' value='Clear Report'>
				<input type='hidden' name='SubAction' value='Print Panelist Schedule'>
				<input type='hidden' name='PanelistID' value='".$row['PanelistID']."'>
				<input type='hidden' name='DisplayContact' value=0>
				<td><input type='submit' value='Itinerary'></td>
				</form>";
		print "</tr>";
	}
	print "<tr><form method='post'>
			<input type='hidden' name='Action' value='Add Equipment'>
			<td></td>
			<td></td>
			<td><input type='text' name='PanelistName'></td>
			<td><input type='text' name='PanelistBadgeName'></td>
			<td></td>
			<td colspan=2><center><input type='submit' value='Add'></td></form>
		</tr>";
	print "</table></div><br><br>";
}

function Display_Refresh_Button()
{
	#print "</table><table><tr><center>";
	print "<tr>";
	print "<form method='post'>";
	print "<input type='hidden' name='Action' value='".$_POST['Action']."'>";
	print "<input type='hidden' name='PanelID' value='".$_POST['PanelID']."'>";
	print "<input type='hidden' name='PanelistID' value='".$_POST['PanelistID']."'>";
	print "<td colspan=30><center><input type='submit' name='submit' value='Refresh'></td>";
	print "</form></tr></table>";

}

function Report_Panelist_Itinerary($PanelistID, $contact)
{
	global $CFG;
	$output = '';
	$output .=  "</tr></table>";
	$query="select Distinct PanelistPubName, PanelistBadgeName, P.PanelistID, P.PanelistEmail,
			P.PanelistPhoneDay,	P.PanelistPhoneEve, P.PanelistPhoneCell
			from `CPDB_Panelist` as P
			inner join `CPDB_P2P` as X
			on P.PanelistID = X.PanelistID
			Where X.ConID = '".$CFG['ConID']."'";
	if (!($PanelistID=='0')) {
		$query = $query . " and P.`PanelistID` = '".$PanelistID."'";
	}
	$query = $query . " order by `IsEquip`, `PanelistPubName`";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)){

		$output .=  "<center>".$CFG['ConName']."</center>";
		$output .=  "<center>Itinerary for <B>".$row['PanelistPubName']."</b></center>";
#		$output .=  "<center>Itinerary for <B>".$row['PanelistBadgeName']."</b></center>";
		if ($contact==1){
			$output .=  "<center>Email Address <B>".$row['PanelistEmail']."</b></center>";
			$output .=  "<center>Phone Number Day <B>".$row['PanelistPhoneDay']."</b></center>";
			$output .=  "<center>Phone Number Eve <B>".$row['PanelistPhoneEve']."</b></center>";
			$output .=  "<center>Phone Number Cell <B>".$row['PanelistPhoneCell']."</b></center>";
		}
		$output .=  "<table border=1><tr><th width='12%'>Panel Start</th><th width='12%'>Panel End</th><th width='76%'>Panel Title</th><tr>";
		$output .=  "<tr><th colspan=2>Panel Location</th><th>Panel Description</th></tr>";
		$output .=  "<tr><th colspan=3>Moderator in <b>Bold</b></th></tr></table><br>";

		$query1="select * from `CPDB_PTR` as S
				inner join `CPDB_P2P` as P
				on S.`PanelID` = P.`PanelID`
				inner join `CPDB_Room` as R
				on R.`RoomID` = S.`RoomID`
				inner join `CPDB_Panels` as D
				on P.`PanelID` = D.`PanelID`
				where P.`PanelistID` = '".$row['PanelistID']."'
				and S.ConID = '".$CFG['ConID']."'
				order by `Start` ";
		Display_Query($query1);
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		while ($row1 = mysql_fetch_assoc($sql1)){
			$output .=  "<Table border=1>";
			$fstart = date("D M j g:i:a",strtotime($row1['Start']));
			$fend  =  date("D M j g:i:a",strtotime($row1['End']));
			$output .=  "<tr><td width='12%'>".$fstart."</td><td width='12%'>".$fend."</td><td width='76%'>".$row1['PanelTitle']."</td></tr>";
			$output .=  "<tr><td colspan=2>".$row1['RoomName']."</td><td>".$row1['PanelDescription']."</td></tr>";
			$output .=  "<tr><td colspan=3>";
			$query2="select * from `CPDB_P2P` as S
					inner join `CPDB_Panelist` as P
					on S.`PanelistID` = P.`PanelistID`
					where S.`PanelID` = '".$row1['PanelID']."'
					order by IsEquip, PanelistPubName";
			Display_Query($query2);
			$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
			while ($row2 = mysql_fetch_assoc($sql2)){
				if ($row2['Moderator'] == 1) $output .=  "<B>";
				$output .=  "<u>".$row2['PanelistPubName']."</u></b> ";
			}
			$output .=  "</table><br>";
		}
		$query1="select * from `CPDB_P2P` as O
				left outer join `CPDB_PTR` as P
				on O.`PanelID` = P.`PanelID`
				inner join `CPDB_Panels` as D
				on O.`PanelID` = D.`PanelID`
				where `PTRID` is NULL
				and `PanelistID` = '".$row['PanelistID']."'";
		Display_Query($query1);
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		while ($row1 = mysql_fetch_assoc($sql1)){
			$output .=  "<Table border=1 bgcolor='#cc66cc'>";
			#$fstart = date("D M j g:i:a",strtotime($row1['Start']));
			#$fend  =  date("D M j g:i:a",strtotime($row1['End']));
			$output .=  "<tr><td colspan=3 width='100%'>".$row1['PanelTitle']."</td></tr>";
			$output .=  "<tr><td colspan=3>".$row1['PanelDescription']."</td></tr>";
			$output .=  "<tr><td colspan=3>";
			$query2="select * from `CPDB_P2P` as S
					inner join `CPDB_Panelist` as P
					on S.`PanelistID` = P.`PanelistID`
					where S.`PanelID` = '".$row1['PanelID']."'";
			Display_Query($query2);
			$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
			while ($row2 = mysql_fetch_assoc($sql2)){
				if ($row2['Moderator'] == 1) $output .=  "<B>";
				$output .=  "<u>".$row2['PanelistName']."</u></b> ";
			}
			$output .=  "</table><br>";

		}
		if ($contact==1){
			$output .=  "<br><Table border=1><tr><th colspan=5>Co Panelist Contact Information</td></tr>";
			$output .=  "<tr><th>Name</th><th>Email</th><th>Day Phone</th><th>Evening Phone</th><th>Cell Phone</th></tr>";
			$queryContact  = "SELECT B.`PanelistID`, P.`PanelistPubName`, P.`PanelistEmail`,
								P.`PanelistPhoneDay`, P.`PanelistPhoneEve`, P.`PanelistPhoneCell`,
								C.`sharephone`, C.`sharemail`, C.`shareemail`
								from CPDB_P2P as A
								inner join CPDB_P2P as B
								on A.`PanelID` = B.`PanelID`
								inner join `CPDB_Panelist` as P
								on B.PanelistID = P.PanelistID
								inner join `CPDB_PanelistCon` as C
								on C.PanelistId = B.PanelistId
								WHERE A.`PanelistID` = ".$row['PanelistID']."
								and B.`PanelistID` <> A.`PanelistID`
								and A.ConID = '".$CFG['ConID']."'
								and C.ConID = '".$CFG['ConID']."'
								group by B.`PanelistID`";
			Display_Query($queryContact,"Contact");
			$sqlContact=mysql_query($queryContact) or die('Query failed: ' . mysql_error());
			while ($rowContact = mysql_fetch_assoc($sqlContact)){
				$output .=  "<tr><td>".$rowContact['PanelistPubName']."</td><td>";
				if ($rowContact['shareemail']=='1') $output .= $rowContact['PanelistEmail'];
				$output .=  "</td><td>";
				if ($rowContact['sharephone']=='1') $output .= $rowContact['PanelistPhoneDay']."</td><td>".$rowContact['PanelistPhoneEve']."</td><td>".$rowContact['PanelistPhoneCell'];
				$output .=  "</td></tr>";
			}
			$output .=  "</table>";
		}
		$output .=  "<br clear=all style='page-break-before:always'>";
	}
	return ($output);
}

function Report_Room_Itinerary($RoomID) {
	global $CFG;
	$query="select * from `CPDB_Room` where `ConID` = '".$CFG['ConID']."'";
	if (!($RoomID=='0')) {
		$query = $query . " and `RoomID` = '".$RoomID."";
	}
	#print "<b><u><i>".$query."</b></u></i><br>";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)){
		print "<center>Room Itinerary for <b>".$row['RoomName']."</b></center>";
		print "<table border=1><tr><th width='12%'>Panel Start</th><th width='12%'>Panel End</th><th width='76%'>Panel Title</th></tr>";
		print "<tr><th colspan=3>Panel Description</th></tr>";
		print "<tr><th colspan=3>Panelists</th></tr></table><br>";
		$query1="Select * from `CPDB_PTR` as S inner join `CPDB_Panels` as P on P.`PanelID` = S.`PanelID` where `RoomID` = '".$row['RoomID']."' order by `Start`, `End`";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		while ($row1 = mysql_fetch_assoc($sql1)){
			print "<table border=1>";
			$fstart = date("D M j g:i:a",strtotime($row1['Start']));
			$fend  =  date("D M j g:i:a",strtotime($row1['End']));
			print "<tr><td width='12%'>".$fstart."</td><td width='12%'>".$fend."</td><td width='76%'>".$row1['PanelTitle']."</td></tr>";
			#print "<tr><td colspan=3>".$row1['PanelDescription']."</td></tr>";
			print "<tr><td colspan=3>";
			$query2="select * from `CPDB_P2P` as S inner join `CPDB_Panelist` as P on S.`PanelistID` = P.`PanelistID` where S.`PanelID` = '".$row1['PanelID']."'";
			$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
			while ($row2 = mysql_fetch_assoc($sql2)){
				if ($row2['Moderator'] == 1) print "<B>";
				print "<u>".$row2['PanelistName']."</u></b> ";
			}
			print "</table><br>";

		}
		print "<br clear=all style='page-break-before:always'>";
	}

}

function Are_You_Sure()
{
	global $CFG;
		$query="select * from `CPDB_Panelist`
				where PanelistID in (
					select Distinct PanelistID from `CPDB_P2P` where ConID = '".$CFG['ConID']."')
				and IsEquip=0
				and PanelistEmail <> ''
			order by PanelistName";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$rowcount = mysql_num_rows($sql);
	print "</tr></table>";
	print "<Center><font size=5 color='red'>You have selected Bulk Mail, ";
	print "<br>Please confirm that you intend to send mail to ".$rowcount." recipients ";
	print "<br>by typing ".'"'."YES".'"'." in the box below</font></center>";
	print "<center><form method='post'> <input type='hidden' name='Action' value='Bulk Email Itineraries'> <input type='hidden' name='PanelistID' value='0'> <input type='hidden' name='DisplayContact' value='".$_POST['DisplayContact']."'> <input type='text' size=10 name='confirm' value='no'> <input type='submit' name='submit' value='submit'>";
}

#function Send_Bulk_Email_Itineraries()
#{
#	print "</tr></table>";
#	global $CFG;
#	$query="Select * from `CPDB_Panelist` where `PanelistEmail` <> ''";
#	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#	while ($row = mysql_fetch_assoc($sql)){
#
#		$eol="\r\n";
#
#		# Common Headers
#		$headers  = 'From: '.$ConName.'Programming Department<'.$CFG['srcEmail'].'>'.$eol;
#		$headers .= 'Reply-To:'.$ConName.'Programming Department<'.$CFG['replyToEmail'].'>'.$eol;
#		$headers .= 'Return-Path: '.$ConName.'Programming Department<'.$CFG['srcEmail'].'>'.$eol;    // these two to set reply address
#		$headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
#		$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
#		$headers .= 'MIME-Version: 1.0'.$eol;
#		$headers .= "Content-type: text/html\r\n";
#
#		$subject= $CFG['ConName']." Panelist Itinerary";
#
#		$output = "Friendly message goes here ". $row['PanelistPubName']. "and  more text goes here";
#		$output .= Report_Panelist_Itinerary ($row['PanelistID'], $_POST['DisplayContact']);
#
#		mail($row['PanelistEmail'], $subject, "<head></head><body>".$output, $headers);
#		print "<br>Sending Itinerarie to ".$row['PanelistName']." at ".$row['PanelistEmail'];
#	}
#
#	$query="Select * from `CPDB_Panelist` where `PanelistEmail` = ''";
#	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#	while ($row = mysql_fetch_assoc($sql)){
#		print "<br>Unable to send Itinerarie to ".$row['PanelistName']." No email listed";
#	}
#
#}

function Build_Option($query, $selval=-1,$defval=0,$deftxt=""){
	# $query = SQL query string returning at least ID and Name values
	# $selval = value that should be Selected (-1 by default)
	# $defval = the value for any default option
	# $deftxt = the text for the default option
	# Default option should not be returned by the query
	global $CFG;
	if (!($deftxt=="")) {
		if ($defval == $selval){
			$output = "<option value='".$defval."' selected>".$deftxt."</option>\r\n";
		} else {
			$output = "<option value='".$defval."'>".$deftxt."</option>\r\n";
		}
	} else {
		$output="";
	}
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		if ($row['ID'] == $selval) {
			$output .= "<option value='".$row['ID']."' selected>".$row['Name']."</option>\r\n";
		} else {
			$output .= "<option value='".$row['ID']."'>".$row['Name']."</option>\r\n";
		}
	}
	return $output;
}

function Display_Header_Options()
{
	global $CFG;
	$query = "select `RoomID` as ID, `RoomName` as Name from `CPDB_Room` where `ConID` = '".$CFG['ConID']."' order by `RoomOrder`";
	$room_opts = Build_Option($query,-1,-1,"All Rooms");
	$query="select `CatID` as ID, `Category` as Name from `CPDB_Category` where `Active` = 1 order by `Category`";
	$cat_opts = Build_Option($query,-1,-1,"All Categories");
	print "<table width='1024px' border=1><tr>";
	print "<td><center><form method='post'>
			<select name='range'>
			<option selected value='-1' >All Panels</options>
			<option value=0>Panels with no Panelists</option>
			<option value=1>Panels with 1-2 Panelists</options>
			<option value=2>Panels with 3-4 Panelists</options>
			<option value=3>Panels with 5 or more Panelists</options>
			</select><br>
			<select name='cat_list[]' multiple>
			".$cat_opts."
			</select><br>
			<input type='submit' name='Action' value='Panels'></form></td>";
	print "<td><center><form method='post'>
			<select name='range'>
			<option selected value='-1'>All Panelists</option>
			<option value=0>Panelists on no Panels</option>
			<option value=1>Panelists on 1-2 Panels</option>
			<option value=2>Panelists on 3-4 Panels</option>
			<option value=3>Panelists on 5 or more Panels</option>
			</select><br>
			<input type='submit' name='Action' value='Panelist'></form></td>";
	print "<td><center><form method='post'>
			<select name='room_range[]' multiple>
			".$room_opts."
			</select><br>
			<select name='cat_range[]' multiple>
			".$cat_opts."
			</select><br>
			<input type='submit' name='Action' value='Display Grid'></form></td>";
#	print "<td rowspan=2><center><form method='post'><input type='submit' name='Action' value='Facilities'></form></td>";
	print "<td rowspan=2><center><form method='post'><input type='submit' name='Action' value='Equipment'></form></td>";
	print "<td rowspan=2><center><form method='post'><input type='submit' name='Action' value='Reports'></form></td>";
	print "</tr><tr>";
	print "<td><center><form method='post'><input type='text' name='PanelID'><br>
			<input type='submit' name='Action' Value='Panel By ID'></form></td>";
	print "<td><center><form method='post'><input type='text' name='PanelistID'><br>
			<input type='submit' name='Action' Value='Panelist By ID'></form></td>";
	print "<td><center><form method='post'><input type='submit' name='Action' Value='Availability Matrix'></form></td>";
	print "</tr><tr>";
#	print "<td><center><form method='post'><input type='submit' name='submit' value='Add Panel'><input type='hidden' name='Action' value='Add Panel Form'></form></td>";
#	print "<td><center><form method='post'><input type='submit' name='submit' value='Add Panelist'><input type='hidden' name='Action' value='Add Panelist Form'></form><td>";


	print "</tr></table>";

}

function Display_Panel_Key()
{
	global $CFG;
	print "<div class='key'>";
	print "<table border=1 width='1024px'><tr><th colspan=8>KEY</th></tr>";
	print "<tr><td><img src='".$CFG['webpath']."/admin/ui_images/lock_on.png'></td><td>Panel is Locked</td>";
	print "<td><img src='".$CFG['webpath']."/admin/ui_images/lock_off.png'></td><td>Panel is Un Locked</td>";
	print "<td><img src='".$CFG['webpath']."/admin/ui_images/solo.png'></td><td>Panel is a Solo Panel</td>";
	print "<td><img src='".$CFG['webpath']."/admin/ui_images/multiple.png'></td><td>Panel is not Solo</td></tr>";
	print "<tr><td><font color='green'><b>P</b></font></td><td>Panel is Publicly Viewable</td>";
	print "<td><font color='red'><b>P</b></font></td><td>Panel is not Publicly Viewable</td>";
	print "<td><font color='green'><b>S</b></font></td><td>Panel is on the Survey</td>";
	print "<td><font color='red'><b>S</b></font></td><td>Panel is not on the Survey</td></tr>";
	print "<tr><td><font color='green'><b>A</b></font></td><td>Panel is Approved</td>";
	print "<td><font color='red'><b>A</b></font></td><td>Panel is not Approved</td>";
	print "<td><img src='".$CFG['webpath']."/admin/ui_images/tech.png'></td><td>Panel has Tech Requirements</td></tr>";
	print "</table>";
	print "</div>";

}

function Form_Add_Panel()
{
	global $CFG;
	$query="select * from `CPDB_Category` where `Active` = '1' and `CatID` in (".$CFG['CategoryList'].") order by `Category`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$cat = "";
	while ($row = mysql_fetch_assoc($sql)) {
		$cat .= "<option value='".$row['CatID']."'>".$row['Category']."</option>";
	}
	print "<div class='MainForm'>";
	print "<table border=1 width='1024px'>";
	print "<tr><td>&nbsp;</td><th>Category</th><th>Title</th><th colspan=2>Description</th></tr>";
	print "<tr><form method='post'><td rowspan=5><input type='submit' name='Action' value='Add Panel'></td>
			<td><select name='CatID'>".$cat."</select></td>
			<td><input type='text' name='PanelTitle'></td>
			<td colspan=2><textarea rows=5 cols=70 name='PanelDescription'></textarea></td></tr>";
	print"<tr><th>Public Grid</th><th>Panelist Survey</th><th colspan=2>Notes</th></tr>";
	print "<tr><td><input type='radio' name='PanelHidePublic' value=1>Hide<br><input type='radio' name='PanelHidePublic' value=0 checked>Show</td>
			<td><input type='radio' name='PanelHideSurvey' value=1>Hide<br><input type='radio' name='PanelHideSurvey' value=0 checked>Show</td>
			<td colspan=2><textarea rows=5 cols=70 name='PanelNotes'></textarea></td></tr>";
#	if ($_POST['Action']=='Window Panels') print "<input type='hidden' name='window' value='true'>";
	print "<tr><th>Panel Type</th><th>Tech Requirements</th><th width='100px'>Aproved</th><td width='300px'> </td></tr>";
	print "<tr><td><input type='radio' name='PanelSolo' value=1>Solo<br><input type='radio' name='PanelSolo' value=0 Checked>Group</td>
			<td><input type='radio' name='PanelTech' value=1>Yes<br><input type='radio' name='PanelTech' value=0 Checked>no</td>
			<td width='100px'><input type='radio' name='PanelApproved' value=1 Checked>Yes<br><input type='radio' name='PanelApproved' value=0>No</td>
			<td> </td></tr>";
	print "</form></table>";
	print "</div>";
}

function Form_Add_Panelist(){
	global $CFG;
	print "<div class='MainForm'>";
	print "<table border=1 width='1024px'>";



}


function View_Panel_Edits(){
	global $CFG;
	print "<div class='MainForm'>";
	print "<table border=1 width='1024px'>";
	print "<tr><th>Edit By</th><th>Edit On</th><th>Panel Category</th><th>Panel Title</th><th>Panel Description</th><th>Panel Notes</th></tr>";
	$query = "select * from `CPDB_PanelEdits` as E inner join `CPDB_Category` as C on C.CatID = E.CatId where `PanelID` = '".$_POST['PanelID']."' order by `EditTime`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['EditBy']."</td>
				<td>".$row['EditTime']."</td>
				<td>".$row['Category']."</td>
				<td>".$row['PanelTitle']."</td>
				<td>".$row['PanelDescription']."</td>
				<td>".$row['PanelNotes']."</td>
		</tr>";
	}
	print "</table>";


}



function Display_Panel_List()
{
	global $CFG;

	print "<div class='datareport'>";
	print "<br><Table border=1 width='1024px' class='sortable'><thead class='sortlink'>";
	print "<tr><th>&nbsp;</th>";
	print "<th>&nbsp;</th>";
	if ($CFG['dispID']== 1) print "<th>&nbsp;</th>";
	if ($CFG['dispRanks']==1) print "<th colspan=5>Rankings</th>";
	if ($CFG['dispRankAgg']==1) print "<th>&nbsp;</th>";
	print  "<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			</tr>";
	print "<tr>";
	print "<th>&nbsp;</th>
			<th>Flags</th>";
	if ($CFG['dispID']== 1) print "<th rowspan=1>Panel-ID</th>";
	if ($CFG['dispRanks']==1) print "<th>1</th><th>2</th><th>3</th><th>4</th><th>5</th>";
	if ($CFG['dispRankAgg']==1) print "<th rowspan=1>Aggregate Rankings</th>";
	print  "<th rowspan=1>Panelists Scheduled</th>
			<th>Room Scheduled</th>
			<th>Moderated</th>
			<th>Category</th>
			<th>Title</th>
			<th>Description</th>
			<th>Created</th></tr>";
	print "</thead><tbody>";
	$query  = "SELECT P.`PanelID`, ";
	$query .= "C.`Category` as PanelCategory, ";
	$query .= "P.`PanelTitle`, ";
	$query .= "P.`PanelDescription`, ";
	$query .= "P.`PanelLocked`, ";
	$query .= "P.`PanelSolo`, ";
	$query .= "P.`PanelTech`, ";
	$query .= "P.`PanelHidePublic`, ";
	$query .= "P.`PanelHideSurvey`, ";
	$query .= "P.`PanelApproved`, ";
	$query .= "P.`PanelCreated`, ";
	$query .= "S.`RoomID`, ";
	$query .= "S.`Start`, ";
	$query .= "S.`End`, ";
	$query .= "O.`PanelID` AS MyCheck, ";
	$query .= "count( O.`PanelID` ) AS PanelistCount ";
	$query .= "FROM `CPDB_Panels` AS P ";
	$query .= "LEFT OUTER JOIN `CPDB_PTR` AS S ON P.`PanelID` = S.`PanelID` ";
	$query .= "inner join `CPDB_Category` as C on P.`CatID` = C.`CatID`";
	$query .= "left outer JOIN `CPDB_P2P` AS O ON P.`PanelID` = O.`PanelID` ";
	$query .= "where P.`ConID` = '".$CFG['ConID']."' and P.`CatID` in (".$CFG['CategoryList'].")";
	$cat_list = $_POST['cat_list'];
	if (in_array("-1", $cat_list)) {
		#do nothing (show all categories)
	} else {
		if (count($cat_list)==0) {
			# do nothing, empty array, default to all categories
		} else {
			$catlist = implode(", ",$_POST['cat_list']);
			$query.= " and P.`CatID` in (".$catlist.")";
		}
	}

	#if ($CFG['TrackMgr'] <> 'All') {
	#	#<
	#	$query.= " Where P.`PanelCategory` = '".$CFG['TrackMgr']."' ";
	#}
	$query .= "GROUP BY P.`PanelID` ";
	if (!($CFG['PanelListFilter']=='')) $query .=" Having ".$CFG['PanelListFilter'];
	$query .= " Order by PanelApproved desc";
	if (!($CFG['PanelListSort']=='')) $query .= ", ".$CFG['PanelListSort'];

	#print "<b><u><i>".$query."</b></u></i><br>";
	Display_Query($query,"Panels");
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	if ($_POST['range']==-1) {
		$lowrange=0;
		$highrange=10000;
	}
	if ($_POST['range']==0) {
		$lowrange=0;
		$highrange=0;
	}
	if ($_POST['range']==1) {
		$lowrange=1;
		$highrange=2;
	}
	if ($_POST['range']==2) {
		$lowrange=3;
		$highrange=4;
	}
	if ($_POST['range']==3) {
		$lowrange=5;
		$highrange=10000;
	}
	while ($row = mysql_fetch_assoc($sql)) {
		if (($row['PanelistCount']>= $lowrange) && ($row['PanelistCount']<= $highrange)) {
			if (($CFG['dispRanks']==1)or ($CFG['dispRankAgg']==1)) {
				$query1="Select `Rank`,count(*) as Tally from `CPDB_PanelRanking` where `ConID` = '".$CFG['ConID']."' and `PanelID` = '".$row["PanelID"]."' Group by `Rank` Order By `Rank`";
				$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
				$ranking["1"]=0;
				$ranking["2"]=0;
				$ranking["3"]=0;
				$ranking["4"]=0;
				$ranking["5"]=0;
				while ($row1 = mysql_fetch_assoc($sql1)){
					$ranking[$row1["Rank"]]=$row1["Tally"];
				}
			}

			print "<tr><td><form method='post'><input type='hidden' name='Action' value='Panel Description'><input type='submit' name='details' value='details'><input type='hidden' name='PanelID' value='".$row["PanelID"]."'></form></td>";
			#####
			# Display Panel detail Icons
			#####

			print "<td>";
			if ($row['PanelLocked']==1){
				print "<img src='".$CFG['webpath']."admin/ui_images/lock_on.png'>";
			} else {
				print "<img src='".$CFG['webpath']."admin/ui_images/lock_off.png'>";
			}
			if ($row['PanelSolo']==1){
				print "<img src='".$CFG['webpath']."admin/ui_images/solo.png'>";
			} else {
				print "<img src='".$CFG['webpath']."admin/ui_images/multiple.png'>";
			}
			if ($row['PanelTech']==1){
				print "<img src='".$CFG['webpath']."admin/ui_images/tech.png'>";
			}
			print "<br>";
			if ($row['PanelHidePublic']==1){
				print "<font color='red'><B>P</b></font>";
			} else {
				print "<font color='Green'><b>P</b></font>";
			}
			print "&nbsp;";
			if ($row['PanelHideSurvey']==1){
				print "<font color='red'><B>S</b></font>";
			} else {
				print "<font color='Green'><B>S</b></font>";
			}
			print "&nbsp;";
			if ($row['PanelApproved']==0){
				print "<font color='red'><B>A</b></font>";
			} else {
				print "<font color='Green'><B>A</b></font>";
			}

			print "</td>";
			#####
			# end of Display Panel Detail Icons
			#####
			if ($CFG['dispID'] == 1) print "<td>".$row["PanelID"]."</td>";
			if ($CFG['dispRanks']==1) {
				print "<td>".$ranking["1"]."</td><td>".$ranking["2"]."</td><td>".$ranking["3"]."</td><td>".$ranking["4"]."</td><td>".$ranking["5"]."</td>";
			}
			if ($CFG['dispRankAgg']==1){
				$agg = $ranking["1"] + $ranking["2"] +$ranking["3"] +$ranking["4"] +$ranking["5"];
				print "<td><center>".$agg."</center></td>";
			}
			if ($row['PanelistCount']==0){
				print "<td bgcolor='red'>";
			} else {
				print "<td>";
			}
			$altdate = $CFG['constartdate']." 00:00:00";
			print "<center>".$row['PanelistCount']."</td>";
			if (($row['RoomID']==0) and (($row['Start']==$altdate) or ($row['Start']=="0000-00-00 00:00:00") or ($row['Start']==""))) {
				print "<td bgcolor='red'>No Room<br>No Time";
			} elseif ($row['RoomID']==0){
				print "<td bgcolor='yellow'>No Room";
			} elseif ($row['Start']==$altdate) {
				print "<td bgcolor='yellow'>No Time";
			} else {
				print "<td>Scheduled";
			}
			#print "<br>Room = ".$row3['RoomID']."<br> Start = ".$row3['Start'];
			print "</td>";
			$query4 = "Select * from `CPDB_P2P` where `PanelID` = '".$row['PanelID']."' and `Moderator` = 1";
			$sql4=mysql_query($query4) or die('Query failed: ' . mysql_error());
			if (mysql_num_rows($sql4) ==1) {
				print "<td>Yes</td>";
			} else {
				print "<td>No</td>";
			}

			print "<TD>".$row["PanelCategory"]."</td><td>".$row["PanelTitle"]."</td><td>".$row["PanelDescription"]."</td><td>".$row['PanelCreated']."</td></tr>";
		}
	}
	print "</tbody></table>";
	print "</div>";

}
function Display_Panelist_List()
{
	global $CFG;

	print "<table><tr><td>";
	print "<div class='datareport'>";
	print "<Table border=1 width='700px' class='sortable'><thead class='sortlink'>";
	print "<tr><th>&nbsp;</th>";
	if ($CFG['dispID'] == 1) print "<th>&nbsp;</th>";
	print "<th>Panelist Name</th><th>Badge Name</th><th>Email Address</th><th>Panels Ranked</th><th>Panels Scheduled</th><th>&nbsp;</th></tr></thead><tbody>";
	#$query="Select * from `CPDB_Panelist` order by `IsEquip`, ".$CFG['PanelistSort'];

#	$query  = "select P.`PanelistID`, `PanelistName`, `PanelistBadgeName`, `IsEquip`, count(O.`PanelistID`) as Tally ";
#	$query .= "from `CPDB_Panelist` as P ";
#	$query .= "left outer JOIN `CPDB_P2P` as O on P.`PanelistID` = O.`PanelistID` ";
#	$query .= "inner join `CPDB_Panels` as E on E.`PanelID` = O.`PanelID` ";
#	$query .= "where E.`ConID` = '".$CFG['ConID']."'";
#	$query .= "Group By `PanelistName`, `PanelistBadgeName`, P.`PanelistID`, `IsEquip` ";
#	$query .= "Having `Tally` > -1";
#	if (!($CFG['PanelistListFilter']=='')) $query.= " and ".$CFG['PanelistListFilter'];
#	$query .= " Order by `IsEquip`, ".$CFG['PanelistListSort'];

	$query = "Select *, 0 as Tally, 0 as RankCt
				from CPDB_Panelist
				where `PanelistID`
				in (
				select V.PanelistID
				from CPDB_V_Panelist2Con as V
				inner join CPDB_Invite as I on I.conId = V.ConID
				and I.PanelistID = V.PanelistID
				where I.InviteState <> 'Unavailable'
				and V.ConID = '".$CFG['ConID']."'
				)
				Order by `IsEquip`, ".$CFG['PanelistListSort'];
	if ($_POST['range']==-1) {
		$lowrange=0;
		$highrange=10000;
	}
	if ($_POST['range']==0) {
		$lowrange=0;
		$highrange=0;
	}
	if ($_POST['range']==1) {
		$lowrange=1;
		$highrange=2;
	}
	if ($_POST['range']==2) {
		$lowrange=3;
		$highrange=4;
	}
	if ($_POST['range']==3) {
		$lowrange=5;
		$highrange=10000;
	}
#	print "<b><u><i>".$query."</b></u></i><br>";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$query1="select * from CPDB_P2P
				where PanelistID = '".$row['PanelistID']."'
				and ConID = '".$CFG['ConID']."'";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$row['Tally'] = mysql_num_rows($sql1);
		$query2="select * from CPDB_PanelRanking
				where PanelistID = '".$row['PanelistID']."'
				and ConID = '".$CFG['ConID']."'";
		$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
		$row['RankCt'] = mysql_num_rows($sql2);

		if (($row['Tally']>= $lowrange) && ($row['Tally']<= $highrange)) {
			#$row1['Tally'] = $row['Tally'];
			$mbgcolor='#99ccff';
			if ($row['IsEquip']==1) $mbgcolor='#cc66cc';
			print "<tr  bgcolor='".$mbgcolor."'><td><form method='post'><input type='hidden' name='Action' value='Panelist Detail'><input type='submit' name='details' value='details'><input type='hidden' name='PanelistID' value='".$row["PanelistID"]."'></form></td>";
			if ($CFG['dispID'] == 1) print "<td>".$row["PanelistID"]."</td>";
			print "<td>".$row["PanelistName"]."</td><td>".$row['PanelistBadgeName']."</td>";
			print "<td>".$row['PanelistEmail']."</td>";
			print "<td><center>".$row['RankCt']."</td>";
			switch($row['Tally']) {
			case 0:
				$mybgcolor='ff3366';
				break;
			case 1:
				$mybgcolor='ffff00';
				break;
			case 2:
				$mybgcolor='ccff00';
				break;
			default:
				$mybgcolor='00ff33';
				break;
			}
			print "<td bgcolor='#".$mybgcolor."'>";
			print "<center>".$row['Tally']."</td>";
			print "<form method='post'><input type='hidden' name='Action' value='Clear Report'><td><input type='submit' name='submit' value='Panelist Itineraries'></td><input type='hidden' name='PanelistID' value='".$row['PanelistID']."'><input type='hidden' name='DisplayContact' value='0'></form></tr>";
		}
	}
	print "</tbody></div>";
	print "</table>";
	print "</td><td valign='top' width='324px'>";
}

function Display_Panelist_Key()
{
	print "<div class='key'>";
	print "<center><table border = 2><tr><th>Key</th></tr>";
	print "<tr bgcolor='#99ccff'><td>Person</td></tr>";
	print "<tr bgcolor='#cc66cc'><td>Equipment</td></tr>";
	print "<tr bgcolor='#ff3366'><td>Scheduled on 0 panels</td></tr>";
	print "<tr bgcolor='#ffff00'><td>Scheduled on 1 panel</td></tr>";
	print "<tr bgcolor='#ccff00'><td>Scheduled on 2 panels</td></tr>";
	print "<tr bgcolor='#00ff33'><td>Scheduled on 3 or more panels</td></tr>";
	print "</table>";
	print "</class>";
	print "</td></tr></table>";

}

function Next_Panel()
{
	global $CFG;
	if ($_POST['subaction'] == 'Previous'){
		$query = "Select `PanelID` from `CPDB_Panels`
					where `PanelId` < '".$_POST['PanelID']."'
					and `ConID` = '".$CFG['ConID']."'
					and `CatID` in (".$CFG['CategoryList'].")
					order by PanelID Desc";
	} else {
		$query = "Select `PanelID` from `CPDB_Panels`
					where `PanelId` > '".$_POST['PanelID']."'
					and `ConID` = '".$CFG['ConID']."'
					and `CatID` in (".$CFG['CategoryList'].")
					order by PanelID";
	}
	Display_Query($query,'Next_Panel');
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$_POST['PanelID']=$row['PanelID'];
}

function Display_Panel_Description()
{
	global $CFG;
#	array_table($CFG,"ff9999",4,"Exploding '$ CFG'");

	$query2 = "Select * from `CPDB_P2P` where `PanelID` = '".$_POST['PanelID']."'";
	$sql=mysql_query($query2) or die('Query failed: ' . mysql_error());
	$PanelistCount = mysql_num_rows($sql);
	$query = "Select *, P.`PanelID` as PID
				from `CPDB_Panels` as P
				LEFT OUTER JOIN `CPDB_PTR` as S
				On P.`PanelID` = S.`PanelID`
				left outer join `CPDB_Panelist` as X
				on X.PanelistID = P.PanelSuggestBy
				where P.`PanelID` = '".$_POST["PanelID"]."' and P.`CatID` in (".$CFG['CategoryList'].")";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	if (mysql_num_rows($sql)==0) {
		print "<font size=4 color='red'>No panels match this this Panel ID, or this panel is in a Category you do not have Edit rights for</font>";
		return;
	}
	$row = mysql_fetch_assoc($sql);
	$approved = $row['PanelApproved'];
	$query="select * from `CPDB_Category` where `Active` = 1 and `CatID` in (".$CFG['CategoryList'].")";
	$sql1=mysql_query($query) or die('Query failed: ' . mysql_error());
	$catopts="";
	while ($row1 = mysql_fetch_assoc($sql1)) {
		$catopts .= "<option value='".$row1['CatID']."' ";
		if ($row1['CatID'] == $row['CatID']) $catopts .= "selected";
		$catopts .= ">".$row1['Category']."</option>";
	}
	if ($row['Start']==''){
		$s_ts = strtotime($CFG['constartdate']." ".$CFG['FirstDailyHour'].":00:00");
	} else {
		$s_ts = strtotime($row['Start']);
	}
	if ($row['End']==''){
		$endtime= $CFG['FirstDailyHour'] +1;
		$e_ts = strtotime($CFG['constartdate']." ".$endtime.":00:00");
	} else{
		$e_ts = strtotime($row['End']);
	}

	$s_ymd = date("Y-m-d",$s_ts);
	for ($i = 0; $i <= $CFG['conrundays']; $i++) {
		$s_cmyd[$i]=date("Y-m-d",strtotime($CFG['constartdate'])+(60*60*24*$i));
		$s_dmyd[$i]=date("l M jS Y",strtotime($CFG['constartdate'])+(60*60*24*$i));
	}
	$s_hr = date("g",$s_ts);
	$s_Hr = date("G",$s_ts);
	$s_mi = date("i",$s_ts);
	$s_ap = date("a",$s_ts);

	$eDif = $e_ts - $s_ts;
	$e_Hd = floor($eDif / (60*60));
	$e_Md = ($eDif / 60 - ($e_Hd * 60));
	$panelLock = $row['PanelLocked'];
	print "<div class='locks'>";
	if ($panelLock==1) {
		print "<table width='1024px'><form method='post'><input type='hidden' name='Action' value='PanelLockToggle'><input type='hidden' name='PanelID' value='".$row['PanelID']."'><tr><td width='340px' align='left'><img src='ui_images/lock_on.png'><font color='red'>LOCKED<img src='ui_images/lock_on.png'></td><td width='340px' align='center'><input type='submit' name='submit' value='UnLock'></td><td width='340px' align='right'><img src='ui_images/lock_on.png'><font color='red'>LOCKED<img src='ui_images/lock_on.png'></td></tr></form></table>";
	} else {
		print "<table width='1024px'><form method='post'><input type='hidden' name='Action' value='PanelLockToggle'><input type='hidden' name='PanelID' value='".$row['PanelID']."'><tr><td width='340px' align='left'><img src='ui_images/lock_off.png'><font color='red'>Un Locked<img src='ui_images/lock_off.png'></td><td width='340px' align='center'><input type='submit' name='submit' value='Lock'></td><td width='340px' align='right'><img src='ui_images/lock_off.png'><font color='red'>Un Locked<img src='ui_images/lock_off.png'></td></tr></form></table>";
	}
	print "<table width='1024px'><form method='post'><input type='hidden' name='PanelID' value='".$_POST['PanelID']."'><input type='hidden' name='Action' value='NextPanel'><tr><td width=50%><center><input type='submit' name='subaction' value='Previous'></td><td width=50%><center><input type='submit' name='subaction' value='Next'></td></tr></form></table>";
	print "</div>";
	print "<table border=1><tr><td width='512px' valign='top'>";
	print "<div class='mainform'>";
	print "<Table Border=1>";
	print "<form method='post' name='target0'><tr><td>Panel ID</td><td>".$row["PID"]."</td><input type='hidden' name='PanelID' value='".$row['PID']."'>";
	print "<td rowspan=6>";
	if ($panelLock==0) print "<input type='submit' name='submit' value='Update\r\nPanel'>";
	print "</td></tr>";
	print "<tr><td>Category</td><td><select name='CatID'>".$catopts."</select></td></tr>";
	print "<tr><td>Title</td><td><input type='text' name='PanelTitle' value='".$row["PanelTitle"]."'></td></tr>";
	print "<tr><td>Description</td><td><textarea rows=5 cols=40 name='PanelDescription'>".$row['PanelDescription']."</textarea></td></tr>";
	print "<tr><td>Panel Notes</td><td><textarea rows=5 cols=40 name='PanelNotes'>".$row['PanelNotes']."</textarea></td></tr><input type='hidden' name='Action' value='Update Panel'>";
	print "<tr><td colspan=2>";
	print "<table border=1><tr><td><Input type='radio' name='PanelHidePublic' value='1' ";
	if ($row['PanelHidePublic']==1) print "checked";
	print ">Hide from Public Grid<br>";
	print "<Input type='radio' name='PanelHidePublic' value='0' ";
	if ($row['PanelHidePublic']==0) print "checked";
	print ">Show on Public Grid</td>";
	print "<td><Input type='radio' name='PanelSolo' value='1' ";
	if ($row['PanelSolo']==1) print " checked ";print "<Input type='radio' name='PanelSolo' value='0' ";
	print ">Panel is a Solo Panel<br>";
	print "<Input type='radio' name='PanelSolo' value='0' ";
	if ($row['PanelSolo']==0) print " checked ";
	print ">Panel is a Group Panel";
	print "</td></tr>";
	print "<tr><td>        <Input type='radio' name='PanelHideSurvey' value='1' ";
	if ($row['PanelHideSurvey']==1) print "checked";
	print ">Hide from PanelistSurvey<br>";
	print "        <Input type='radio' name='PanelHideSurvey' value='0' ";
	if ($row['PanelHideSurvey']==0) print "checked";
	print ">Show on PanelistSurvey";
	print "</td><td>";
	print "<Input type='radio' name='PanelTech' value='1' ";
	if ($row['PanelTech']==1) print "checked";
	print ">Panel has Tech Requirements<br>";
	print "<Input type='radio' name='PanelTech' value='0' ";
	if ($row['PanelTech']==0) print "checked";
	print ">Panel has no Tech Requirements";
	print "</td></tr>";
	print "<tr><td>";
	$pa0='';
	$pa1='';
	$mstate='';
	if ($row['PanelApproved']==0) {
		$pa0='checked';
	} else {
		$pa1='checked';
	}
		if (($row['RoomID'] == 0) and ($PanelistCount == 0)) {
		$mstate = '';
	} else {
		#$mstate = 'disabled';
	}
	print "Panel Approved<br>
		Yes<input name='PanelApproved' type=radio value=1 ".$pa1." ".$mstate.">
		No <input name='PanelApproved' type=radio value=0 ".$pa0." ".$mstate."></td>";
	$ph0 = $ph1 = '';
	if ($row['PanelHighlited']==0) {
		$ph0='checked';
	} else {
		$ph1='checked';
	}
	print "<td>Panel Highlited<br>
		Yes<input name='PanelHighlited' type=radio value=1 ".$ph1.">
		No<input name='PanelHighlited' type=radio value=0 ".$ph0."></td></tr>";
	$psb=$row['PanelSuggestBy'];

	if ($row['PanelSuggestBy'] == 0) $row['PanelistName']='Admin';
	if ($row['PanelSuggestBy'] == 9999) $row['PanelistName']='WebForm';

	print "<tr><td colspan=2>Panel Suggested by <B>".$row['PanelistName']."</b><br>Panel Created on <B>".$row['PanelCreated']."</b></td></tr>";

	print "</td></tr>";
	print "</form>";
	print "<tr><table width='100%'>";
	print "<tr><form method='post'><input type='hidden' name='Action' value='Clone Panel'><input type='hidden' name='PanelID' value='".$row['PID']."'><td><center><input type='submit' name='submit' value='CLONE Panel'></td></form>";
	#print "<form method='post'><input type='hidden' name='Action' value='Splinter Panel'><input type='hidden' name='PanelID' value='".$row['PID']."'><td><center><input type='submit' name='submit' value='SPLINTER Panel'></td></form>";

	if (($row['RoomID'] == 0) and ($PanelistCount == 0)) print "<form method='post'><input type='hidden' name='Action' value='Delete Panel'><input type='hidden' name='PanelID' value='".$row['PID']."'><td><center><input type='submit' name='submit' value='DELETE Panel'></td></form>";
	print "<form method='post'><input type='hidden' name='Action' value='View Panel Edits'><input type='hidden' name='PanelID' value='".$row['PID']."'><td><center><input type='submit' name='submit' value='View Panel Edits'></td></form>";
	print "</tr></table></tr></table>";


	#print "<script type='text/javascript'>\r\ndocument.forms['target0']['PanelDescription'].value='".$row['PanelDescription']."'\r\n</script>";
	#print "<script type='text/javascript'>\r\ndocument.forms['target0']['PanelNotes'].value='".$row['PanelNotes']."'\r\n</script>";
	print "</div>";

	#######
	# Begin Panel Scheduling Form
	#######
	print "</td><div class='scheduling'><td valign='top' width='512px' class='scheduling'>";
	if ($approved==1){
		print "<table border=1><tr><td colspan=3><b><center>Panel Scheduling</b></td></tr>";
		print "<tr><td>Panel Start Time</td>";
		print "<form method='post' name='target1'><td>";
			print "<select name='StartDow'>";
				for ($i = 0; $i < $CFG['conrundays']; $i++) {
					print "\r\n<option value='".$s_cmyd[$i]."' ";
					if ($s_ymd == $s_cmyd[$i]) print "selected";
					print ">".$s_dmyd[$i]."</option>";
				}
			print "</select>";
			print "<select name='StartHour'>";
				for ($i = 0; $i <= 11; $i++) {
					if ($i==0) {
						$j=12;
					} else {
						$j=$i;
					}
					print "\r\n<option value='".$i."' ";
					if ($s_hr == $j) print "selected";
					print ">".$j."</option>";
				}
			print "</select>";
			print ":<select name='StartMinute'>";
				for ($i = 0; $i < 60; $i=$i + 5) {
					$j=$i;
					if ($j<10) $j = "0".$j;
					print "\r\n<option value='".$j."' ";
					if ($s_mi == $i) print "selected";
					print ">".$j."</option>";
				}
			print "</select>";
			print "<select name='StartHalf'>";
				print "\r\n<option value='pm'";
					if ($s_ap=="pm") print " selected";
				print ">pm / Noon</option>";
				print "\r\n<option value='am'";
					if ($s_ap=="am") print " selected";
				print ">am / Midnight</option>";
			print "</select></td></tr>";
		print "<tr><td>Panel Duration</td><td>";
			print "<select name='DurationHours'>";
				for ($i = 0; $i <= 24; $i++) {
					print "\r\n<option value='".$i."' ";
					if ($i==$e_Hd) print "selected";
					print ">".$i."</option>";
				}

			print "</select>";
			print "<select name='DurationMinutes'>";
				for ($i = 0; $i < 60; $i=$i + 5) {
					$j=$i;
					if ($j<10) $j = "0".$j;
					print "\r\n<option value='".$j."' ";
					if ($e_Md == $i) print "selected";
					print ">".$j."</option>";
				}
			print "</select>";
		print "</td></tr>";
		print "<tr><td>Panel Location</td><td>";
			print "<select name='RoomID'>";
				print "\r\n<option value=0 ";
				if ($row['RoomID']=='0') print "selected";
				print ">No room assigned</option>";
			$query1="select * from `CPDB_Room` where `ConID` = '".$CFG['ConID']."' and `RoomHideGrid`=0 order by `RoomOrder`";
			$sql=mysql_query($query1) or die('Query failed: ' . mysql_error());
			while ($row1 = mysql_fetch_assoc($sql)) {
				print "\r\n<option value='".$row1['RoomID']."' ";
				if ($row1['RoomID']==$row['RoomID']) {
					print "selected";
					$MRoomZone = $row1['RoomZone'];
				}
				print ">".$row1['RoomName']." / ".$row1['RoomPurpose']."</option>";
			}
			print "</select>";
		print "\r\n</td></tr>";
		print "/r/n<tr><td>Room Setup</td><td>";

		print "<select name='SetID'>";
		print "<option value=0 ";
		if ($row['SetID'] == 0) print " selected";
		print ">No Set Selected</option>";
		$query1="select * from `CPDB_RoomSets` where `ConID` = '".$CFG['ConID']."' order by `SetName`";
		$sql=mysql_query($query1) or die('Query failed: ' . mysql_error());
		while ($row1 = mysql_fetch_assoc($sql)) {
			print "\r\n<option value='".$row1['SetID']."' ";
			if ($row1['SetID']==$row['SetID']) {
				print "selected";
			}
			print ">".$row1['SetName']."</option>";
		}
		print "</select>";
		print "\r\n</td></tr>";


		print "<tr><td>Scheduling Notes</td><td><textarea rows=5 cols=40 name='SchedNotes'></textarea></td></tr>";
		if ($panelLock==0) print "<tr><td colspan=2><center><input type='submit' name='submit' value='Update\r\nSchedule'>";
		print "<input type='hidden' name='Action' value='Update Schedule'>";
		print "<input type='hidden' name='PanelID' value='".$_POST['PanelID']."'></form>";
			#print $row['PTRID'];
			if (!(is_null($row['PTRID']))) {
				print "<form method='post'>
					<input type='hidden' name='Action' value='Unschedule'>
					<input type='hidden' name='PtrID' value='".$row['PTRID']."'>
					<input type='hidden' name='PanelID' value='".$_POST['PanelID']."'>
					<input type='submit' name='submit' value='Unschedule'>
					</form>";
		}
		print "</td></tr>";
		print "<script type='text/javascript'>\r\ndocument.forms['target1']['SchedNotes'].value='".$row['SchedNotes']."'\r\n</script>";

		print "</table>\r\n";
	}
	print "</td></tr></div>";
	print "<tr><td><center>";
	#####
	# Display KEY
	#####
	print "<div class='key'>";
	print "<table border=1><tr><td colspan=2><center><b><u><i>key</i></b></u></td></tr>";
	print "<tr><td><b>Sched</b></td><td>Panelist has notes in his/her scheduling request field</td></tr>";
	print "<tr><td><b>Phys</b></td><td>Panelist has notes in his/her Physical Requirements request field</td></tr>";
	print "<tr><td><b>Slot</b></td><td>Panel is scheduled outside of the timeframe specified by the panelist</td></tr></table>";
	print "</td></div>";

	########
	# Scheduling conflicts in PTR table
	########
	print "<td valign='top' class='schedconflict'><div class='schedconflict'><table border=1><tr><td colspan=5><center><b>Panel Scheduling Conflicts</b></td><tr>";
	print "<tr><th></th><th>Panel Title</th><th>Start Time</th><th>End Time</th><th>Room</th></tr>";
		$query="select * from `CPDB_PTR` as S inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID` where `PanelID` = '".$_POST['PanelID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		$row = mysql_fetch_assoc($sql);
		$curStart = $row['Start'];
		$curEnd = $row['End'];
		#$curRoom = $row['RoomID'];
		$curPanel = $row['PanelID'];
		$roomList = "'".$row['RoomID']."','".$row['RoomChild1ID']."','".$row['RoomChild2ID']."','".$row['RoomChild3ID']."','".$row['RoomChild4ID']."','".$row['RoomChild5ID']."','".$row['RoomChild6ID']."','".$row['RoomChild7ID']."','".$row['RoomChild8ID']."','".$row['RoomChild9ID']."','".$row['RoomChild10ID']."'";

		$query = "select * from `CPDB_PTR` as S ";
		$query .= "inner join `CPDB_Panels` as P on P.`PanelID` = S.`PanelID` ";
		$query .= "inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID`  ";
		$query .= "where (S.`RoomID` in (".$roomList.") or (`RoomChild1ID` = '".$row['RoomID']."')or (`RoomChild2ID` = '".$row['RoomID']."')or (`RoomChild3ID` = '".$row['RoomID']."')or (`RoomChild4ID` = '".$row['RoomID']."')or (`RoomChild5ID` = '".$row['RoomID']."')or (`RoomChild6ID` = '".$row['RoomID']."')or (`RoomChild7ID` = '".$row['RoomID']."') or (`RoomChild8ID` = '".$row['RoomID']."') or (`RoomChild9ID` = '".$row['RoomID']."') or (`RoomChild10ID` = '".$row['RoomID']."'))";
		$query .= "and ((`Start` between '".$curStart."' and '".$curEnd."') or (`End` between '".$curStart."' and '".$curEnd."') or('".$curStart."' between `Start` and `End`) or ('".$curEnd."' between `Start` and `End`)) and `Start` <> '".$curEnd."' and `End` <> '".$curStart."'";
		#print "<b><i>".$query."</b></i><br>";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_assoc($sql)) {
			if (!($row['PanelID']==$_POST['PanelID'])) {
				print "<tr bgcolor='red'><form method='post'><input type='hidden' name='Action' value='Panel Description'><input type='hidden' name='PanelID' value='".$row['PanelID']."'><td><input type='submit' name='submit' value='details'></td></form>";
				print "<td>".$row['PanelTitle']."</td><td>".date("l M j ".$CFG['TimeFormat'],strtotime($row['Start']))."</td><td>".date("l M j ".$CFG['TimeFormat'],strtotime($row['End']))."</td><td>".$row['RoomName']."</td></tr>";
			}
		}

	print "</table></td></tr></div>";


	##########
	# Requesting Panelists
	##########

	print "<tr><td valign='top'>";
	print "<div class='requested'>";
	print "<table border=1 width='512px' class='sortable'>";
	print "<thead class='sortlink'><tr><td colspan=9><center><font size=4>Requesting Panelists </font></td></tr>";
	print "<tr><td colspan=9><table border=1><tr>";
	print "<td class='mbgcolor'>No Adjacent Scheduled Panels</td>";
	print "<td class='caution'>Adjacent Scheduled Panels in current Room Zone</td>";
	print "<td class='warning'>Adjacent Scheduled Panels in other Room Zones</td>";
	print "<td class='conflict'>Concurrent Scheduled Panels</td>";
	print "</tr></table>";
	print "</td></tr>";
	print "<tr><td></td><th>Panelist</th><th>Publications Name</th><th>Ranking</th>";
	if ($CFG['dispID'] == 1) print "<th>Panelist ID</th>";
	print "<th>Ranked<br>Panels</th><th>Placed<br>Panels</th><th>&nbsp;</th>";
	print "</tr></thead><tbody>";
	$AssignedPanelists = array();
	$RequestingPanelists = array();
	$query="Select *
			from `CPDB_P2P`
			where `PanelID` = ".$_POST["PanelID"]."
			and `ConID` =".$CFG['ConID'];
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		/*Build array with all Assigned panelists*/
		array_push($AssignedPanelists,$row["PanelistID"]);
	}
	$query="Select `PanelistName`,P.`PanelistID`,`Rank` ,`PanelistPubName`, C.`SchedReqs`, C.`PhysReqs`
			from `CPDB_PanelRanking` as R
			inner join `CPDB_Panelist` as P
			on R.`PanelistID` = P.`PanelistID`
			inner join `CPDB_PanelistCon` as C
			on C.`PanelistID` = P.`PanelistID` and C.`ConID` = '".$CFG['ConID']."'
			where R.`PanelID` = '".$_POST["PanelID"]."'
			order by `Rank`,".$CFG['PanelistListSort'];
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		array_push($RequestingPanelists,$row["PanelistID"]);
		$query2 = " select *
					from `CPDB_P2P` as X
					inner join `CPDB_PTR` as S
					on X.`PanelID` = S.`PanelID` ";
		$query2 .= "where X.`PanelistID` = '".$row['PanelistID']."'
					and X.`PanelID` <> '".$_POST['PanelID']."' ";
		$query2 .= "and ((`Start` between '".$curStart."' and '".$curEnd."') or (`End` between '".$curStart."' and '".$curEnd."') or('".$curStart."' between `Start` and `End`) or ('".$curEnd."' between `Start` and `End`)) and `Start` <> '".$curEnd."' and `End` <> '".$curStart."'";

		$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
		$mbgcolor='noconflict';
		if (mysql_num_rows($sql2)>0) {
			#<
			$mbgcolor='conflict';
		} else {
			$query2 = "select * from `CPDB_P2P` as X inner Join `CPDB_PTR` as S on X.`PanelID` = S.`PanelID` ";
			$query2 .= " inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID` ";
			$query2 .= "where X.`PanelistID` = '".$row['PanelistID']."' and X.`PanelID` <> '".$_POST['PanelID']."' ";
			$query2 .= "and (`Start` = '".$curEnd."' or `End` = '".$curStart."') ";
			$query2 .= "and R.`RoomZone` = '".$MRoomZone."' ";
			$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
			if (mysql_num_rows($sql2)>0) {
				#<
				$mbgcolor='caution';
			} else {
				$query2 = "select * from `CPDB_P2P` as X inner Join `CPDB_PTR` as S on X.`PanelID` = S.`PanelID` ";
				$query2 .= " inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID` ";
				$query2 .= "where X.`PanelistID` = '".$row['PanelistID']."' and X.`PanelID` <> '".$_POST['PanelID']."' ";
				$query2 .= "and (`Start` = '".$curEnd."' or `End` = '".$curStart."') ";
				$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
				if (mysql_num_rows($sql2)>0) {
					#<
					$mbgcolor='warning';
				}
			}
		}
		$queryRankedPanels = "select count(*) as Tally from `CPDB_PanelRanking` where `PanelistID` = '".$row['PanelistID']."' and `ConID` = '".$CFG['ConID']."'";
		$sqlRankedPanels=mysql_query($queryRankedPanels) or die('Query failed: ' . mysql_error());
		$queryPlacedPanels = "select count(*) as Tally from `CPDB_P2P` where `PanelistID` = '".$row['PanelistID']."' and `ConID` = '".$CFG['ConID']."'";
		$sqlPlacedPanels=mysql_query($queryPlacedPanels) or die('Query failed: ' . mysql_error());
		$rowRankedPanels = mysql_fetch_assoc($sqlRankedPanels);
		$rowPlacedPanels = mysql_fetch_assoc($sqlPlacedPanels);

		print "<tr class='".$mbgcolor."'>";
		print "<form method='post'><input type='hidden' name='Action' value='Panelist Detail'><td><input type='submit' name='details' value='details'>";
		if (!($row['SchedReqs']=='')) {
			print '<br><b><a href="" onMouseOver="alert(';
			print "'".str_replace("'","`",str_replace("\r\n","",$row['SchedReqs']))."'";
			print ');return true;">Sched</a></b>';
		}
		if (!($row['PhysReqs']=='')) {
			print '<br><b><a href="" onMouseOver="alert(';
			print "'".str_replace("'","`",str_replace("\r\n","",$row['PhysReqs']))."'";
			print ');return true;">Phys</a></b>';
		}
		####################################################################################
		# display slot message if the number of slots the panelist is available for
		# durring the time of the panel is less than the number of hours the panel runs
		####################################################################################
		$querySlotsTtl = "select * from CPDB_Availability
						where `ConID` = '".$CFG['ConID']."'
						and `PanelistID` = '".$row['PanelistID']."'";
		$sqlSlotsTtl=mysql_query($querySlotsTtl) or die('Query failed: ' . mysql_error());
		if (mysql_num_rows($sqlSlotsTtl)==0){
			# do nothing no message suppressed as panelist failed to fill out availability
		} else {
			$queryNewSlot = "select * from CPDB_Availability
								where `ConID` = '".$CFG['ConID']."'
								and `PanelistID` = '".$row['PanelistID']."'
								and `AvailHour` >= '".date("Y-m-d H:i:s",$s_ts)."'
								and `AvailHour` < '".date("Y-m-d H:i:s",$e_ts)."'
								order by `AvailHour`";
			$sqlNewSlot=mysql_query($queryNewSlot) or die('Query failed: ' . mysql_error());
			$newSlotDur = ($e_ts - $s_ts)/60/60;
			$newSlotRC = mysql_num_rows($sqlNewSlot);
			if (!($newSlotRC== $newSlotDur)) {
				print "<br><b><font color='red' size=1>Not Available</font></b>";
			}
		}


		print "</td><input type='hidden' name='PanelistID' value=".$row["PanelistID"]."></form>\r\n";
		print "<td>".$row["PanelistName"]."</td><td>".$row['PanelistPubName']."</td><td>".$row["Rank"]."</td>";
		if ($CFG['dispID'] == 1) print "<td>".$row["PanelistID"]."</td>";
		print "<td>".$rowRankedPanels['Tally']."</td>";
		print "<td>".$rowPlacedPanels['Tally']."</td>";
		if (!in_array($row["PanelistID"],$AssignedPanelists)) {
			print"<form method='post'>";
			print "<td>";
			if ($panelLock==0 && $approved==1) print "<input type='submit' name='submit' value='Add'>";
			print "</td>";
			print "<input type='hidden' name='Action' value='Add Panelist 2 Panel'>";
			print "<input type='hidden' name='PanelID' value='".$_POST["PanelID"]."'>";
			print "<input type='hidden' name='PanelistID' value='".$row["PanelistID"]."'>";
			print "</form>";
		} else {
			print "<td>&nbsp;</td>";
		}
		print "</tr>";
	}
	print "</tbody><tfoot>";
	print "<tr><form method='post'><td> </td><td colspan=3><select name='PanelistID'>";
	$query="Select * from `CPDB_Panelist`
			where `PanelistID` in (select `PanelistID` from `CPDB_V_Panelist2Con` where `ConID` = '".$CFG['ConID']."')
			order by `IsEquip`,`PanelistName`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		if ((!in_array($row["PanelistID"],$RequestingPanelists))&&(!in_array($row["PanelistID"],$AssignedPanelists))) {
			if ($row['IsEquip']==1) {
				print "<option value='".$row["PanelistID"]."'>EQUIPMENT: ".$row["PanelistName"]."</option>";
			} else {
				print "<option value='".$row["PanelistID"]."'>".$row["PanelistName"]."</option>";
			}
		}
	}
	print "</select></td><td>";
	if ($panelLock==0 && $approved==1) print "<input type='submit' name='submit' value='Add'>";
	print "<input type='hidden' name='Action' value='Add Panelist 2 Panel and Rank'><input type='hidden' name='PanelID' value='".$_POST["PanelID"]."'></td></form></tr>";
	$PanelistList = "";
	foreach ($RequestingPanelists as $value){
		if ($PanelistList==""){
			$PanelistList .= $value;
		} else {
			$PanelistList .= ",".$value;
		}
	}
	if ($CFG['USERLEVEL']=='Admin') {
	print "<tr><td colspan=7>The below insert method will not send an invite, and may result in duplicate records for a panelist</td></tr>";
	print "<tr><td><form method='post'>&nbsp;</td>
			<input type='hidden' name='Action' value='Create and Add Panelist'>
			<input type='hidden' name='PanelID' value='".$_POST["PanelID"]."'>
			<td colspan=2>Last Name<br><input type='text' name='PanelistLastName'></td>
			<td colspan=2>First Name<br><input type='text' name='PanelistFirstName'></td>
			<td colspan=2 rowspan=2><input type='submit' name='submit' value='submit'></form></td></tr>
			<tr><td>&nbsp;</td>
			<td colspan=2>Pub Name<br><input type='text' name='PanelistPubName'></td>
			<td colspan=2>Email Address<br><input type='text' name='PanelistEmail'></td>
			<tr>";
	}
	print "<tr><form method='post' target='Availability matrix'><td colspan=8><input type='submit' name='Action' value='Availability Matrix'></td><input type='hidden' name='PanelistList' value='".$PanelistList."'></form></tr>";
	print "</tfoot></table></td>";
	print "</div>";

	#########
	#Display Assigned Panelists
	#########
	print "<div class='seated'>";
	print "<td valign='top' class='seated'>";
	print "<table border=1 class='sortable'>";
	print "<thead class='sortlink'><tr><td colspan=4><font size=4><center>Assigned Panelists</font></td></tr>";
	print "<tr><th>ID</th><th>Name</th><th>Moderator</th><th>&nbsp;</th></tr></thead><tbody>";
	$query="select * from `CPDB_Panelist` P inner join `CPDB_P2P` as L on P.`PanelistID` = L.`PanelistID` where L.`PanelID` = '".$_POST["PanelID"]."' order by Moderator desc, PanelistName";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$query2 = " select * from `CPDB_P2P` as X inner join `CPDB_PTR` as S on X.`PanelID` = S.`PanelID` ";
		$query2 .= "where X.`PanelistID` = '".$row['PanelistID']."' and X.`PanelID` <> '".$_POST['PanelID']."' ";
		$query2 .= "and ((`Start` between '".$curStart."' and '".$curEnd."') or (`End` between '".$curStart."' and '".$curEnd."') or('".$curStart."' between `Start` and `End`) or ('".$curEnd."' between `Start` and `End`)) and `Start` <> '".$curEnd."' and `End` <> '".$curStart."'";
		$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
		$mbgcolor='noconflict';
		if (mysql_num_rows($sql2)>0) $mbgcolor='conflict';
		#<
		print "<tr class='".$mbgcolor."'>";
		if ($CFG['dispID'] == 1) print "<td>".$row["PanelistID"];"</td>";
		print "<td>".$row["PanelistName"];
		if (!($row['SchedReqs']=='')) print "<br><B>Sched</b>";
		if (!($row['PhysReqs']=='')) print "<br><B>Phys</b>";


		####################################################################################
		# display slot message if the number of slots the panelist is available for
		# durring the time of the panel is less than the number of hours the panel runs
		####################################################################################
		$querySlotsTtl = "select * from CPDB_Availability
						where `ConID` = '".$CFG['ConID']."'
						and `PanelistID` = '".$row['PanelistID']."'";
		$sqlSlotsTtl=mysql_query($querySlotsTtl) or die('Query failed: ' . mysql_error());
		if (mysql_num_rows($sqlSlotsTtl)==0){
			# do nothing no message suppressed as panelist failed to fill out availability
		} else {
			$queryNewSlot = "select * from CPDB_Availability
								where `ConID` = '".$CFG['ConID']."'
								and `PanelistID` = '".$row['PanelistID']."'
								and `AvailHour` >= '".date("Y-m-d H:i:s",$s_ts)."'
								and `AvailHour` < '".date("Y-m-d H:i:s",$e_ts)."'
								order by `AvailHour`";
			$sqlNewSlot=mysql_query($queryNewSlot) or die('Query failed: ' . mysql_error());
			$newSlotDur = ($e_ts - $s_ts)/60/60;
			$newSlotRC = mysql_num_rows($sqlNewSlot);
			if (!($newSlotRC== $newSlotDur)) {
				print "<br><b><font color='red' size=1>Not Available at this time</font></b>";
			}
		}
		print "</td><td>";
		if ($row["Moderator"] == '1') {
			print "Moderator";
		} else {
			print "<form method='post'><input type='hidden' name='Action' value='Make Moderator'><input type='hidden' name='P2PID' value='".$row["P2PID"]."'><input type='hidden' name='PanelID' value='".$row["PanelID"]."'>";
			if ($panelLock==0) print "<input type='submit' value='Make\r\nModerator'> ";
		}
		print "</td></form><form method='post'><td>";
		if ($panelLock==0) print "<input type='submit' value='Remove'>";
		print "<input type='hidden' name='Action' value='Remove Panelist From Panel'><input type='hidden' name='P2PID' value='".$row["P2PID"]."'><input type='hidden' name='PanelID' value='".$row["PanelID"]."'></td></form></tr>";
	}
	$PanelistList = "";
	foreach ($AssignedPanelists as $value){
		#<
		if ($PanelistList==""){
			$PanelistList .= $value;
		} else {
			$PanelistList .= ",".$value;
		}
	}
	print "</tbody><tfoot><tr><form method='post' target='Availability matrix'><td colspan=4><input type='submit' name='Action' value='Availability Matrix'></td><input type='hidden' name='PanelistList' value='".$PanelistList."'></form></tr>";
	$emailquery="SELECT group_concat(PanelistEmail ORDER BY PanelistEmail DESC SEPARATOR ';<br>') as emaillist
	FROM `CPDB_P2P` as L
	inner join CPDB_Panelist as P
	on P.PanelistID = L.PanelistID
	WHERE `PanelID` = '".$_POST["PanelID"]."'
	group by Panelid";
	$sqlemailquery=mysql_query($emailquery) or die('Query failed: ' . mysql_error());
	while ($emailrow = mysql_fetch_assoc($sqlemailquery)) {
		print "<tr><td colspan=1>Email Addresses</td><td colspan=3>".$emailrow['emaillist']."</td></tr>";
	}
	print "</tfoot></table></td></tr>";
	print "&nbsp;</table>";
	print "</div>";
}

function Display_Panelist_Guest_Detail()
{


}

function Display_Panelist_Detail()
{
	global $CFG;
	$query="select * from `CPDB_Panelist` as P inner join `CPDB_PanelistCon` as C on P.PanelistId = C.PanelistID where P.PanelistId = '".$_POST['PanelistID']."' and C.ConID = '".$CFG['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	if (mysql_num_rows($sql)==0) {
		print "<font color='red'>The panelist selected does not exist for the current convention year</font><br>";
		return;
	}
	$row = mysql_fetch_assoc($sql);
	if ($row['IsEquip']==1) {
		print "<font color='red'>The item selected is a piece of equipment, and can not be edited here</font><br>";
		return;
	}
#	array_table($CFG,"ff9999",4,"Exploding '$ CFG'");
	print "";

		print "<table width='340px'><tr>
				<form method='post'>
				<input type='hidden' name='Action' value='Clear Report'>
				<input type='hidden' name='PanelistID' value='".$_POST['PanelistID']."'>
				<input type='hidden' name='DisplayContact' value=1>
				<input type='hidden' name='SubAction' value='Print Panelist Schedule'>
				<td><input type='submit' name='submit' value='Printable Schedule\r\nWith Contact Info'></td>
				</form>";
		print "<form method='post'>
				<input type='hidden' name='Action' value='Clear Report'>
				<input type='hidden' name='PanelistID' value='".$_POST['PanelistID']."'>
				<input type='hidden' name='DisplayContact' value=0>
				<input type='hidden' name='SubAction' value='Print Panelist Schedule'>
				<td><input type='submit' name='submit' value='Printable Schedule\r\nWith No Contact Info'></td>
				</form>";
		print "<form method='post' target='new' action='tentcard.php'>
				<td><input type='submit' name='submit' value='Printable\r\nTent Card'></td>
				<input type='hidden' name='cid' value='".$CFG['ConID']."'>
				<input type='hidden' name='pid' value='".$_POST['PanelistID']."'>
				</form>";
		print "</tr></table>";

	$timeopts = " <option value='10:00am'>10:00am</option>
					<option value='11:00am'>11:00am</option>
					<option value='12:00pm'>Noon</option>
					<option value='1:00pm'>1:00pm</option>
					<option value='2:00pm'>2:00pm</option>
					<option value='3:00pm'>3:00pm</option>
					<option value='4:00pm'>4:00pm</option>
					<option value='5:00pm'>5:00pm</option>
					<option value='6:00pm'>6:00pm</option>
					<option value='7:00pm'>7:00pm</option>
					<option value='8:00pm'>8:00pm</option>
					<option value='9:00pm'>9:00pm</option>
					<option value='10:00pm'>10:00pm</option>
					<option value='11:00pm'>11:00pm</option>
					<option value='12:00am'>Midnight</option>
					<option value='1:00am'>1:00am</option>
					<option value='2:00am'>2:00am</option>
					<option value='3:00am'>3:00am</option>
					<option value='4:00am'>4:00am</option>";
	$query="Select * from `CPDB_Panelist` where PanelistID = '".$_POST["PanelistID"]."'";
	$query="select * from `CPDB_Panelist` as P inner join `CPDB_PanelistCon` as C on P.PanelistId = C.PanelistID where P.PanelistId = '".$_POST['PanelistID']."' and C.ConID = '".$CFG['ConID']."'";
#	display_query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);

	if ($CFG['EditPanelist']=="GRANT") {
		#if Edit Permisions exist, enable the form
		print "<form name='target16' method='post'>
		<input type='hidden' name='Action' value='Update Panelist'>
		<input type='hidden' name='PanelistID' value='".$_POST['PanelistID']."'>";
	}

		print "<table border=1><tr><td  valign='top' width='512px'>";
print "<div class='secform'>";
		if ($CFG['dispID'] == 1) print "Panelist ID = ".$row["PanelistID"];
		if ($CFG['EditPanelist']=="GRANT"){
			print "<br>Editable";
		} else {
			print "<br>Panelist is not Editable";
		}
		print "<table border=1 valign='top' width='100%'><tr><th colspan=2 width='100%'>Panelist Information</th></tr>";
		print "<tr><td>Name</td><td><input type='text' name='PanelistName' value='".$row['PanelistName']."'></td></tr>
				<tr><td>Badge Name</td><td><input type='text' name='PanelistBadgeName' value='".$row['PanelistBadgeName']."'></td></tr>
				<tr><td>Pubs Name</td><td><input type='text' name='PanelistPubName' value='".$row['PanelistPubName']."'></td></tr>
				<tr><td>Last Name</td><td><input type='text' name='PanelistLastName' value='".$row['PanelistLastName']."'></td></tr>
				<tr><td>First Name</td><td><input type='text' name='PanelistFirstName' value='".$row['PanelistFirstName']."'></td></tr>";
		if ($CFG['ViewContact']=="GRANT") print "<tr><td>Address</td><td><input type='text' name='PanelistAddress' value='".$row['PanelistAddress']."'></td></tr>
				<tr><td>City</td><td><input type='text' name='PanelistCity' value='".$row['PanelistCity']."'></td></tr>
				<tr><td>State</td><td><input type='text' name='PanelistState' value='".$row['PanelistState']."'></td></tr>
				<tr><td>Zip</td><td><input type='text' name='PanelistZip' value='".$row['PanelistZip']."'></td></tr>
				<tr><td>Email</td><td><input type='text' name='PanelistEmail' value='".$row['PanelistEmail']."'></td></tr>
				<tr><td>Day Phone</td><td><input type='text' name='PanelistPhoneDay' value='".$row['PanelistPhoneDay']."'></td></tr>
				<tr><td>Eve Phone</td><td><input type='text' name='PanelistPhoneEve' value='".$row['PanelistPhoneEve']."'></td></tr>
				<tr><td>Cell Phone</td><td><input type='text' name='PanelistPhoneCell' value='".$row['PanelistPhoneCell']."'></td></tr>";
		print "<tr><td>List on Web Site</td><td>";
		if ($row["listme"] =='1') {
			print "<select name='listme'><option value='yes' selected>Yes</option><option value='no'>No</option></select>";
		} else {
			print "<select name='listme'><option value='yes'>Yes</option><option value='no' selected>No</option></select>";
		}
		print "</td></tr>";
		print "<tr><td>Share Phone Contact</td><td>";
		if ($row["sharephone"] =='1') {
			print "<select name='sharephone'><option value='1' selected>Yes</option><option value='0'>No</option></select>";
		} else {
			print "<select name='sharephone'><option value='1'>Yes</option><option value='0' selected>No</option></select>";
		}
		print "</td></tr>";
		print "<tr><td>Share EMail Contact</td><td> ";
		if ($row["shareemail"] =='1') {
			print "<select name='shareemail'><option value='1' selected>Yes</option><option value='0'>No</option></select>";
		} else {
			print "<select name='shareemail'><option value='1'>Yes</option><option value='0' selected>No</option></select>";
		}
		print "</td></tr>";
		print "<tr><td>Share Mail Contact</td><td> ";
		if ($row["sharemail"] =='1') {
			print "<select name='sharemail'><option value='1' selected>Yes</option><option value='0'>No</option></select>";
		} else {
			print "<select name='sharemail'><option value='1'>Yes</option><option value='0' selected>No</option></select>";
		}
		print "</td></tr>";
	print "</table>";
	$AVAIL['garbage']='';
	$query1="select * from CPDB_Availability where `PanelistID` = '".$_POST['PanelistID']."' and `ConID` = '".$CFG['ConID']."'";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	while ($row1 = mysql_fetch_assoc($sql1)) {
		$AVAIL[$row1['AvailHour']] = 1;
	}
	$query2="select * from CPDB_PanelistCon where `ConID` = '".$CFG['ConID']."' and `PanelistID` = '".$_POST['PanelistID']."'";
	$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
	$row2 = mysql_fetch_assoc($sql2);

	$MAX['garbage'] = '';
	$query3="select * from CPDB_MaxPanels where `ConID` = '".$CFG['ConID']."' and `PanelistID` = '".$_POST['PanelistID']."'";
	$sql3=mysql_query($query3) or die('Query failed: ' . mysql_error());
	while ($row3 = mysql_fetch_assoc($sql3)) {
		$MAX[$row3['Date']] = $row3['MaxPanels'];
	}
	$query4="select * from `CPDB_PanelistCon` where `ConID` = '".$CFG['ConID']."' and `PanelistID` = '".$_POST['PanelistID']."'";
	$sql4=mysql_query($query4) or die('Query failed: ' . mysql_error());
	$row4 = mysql_fetch_assoc($sql4);


	$StartTime = strtotime($CFG['constartdate']." 00:00:00");
	$StartHour = $StartTime + (60*60*($CFG['ConStartHour']-1));
	$EndHour = $StartTime + (($CFG['conrundays']-1)*24*60*60) + (60*60*$CFG['ConEndHour']);

	print "<table><tr><th colspan=2><center>Availability Key</th></tr>
			<tr><td bgcolor='navy'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Oustside of Convention Hours</td></tr>
			<tr><td bgcolor='green'>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Panelist is Available to be placed on Panels</td></tr>
			<tr><td bgcolor='thistle'>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Panelist is not available to be placed on panels</td></tr></table>";
	print "</td><td valign='top'>";
	print "<div class='secform'>";
	print "<table border=1><tr><td colspan=25><center>Availability</td>";

	print "<tr><td></td>";
	if ($CFG['LastDailyHour']==0) {
		$myLastHour = 24;
	} else {
		$myLastHour = $CFG['LastDailyHour'];
	}
	for($i=$CFG['FirstDailyHour'];$i<=$myLastHour;$i++){
		$txt = date('g A',(60*60*($i+8)));
		#$txt .= " ".$i;
		print "<td>".$txt."</td>";
	}
	print "<td>Max Panels</td>";
	print "</tr>";
	for ($i=0;$i<$CFG['conrundays'];$i++){
		#Days
		$mydate1=date('M j',$StartTime+($i*24*60*60));
		$mydate2=date('Y-m-d',$StartTime+($i*24*60*60));
		print "<tr><td>".$mydate1."</td>";
		for ($j=$CFG['FirstDailyHour'];$j<=$myLastHour;$j++) {
		# hours
			$now = $StartTime+($i*24*60*60)+(($j+0)*60*60);
			$slot=date('Y-m-d H:i:s',$now);
			print "<td ";
			if (($now<=$StartHour) || ($now >=$EndHour)) {
				#<
				print " bgcolor='navy'> ";

			} else {
				if (array_key_exists($slot, $AVAIL)) {
					print " bgcolor='green'> ";
				} else {
					print " bgcolor='thistle'> ";
				}
					print "<input type='checkbox' name='Availability[]' value='".$slot."' ";
					if (array_key_exists($slot, $AVAIL)) print " checked ";
					print ">";
			}
			print "</td>\r\n";
		}
		print "<td><input type=text size=3 name='Max-".$mydate2."' value='".$MAX[$mydate2]."'></td>";
		print "</tr>";
	}
	print "</table>";

	print "<table><tr><th>Schedule Requests</th>
			<td><textarea rows=5 cols=40 name='SchedReqs'>".$row4['SchedReqs']."</textarea></td><td rowspan=3><img src='../img.php?pid=".$_POST['PanelistID']."'></td></tr>
			<tr><th>Physical Requests</th>
			<td><textarea rows=5 cols=40 name='PhysReqs'>".$row4['PhysReqs']."</textarea></td></tr>
			<tr><th>Biography</th>
			<td><textarea rows=5 cols=40 name='Biography'>".$row['Biography']."</textarea></td></tr>
			<tr><td colspan=2><center>";
	if ($CFG['EditPanelist']=="GRANT"){
		print "<center><input type='submit' name='submit' value='Update Panelist'></form>";
	} else {
		print "</form>";
	}
	print "</td><td>";

	print "</div>";
	print "<div class='fileupdate'>";
	print "<br><center><B>Insert/Update Picture</b></center>";
	print "<table border=1><form method='post' ENCTYPE='multipart/form-data'><input type='hidden' name='Action' value='Update Image'><input type='hidden' name='PanelistID' value='".$row['PanelistID']."'><input type='hidden' name='ImageID' value='".$row['ImageID']."'>";
	print "<tr><td>New Image</td></tr><tr><td><input type='file' name='userfile' size=20></td></tr>";
	print "<tr><td colspan=2><input type='submit' name='submit' value='Change Image'></td></tr></form></table>";
	print "</div>";
	print "</div>";
	print "</td></tr></table>";


print "</td></tr></table><table border=1><tr><td valign='top' width='340px'>";
#######
# Panels Assigned
#######
	print "<div class='seated'>";
	print "<table border=1 width='512px'><tr><td colspan=5 width='340px'><center><b>Panels Assigned</b></center></td></tr>";
	$query1 =  " select * from `CPDB_P2P` as X ";
	$query1 .= "left outer join `CPDB_PTR` as S on X.`PanelID` = S.`PanelID` ";
	$query1 .= "left outer join `CPDB_Room` as R on S.`RoomID` = R.`RoomID` ";
	$query1 .= "left outer join `CPDB_Panels` as P on P.`PanelId` = X.`PanelID` ";
	$query1 .= "where X.`PanelistID` = '".$_POST['PanelistID']."' and P.`ConID` = '".$CFG['ConID']."' order by S.`Start`";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	while ($row1 = mysql_fetch_assoc($sql1)){
		$query2="select * from `CPDB_P2P` as O inner Join `CPDB_PTR` as S on O.`PanelID` = S.`PanelID` where O.`PanelistID` = '".$_POST['PanelistID']."' ";
		$query2 .= "and ((`Start` between '".$row1['Start']."' and '".$row1['End']."') or (`End` between '".$row1['Start']."' and '".$row1['End']."') or('".$row1['Start']."' between `Start` and `End`) or ('".$row1['End']."' between `Start` and `End`)) and `Start` <> '".$row1['End']."' and `End` <> '".$row1['Start']."'";
		$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		if(!(mysql_num_rows($sql2) == 1)) {
			print "<div class='conflict'>";
		} else {
			print "<div class='noconflict'>";
		}
		print "<tr'><form method='post'><input type='hidden' name='Action' value='Panel Description'><input type='hidden' name='PanelID' value='".$row1['PanelID']."'><td><input type='submit' name='submit' value='details'></td></form>";
		print "<td>".$row1['PanelTitle']."</td>";
		if (!($row1['RoomID'] =='')) {
			print "<td>".$row1['RoomName']."</td>";
		} else {
			print "<td bgcolor='#cc66cc'>No Room</td>";
		}
		if ($row1['Start'] == ''){
			print "<td bgcolor='#cc66cc'>No Start Time</td>";
		} else {
			print "<td>".date("D M j g:i a",strtotime($row1['Start']))."</td>";
		}
		if ($row1['End'] == ''){
			print "<td bgcolor='#cc66cc'>No End Time</td>";
		} else {
			print "<td>".date("D M j g:i a",strtotime($row1['End']))."</td>";
		}
		print "</div></tr>";
	}
	print "</table>";
	print "</div>";
	print "</td><td>";

#######
# Panels Requested
#######

#	print "<br><hr><br>";
	print "<div class='requested'>";
	print "<table border=1 width='512px'><tr><td colspan=6>";
	print "<center><b>Panels Requested</b></center>";
	print "</td></tr>";
	print "<tr><td></td>";
			if ($CFG['dispID'] == 1) print "<th>Panel ID</th>";
	print "<TH>Rank</th><th>Panel Title</th><th>Panelists<br>Assigned</th><th>remove<br>Request</th></tr>";

	$pidlst='';
	$query="Select * from `CPDB_PanelRanking` as R inner join `CPDB_Panels` as P on P.`PanelID` = R.`PanelID` where R.`PanelistID` = '".$_POST["PanelistID"]."' and P.`ConID` = '".$CFG['ConID']."' order by `Rank`, R.`PanelID`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$query1="select count(*) as Tally from `CPDB_P2P` where `PanelID` = '".$row['PanelID']."'";
		$query2="select * from `CPDB_PTR` where `PanelID` = '".$row['PanelID']."'";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
		$row1 = mysql_fetch_assoc($sql1);
		$row2 = mysql_fetch_assoc($sql2);
		if (mysql_num_rows($sql2) == 0) {
			$bg='conflict';
			$sdow='';
			$stod='';
		} elseif (!((!($row2['RoomID']==0)) and (!($row2['Start']=='0000-00-00 00:00:00')))) {
			$bg='conflict';
			$sdow='';
			$stod = '';
		} else {
			$bg='noconflict';
			$sdow=date("D",strtotime($row2['Start']));
			$stod=date("g:i a",strtotime($row2['Start']));

		}
		print "<tr><td><form method='post'><input type='hidden' name='Action' value='Panel Description'><input type='submit' name='details' value='details'><input type='hidden' name='PanelID' value='".$row["PanelID"]."'></form></td>";
		if ($CFG['dispID'] == 1) print "<td>".$row["PanelID"]."</td>";
		print "<td>".$row["Rank"]."</td><td>".$row["PanelTitle"]."</td>";
		print "<td class='".$bg."'>".$row1['Tally'];
		if (!($sdow=='')) print "<br>".$sdow;
		if (!($stod=='')) print "<br>".$stod;
		print "</td>";
		print "<form method='post'><input type='hidden' name='Action' value='Remove Panel Ranking'><input type='hidden' name='PanelRankID' value='".$row['PanelRankID']."'><td><input type='image' name='submit' SRC='../admin/ui_images/delete.JPG' alt='Remove' value='Remove'></td><input type='hidden' name='PanelistID' value='".$row['PanelistID']."'></form></tr>";
		$pidlst = $pidlst . "'".$row['PanelID']."', ";
	}
	print "</table>";
	print "<br>Add Ranked Panels<table>";
	print "<form method='post'><tr><td>Panel<select name='PanelID' length=20>";
	$query="Select * from `CPDB_Panels` where `PanelID` not in (".$pidlst."'') and `ConID` = '".$CFG['ConID']."' order by `PanelTitle`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<option value='".$row['PanelID']."'>".$row['PanelTitle']."</option>\r\n";
	}
	print "</select>";
	print "</td></tr>";
	print "<tr><td>Rank<Select name='Rank'><option value=1>1</option><option value=2>2</option><option value=3>3</option><option value=4>4</option><option value=5>5</option></select></td></tr>";
	print "<tr><td><input type='submit' name='submit' value='Add Ranking'></td></tr>";
	print "<input type='hidden' name='PanelistID' value='".$_POST['PanelistID']."'><input type='hidden' name='Action' value='Insert Panelist Ranking'></table>";
	print "</td></tr></table>";
	print "</div>";
}

###############################################
#
# Facility Functions moved to facilities.php
# 10/25/2009
#
###############################################
#function Display_Facilities()
#{
#	global $CFG;
#	$RoomAray["0"]=" ";
#	$query="Select * from `CPDB_Room` as R inner join `CPDB_Zone` as Z on R.`RoomZone` = Z.`ZoneID` where R.`ConID` = '".$CFG['ConID']."' order by `RoomOrder`, `RoomName`";
#	Display_Query($query);
#	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#	while ($row = mysql_fetch_assoc($sql)) {
#		if (($row["RoomChild1ID"]=='0')&&($row["RoomChild2ID"]=='0')&&($row["RoomChild3ID"]=='0')&&($row["RoomChild4ID"]=='0')&&($row["RoomChild5ID"]=='0')&&($row["RoomChild6ID"]=='0')&&($row["RoomChild7ID"]=='0')&&($row["RoomChild8ID"]=='0')&&($row["RoomChild9ID"]=='0')&&($row["RoomChild10ID"]=='0')){
#			$RoomAray[$row["RoomID"]]=$row["RoomName"];
#		}
#	}
#	print "<div class='datareport'>";
#	print "<table><tr><td valign='top'><center><font size=4>Rooms</font></center>";
#	print "<Table border=1><tr><td></td>";
#	if ($CFG['dispID'] == 1) print "<td>Room<br>ID</td>";
#	print "<td>Panels<br>Scheduled</td>";
#	print "<td>Room Name</td>";
#	print "<td>Room<br>Sqr. Ft.</td>";
#	print "<td>Child 1</td>";
#	print "<td>Child 2</td>";
#	print "<td>Child 3</td>";
#	print "<td>Child 4</td>";
#	print "<td>Child 5</td>";
#	print "<td>Child 6</td>";
#	print "<td>Child 7</td>";
#	print "<td>Child 8</td>";
#	print "<td>Child 9</td>";
#	print "<td>Child 10</td>";
#	print "<td>Room Order</td>";
#	print "<td>Room Zone</td>";
#	print "<td>Hide Grid</td></tr>";
#
#	$myrowcolor='#ffff88';
#	mysql_data_seek($sql,0);
#	while ($row = mysql_fetch_assoc($sql)) {
#		if ($myrowcolor=='#ffff88') {
#			$myrowcolor='#808080';
#		} else {
#			$myrowcolor='#ffff88';
#		}
#		print "\n\r<tr bgcolor='".$myrowcolor."'>";
#		$query1="select * from `CPDB_PTR` where `RoomID` = '".$row['RoomID']."'";
#		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
#		$ttlrow = mysql_num_rows($sql1);
#		if ($ttlrow == 0) {
#			#print "<form method='post'><td rowspan=2><input type='submit' name='submit' value='Remove Room'><input type='hidden' name='Action' value='Remove Room'><input type='hidden' name='RoomID' value='".$row["RoomID"]."'></td></form>";
#			print "<td rowspan=2>&nbsp;</td>";
#		} else {
#			print "<form method='post'><td rowspan=2><input type='submit' name='submit' value='Room Schedule'></td><input type='hidden' name='Action' value='Room Schedule'><input type='hidden' name='RoomID' value='".$row["RoomID"]."'></form>";
#		}
#		$query2="select count(*) as Tally from `CPDB_PTR` where `RoomID` = '".$row['RoomID']."' ";
#		$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
#		$row2 = mysql_fetch_assoc($sql2);
#		if ($CFG['dispID'] == 1) print "<td>".$row["RoomID"]."</td>";
#		print "<td>".$row2['Tally']."</td>";
#		print "<td>".$row["RoomName"]."</td>";
#		print "<td>".$row["RoomSqr"]."</td>";
#		print "<td>".$RoomAray[$row["RoomChild1ID"]]."</td>";
#		print "<td>".$RoomAray[$row["RoomChild2ID"]]."</td>";
#		print "<td>".$RoomAray[$row["RoomChild3ID"]]."</td>";
#		print "<td>".$RoomAray[$row["RoomChild4ID"]]."</td>";
#		print "<td>".$RoomAray[$row["RoomChild5ID"]]."</td>";
#		print "<td>".$RoomAray[$row["RoomChild6ID"]]."</td>";
#		print "<td>".$RoomAray[$row["RoomChild7ID"]]."</td>";
#		print "<td>".$RoomAray[$row["RoomChild8ID"]]."</td>";
#		print "<td>".$RoomAray[$row["RoomChild9ID"]]."</td>";
#		print "<td>".$RoomAray[$row["RoomChild10ID"]]."</td>";
#		print "<td>".$row["RoomOrder"]."</td>";
#		print "<td>".$row['ZoneName']."</td>";
#		print "<td>".$row['RoomHideGrid']."</td>";
#		print "</tr>\n\r";
#		if ($CFG['dispID'] == 1) {
#			print "<tr bgcolor='".$myrowcolor."'><td colspan=17>".$row['RoomNotes']."</td></tr>";
#		} else {
#			print "<tr bgcolor='".$myrowcolor."'><td colspan=16>".$row['RoomNotes']."</td></tr>";
#		}
#		print "\n\r";
#	}
#	print "</table>";
#	print "<div class='secform'>";
#	if ($CFG['dispID'] == 1) print "<table><tr><td colspan=4><form method='post'><input type='submit' name='submit' value='Edit Room'><input type='hidden' name='Action' value='Edit Room'><input type='text' name='RoomID' size=3></form></td></tr>";
#	print "</table>";
#	$options="<option value='0' selected></option>";
#	foreach ($RoomAray as $key => $value){
#		/*<*/
#		$options = $options . "<option value='".$key."'>".$value."</option>\r\n";
#	}
#
#	print "</td><td valign='top'>";
#	print "<center><font size=4>Insert new Room</font></center>";
#	Form_Add_Room( $options);
#
#	print "</td></tr></table>";
#}
#
#function Form_Add_Room ($options)
#{
#	global $CFG;
#	$query="Select * from `CPDB_Zone` where `ConID` = '".$CFG['ConID']."' order by `ZoneName`";
#	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#	$zone = '';
#	while ($row = mysql_fetch_assoc($sql)) {
#		$zone .= "<option value='".$row['ZoneID']."'>".$row['ZoneName']."</option>";
#	}
#	print "<div class='MainForm'>";
#	print "<table border=1><form method='post'>";
#	print "<tr><td>Room Name</td><td><input type='text' name='RoomName'></td></tr>";
#	print "<tr><td>Room Sqr. Ft.</td><td><input type='text' name='RoomSqr'></td></tr>";
#	print "<tr><td>Room Order</td><td><input type='text' name='RoomOrder'></td></tr>";
#	print "<tr><td>Child Room 1</td><td><select name='ChildRoom1ID'>".$options."</select></td></tr>";
#	print "<tr><td>Child Room 2</td><td><select name='ChildRoom2ID'>".$options."</select></td></tr>";
#	print "<tr><td>Child Room 3</td><td><select name='ChildRoom3ID'>".$options."</select></td></tr>";
#	print "<tr><td>Child Room 4</td><td><select name='ChildRoom4ID'>".$options."</select></td></tr>";
#	print "<tr><td>Child Room 5</td><td><select name='ChildRoom5ID'>".$options."</select></td></tr>";
#	print "<tr><td>Child Room 6</td><td><select name='ChildRoom6ID'>".$options."</select></td></tr>";
#	print "<tr><td>Child Room 7</td><td><select name='ChildRoom7ID'>".$options."</select></td></tr>";
#	print "<tr><td>Child Room 8</td><td><select name='ChildRoom8ID'>".$options."</select></td></tr>";
#	print "<tr><td>Child Room 9</td><td><select name='ChildRoom9ID'>".$options."</select></td></tr>";
#	print "<tr><td>Child Room 10</td><td><select name='ChildRoom10ID'>".$options."</select></td></tr>";
#	print "<tr><td>Notes</td><td><textarea name='RoomNotes'></textarea></td></tr>";
#	print "<tr><td>Room Zone</td><td><select name='RoomZone'>".$zone."</select></td></tr>";
#	print "<tr><td>Hide on Grid</td><td><input type='radio' name='RoomHideGrid' value='0' Checked>NO<br><input type='radio' name='RoomHideGrid' value='1'>Yes</td></tr>";
#	print "<tr><td colspan=2><center><input type='submit' name='Add Room' value='Add Room'><input type='hidden' name='Action' value='Add Room'></td></form></tr>";
#	print "</table>";
#}
#
#function Form_Edit_Room()
#{
#	global $CFG;
#	print "<div class='MainForm'>";
#	$query= "Select * from `CPDB_Room` where `ConID` = '".$CFG['ConID']."'";
#	$query1= "Select * from `CPDB_Room` where RoomID = '".$_POST['RoomID']."'";
#	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
#	$row1 = mysql_fetch_assoc($sql1);
#
#	$query2="Select * from `CPDB_Zone` where `ConID` = '".$CFG['ConID']."' order by `ZoneName`";
#	$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
#	$zone = '';
#	while ($row2 = mysql_fetch_assoc($sql2)) {
#		$zone .= "<option value='".$row2['ZoneID']."'";
#		if ($row1['RoomZone']==$row2['ZoneID']) $zone .= " selected ";
#		$zone .= ">".$row2['ZoneName']."</option>";
#	}
#
#	print "<table border=1><form method='post'><tr><td>";
#	print "<table><tr><td>Room Name</td><td><input type='text' name='RoomName' value='".$row1['RoomName']."'></td></tr>";
#	print "<tr><td>Room Sqr Ft.</td><td><input type='text' name='RoomSqr' value='".$row1['RoomSqr']."'></td></tr>";
#	print "<tr><td>Room Notes</td><td><textarea name='RoomNotes' rows=7 cols=30>".$row1['RoomNotes']."</textarea></td></tr>";
#	print "<tr><td>Room Order</td><td><input type='text' name='RoomOrder' value='".$row1['RoomOrder']."'></td></tr>";
#	print "<tr><td>Room Zone</td><td><select name='RoomZone'>".$zone."</select></td></tr>";
#	print "<tr><td>Hide from grid</td><td><input type='radio' name='RoomHideGrid' value='0'";
#	if ($row1['RoomHideGrid']=='0') print " Checked ";
#	print "                                >No<br><input type='radio' name='RoomHideGrid' value='1'";
#	if ($row1['RoomHideGrid']=='1') print " Checked ";
#	print"								   >Yes</td></tr>";
#	print "</table></td><td><table border=1>";
#	for ($i = 1; $i <= 10; $i ++ ){
#		$rowname= "RoomChild".$i."ID";
#		#print $rowname;
#		print "<tr><td> Child Room ".$i."</td><td><select name='ChildRoom".$i."ID'>";
#		print "<option value='0' selected></option>";
#		while ($row = mysql_fetch_assoc($sql)) {
#			print "<option value='".$row['RoomID']."'";
#
#			if ($row['RoomID'] == $row1[$rowname]) print " selected ";
#			print ">".$row['RoomName']."</option>\r\n";
#		}
#		print "</select></td></tr>";
#		mysql_data_seek($sql,0);
#	}
#	print "</table></tr><tr><td colspan=2>";
#	print "<input type='submit' name='Save Changes' value='Save Changes'><input type='hidden' name='Action' value='Update_Room'><input type='hidden' name='RoomID' value='".$_POST['RoomID']."'></form>";
#	print "</td></tr></table>";
#
#}

function Display_Grid()
{
	global $CFG;
	print "<font size=5>Paneling Grid</font><br>";
	print "<br><table border=1><tr><td><b>Key</b></td></tr>";
	print "<tr><td bgcolor='red'>Conflict</td></tr>";
	print "<tr><td bgcolor='lime'>Publicly displayable panel</td></tr>";
	print "<tr><td bgcolor='#ff00ff'>Panel/Event hidden from Public viewa</td></tr>";
	print "<tr><td bgcolor='#6666cc'>Space is Blocked by Parent/Child room Events</td></tr>";
	print "<tr><td bgcolor='aqua'>Open Time Slot, No pnale Scheduled</td></tr></table><br>";

	## CFG vars to remember
	## constartdate
	## conrundays
	## ConStartHour
	## ConEndHour
	## FirstDailyHour
	## LastDailyHour

	## $_POST[room_range[]] contains rooms to display
	## all rooms if -1 or empty

	## $_POST[cat_range[]] contains categories of panels to display
	## all categories if -1 or empty


	$grid=array();
	# pad grid with 0 (zero)
	$StartTime = strtotime($CFG['constartdate']);
	$StartDate = $StartTime;
	$EndTime = $StartTime + (60*60*24*$CFG['conrundays']);
	$query="select * from `CPDB_Room` where `ConID` = '".$CFG['ConID']."'";
	$room_range = $_POST['room_range'];
	#array_table($room_range);
	if (in_array("-1", $room_range)) {
		#do nothing (get all rooms)
	} else {
		if (count($room_range)==0) {
			# do nothing, empty array, default to all rooms
		} else {
			$roomlist = implode(", ",$_POST['room_range']);
			$query.= " and `RoomID` in (".$roomlist.")";
		}
	}
	Display_Query($query,"rooms to Display");
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		for ($i = $StartTime;$i < $EndTime; $i = $i + 1800){
			$grid[$i][$row['RoomID']]=0;
		}
	}
	$query = "select * from `CPDB_PTR` where `ConID` = '".$CFG['ConID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$StartDTS=strtotime($row['Start']);
		$EndDTS=strtotime($row['End']);

		$query1="select * from `CPDB_PTR` as S inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID` where `PanelID` = '".$row['PanelID']."'";
		Display_Query($query1);
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$row1 = mysql_fetch_assoc($sql1);
		$curPanel = $row['PanelID'];
		$roomList = "'".$row1['RoomID']."','".$row1['RoomChild1ID']."','".$row1['RoomChild2ID']."','".$row1['RoomChild3ID']."','".$row1['RoomChild4ID']."','".$row1['RoomChild5ID']."','".$row1['RoomChild6ID']."','".$row1['RoomChild7ID']."','".$row1['RoomChild8ID']."','".$row1['RoomChild9ID']."','".$row1['RoomChild10ID']."'";
		$curStart = $row['Start'];
		$curEnd = $row['End'];

		$queryCol = "select * from `CPDB_PTR` as S ";
		$queryCol .= "inner join `CPDB_Panels` as P on P.`PanelID` = S.`PanelID` ";
		$queryCol .= "inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID`  ";
		$queryCol .= "where (S.`RoomID` in (".$roomList.") or (`RoomChild1ID` = '".$row['RoomID']."')or (`RoomChild2ID` = '".$row['RoomID']."')or (`RoomChild3ID` = '".$row['RoomID']."')or (`RoomChild4ID` = '".$row['RoomID']."')or (`RoomChild5ID` = '".$row['RoomID']."')or (`RoomChild6ID` = '".$row['RoomID']."')or (`RoomChild7ID` = '".$row['RoomID']."') or (`RoomChild8ID` = '".$row['RoomID']."') or (`RoomChild9ID` = '".$row['RoomID']."') or (`RoomChild10ID` = '".$row['RoomID']."'))";
		$queryCol .= "and ((`Start` between '".$curStart."' and '".$curEnd."') or (`End` between '".$curStart."' and '".$curEnd."') or('".$curStart."' between `Start` and `End`) or ('".$curEnd."' between `Start` and `End`)) and `Start` <> '".$curEnd."' and `End` <> '".$curStart."'";
		Display_Query($queryCol,"Query colums");
		$sqlCol=mysql_query($queryCol) or die('Query failed: ' . mysql_error());
		$ttlrow = mysql_num_rows($sqlCol);



		for ($i = $StartDTS; $i < $EndDTS; $i=$i + 1800) {
			if (!($ttlrow =='1')){
				$grid[$i][$row['RoomID']] = -1 ;
			} else {
				$grid[$i][$row['RoomID']] = $row['PanelID'] ;
			}
		}
	}

	#####
	# Figgure out Parent Child Relationship
	#####
		$PC = array();
		$CP = array();
		$queryChild  = "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild1ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild2ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild3ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild4ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild5ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild6ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild7ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild8ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild9ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild10ID` = B.`RoomID`)";
		Display_Query($queryChild,"Query Child");
		$sqlChild=mysql_query($queryChild) or die('Query failed: ' . mysql_error());
		$child='false';
		while ($rowChild = mysql_fetch_assoc($sqlChild)) {
			$child='true';
			$temp = $rowChild['ParentRoom'].":".$rowChild['RoomID'];
			$tempP = "0:".$rowChild['ParentRoom'];
			$tempC = $rowChild['RoomID'].":0";
			$PC[$rowChild['ParentRoom']][$temp]=$rowChild['RoomID'];
			$PC['0'][$tempP]=$rowChild['ParentRoom'];
			$CP[$rowChild['RoomID']][$temp]= $rowChild['ParentRoom'];
			$CP['0'][$tempC]=$rowChild['RoomID'];
		}
#	foreach ($PC[0] as $key => $value){
#		#<
#		# $ value is all parents
#	}
#	print "<BR>=====<br>";
#	foreach ($CP[0] as $key => $value){
#		#<
#		# $value is all children
#	}
	#####
	# Parents should mask Children when Scheduled
	#####
	$StartTime = strtotime($CFG['constartdate']);
	$EndTime = $StartTime + (60*60*24*$CFG['conrundays']);
	if ($child =='true'){
		foreach ($PC[0] as $key => $value){
			#<
			for ($i = $StartTime;$i<$EndTime;$i =$i + 1800){
				if ($grid[$i][$value] >0) {
					#<
					foreach ($PC[$value] as $value1){
						$grid[$i][$value1]= -2;
					}
				}
			}
		}


		#####
		# Children should mask Parent when scheduled
		#####
		foreach ($CP[0] as $key => $value){
			for ($i = $StartTime;$i<$EndTime;$i = $i + 1800){
				if (!($grid[$i][$value] <1)) {
					foreach ($CP[$value] as $value1){
						$grid[$i][$value1]= -2;
					}
				}
			}
		}
	}
	$queryRoom = "select * from `CPDB_Room` where `ConID` = '".$CFG['ConID']."' ";
	if (in_array("-1", $room_range)) {
		#do nothing (get all rooms)
	} else {
		if (count($room_range)==0) {
			# do nothing, empty array, default to all rooms
		} else {
			$roomlist = implode(", ",$_POST['room_range']);
			$queryRoom.= " and `RoomID` in (".$roomlist.")";
		}
	}
	$queryRoom .= "order by `RoomOrder`, `RoomName`";
	Display_Query($queryRoom,"Rooms");
	$sqlRoom=mysql_query($queryRoom) or die('Query failed: ' . mysql_error());
	print "<tr><th></th>";

print "<br  clear=all style='page-break-before:always'>";

	#######
	# Finaly it`s time to display the grid
	#######
	for ($d = 0;$d<$CFG['conrundays'];$d=$d+1){
		if ($d==0) {
			$FirstHour = $CFG['ConStartHour'];
		} else {
			$FirstHour = $CFG['FirstDailyHour'];
		}
			if ($d==($CFG['conrundays']-1)) {
			$LastHour = $CFG['ConEndHour'];
		} else {
			$LastHour = $CFG['LastDailyHour'];
		}

		######
		# Start the Daily Loop
		######
		$dailyhours = ($LastHour - $FirstHour)*2;
		print "<table border=1><tr><td colspan=50><center>";
		print date("l F jS",$StartDate+($d*60*60*24));
		print "</td></tr><tr><td width='40'></td>";
		for ($i = $StartDate+($d*60*60*24); $i < $StartDate+(($d+1)*60*60*24); $i=$i + 1800){
			print "<td colspan=1 width=40>".date($CFG['TimeFormat'],$i)."</td>";
			if (date("G:i",$i)=='23:30') print "<td width='40'></td>";
		}
		print "</tr>";
		#####
		# Go to the first room, and loop through rooms
		#####
		mysql_data_seek($sqlRoom,0);
		while ($rowRoom = mysql_fetch_assoc($sqlRoom)) {
			print "<tr><td>".$rowRoom['RoomName']."</td>";
			$j=$StartTime+($d*60*60*24); # J is now the beginning of the current day
			for ($i = $StartDate+($d*60*60*24); $i < $StartDate+(($d+1)*60*60*24); $i=$i + 1800){
				#####
				# Start Stepping through the day in 30 min increments
				#####
				#if ($j<$i){
					$i=$j;
				#}
				while (($grid[$i][$rowRoom['RoomID']] == $grid[$j][$rowRoom['RoomID']])and ($j<$StartDate+(($d+1)*60*60*24))){
					$j=$j+1800;
					#####
					# Add 30 minutes to J until panel ends or day ends
					#####
				}
				#if (!(date("D",$i)) == (date("D",$j -1800))){
				#	$j= strtotime(date("Y-m-d 23:30",$i));
					$cols = ($j - $i)/1800 ;
				#} else {
				#	$cols = ($j - $i)/1800 ;
				#}
				if ($i > $StartDate+($d*60*60*24)) {
					#<
					#$cols=$cols+1;
				}

				$cl = 'lime';
				if ($grid[$i][$rowRoom['RoomID']]==0) {
					$cl='aqua';
					print "<td colspan='".$cols."' bgcolor='".$cl."'>&nbsp;</td>";
					#print "<td colspan='".$cols."' bgcolor='".$cl."'>".$i."<br>".$j."<br>".$cols."<br>".$d."</td>";
				} elseif ($grid[$i][$rowRoom['RoomID']]==-1) {
					$cl='red';
					$i1=$i - 1800;
					print "<form method='post'><input type='hidden' name='Action' value='Schedule Conflict Detail'><input type='hidden' name='Start' value='".$i1."'><input type='hidden' name='End' value='".$j."'><input type='hidden' name='RoomID' value='".$rowRoom['RoomID']."'><td colspan='".$cols."' bgcolor='".$cl."'><center><input type='submit' name='submit' value='Conflict details'></td></form>";
				} elseif ($grid[$i][$rowRoom['RoomID']]==-2) {
					$cl='#6666cc';
					$i1=$i - 1800;
					print "<td colspan='".$cols."' bgcolor='".$cl."'>&nbsp;</td>";
				} else {
					$queryPanel = "Select * from `CPDB_Panels` where `PanelID` = '".$grid[$i][$rowRoom['RoomID']]."'";
					Display_Query($queryPanel,"Panels");
					$sqlPanel=mysql_query($queryPanel) or die('Query failed: ' . mysql_error());
					$rowPanel = mysql_fetch_assoc($sqlPanel);
					if ($rowPanel['PanelHidePublic']==1) $cl='#ff00ff';
					print "<form method='post'><input type='hidden' name='Action' value='Panel Description'><input type='hidden' name='PanelID' value='".$grid[$i][$rowRoom['RoomID']]."'><td colspan='".$cols."' bgcolor='".$cl."'>";
					print "<input type='submit' name='submit' value='detail'>".$rowPanel['PanelTitle']."</td></form>";
				}
				$i = $j;
				if (date("G:i",$i)=='23:30') print "<td>".$rowRoom['RoomName']."</td>";

			}
		print "<td>".$rowRoom['RoomName']."</td></tr>";
		}
		#####
		# Print hours at the bottom of each day`s grid
		#####
		print "<tr><td></td>";
		for ($i = $StartDate+($d*60*60*24); $i < $StartDate+($d*60*60*24); $i=$i + 1800){
			print "<td colspan=1>".date($CFG['TimeFormat'],$i)."</td>";
			if (date("G:i",$i)=='23:30') print "<td></td>";
		}
		print "</tr>";
	print "</table>";
	print "<br  clear=all style='page-break-before:always'>";
	}
}

function Orig_Display_Grid()
{
	global $CFG;
	print "<font size=5>Paneling Grid</font><br>";
	print "<br><table border=1><tr><td><b>Key</b></td></tr>";
	print "<tr><td bgcolor='red'>Conflict</td></tr>";
	print "<tr><td bgcolor='lime'>Publicly displayable panel</td></tr>";
	print "<tr><td bgcolor='#ff00ff'>Panel/Event hidden from Public viewa</td></tr>";
	print "<tr><td bgcolor='#6666cc'>Space is Blocked by Parent/Child room Events</td></tr>";
	print "<tr><td bgcolor='aqua'>Open Time Slot, No pnale Scheduled</td></tr></table><br>";

	## CFG vars to remember
	## constartdate
	## conrundays
	## ConStartHour
	## ConEndHour
	## FirstDailyHour
	## LastDailyHour

	$grid=array();
	# pad grid with 0 (zero)
	$StartTime = strtotime($CFG['constartdate']);
	$StartDate = $StartTime;
	$EndTime = $StartTime + (60*60*24*$CFG['conrundays']);
	$query="select * from `CPDB_Room` where `ConID` = '".$CFG['ConID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		for ($i = $StartTime;$i < $EndTime; $i = $i + 1800){
			$grid[$i][$row['RoomID']]=0;
		}
	}
	$query = "select * from `CPDB_PTR` where `ConID` = '".$CFG['ConID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$StartDTS=strtotime($row['Start']);
		$EndDTS=strtotime($row['End']);

		$query1="select * from `CPDB_PTR` as S inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID` where `PanelID` = '".$row['PanelID']."'";
		Display_Query($query1);
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$row1 = mysql_fetch_assoc($sql1);
		$curPanel = $row['PanelID'];
		$roomList = "'".$row1['RoomID']."','".$row1['RoomChild1ID']."','".$row1['RoomChild2ID']."','".$row1['RoomChild3ID']."','".$row1['RoomChild4ID']."','".$row1['RoomChild5ID']."','".$row1['RoomChild6ID']."','".$row1['RoomChild7ID']."','".$row1['RoomChild8ID']."','".$row1['RoomChild9ID']."','".$row1['RoomChild10ID']."'";
		$curStart = $row['Start'];
		$curEnd = $row['End'];

		$queryCol = "select * from `CPDB_PTR` as S ";
		$queryCol .= "inner join `CPDB_Panels` as P on P.`PanelID` = S.`PanelID` ";
		$queryCol .= "inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID`  ";
		$queryCol .= "where (S.`RoomID` in (".$roomList.") or (`RoomChild1ID` = '".$row['RoomID']."')or (`RoomChild2ID` = '".$row['RoomID']."')or (`RoomChild3ID` = '".$row['RoomID']."')or (`RoomChild4ID` = '".$row['RoomID']."')or (`RoomChild5ID` = '".$row['RoomID']."')or (`RoomChild6ID` = '".$row['RoomID']."')or (`RoomChild7ID` = '".$row['RoomID']."') or (`RoomChild8ID` = '".$row['RoomID']."') or (`RoomChild9ID` = '".$row['RoomID']."') or (`RoomChild10ID` = '".$row['RoomID']."'))";
		$queryCol .= "and ((`Start` between '".$curStart."' and '".$curEnd."') or (`End` between '".$curStart."' and '".$curEnd."') or('".$curStart."' between `Start` and `End`) or ('".$curEnd."' between `Start` and `End`)) and `Start` <> '".$curEnd."' and `End` <> '".$curStart."'";
		$sqlCol=mysql_query($queryCol) or die('Query failed: ' . mysql_error());
		$ttlrow = mysql_num_rows($sqlCol);



		for ($i = $StartDTS; $i < $EndDTS; $i=$i + 1800) {
			if (!($ttlrow =='1')){
				$grid[$i][$row['RoomID']] = -1 ;
			} else {
				$grid[$i][$row['RoomID']] = $row['PanelID'] ;
			}
		}
	}

	#####
	# Figgure out Parent Child Relationship
	#####
		$PC = array();
		$CP = array();
		$queryChild  = "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild1ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild2ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild3ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild4ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild5ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild6ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild7ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild8ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild9ID` = B.`RoomID`) union ";
		$queryChild .= "(SELECT B.`RoomID`, A.`RoomID` as ParentRoom FROM `CPDB_Room` AS A INNER JOIN `CPDB_Room` AS B ON A.`RoomChild10ID` = B.`RoomID`)";
		$sqlChild=mysql_query($queryChild) or die('Query failed: ' . mysql_error());
		$child='false';
		while ($rowChild = mysql_fetch_assoc($sqlChild)) {
			$child='true';
			$temp = $rowChild['ParentRoom'].":".$rowChild['RoomID'];
			$tempP = "0:".$rowChild['ParentRoom'];
			$tempC = $rowChild['RoomID'].":0";
			$PC[$rowChild['ParentRoom']][$temp]=$rowChild['RoomID'];
			$PC['0'][$tempP]=$rowChild['ParentRoom'];
			$CP[$rowChild['RoomID']][$temp]= $rowChild['ParentRoom'];
			$CP['0'][$tempC]=$rowChild['RoomID'];
		}
#	foreach ($PC[0] as $key => $value){
#		#<
#		# $ value is all parents
#	}
#	print "<BR>=====<br>";
#	foreach ($CP[0] as $key => $value){
#		#<
#		# $value is all children
#	}
	#####
	# Parents should mask Children when Scheduled
	#####
	$StartTime = strtotime($CFG['constartdate']);
	$EndTime = $StartTime + (60*60*24*$CFG['conrundays']);
	if ($child =='true'){
		foreach ($PC[0] as $key => $value){
			#<
			for ($i = $StartTime;$i<$EndTime;$i =$i + 1800){
				if ($grid[$i][$value] >0) {
					#<
					foreach ($PC[$value] as $value1){
						$grid[$i][$value1]= -2;
					}
				}
			}
		}


		#####
		# Children should mask Parent when scheduled
		#####
		foreach ($CP[0] as $key => $value){
			for ($i = $StartTime;$i<$EndTime;$i = $i + 1800){
				if (!($grid[$i][$value] <1)) {
					foreach ($CP[$value] as $value1){
						$grid[$i][$value1]= -2;
					}
				}
			}
		}
	}
	$queryRoom = "select * from `CPDB_Room` where `ConID` = '".$CFG['ConID']."' order by `RoomOrder`, `RoomName`";
	Display_Query($queryRoom);
	$sqlRoom=mysql_query($queryRoom) or die('Query failed: ' . mysql_error());
	print "<tr><th></th>";

print "<br  clear=all style='page-break-before:always'>";

	#######
	# Finaly it`s time to display the grid
	#######
	for ($d = 0;$d<$CFG['conrundays'];$d=$d+1){
		if ($d==0) {
			$FirstHour = $CFG['ConStartHour'];
		} else {
			$FirstHour = $CFG['FirstDailyHour'];
		}
			if ($d==($CFG['conrundays']-1)) {
			$LastHour = $CFG['ConEndHour'];
		} else {
			$LastHour = $CFG['LastDailyHour'];
		}

		######
		# Start the Daily Loop
		######
		$dailyhours = ($LastHour - $FirstHour)*2;
		print "<table border=1><tr><td colspan=50><center>";
		print date("l F jS",$StartDate+($d*60*60*24));
		print "</td></tr><tr><td width='40'></td>";
		for ($i = $StartDate+($d*60*60*24); $i < $StartDate+(($d+1)*60*60*24); $i=$i + 1800){
			print "<td colspan=1 width=40>".date($CFG['TimeFormat'],$i)."</td>";
			if (date("G:i",$i)=='23:30') print "<td width='40'></td>";
		}
		print "</tr>";
		#####
		# Go to the first room, and loop through rooms
		#####
		mysql_data_seek($sqlRoom,0);
		while ($rowRoom = mysql_fetch_assoc($sqlRoom)) {
			print "<tr><td>".$rowRoom['RoomName']."</td>";
			$j=$StartTime+($d*60*60*24); # J is now the beginning of the current day
			for ($i = $StartDate+($d*60*60*24); $i < $StartDate+(($d+1)*60*60*24); $i=$i + 1800){
				#####
				# Start Stepping through the day in 30 min increments
				#####
				#if ($j<$i){
					$i=$j;
				#}
				while (($grid[$i][$rowRoom['RoomID']] == $grid[$j][$rowRoom['RoomID']])and ($j<$StartDate+(($d+1)*60*60*24))){
					$j=$j+1800;
					#####
					# Add 30 minutes to J until panel ends or day ends
					#####
				}
				#if (!(date("D",$i)) == (date("D",$j -1800))){
				#	$j= strtotime(date("Y-m-d 23:30",$i));
					$cols = ($j - $i)/1800 ;
				#} else {
				#	$cols = ($j - $i)/1800 ;
				#}
				if ($i > $StartDate+($d*60*60*24)) {
					#<
					#$cols=$cols+1;
				}

				$cl = 'lime';
				if ($grid[$i][$rowRoom['RoomID']]==0) {
					$cl='aqua';
					print "<td colspan='".$cols."' bgcolor='".$cl."'>&nbsp;</td>";
					#print "<td colspan='".$cols."' bgcolor='".$cl."'>".$i."<br>".$j."<br>".$cols."<br>".$d."</td>";
				} elseif ($grid[$i][$rowRoom['RoomID']]==-1) {
					$cl='red';
					$i1=$i - 1800;
					print "<form method='post'><input type='hidden' name='Action' value='Schedule Conflict Detail'><input type='hidden' name='Start' value='".$i1."'><input type='hidden' name='End' value='".$j."'><input type='hidden' name='RoomID' value='".$rowRoom['RoomID']."'><td colspan='".$cols."' bgcolor='".$cl."'><center><input type='submit' name='submit' value='Conflict details'></td></form>";
				} elseif ($grid[$i][$rowRoom['RoomID']]==-2) {
					$cl='#6666cc';
					$i1=$i - 1800;
					print "<td colspan='".$cols."' bgcolor='".$cl."'>&nbsp;</td>";
				} else {
					$queryPanel = "Select * from `CPDB_Panels` where `PanelID` = '".$grid[$i][$rowRoom['RoomID']]."'";
					$sqlPanel=mysql_query($queryPanel) or die('Query failed: ' . mysql_error());
					$rowPanel = mysql_fetch_assoc($sqlPanel);
					if ($rowPanel['PanelHidePublic']==1) $cl='#ff00ff';
					print "<form method='post'><input type='hidden' name='Action' value='Panel Description'><input type='hidden' name='PanelID' value='".$grid[$i][$rowRoom['RoomID']]."'><td colspan='".$cols."' bgcolor='".$cl."'>";
					print "<input type='submit' name='submit' value='detail'>".$rowPanel['PanelTitle']."</td></form>";
				}
				$i = $j;
				if (date("G:i",$i)=='23:30') print "<td>".$rowRoom['RoomName']."</td>";

			}
		print "<td>".$rowRoom['RoomName']."</td></tr>";
		}
		#####
		# Print hours at the bottom of each day`s grid
		#####
		print "<tr><td></td>";
		for ($i = $StartDate+($d*60*60*24); $i < $StartDate+($d*60*60*24); $i=$i + 1800){
			print "<td colspan=1>".date($CFG['TimeFormat'],$i)."</td>";
			if (date("G:i",$i)=='23:30') print "<td></td>";
		}
		print "</tr>";
	print "</table>";
	print "<br  clear=all style='page-break-before:always'>";
	}
}

function 	Display_Schedule_Conflict_Detail()
{
	global $CFG;
	$curStart = date("Y-m-d G:i:s",$_POST['Start']);
	$curEnd = date("Y-m-d G:i:s",$_POST['End']);
	#$query1="select * from `".$CFG['dbPrefix']."PTR` as S inner join `".$CFG['dbPrefix']."Room` as R on S.`RoomID` = R.`RoomID` where `PanelID` = '".$row['PanelID']."'";
	$query1="select * from `CPDB_Room` where `RoomID` = '".$_POST['RoomID']."'";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	$row1 = mysql_fetch_assoc($sql1);
	$curPanel = $row['PanelID'];
	$roomList = "'".$row1['RoomID']."','".$row1['RoomChild1ID']."','".$row1['RoomChild2ID']."','".$row1['RoomChild3ID']."','".$row1['RoomChild4ID']."','".$row1['RoomChild5ID']."','".$row1['RoomChild6ID']."','".$row1['RoomChild7ID']."','".$row1['RoomChild8ID']."','".$row1['RoomChild9ID']."','".$row1['RoomChild10ID']."'";

	$queryCol = "select * from `CPDB_PTR` as S ";
	$queryCol .= "inner join `CPDB_Panels` as P on P.`PanelID` = S.`PanelID` ";
	$queryCol .= "inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID`  ";
	$queryCol .= "where (S.`RoomID` in (".$roomList.") or (`RoomChild1ID` = '".$_POST['RoomID']."')or (`RoomChild2ID` = '".$_POST['RoomID']."')or (`RoomChild3ID` = '".$_POST['RoomID']."')or (`RoomChild4ID` = '".$_POST['RoomID']."')or (`RoomChild5ID` = '".$_POST['RoomID']."')or (`RoomChild6ID` = '".$_POST['RoomID']."')or (`RoomChild7ID` = '".$_POST['RoomID']."') or (`RoomChild8ID` = '".$_POST['RoomID']."') or (`RoomChild9ID` = '".$_POST['RoomID']."') or (`RoomChild10ID` = '".$_POST['RoomID']."'))";
	$queryCol .= "and ((`Start` between '".$curStart."' and '".$curEnd."') or (`End` between '".$curStart."' and '".$curEnd."') or('".$curStart."' between `Start` and `End`) or ('".$curEnd."' between `Start` and `End`)) and `Start` <> '".$curEnd."' and `End` <> '".$curStart."'";
	$sqlCol=mysql_query($queryCol) or die('Query failed: ' . mysql_error());
	#print "<B><i>".$queryCol."</i></b><br>";
	print "<table border=1>";
	while ($rowCol = mysql_fetch_assoc($sqlCol)) {
		print "<tr><td>".$rowCol['PanelTitle']."</td><td>".$rowCol['RoomName']."</td><td>".$rowCol['Start']."</td><td>".$rowCol['End']."</td></tr>";
	}
	print "</table>";
}

function Display_Reports_Options()
{
	global $CFG;
	print "<table width='100%'><tr>";
	print "<td><center><form method='post'><input type='submit' name='submit' value='Panelist Itineraries'></td><input type='hidden' name='Action' value='Clear Report'><input type='hidden' name='PanelistID' value='0'><input type='hidden' name='DisplayContact' value=0></form>";
	print "<td><center><form method='post'><input type='submit' name='submit' value='Panelist Itineraries\r\nWith Contact Info'></td><input type='hidden' name='Action' value='Clear Report'><input type='hidden' name='PanelistID' value='0'><input type='hidden' name='DisplayContact' value=1><input type='hidden' name='SubAction' value='Print Panelist Schedule'></form>";
	print "<td><center><form method='post'><input type='submit' name='submit' value='Room Itineraries'></td><input type='hidden' name='Action' value='Clear Report'><input type='hidden' name='RoomID' value='0'></form>";
	print "</tr><tr><td><center><form method='post'><input type='submit' name='submit' value='Email\r\nPanelist Itineraries'></td><input type='hidden' name='Action' value='Clear Report'><Input type='hidden' name='SubAction' value='Email Panelist Schedule'><input type='hidden' name='PanelistID' value='0'><input type='hidden' name='DisplayContact' value=0></form>";
	print "<td><center><form method='post'><input type='submit' name='submit' value='Email\r\nPanelist Itineraries\r\nWith Contact Info'></td><input type='hidden' name='Action' value='Clear Report'><input type='hidden' name='PanelistID' value='0'><input type='hidden' name='DisplayContact' value=1><input type='hidden' name='SubAction' value='Email Panelist Schedule'></form>";
	print "<td></td>";

}

function AvailabilityMatrix(){
	global $CFG;
	$StartTime = strtotime($CFG['constartdate']." 00:00:00");
	$StartHour = $StartTime + (60*60*($CFG['ConStartHour']-1));
	$EndHour = $StartTime + (($CFG['conrundays']-1)*24*60*60) + (60*60*$CFG['ConEndHour']);
	if ($CFG['LastDailyHour']==0) {
		$myLastHour = 24;
	} else {
		$myLastHour = $CFG['LastDailyHour'];
	}
	$myFirstHour = $CFG['FirstDailyHour'];
	$lstPnlst = "";

	#######
	# -1 							black
	# 0 default value				grey
	# 1 unavailable					aqua
	# 2 Available					blue
	# 3 unavailable and Booked		red
	# 4 Available and booked		coral
	#######
	print "<table border=1><tr>
			<td></td><th>On Panel</th><th>Not on Panel</th></tr>
			<th>Available</th><td bgcolor='coral'> </td><td bgcolor='aqua'></td></tr>
			<th>Not Available</th><td bgcolor='Red'> </td><td bgcolor='blue'></td></tr></table>";

	# Select all valid panelists (and equipment) #
	$query = "Select PanelistPubName from `CPDB_Panelist` as P
				inner join `CPDB_PanelistCon` as C
				on P.PanelistId = C.PanelistID
				inner join `CPDB_Invite` as I
				on I.PanelistId = P.PanelistID
				where C.ConID = '".$CFG['ConID']."'
				and I.InviteState <> 'Unavailable' ";
	if (array_key_exists('PanelistList',$_POST)){
		$query .= " and P.PanelistID in (".$_POST['PanelistList'].") ";
	}
	$query .= "Order by P.IsEquip, P.PanelistLastName, P.PanelistFirstName";
	Display_Query($query,'Availability Matrix');
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		for($i=$StartHour;$i <=$EndHour;$i+=60*30){
			$MATRIX[$row['PanelistPubName']][$i]=0;
		}
	$ColList[]=$row['PanelistPubName'];
	}
	$FinColList=array_unique($ColList);

	# Select all Availability records for valid panelists #
	$query = "Select PanelistPubName, AvailHour from `CPDB_Panelist` as P
				inner join `CPDB_PanelistCon` as C
				on P.PanelistId = C.PanelistID
				inner join `CPDB_Invite` as I
				on I.PanelistId = P.PanelistID
				inner join `CPDB_Availability` as A
				on A.PanelistId = P.PanelistId
				where C.ConID = '".$CFG['ConID']."'
				and A.ConID = '".$CFG['ConID']."'
				and I.InviteState <> 'Unavailable' ";
	if (array_key_exists('PanelistList',$_POST)){
		$query .= " and P.PanelistID in (".$_POST['PanelistList'].") ";
	}
	Display_Query($query,'Availability Matrix');
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$myhr=strtotime($row['AvailHour']);
		$myhlf = $myhr + (60*30);
		$MATRIX[$row['PanelistPubName']][$myhr]=1;
		$MATRIX[$row['PanelistPubName']][$myhlf]=1;
		$AvailList[]=$row['PanelistPubName'];
	}

	# Fill all instances where no avail records exist for a panelist #
	$OutList = array_diff($ColList, $AvailList);
	foreach ($OutList as $val) {
		for($i=$StartHour;$i <=$EndHour;$i+=60*30){
			$MATRIX[$val][$i]=1;
		}
	}


	# select all Scheduled events for all Valid Panelists #
	$query="Select PanelistPubName, Start, End from `CPDB_PTR` as R
			inner join `CPDB_P2P` as L on R.PanelId = L.PanelID
			inner join `CPDB_Panelist` as P on P.PanelistID = L.PanelistID
			where R.ConID = '".$CFG['ConID']."' ";
	if (array_key_exists('PanelistList',$_POST)){
		$query .= " and P.PanelistID in (".$_POST['PanelistList'].") ";
	}
	Display_Query($query,'Availability Matrix');
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$st=strtotime($row['Start']);
		$en=strtotime($row['End']);
		for ($i=$st;$i<$en;$i+=60*30){
			if ($MATRIX[$row['PanelistPubName']][$i]==0){
				$MATRIX[$row['PanelistPubName']][$i]=3;
			} else {
				$MATRIX[$row['PanelistPubName']][$i]=2;
			}
		}
	}

	print "<table border=1><tr><td></td>";
	for($i=$StartHour;$i <=$EndHour;$i+=60*30){
		$hour= date('G',$i);
		if (($hour>=$myFirstHour) && ($hour <= $myLastHour)){
			$txt= date('D g i A',$i);
			print "<td>".$txt."</td>";
			$daybreak=0;
		} else {
			if ($daybreak==0){
				$daybreak=1;
				print "<td>&nbsp;</td>";
			}
		}
	}
	print "</tr>";
	foreach ($FinColList as $panelist) {
		print "<tr><td>".$panelist."</td>";
		for($i=$StartHour;$i <=$EndHour;$i+=60*30){
			$hour= date('G',$i);
			if (($hour>=$myFirstHour) && ($hour <= $myLastHour)){
				$daybreak=0;
				switch($MATRIX[$panelist][$i]){
					case 0:
					print "<td bgcolor='blue'>&nbsp;</td>";
					break;
					case 1:
					print "<td bgcolor='aqua'>&nbsp;</td>";
					break;
					case 2:
					print "<td bgcolor='coral'>&nbsp;</td>";
					break;
					case 3:
					print "<td bgcolor='red'>&nbsp;</td>";
					break;
				}
			} else {
				if ($daybreak==0){
					$daybreak=1;
					print "<td>".$panelist."</td>";
				}
			}
		}
		print "</tr>";
	}
	#array_table($MATRIX);
}


function Data_Generate_Availability(){
	global $CFG;
		if (array_key_exists('Availability',$_POST)){
			#print "!!!!!!yes!!!!!<br>";
			$query = "delete from `CPDB_Availability` where `PanelistID` = '".$_POST['PanelistID']."' and `ConID` = '".$CFG['ConID']."'";
			Display_Query($query);
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			foreach ($_POST['Availability'] as $value) {
				$query="Insert into `CPDB_Availability` (
						`PanelistID`,
						`ConID`,
						`AvailHour`
						) values (
						'".$_POST['PanelistID']."',
						'".$CFG['ConID']."',
						'".$value."'
						)";
				Display_Query($query);
				$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			}
		} else {
			# no Availability records to change, do nothing
		}
}


function Data_Update_Availability()
{
	global $CFG;
	$query="Update `CPDB_Availability`
			set `PanelistID` = 	'".$_POST['PanelistID']."',
			`ConID` = 			'".$CFG['ConID']."',
			`AvailHour` = 		'".$_POST['AvailHour']."'
			where `AvailID` = '".$_POST['AvailID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

}

function Data_Insert_Availability()
{
	global $CFG;
	$query="insert into `CPDB_Availability`
		(`PanelistID`,
		`ConID`,
		`AvailHour`)
		values
		('".$_POST["PanelistID"]."',
		'".$CFG["ConID"]."',
		'".$_POST["AvailHour"]."')";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['AvailID'] = mysql_insert_id();
}

function Data_Delete_Availability(){
	$query = "delete from `CPDB_Availability` where `AvailID` = '".$_POST['AvailID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Update_Guest()
{
	global $CFG;
	$query="update CPDB_Guest
			set `PanelistID` = 			'".$_POST['PanelistID']."',
			`ConID` = 					'".$CFG['ConID']."',
			`GOPanelistName` = 			'".$_POST['GOPanelistName']."',
			`GOPanelistBadgeName` = 	'".$_POST['GOPanelistBadgeName']."',
			`GOPanelistAddress` = 		'".$_POST['GOPanelistAddress']."',
			`GOPanelistCity` = 			'".$_POST['GOPanelistCity']."',
			`GOPanelistState` = 		'".$_POST['GOPanelistState']."',
			`GOPanelistZip` = 			'".$_POST['GOPanelistZip']."',
			`GOPanelistPhone` = 		'".$_POST['GOPanelistPhone']."',
			`GOPanelistEmail` = 		'".$_POST['GOPanelistEmail']."',
			`GOPanelistGuest` = 		'".$_POST['GOPanelistGuest']."'
			Where `GuestID` = '".$_POST['GuestID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Insert_Guest()
{
	global $CFG;
	$query="insert into `CPDB_Guest`(
		`PanelistID`,
		`ConID`,
		`GOPanelistName`,
		`GOPanelistBadgeName`,
		`GOPanelistAddress`,
		`GOPanelistCity`,
		`GOPanelistState`,
		`GOPanelistZip`,
		`GOPanelistPhone`,
		`GOPanelistEmail`,
		`GOPanelistGuest`
		) values (
		'".$_POST["PanelistID"]."',
		'".$_POST["PanelistID"]."',
		'".$CFG["ConID"]."',
		'".$_POST["GOPanelistName"]."',
		'".$_POST["GOPanelistBadgeName"]."',
		'".$_POST["GOPanelistAddress"]."',
		'".$_POST["GOPanelistCity"]."',
		'".$_POST["GOPanelistState"]."',
		'".$_POST["GOPanelistZip"]."',
		'".$_POST["GOPanelistPhone"]."',
		'".$_POST["GOPanelistEmail"]."',
		'".$_POST["GOPanelistGuest"]."'
		)";

	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['GuestID'] = mysql_insert_id();
}

function Data_Delete_Guest(){
	$query = "delete from `CPDB_Guest` where `GuestID` = '".$_POST['GuestID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Generate_Maxpanels(){
	global $CFG;
	#Go through $_POST looking for Max-Date
	for ($i = 0; $i <= ($CFG['conrundays'] - 1); $i++) {
		$x = strtotime($CFG['constartdate']) ;
	    $workingdate = date("Y-m-d",$x + (24*60*60*$i));
	    if (array_key_exists("Max-".$workingdate,$_POST)) {
	    	# determine if this is an Insert or Update
	    	$query1 = "select * from `CPDB_MaxPanels` where `PanelistID` = '".$_POST['PanelistID']."' and `ConID` = '".$CFG['ConID']."' and `Date` = '".$workingdate."'";
	    	Display_Query($query1);
			$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
			$row = mysql_fetch_assoc($sql1);
			if (mysql_num_rows($sql1) == 0) {
				#Insert
				Data_Insert_Maxpanels($workingdate, $_POST['Max-'.$workingdate]);
			} else {
				#Update
				Data_Update_Maxpanels($row['MPID'],$_POST['Max-'.$workingdate],$workingdate);
			}
	    }
	    #print "Max-".$workingdate." ".$workingdate." ".$_POST['Max-'.$workingdate]."<br>";
	}


}

function Data_Update_Maxpanels($MpID, $Panels, $Date)
{
	global $CFG;
		$query="Update `CPDB_MaxPanels`
				set `PanelistID` = 	'".$_POST['PanelistID']."',
				`ConID` = 			'".$CFG['ConID']."',
				`Date` = 			'".$Date."',
				`MaxPanels` = 		'".$Panels."'
				where `MPID` = 		'".$MpID."'";
		Display_Query($query);
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Insert_Maxpanels($Date, $Panels)
{
	global $CFG;
	$query="insert into `CPDB_MaxPanels`
		(`PanelistID`,
		`ConID`,
		`Date`,
		`MaxPanels`
		) values (
		'".$_POST["PanelistID"]."',
		'".$CFG["ConID"]."',
		'".$Date."',
		'".$Panels."'
		)";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['MPID'] = mysql_insert_id();
}

function Data_Delete_Maxpanels(){
	$query = "delete from `CPDB_MaxPanels` where `MPID` = '".$_POST['MPID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Update_P2P()
{
	global $CFG;
	$query="Update `CPDB_P2P`
			set `PanelistID` = 	'".$_POST['PanelistID']."',
			`PanelID` = 		'".$_POST['PanelID']."',
			`Moderator` = 		'".$_POST['Moderator']."',
			`ConID` = 			'".$CFG['ConID']."'
			where `P2PID` = '".$_POST['P2PID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Insert_P2P()
{
	global $CFG;
	$query="insert into `CPDB_P2P`
		(`PanelistID`,
		`ConID`,
		`PanelID`,
		`Moderator`
		) values (
		'".$_POST["PanelistID"]."',
		'".$CFG["ConID"]."',
		'".$_POST["PanelID"]."',
		'".$_POST["Moderator"]."'
		)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['P2PID'] = mysql_insert_id();
}

function Data_Delete_P2P(){
	$query = "delete from `CPDB_P2P` where `P2PID` = '".$_POST['P2PID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Toggle_Moderator() {
	global $CFG;
	$query = "Update `CPDB_P2P` set `Moderator` = 0 where `PanelID` = '".$_POST['PanelID']."' and `ConID` = '".$CFG['ConID']."'";
	$query1 = "Update `CPDB_P2P` set `Moderator` = 1 where `P2PID` = '".$_POST['P2PID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	Display_Query($query1);
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
}

function Data_Update_Panelist()
{
	global $CFG;
	$query="update CPDB_Panelist
			set `PanelistName` = 	'".magic_escape($_POST['PanelistName'])."',
			`PanelistLastName` = 	'".magic_escape($_POST['PanelistLastName'])."',
			`PanelistFirstName` = 	'".magic_escape($_POST['PanelistFirstName'])."',
			`PanelistPubName` = 	'".magic_escape($_POST['PanelistPubName'])."',
			`PanelistBadgeName` = 	'".magic_escape($_POST['PanelistBadgeName'])."',
			`PanelistAddress` = 	'".magic_escape($_POST['PanelistAddress'])."',
			`PanelistCity` = 		'".magic_escape($_POST['PanelistCity'])."',
			`PanelistState` = 		'".magic_escape($_POST['PanelistState'])."',
			`PanelistZip` = 		'".magic_escape($_POST['PanelistZip'])."',
			`PanelistEmail` = 		'".magic_escape($_POST['PanelistEmail'])."',
			`PanelistPhoneDay` = 	'".magic_escape($_POST['PanelistPhoneDay'])."',
			`PanelistPhoneEve` = 	'".magic_escape($_POST['PanelistPhoneEve'])."',
			`PanelistPhoneCell` = 	'".magic_escape($_POST['PanelistPhoneCell'])."',
			`Biography` = '".magic_escape($_POST['Biography'])."'
			Where `PanelistID` = '".$_POST['PanelistID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#	$MAX['garbage'] = '';
#	$query3="select * from CPDB_MaxPanels where `ConID` = '".$CFG['ConID']."' and `PanelistID` = '".$_POST['PanelistID']."'";
#	$sql3=mysql_query($query3) or die('Query failed: ' . mysql_error());
#	while ($row3 = mysql_fetch_assoc($sql3)) {
#		$MAX[$row3['Date']] = $row3['MaxPanels'];
#	}
#	unset($MAX['garbage']);
#	array_table($MAX);
#	foreach($_POST as $key=> $value) {
#		#<
#		$x=preg_match('/^Max-(\\d\\d\\d\\d-\\d\\d-\\d\\d)/',$key,$matches);
#		if ($x==1) {
#			$MAX[$matches[1]]=$value;
#		}
#	}
#	array_table($MAX);
}

function Data_Insert_Panelist_Short(){
	global $CFG;
	$query="insert into `CPDB_Panelist` (
		`PanelistName`,
		`PanelistLastName`,
		`PanelistFirstName`,
		`PanelistPubName`,
		`PanelistEmail`
		) values (
		'".$_POST["PanelistLastName"].", ".$_POST["PanelistFirstName"]."',
		'".$_POST["PanelistLastName"]."',
		'".$_POST["PanelistFirstName"]."',
		'".$_POST["PanelistPubName"]."',
		'".$_POST["PanelistEmail"]."'
		)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['PanelistID'] = mysql_insert_id();
	$query="insert into `CPDB_PanelistCon` (
		`PanelistID`,
		`ConID`
	) values (
		'".$_POST['PanelistID']."',
		'".$CFG['ConID']."'
	)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="INSERT INTO `CPDB_Invite` (
		`InviteGUID` ,
		`InviteState` ,
		`InviteDate` ,
		`PanelistID` ,
		`ConID`
		) VALUES (
		UUID(),
		'BackEndForce',
		NOW(),
		'".$_POST['PanelistID']."',
		'".$CFG['ConID']."'
		)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

}
function Data_Insert_Panelist()
{
	global $CFG;
	$query="insert into `CPDB_Panelist` (
		`PanelistName`,
		`PanelistLastName`,
		`PanelistFirstName`,
		`PanelistPubName`,
		`PanelistBadgeName`,
		`PanelistAddress`,
		`PanelistCity`,
		`PanelistState`,
		`PanelistZip`,
		`PanelistPhoneDay`,
		`PanelistPhoneEve`,
		`PanelistPhoneCell`,
		`PanelistEmail`,
		`GroupName`,
		`GroupEvent`,
		`lsitme`,
		`sharephone`,
		`shareemail`,
		`sharemail`,
		`Biography`,
		`SchedeReqs`,
		`PhysReqs`,
		`IsEquip`,
		`SubmittedDTS`,
		`DNI`
		) values (
		'".$_POST["PanelistName"]."',
		'".$_POST["PanelistLastName"]."',
		'".$_POST["PanelistFirstName"]."',
		'".$_POST["PanelistPubName"]."',
		'".$_POST["PanelistBadgeName"]."',
		'".$_POST["PanelistAddress"]."',
		'".$_POST["PanelistCity"]."',
		'".$_POST["PanelistState"]."',
		'".$_POST["PanelistZip"]."',
		'".$_POST["PanelistPhoneDay"]."',
		'".$_POST["PanelistPhoneEve"]."',
		'".$_POST["PanelistPhoneCell"]."',
		'".$_POST["PanelistEmail"]."',
		'".$_POST["GroupName"]."',
		'".$_POST["GroupEvent"]."',
		'".$_POST["lsitme"]."',
		'".$_POST["sharephone"]."',
		'".$_POST["shareemail"]."',
		'".$_POST["sharemail"]."',
		'".$_POST["Biography"]."',
		'".$_POST["SchedeReqs"]."',
		'".$_POST["PhysReqs"]."',
		'".$_POST["IsEquip"]."',
		'".$_POST["SubmittedDTS"]."',
		'".$_POST["DNI"]."'
		)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['PanelistID'] = mysql_insert_id();
}

function Data_Delete_Panelist(){
	$query = "delete from `CPDB_Panelist` where `PanelistID` = '".$_POST['PanelistID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Update_Panelistcon()
{
	global $CFG;
	$query1="select `PcID` from `CPDB_PanelistCon` where `PanelistID` = '".$_POST['PanelistID']."' and `ConID` = '".$CFG['ConID']."'";
	Display_Query($query1);
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	if (mysql_num_rows($sql1) == 0) {
		Data_Insert_Panelistcon();
	} else {
		$row = mysql_fetch_assoc($sql1);
		$query="Update CPDB_PanelistCon
				set
				`PanelistID` = 		'".$_POST['PanelistID']."',
				`ConID` = 			'".$CFG['ConID']."',
				`SchedReqs` = 		'".$_POST['SchedReqs']."',
				`PhysReqs` = 		'".$_POST['PhysReqs']."',
				`listme` = 			'".$_POST['listme']."',
				`sharephone` = 		'".$_POST['sharephone']."',
				`shareemail` = 		'".$_POST['shareemail']."',
				`sharemail` = 		'".$_POST['sharemail']."',
				`comped` = 			'".$_POST['comped']."'
				where `PcID` = '".$row['PcID']."'";
		Display_Query($query);
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	}
}

function Data_Insert_Panelistcon()
{
	global $CFG;
	$query="insert into `CPDB_PanelistCon`
		(`PanelistID`,
		`ConID`,
		`SchedReqs`,
		`PhysReqs`,
		`listme`,
		`sharephone`,
		`shareemail`,
		`sharemail`,
		`comped`
		) values (
		'".$_POST["PanelistID"]."',
		'".$CFG["ConID"]."',
		'".$_POST["SchedReqs"]."',
		'".$_POST["PhysReqs"]."',
		'".$_POST["listme"]."',
		'".$_POST["sharephone"]."',
		'".$_POST["shareemail"]."',
		'".$_POST["sharemail"]."',
		'".$_POST["comped"]."'
		)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['PcID'] = mysql_insert_id();
}

function Data_Delete_Panelistcon(){
	$query = "delete from `CPDB_PanelistCon` where `PcID` = '".$_POST['PcID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Update_Panelranking()
{
	global $CFG;
	$query="Update `CPDB_PanelRanking`
			set `PanelistID` = 	'".$_POST['PanelistID']."',
			`PanelID` = 		'".$_POST['PanelID']."',
			`Rank` = 			'".$_POST['Rank']."',
			`Moderate` = 		'".$_POST['Moderate']."',
			`ConID` = 			'".$CFG['ConID']."'
			where `PanelRankID` = '".$_POST['PanelRankID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Insert_Panelranking()
{
	global $CFG;
	if (!(array_key_exists('Moderate',$_POST))){
		$_POST['Moderate']=0;
	}
	if (!(array_key_exists('Rank',$_POST))){
		$_POST['Rank']=6;
	}
	$query="insert into `CPDB_PanelRanking`
		(`PanelistID`,
		`ConID`,
		`PanelID`,
		`Moderate`,
		`Rank`
		) values (
		'".$_POST["PanelistID"]."',
		'".$CFG["ConID"]."',
		'".$_POST["PanelID"]."',
		'".$_POST["Moderate"]."',
		'".$_POST["Rank"]."'
		)";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['PanelRankID'] = mysql_insert_id();
}

function Data_Delete_Panelranking(){
	$query = "delete from `CPDB_PanelRanking` where `PanelRankID` = '".$_POST['PanelRankID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Update_Panels()
{
	global $CFG;
	$query="select * from `CPDB_Panels` where `PanelID` = '".$_POST['PanelID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	if ($row['PanelLocked'] == 0 ) {
		$query="Update `CPDB_Panels`
				set `ConID` = 			'".$CFG['ConID']."',
				`CatID` = 				'".$_POST['CatID']."',
				`PanelTitle` = 			'".magic_escape($_POST['PanelTitle'])."',
				`PanelDescription` = 	'".magic_escape($_POST['PanelDescription'])."',
				`PanelNotes` = 			'".magic_escape($_POST['PanelNotes'])."',
				`PanelHidePublic` = 	'".$_POST['PanelHidePublic']."',
				`PanelHideSurvey` = 	'".$_POST['PanelHideSurvey']."',
				`PanelSolo` = 			'".$_POST['PanelSolo']."',
				`PanelApproved` = 		'".$_POST['PanelApproved']."',
				`PanelTech` = 			'".$_POST['PanelTech']."',
				`PanelSuggestBy` = 		'".$_POST['PanelSuggestBy']."',
				`PanelHighlited` = 		'".$_POST['PanelHighlited']."'
				where `PanelID` = '".$_POST['PanelID']."'";
				Display_Query($query);
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		if ($row['PanelTitle']==$_POST['PanelTitle'] && $row['PanelDescription']==$_POST['PanelTDescription'] && $row['PanelNotes']==$_POST['Panelnotes'] && $row['CatID']==$_POST['CatID']) {
			# no change on Edit tracked Fields, do nothing
		} else {
			Insert_Panel_Edits();
		}
	} else {
		print "<font color='red'><B>can not update this panel, it is locked</b></font>";
	}
}

function Data_Insert_Panels()
{
	global $CFG;
	$query="insert into `CPDB_Panels`
		(`ConID`,
		`CatID`,
		`PanelTitle`,
		`PanelDescription`,
		`PanelNotes`,
		`PanelHidePublic`,
		`PanelHideSurvey`,
		`PanelLocked`,
		`PanelSolo`,
		`PanelApproved`,
		`PanelTech`,
		`PanelSuggestBy`,
		`PanelCreated`
		) values (
		'".$CFG["ConID"]."',
		'".$_POST["CatID"]."',
		'".magic_escape($_POST["PanelTitle"])."',
		'".magic_escape($_POST["PanelDescription"])."',
		'".magic_escape($_POST["PanelNotes"])."',
		'".$_POST["PanelHidePublic"]."',
		'".$_POST["PanelHideSurvey"]."',
		'0',
		'".$_POST["PanelSolo"]."',
		'".$_POST["PanelApproved"]."',
		'".$_POST["PanelTech"]."',
		'".$_POST["PanelSuggestBy"]."',
		now()
		)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['PanelID'] = mysql_insert_id();
	Insert_Panel_Edits();

}

function Insert_Panel_Edits(){
	global $CFG;
	$query="Insert into `CPDB_PanelEdits`
			(`EditTime`,
			`EditBy`,
			`PanelID`,
			`PanelTitle`,
			`PanelDescription`,
			`PanelNotes`,
			`CatID`
			) values (
			now(),
			'".$CFG['USERNAME']."',
			'".$_POST['PanelID']."',
			'".magic_escape($_POST['PanelTitle'])."',
			'".magic_escape($_POST['PanelDescription'])."',
			'".magic_escape($_POST['PanelNotes'])."',
			'".$_POST['CatID']."'
			)";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

}

function Data_Delete_Panels(){
	global $CFG;
	$query = "delete from `CPDB_Panels` where `PanelID` = '".$_POST['PanelID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Togle_Lock_Panel(){
	global $CFG;
	$query="Select * from `CPDB_Panels` where `PanelID` = '".$_POST['PanelID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	if ($row['PanelLocked']==0) {
		$_POST['PanelLocked'] = 1;
	} else {
		$_POST['PanelLocked'] = 0;
	}
	$query="Update `CPDB_Panels`
			set `PanelLocked` = '".$_POST['PanelLocked']."'
			where `PanelID` = '".$_POST['PanelID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Clone_Panel() {
	$query="Select * from `CPDB_Panels` where `PanelID` = '".$_POST['PanelID']."'";
	$_POST['OldPanelID']=$_POST['PanelID'];
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
			$_POST["PanelTitle"]		= $row["PanelTitle"];
			$_POST["PanelDescription"]	= $row["PanelDescription"];
			$_POST["PanelNotes"]		= $row["PanelNotes"];
			$_POST["PanelHidePublic"]	= $row["PanelHidePublic"];
			$_POST["PanelHideSurvey"]	= $row["PanelHideSurvey"];
			$_POST["PanelSolo"]			= $row["PanelSolo"];
			$_POST["PanelApproved"]		= $row["PanelApproved"];
			$_POST["PanelTech"]			= $row["PanelTech"];
			$_POST["PanelSuggestBy"]	= $row["PanelSuggestBy"];
			$_POST['CatID'] 			= $row['CatID'];
	Data_Insert_Panels();
	$query=" Insert into `CPDB_PanelRanking` (`PanelistID`,`PanelID`,`Rank`,`moderate`,`ConID`)
			select `PanelistID`, ".$_POST['PanelID']." as PanelID, `Rank`, `Moderate`, `ConID` from `CPDB_PanelRanking` where `PanelID` = '".$_POST['OldPanelID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Generate_Ptr(){
	global $CFG;
	if ($_POST['StartHour'] == 0) $_POST['StartHour'] = 12;
	$x = strtotime($_POST['StartDow'].' '.$_POST['StartHour'].':'.$_POST['StartMinute'].':00 '.$_POST['StartHalf']) ;
	$_POST['Start'] = date("Y-m-d H:i:s",$x);
	$y=$x+(60*60*$_POST['DurationHours'])+($_POST['DurationMinutes']*60);
	$_POST['End'] = date("Y-m-d H:i:s",$y);
	$query="Select * from `CPDB_PTR` where `ConID` = '".$CFG['ConID']."' and `PanelID` = '".$_POST['PanelID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	if (mysql_num_rows($sql) == 0) {
		Data_Insert_Ptr();
	} else {
		$row = mysql_fetch_assoc($sql);
		$_POST['PTRID'] = $row['PTRID'];
		Data_Update_Ptr();
	}
}

function Data_Update_Ptr()
{
	global $CFG;
	$query="Update `CPDB_PTR`
			set `PanelID` = '".$_POST['PanelID']."',
			`RoomID` = 		'".$_POST['RoomID']."',
			`SetID`	=		'".$_POST['SetID']."',
			`Start` = 		'".$_POST['Start']."',
			`End` = 		'".$_POST['End']."',
			`SchedNotes` =	'".$_POST['Schednotes']."',
			`ConID` = 		'".$CFG['ConID']."'
			where `PTRID` = '".$_POST['PTRID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Insert_Ptr()
{
	global $CFG;
	$query="insert into `CPDB_PTR`
		(`PanelID`,
		`RoomID`,
		`SetID`,
		`Start`,
		`End`,
		`SchedNotes`,
		`ConID`
		) values (
		'".$_POST["PanelID"]."',
		'".$_POST["RoomID"]."',
		'".$_POST["SetID"]."',
		'".$_POST["Start"]."',
		'".$_POST["End"]."',
		'".$_POST["SchedNotes"]."',
		'".$CFG["ConID"]."'
		)";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$_POST['PTRID'] = mysql_insert_id();
}

function Data_Delete_Ptr(){
	$query = "delete from `CPDB_PTR` where `PtrID` = '".$_POST['PtrID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Delete_Ptr_by_PanelID(){
	$query = "delete from `CPDB_PTR` where `PanelID` = '".$_POST['PanelID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

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

function Data_Update_Equipment(){
	global $CFG;
	$query="Update `CPDB_Panelist`
			set `PanelistName` = 		'".$_POST['PanelistName']."',
				`PanelistBadgeName` =	'".$_POST['PanelistBadgeName']."'
			where `PanelistID` = '".$_POST['PanelistID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Disable_Equipment(){
	global $CFG;
	$query="Delete from `CPDB_PanelistCon` where `PanelistID` = '".$_POST['PanelistID']."' and `ConID` = '".$CFG['ConID']."'";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Add_Equipment(){
	$query="Insert into `CPDB_Panelist` (
			`PanelistName`,
			`PanelistBadgeName`,
			`IsEquip`
			) values (
			'".$_POST['PanelistName']."',
			'".$_POST['PanelistBadgeName']."',
			'1'
			)";
	Display_Query($query);
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
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

//	The server currently has magic_quotes_gpc set which adds slashes to all post data
//	This removes those slashes (if any)
function magic_escape ($str)
{
	if  (get_magic_quotes_gpc ())	//	Depreciated - will you please change php.ini to remove this!
		$str = stripslashes ($str);
	return mysql_real_escape_string ($str);
}

?>
