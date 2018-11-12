<?php
require_once("config.php");
require_once("page_match.php");
#$CFG['debug']=1;
$qrydbg=1;
$invitemode = 0;

# ?InviteGUID=XXXXXXXXXXXXXXXXXXXXXX&InviteID=##&maint=xyzzy
if (array_key_exists('InviteGUID',$_GET)) $_POST['InviteGUID'] = $_GET['InviteGUID'];
if (array_key_exists('InviteID',$_GET)) $_POST['InviteID'] = $_GET['InviteID'];
if (array_key_exists('maint',$_GET)) $_POST['maint'] = $_GET['maint'];
if (!array_key_exists('maint',$_POST)) $_POST['maint']='no';
if (($CFG['maintenance'] == 1) && (!($_POST['maint']==$CFG['MaintenanceCode']))) $_POST['page'] = 'maintenance';
if (!array_key_exists('page',$_POST)) $_POST['page'] = 'intro';

$_POST['garbage']='garbage';

page_header();
if (($CFG['maintenance'] == 1)&& ($_POST['maint']==$CFG['MaintenanceCode']))  {
	$CFG['debug']=$CFG['maintdebug'];
	debug();
#	debug_CFG();
}
debug();
page_top();
	print "<tr><td colspan=3>";
		print "<center><font size=5 color='black'>";
		print $CFG['ConName'];
		print " Panelist Survey ";
		if ($CFG['maintenance'] ==1) print "<font color='red'> Maintenance Mode Enabled </font>";
	print "</td></tr>";
page_left();
validate_panelist();
load_con_times();

$errors = do_actions();
page_center($errors);
page_right();
page_footer();

function load_con_times()
{
	global $CFG;
	$query="select * from CPDB_Convention
			where `ConID` = '".$_POST['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$CFG['ConDate']= $row['ConDate'];						#The First day of the convention
		$CFG['ConDays']= $row['ConDays'];						#The number of days the Convention runs
		$CFG['ConStartHour']= $row['ConStartHour'];				#The Hour the convention Starts on the First Day (military time)
		$CFG['ConEndHour']= $row['ConEndHour'];					#The Hour the convention Ends on the Last Day (military time)
		$CFG['FirstDailyHour']= $row['FirstDailyHour'];			#The First Hour of Daily Programming
		$CFG['LastDailyHour']= $row['LastDailyHour'];			#The Last Hour of Daily Programming
	}
}
function do_actions()
{
	global $CFG;
	global $PAGE;
	global $_POST;
	$errors = '';
	switch($_POST['page'])
	{
			case "intro":
				$pageid=1;
				break;
			case "contact":
				$errors = save_contact();
				$pageid=2;
				break;
			case "image":
				$errors = save_image();
				$pageid=3;
				break;
			case "avail":
				$errors = save_avail();
				$pageid=4;
				break;
			case "guest":
				$errors = save_guest();
				$pageid=5;
				break;
			case "guest_reg":
				$pageid=6;
				break;
			case "suggest":
				$errors = save_suggest();
				$pageid=7;
				break;
			case "panel_notes":
				$pageid=8;
				break;
			case "select":
				$errors = save_select();
				$pageid=9;
				break;
			case "close":
				$pageid=10;
				break;
			case "maintenance":
				$pageid=99;
				break;
	}
	update_invite_state($pageid);
	if ($errors == '') {
		if ($_POST['Action']=='Next')
		{
			for($i=$pageid + 1; $i <=10; $i++) {
				if ($PAGE[$i] == 1 ) {
					$pageid=$i;
					break;
				}
			}
		}
		if ($_POST['Action']=='Prev')
		{
			for($i=$pageid - 1; $i >0; $i--) {
				#<<
				if ($PAGE[$i] == 1 ) {
					$pageid=$i;
					break;
				}
			}
		}
		if ($_POST['Action']=='Opt Out')
		{
			$pageid=0;
			$_POST['page']="optout";
			panelist_optout();
		}
		if ($_POST['Action']=='Unavailable')
		{
			$pageid=0;
			$_POST['page']="unavailable";
			panelist_unavailable();
		}


		switch ($pageid)
		{
			case 0:
				break;
			case 1:
				$_POST['page']="intro";
				break;
			case 2:
				$_POST['page']="contact";
				break;
			case 3:
				$_POST['page']="image";
				break;
			case 4:
				$_POST['page']="avail";
				break;
			case 5:
				$_POST['page']="guest";
				break;
			case 6:
				$_POST['page']="guest_reg";
				break;
			case 7:
				$_POST['page']="suggest";
				break;
			case 8:
				$_POST['page']="panel_notes";
				break;
			case 9:
				$_POST['page']="select";
				break;
			case 10:
				$_POST['page']="close";
				break;
			case 99:
				$_POST['page'] = "maintenance";
				break;
			default:
				$_POST['page']="Denied";
				break;
		}
	}


	return $errors;
}
function validate_panelist()
{
	global $CFG;
	if (!array_key_exists('InviteGUID',$_POST)) {
		$_POST['page']='Denied';
		return;
	}
	$query = "select * from CPDB_Invite
				where InviteID ='".$_POST['InviteID']."'
				and InviteGUID = '".$_POST['InviteGUID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	if (mysql_num_rows($sql) == 1 ) {
		$row = mysql_fetch_assoc($sql);
		$_POST['ConID'] = $row['ConID'];
		$_POST['PanelistID']=$row['PanelistID'];
		if (!array_key_exists('PanelistPubName',$_POST)){
			$query="Select PanelistPubName from CPDB_Panelist where PanelistID = '".$row['PanelistID']."'";
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			$_POST['PanelistPubName']=$row['PanelistPubName'];
		}
	} else {
		$_POST['page'] = 'Denied';
	}


}


function page_center($errors)
{
	print "<td width=75%>";
	print $errors;
	switch ($_POST['page'])
	{
		case "intro":
			page_intro();
			break;
		case "contact":
			page_contact();
			break;
		case "image":
			page_image();
			break;
		case "avail":
			page_avail();
			break;
		case "guest":
			page_guest();
			break;
		case "guest_reg":
			page_guest_reg();
			break;
		case "suggest":
			page_suggest();
			break;
		case "panel_notes":
			page_panel_notes();
			break;
		case "select":
			page_select();
			break;
		case "close":
			page_close();
			break;
		case "Denied":
			page_access_denied();
			break;
		case "optout":
			page_optout();
			break;
		case "unavailable":
			page_unavailable();
			break;
		case "maintenance":
			page_maintenance();
			break;
		default:
			page_default();
	}
	print "</td>";
}

function page_intro()
{
	global $CFG;
	#print "INTRO";
	if (array_key_exists ('Unavailable',$_POST)){
		#do nothing
	} else {
		$_POST['Unavailable']=0;
	}
	print "<form Action='survey.php' method='post'>
			<input type='hidden' name='page' value='intro'>
			<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
			<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
			<input type='hidden' name='maint' value='".$_POST['maint']."'>
			<input type='hidden' name='Unavailable' value='".$_POST['Unavailable']."'>

			";
	print "<table width=600 align='center'><tr><td>";
	print file_get_contents('text/InvitePage.txt');
	print "</td></tr></table>";
	print "<br><center>
			<input type='submit' name='Action' value='Next'>
			<br>&nbsp;
			<table border=1 width=100%><tr><td><center><font color='black'>
			I am unable to attend ".$CFG['Con']." this year, please keep me in your records, for future years</font>
			<br><input type='submit' name='Action' value='Unavailable'>
			</td></tr><tr><td><center><font color='black'>
			I am not interested in attending any ".$CFG['Con'].". Please do not invite me again.</font>
			<br><input type='submit' name='Action' value='Opt Out'>
			</td></form></tr></table>

			";

}

function page_contact()
{
	global $CFG;
	$query = "select * from CPDB_Panelist where PanelistID = '".$_POST['PanelistID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);


	print "<center>Please Verify and update your contact information";
	print "<form Action='survey.php' method='post' name='survey'>
			<input type='hidden' name='page' value='".$_POST['page']."'>
			<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
			<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
			<input type='hidden' name='maint' value='".$_POST['maint']."'>
			<input type='hidden' name='Unavailable' value='".$_POST['Unavailable']."'>
			";

			print "<table border=1>
					<tr><td>First Name</td><td><input type='text' name='PanelistFirstName' value='".$row['PanelistFirstName']."'></td></tr>
					<tr><td>Last Name</td><td><input type='text' name='PanelistLastName' value='".$row['PanelistLastName']."'></td></tr>
					<tr><td>By Line<br><div class='commenttext'>This is how your name will appear in our publications</div></td><td><input type='text' name='PanelistPubName' value='".$row['PanelistPubName']."'></td></tr>
					<tr><td>Badge Name</td><td><input type='text' name='PanelistBadgeName' value='".$row['PanelistBadgeName']."'></td></tr>
					<tr><td>Address</td><td><input type='text' name='PanelistAddress' value='".$row['PanelistAddress']."'></td></tr>
					<tr><td>City</td><td><input type='text' name='PanelistCity' value='".$row['PanelistCity']."'></td></tr>
					<tr><td>State</td><td><input type='text' name='PanelistState' value='".$row['PanelistState']."'></td></tr>
					<tr><td>Postal/Zip Code</td><td><input type='text' name='PanelistZip' value='".$row['PanelistZip']."'></td></tr>
					<tr><td>Day Phone</td><td><input type='text' name='PanelistPhoneDay' value='".$row['PanelistPhoneDay']."'></td></tr>
					<tr><td>Evening Phone</td><td><input type='text' name='PanelistPhoneEve' value='".$row['PanelistPhoneEve']."'></td></tr>
					<tr><td>Cell Phone</td><td><input type='text' name='PanelistPhoneCell' value='".$row['PanelistPhoneCell']."'></td></tr>
					<tr><td>Email Address</td><td><input type='text' name='PanelistEmail' value='".$row['PanelistEmail']."'></td></tr>
					<tr><td>Biography<br><div class='commenttext'>Your Bio can have no more than ".$CFG['BiographySize']." characters<br>you have
					<script type='text/javascript'>document.write('<input type=text name=limit size=1 readonly value='+count+' style=color:aqua;background-color:black;border-color:black;border-width:0;font-size:x-small >');</script>
					characters left.</div></td><td><textarea name='Biography' cols=30 rows=5 onkeyup=limiter()>".$row['Biography']."</textarea></td></tr>";

	print "<tr><td colspan=2><center><input type='submit' name='Action' value='Prev'>
		<input type='submit' name='Action' value='Next'></td></tr>
			</form></table>
			";
}

function page_image()
{
	global $CFG;
	$query="Select * from CPDB_Image where PanelistID = '".$_POST['PanelistID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	#print "Image";
	print "<form Action='survey.php' method='post' enctype='multipart/form-data'>
			<input type='hidden' name='page' value='".$_POST['page']."'>
			<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
			<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
			<input type='hidden' name='maint' value='".$_POST['maint']."'>
			<input type='hidden' name='Unavailable' value='".$_POST['Unavailable']."'>
			";

	if (mysql_num_rows($sql)>0) {
		#<
		print "<center>We currently have the following image file for you<br>
				<img src='img.php?pid=".$_POST['PanelistID']."'><br>
				If you need to update this image, please submit the new image here<br>";
	} else {
		print "<center>We currently do not have an image file available for you<br>
				Please submit an image file<br>";

	}
	print "<br>We will have problems processing any images larger than 50 KB.<br>If you image is larger than 50KB, please resize it before uploading it here, <a href='http://www.picresize.com/'>www.picresize.com</a> is one of the many 0nline tools that can do this for you<br><br>";
	print "<input type='File' name='userfile' size=40>";
	print "<br>
			<input type='submit' name='Action' value='Prev'>
			<input type='submit' name='Action' value='Next'>
			</form>
			";
}

function page_avail()
{
	global $CFG;

	$AVAIL['garbage']='';
	$query="select * from CPDB_Availability where `PanelistID` = '".$_POST['PanelistID']."' and `ConID` = '".$_POST['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$AVAIL[$row['AvailHour']] = 1;
	}
	$query="select * from CPDB_PanelistCon where `ConID` = '".$_POST['ConID']."' and `PanelistID` = '".$_POST['PanelistID']."'";
	$sql1=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row1 = mysql_fetch_assoc($sql1);
		$listmeY = $listmeN = '';
		if ($row1['listme']==1) {
			$listmeY = 'Checked';
		} else {
			$listmeN = 'Checked';
		}
		$sharephoneY=$sharephoneN='';
		if ($row1['sharephone']==1) {
			$sharephoneY = 'Checked';
		} else {
			$sharephoneN = 'Checked';
		}
		$shareemailY= $shareemailN ='';
		if ($row1['shareemail']==1) {
			$shareemailY = 'Checked';
		} else {
			$shareemailN = 'Checked';
		}
		$sharemailY= $sharemailN ='';
		if ($row1['sharemail']==1) {
			$sharemailY = 'Checked';
		} else {
			$sharemailN = 'Checked';
		}


	$MAX['garbage'] = '';
	$query="select * from CPDB_MaxPanels where `ConID` = '".$_POST['ConID']."' and `PanelistID` = '".$_POST['PanelistID']."'";
	$sql2=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row2 = mysql_fetch_assoc($sql2)) {
		$MAX[$row2['Date']] = $row2['MaxPanels'];
	}
	#print "Availability";
	print "<form Action='survey.php' method='post'>
			<input type='hidden' name='page' value='".$_POST['page']."'>
			<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
			<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
			<input type='hidden' name='maint' value='".$_POST['maint']."'>
			<input type='hidden' name='Unavailable' value='".$_POST['Unavailable']."'>
			";

	$StartTime = strtotime($CFG['constartdate']." 00:00:00");
	$StartHour = $StartTime + (60*60*($CFG['ConStartHour']-1));
	$EndHour = $StartTime + (($CFG['conrundays']-1)*24*60*60) + (60*60*$CFG['ConEndHour']);
	if ($CFG['LastDailyHour']==0) $CFG['LastDailyHour']=24;
	print "<table border=1><tr><td colspan=25><center></td>";

	print "<tr><td></td>";

	for($i=$CFG['FirstDailyHour'];$i<=$CFG['LastDailyHour'];$i++){
		$txt = date('g A',(60*60*($i+8)));
		#$txt .= " ".$i;
		print "<td>".$txt."</td>";
	}
	print "</tr>";
	for ($i=0;$i<$CFG['conrundays'];$i++){
		#Days
		print "<tr><td>".date('M j Y',$StartTime+($i*24*60*60))."</td>";
		for ($j=$CFG['FirstDailyHour'];$j<=$CFG['LastDailyHour'];$j++) {
		# hours
			$now = $StartTime+($i*24*60*60)+(($j+0)*60*60);
			$slot=date('Y-m-d H:i:s',$now);
			print "<td ";
			if (array_key_exists($slot, $AVAIL)) print " bgcolor='green' ";
			if (($now<=$StartHour) || ($now >=$EndHour)) {
				#<
				print " bgcolor='silver' ";
			} else {
				print "><input type='checkbox' name='Availability[]' value='".$slot."' ";
				if (array_key_exists($slot, $AVAIL)) print " checked ";
			}
			print "></td>\r\n";
		}
		print "</tr>";
	}
	print "</table>";


	print "<table border=1 width='600'><tr><td>";
	print "<table><tr><td colspan=2>Please enter the number of panels you are able to do each day</td></tr><tr><td>Date</td><td>Panels</td></tr>";
	for ($i=0;$i<$CFG['conrundays'];$i++){
		#Days
		$slot=date('Y-m-d',$StartTime+($i*24*60*60));
		print "<tr><td>".$slot."</td>";
		if (array_key_exists($slot,$MAX)) {
			$val=$MAX[$slot];
		} else {
			$val=3;
		}
		print "<td><input type='text' name='MaxPanels_".$slot."' value='".$val."' size=3></td></tr>";
	}
	print "</table>";
	print "<td>Special Scheduling Requests<br><textarea cols=20 rows=6 name='SchedReqs'>".$row1['SchedReqs']."</textarea><br>Please leave this field empty if you have no special Scheduling requests</td>";
	print "<td >Special Physical Requirements<br><textarea cols=20 rows=6 name='PhysReqs'>".$row1['PhysReqs']."</textarea><br>Please leave this field empty if you have no special Physical requirements</td>";
	print "</tr></table>";
	print "<table border=1><tr><td>You may list me in promotional material for the convention</td><td>Yes<input type='radio' name='listme' value=1 ".$listmeY." ><br>No<input type='radio' name='listme' value=0 ".$listmeN." ></td></tr>
			<tr><td>May we share the following with Co-Panelists to facilitate pre convention coordination</td><td>
			<table><tr><td>Phone</td><td>Yes<input type='Radio' name='sharephone' value=1 ".$sharephoneY." ><br>No<input type='radio' name='sharephone' value=0 ".$sharephoneN." ></td></tr>
			<tr><td>EMail</td><td>Yes<input type='Radio' name='shareemail' value=1 ".$shareemailY." ><br>No<input type='radio' name='shareemail' value=0 ".$shareemailN." ></td></tr>
			<tr><td>Mail</td><td>Yes<input type='Radio' name='sharemail' value=1 ".$sharemailY." ><br>No<input type='radio' name='sharemail' value=0 ".$sharemailN." ></td></tr>
			</table></tr></table>
			";

	print "<br><center>
			<input type='submit' name='Action' value='Prev'>
			<input type='submit' name='Action' value='Next'>
			</form>
			";
}

function page_guest()
{
	global $CFG;
	#print "Guest";
	$query = "Select * from CPDB_Guest
				where `PanelistID` = '".$_POST['PanelistID']."' and `ConID` = '".$_POST['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	print "<table width=600 align='center'><tr><td>";
	print file_get_contents('text/GuestOfPanelistPage.txt');
	print "</td></tr></table>";
	print "<form Action='survey.php' method='post'>
			<input type='hidden' name='page' value='".$_POST['page']."'>
			<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
			<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
			<input type='hidden' name='maint' value='".$_POST['maint']."'>
			<input type='hidden' name='Unavailable' value='".$_POST['Unavailable']."'>
			";

	print "<table width=600 border=1 align='center'><tr><td colspan=2>Please enter the information about your Guest below</td></tr>
			<tr><Td width='150'>Name</td><td><input type='text' name='GOPanelistName' value='".$row['GOPanelistName']."'></td></tr>
			<tr><Td>Badge Name</td><td><input type='text' name='GOPanelistBadgeName' value='".$row['GOPanelistBadgeName']."'></td></tr>
			<tr><Td>Address</td><td><input type='text' name='GOPanelistAddress' value='".$row['GOPanelistAddress']."'></td></tr>
			<tr><Td>City</td><td><input type='text' name='GOPanelistCity' value='".$row['GOPanelistCity']."'></td></tr>
			<tr><Td>State</td><td><input type='text' name='GOPanelistState' value='".$row['GOPanelistState']."'></td></tr>
			<tr><Td>Zip</td><td><input type='text' name='GOPanelistZip' value='".$row['GOPanelistZip']."'></td></tr>
			<tr><Td>Phone</td><td><input type='text' name='GOPanelistPhone' value='".$row['GOPanelistPhone']."'></td></tr>
			<tr><Td>Email</td><td><input type='text' name='GOPanelistEmail' value='".$row['GOPanelistEmail']."'></td></tr>
			<tr><td colspan=2>";

	print "<br></td></tr></table><br><center>
			<input type='submit' name='Action' value='Prev'>
			<input type='submit' name='Action' value='Next'>
			</form>
			";
}

function page_guest_reg(){
	global $CFG;
	#print "Close";
	print "<form Action='survey.php' method='post'>
			<input type='hidden' name='page' value='".$_POST['page']."'>
			<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
			<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
			<input type='hidden' name='maint' value='".$_POST['maint']."'>
			<input type='hidden' name='Unavailable' value='".$_POST['Unavailable']."'>
			";
	print "<table width=600 align='center'><tr><td>";
	print file_get_contents('text/GuestRegPage.txt');
#	$query="select concat('G',old_password(concat_ws(',',`GuestID`,`ConID`,`GOPanelistEmail`))) as Code from `CPDB_Guest` where PanelistID = '".$_POST['PanelistID']."'and `ConID` = '".$_POST['ConID']."'";
	$query = "select concat(_utf8 'G', old_password(concat_ws(_utf8 ',', `I`.`InviteID` , `I`.`ConID` , `I`.`InviteGUID`))) as Code
				from CPDB_Invite as I
				Where I.InviteGUID = '".$_POST['InviteGUID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);

		print "<br> = <font color='blue'>".$row['Code']."</font><br>";

	print "</td></tr></table>";
	print "<br><center>
			<input type='submit' name='Action' value='Prev'>
			<input type='submit' name='Action' value='Next'>
			</form>
			";
}

function page_suggest()
{
	global $CFG;
	#print "Suggestions";
	$CatOptions='';
	if (!array_key_exists('CatID',$_POST)) $_POST['CatID'] = 0;
	$query="Select * from CPDB_Category where Active=1 Order by Category";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$CatOptions.="<option value='".$row['CatID']."' " ;
		if ($row['CatID'] == $_POST['CatID']) $CatOptions.=" Selected ";
		$CatOptions.= " >".$row['Category']."</option>";
	}
	$query = "select * from CPDB_Panels as P
			inner join CPDB_Category as C
			on P.CatID = C.CatID
			where P.`ConID` = '".$_POST['ConID']."'
			and P.`PanelHideSurvey` = 0
			order by C.Category, P.PanelTitle";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	print "<table width=600 border=0><tr><td><center><font size='+1'>Please click on the Categories below to Expand/Colapse the list of Suggested panels in that category</font></td></tr>";
	$catgroup="";
	while ($row = mysql_fetch_assoc($sql)) {
		if ($catgroup <> $row['Category']){
			if($catgroup <> "") {
				#<
				print"</tbody></table>";
			}
			print "<table border=1 width=600><thead><tr><th colspan=2 onClick='showHide(this)' ><img src='p-m.JPG'>&nbsp;&nbsp;&nbsp;".$row['Category']."</td></tr></thead><tbody style='display:none';>";
			$catgroup = $row['Category'];
		}
		print "<tr ><td width=25%>".$row['PanelTitle']."</td><td width=75%>".$row['PanelDescription']."</td></tr>\r\n";



	}
	print "</tbody><table></table>";

	print "<form Action='survey.php' method='post'>
			<input type='hidden' name='page' value='".$_POST['page']."'>
			<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
			<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
			<input type='hidden' name='maint' value='".$_POST['maint']."'>
			<input type='hidden' name='Unavailable' value='".$_POST['Unavailable']."'>
			";

#	print "<table width=600 border=1><tr><td colspan=3><b><center>Suggested Panels</b></center></td></tr>
#			<tr><th width=15%>Category</th><th width=30%>Title</th><th width=55%>Description</th></tr>";
#	while ($row = mysql_fetch_assoc($sql)) {
#		print "<tr><td>".$row['Category']."</td><td>".$row['PanelTitle']."</td><td>".$row['PanelDescription']."</td></tr>";
#
#	}
#	print "</table>";

	print "<center><table><tr><td colspan=2>Please enter your panel suggestions below<br>
			Use the Submit button at the bottom of this form to add your suggestion<br>
			Fields with * are required</td></tr>
			<tr><td>* Category</td><td><select name='CatID'>".$CatOptions."</select></td></tr>
			<tr><td>* Title</td><td><input type='text' name='PanelTitle' value='".$_POST['PanelTitle']."'></td></tr>
			<tr><td>* Panel Description</td><td><textarea cols=40 rows=6 name='PanelDescription'>".$_POST['PanelDescription']."</textarea></td></tr>
			<tr><td>Panel Format</td><td><input type='radio' name='PanelType' value='Solo'>Solo<br>
											<input type='radio' name='PanelType' value='Small_Group'>Small Group Discussion<br>
											<input type='radio' name='PanelType' value='Large_Group'>Large Group Presentation</td></tr>

			<tr><td>Tech Requirements</td><td><textarea cols=40 rows=6 name='TechReqs'>".$_POST['TechReqs']."</textarea></td></tr>
			<tr><td>Panel Layout</td><td><input type='radio' name='PanelLayout' value='Theatre'>Theatre<br>
											<input type='radio' name='PanelLayout' value='Classroom'>Classroom<br>
											<input type='radio' name='PanelLayout' value='Round'>In the Round(open discussion)<br></td></tr>
			<tr><td>Suggested Panelists</td><td><textarea cols=40 rows=6 name='SuggPanelist'>".$_POST['SuggPanelist']."</textarea></td></tr>
			<tr><td></td><td><input type='checkbox' name='PlaceMe' value=1 CHECKED>I want to be on this panel</td></tr>
			<tr><td></td><td><input type='checkbox' name='ModerateMe' value=1>I want to Moderate this panel</td></tr>
			<tr><td colspan=2><center><input type='submit' name='Action' value='Submit'></td></tr>
			</table>";
	print "<br><center>
			<input type='submit' name='Action' value='Prev'>
			<input type='submit' name='Action' value='Next'>
			</form>
			";
}

function page_panel_notes(){
	global $CFG;
	#print "Close";
	print "<form Action='survey.php' method='post'>
			<input type='hidden' name='page' value='".$_POST['page']."'>
			<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
			<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
			<input type='hidden' name='maint' value='".$_POST['maint']."'>
			<input type='hidden' name='Unavailable' value='".$_POST['Unavailable']."'>
			";
	print "<table width=600 align='center'><tr><td>";
	print file_get_contents('text/PanelNotesPage.txt');
	print "</td></tr></table>";
	print "<br><center>
			<input type='submit' name='Action' value='Prev'>
			<input type='submit' name='Action' value='Next'>
			</form>
			";
}

function page_select()
{
	global $CFG;
	$query = "select P.`PanelID`, C.`Category`, P.`PanelTitle`, P.`PanelDescription`  from CPDB_Panels as P
				inner join CPDB_Category as C on P.CatID = C.CatID
				where P.`ConID` = '".$_POST['ConID']."'
				and P.`PanelHideSurvey` = 0
				order by C.Category,P.PanelTitle";


	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	print "<form Action='survey.php' method='post'>
			<input type='hidden' name='page' value='".$_POST['page']."'>
			<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
			<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
			<input type='hidden' name='maint' value='".$_POST['maint']."'>
			<input type='hidden' name='Unavailable' value='".$_POST['Unavailable']."'>
			";
	###############################
	# Form Body Goes Here
	###############################
	print "Please rate the Panels you would like to be on from
		<br><font color='Black'><ol>
	<li><font color='Blue'>You really want to be on this panel.</font></li>
	<li><font color='Blue'>Would like to do this panel.</font></li>
	<li><font color='Blue'>Something I am interested but don't have to be on.</font></li>
	<li><font color='Blue'>Not really interested but can talk about it.</font></li>
	<li><font color='Blue'>If you need me to be on this panel sure why not?</font></li></ol></font>

       <br>If you don't now want to be an the panel at all do not mark at all
       <br>
	       <br>If you have selected a panel that you do not want to be on,
	       <br>Select the Radio Button next to the X
	       <br> If you do not see the X, click Next, and then Prev<br>
	       <br>If you would like to Moderate a panel, click the Checkbox under Moderate<br>";


	print "<table width=600 border=1><tr><td colspan=5><b><center>Panel Selection</b></center></td></tr>";

	$ccatgroup="";

	while ($row = mysql_fetch_assoc($sql)) {
		if ($catgroup <> $row['Category']){
			if($catgroup <> "") {
				#<
				print"</tbody></table>";
			}
			print "<table border=1 width=600><thead><tr><th colspan=4 onClick='showHide(this)' ><img src='p-m.JPG'>&nbsp;&nbsp;&nbsp;".$row['Category']."</td></tr></thead><tbody style='display:none';>
			<tr><td width=10%>Ranking<br></td><td width=5%>Moderate</td><td width=25%>Title</td><td width=45%>Description</td></tr>";
			$catgroup = $row['Category'];
		}
		$query1="select * from CPDB_PanelRanking
					where `PanelID` = '".$row['PanelID']."'
					and `PanelistID`='".$_POST['PanelistID']."'";
		$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		$row1 = mysql_fetch_assoc($sql1);
		$moderate='';
		if ($row1['Moderate']==1) $moderate= ' checked ';
		$Rank['1']=$Rank['2']=$Rank['3']=$Rank['4']=$Rank['5']='';
		$Rank[$row1['Rank']]= " Checked ";
		$rankName = "Rank_".$row['PanelID'];
		$modName = "Mod_".$row['PanelID'];
		print "<tr><td><table><tr>
								<td><center>1</td>
								<td><center>2</td>
								<td><center>3</td>
								<td><center>4</td>
								<td><center>5</td>";
		if ($row1['Rank']>0){
		#<
			print "				<td><font color='red'><center>X</font></td>";
		}
		print "						</tr>
								<tr><td><input type='radio' name='".$rankName."' value=1".$Rank['1']."></td>
								<td><input type='radio' name='".$rankName."' value=2".$Rank['2']."></td>
								<td><input type='radio' name='".$rankName."' value=3".$Rank['3']."></td>
								<td><input type='radio' name='".$rankName."' value=4".$Rank['4']."></td>
								<td><input type='radio' name='".$rankName."' value=5".$Rank['5']."></td>";
		if ($row1['Rank']>0){
		#<
			print "				<td><input type='radio' name='".$rankName."' value=0></td>";
		}
		print "						</tr>
								</table></td>
					<td><input type='checkbox' name='".$modName."' value=1".$moderate."></td>
					<td>".$row['PanelTitle']."</td>
					<td>".$row['PanelDescription']."</td></tr>";

	}
	print "</table>";

	print "<br><center>
			<input type='submit' name='Action' value='Prev'>
			<input type='submit' name='Action' value='Next'>
			</form>
			";
}

function page_close()
{
	global $CFG;
	#print "Close";
	print "<form Action='survey.php' method='post'>
			<input type='hidden' name='page' value='".$_POST['page']."'>
			<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
			<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
			<input type='hidden' name='maint' value='".$_POST['maint']."'>
			<input type='hidden' name='Unavailable' value='".$_POST['Unavailable']."'>
			";
	print "<table width=600 align='center'><tr><td>";
	print file_get_contents('text/ClosePage.txt');
	print "</td></tr></table>";
	print "<br><center>
			<input type='submit' name='Action' value='Prev'>
			</form>
			";
}



function page_maintenance()
{
	global $CFG;
	#print "Close";
	print "<table width=600 align='center'><tr><td>";
	print file_get_contents('text/MaintenancePage.txt');
	print "</td></tr></table>";
	print "<br><center>
			";


}

function page_default()
{
	global $CFG;
	print "<B>We are sorry, but you should never see this page<br>
			Something obviously went wrong somewhere<br>
			Please try again<br>
			Thank you, The Programming Team</b>";
}
function page_access_denied()
{
	global $CFG;
	print "<center><font size=5 color='red'>";
	print "We are Sorry, but this does not appear to be a valid invitation.  Please contact the Programming department for help";
	print "</font><br>";
	print "<b>If you have any questions, please contact us at <a href='mailto:".$CFG['helpEmail']."'>Programming Survey Help</a>";
	print "</center>";

}
function page_optout()
{
	global $CFG;
	#print "OptOut";
	print file_get_contents('text/OptOutPage.txt');

}

function page_unavailable()
{
	global $CFG;
	#print "Unavailable";
	print file_get_contents('text/UnavailablePage.txt');
	print "<center><form Action='survey.php' method='post'>
				<input type='hidden' name='page' value='guest_reg'>
				<input type='hidden' name='InviteGUID' value='".$_POST['InviteGUID']."'>
				<input type='hidden' name='InviteID'value='".$_POST['InviteID']."'>
				<input type='hidden' name='maint' value='".$_POST['maint']."'>
				<input type='hidden' name='Unavailable' value='1'>
				<input type='submit' name='Action' value='Next'>
			</form></center><br>";
}

function update_invite_state($pageid)
{
	global $CFG;
	$query="select * from CPDB_Invite_Status
			where `InviteID` = '".$_POST['InviteID']."'
			and `PageID` = '".$pageid."'
			and `SurveyState` = '".$CFG['SurveyStage']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	if (mysql_num_rows($sql)==0){
		#insert, first time we have seen this combo
		$query = "Insert into CPDB_Invite_Status
					(`InviteID`,`PageID`,`SurveyState`,`TimeStamp`)
					values
					('".$_POST['InviteID']."','".$pageid."','".$CFG['SurveyStage']."',now())";
	} else {
		#update, this combo already exists, just change the timestamp
		$query="Update CPDB_Invite_Status
				Set `Timestamp` = now()
				where `InviteID` = '".$_POST['InviteID']."'
				and `PageID` = '".$pageid."'
				and `SurveyState` = '".$CFG['SurveyStage']."'";
	}
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function panelist_unavailable()
{
	global $CFG;
	$query="Update CPDB_Invite
			set `InviteState` = 'Unavailable'
			where `InviteID` = '".$_POST['InviteID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}
function panelist_optout()
{
	global $CFG;
	$query="Update CPDB_Panelist
			set `DNI` = 1
			where `PanelistID` = '".$_POST['PanelistID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$query="Update CPDB_Invite
			set `InviteState` = 'OptOut'
			where `InviteID` = '".$_POST['InviteID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}


function save_contact()
{
	global $CFG;
	$errors = "";
	if (strlen($_POST['PanelistLastName'])<2) {
		$errors .= "<font color='red'>Last Name Can not be Blank</font><br>";
	}
	if (strlen($_POST['PanelistAddress'])<2) {
		$errors .= "<font color='red'>Address Can not be Blank</font><br>";
	}
	if (strlen($_POST['PanelistCity'])<2) {
		$errors .= "<font color='red'>City Can not be Blank</font><br>";
	}
	if (strlen($_POST['PanelistState'])<2) {
		$errors .= "<font color='red'>State/Province Can not be Blank</font><br>";
	}
	if (strlen($_POST['PanelistZip'])<2) {
		$errors .= "<font color='red'>Zip/Postal code Can not be Blank</font><br>";
	}
	if (strlen($_POST['PanelistEmail'])<2) {
		$errors .= "<font color='red'>Email Address Can not be Blank</font><br>";
	}
	if ($errors == "") {
		$query="update CPDB_Panelist
				Set
					`PanelistName` = '".$_POST['PanelistLastName'].", ".$_POST['PanelistFirstName']."',
					`PanelistLastName` = '".$_POST['PanelistLastName']."',
					`PanelistFirstName` = '".$_POST['PanelistFirstName']."',
					`PanelistBadgeName` = '".$_POST['PanelistBadgeName']."',
					`PanelistPubName` = '".$_POST['PanelistPubName']."',
					`PanelistAddress` = '".$_POST['PanelistAddress']."',
					`PanelistCity` = '".$_POST['PanelistCity']."',
					`PanelistState` = '".$_POST['PanelistState']."',
					`PanelistZip` = '".$_POST['PanelistZip']."',
					`PanelistPhoneDay` = '".$_POST['PanelistPhoneDay']."',
					`PanelistPhoneEve` = '".$_POST['PanelistPhoneEve']."',
					`PanelistPhoneCell` = '".$_POST['PanelistPhoneCell']."',
					`PanelistEmail` = '".$_POST['PanelistEmail']."',
					`Biography` = '".$_POST['Biography']."'
					where `PanelistID` = '".$_POST['PanelistID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		if ($_POST['Unavailable']==1) {
			$query="Update CPDB_Invite
					Set `InviteState` = 'Responded-Unavailable'
					where `InviteID` = '".$_POST['InviteID']."'";
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		} else {
			$query="select * from CPDB_PanelistCon where `PanelistID` ='".$_POST['PanelistID']."' and `conID` = '".$_POST['ConID']."'";
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			if (mysql_num_rows($sql)==0) {
				$query="Insert into CPDB_PanelistCon
						(`PanelistID`,`ConID`)
						values
						('".$_POST['PanelistID']."','".$_POST['ConID']."')";
				$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			}
			$StartTime = strtotime($CFG['constartdate']." 00:00:00");
			for ($i=0;$i<$CFG['conrundays'];$i++){
				#Days
				$slot=date('Y-m-d',$StartTime+($i*24*60*60));
				$query="Select * from CPDB_MaxPanels where `PanelistID` ='".$_POST['PanelistID']."' and `conID` = '".$_POST['ConID']."' and `Date` = '".$slot."'";
				$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
				if (mysql_num_rows($sql)==0) {
					$query="insert into CPDB_MaxPanels
							(`PanelistID`, `ConID`, `Date`, `MaxPanels`)
							values
							('".$_POST['PanelistID']."','".$_POST['ConID']."','".$slot."','0')";
					$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
				}
			}
			$query = "Select * from CPDB_Guest
							where `PanelistID` = '".$_POST['PanelistID']."' and `ConID` = '".$_POST['ConID']."'";
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			if (mysql_num_rows($sql)==0) {
				$query = "Insert into CPDB_Guest
							(`PanelistID`,`ConID`)
							values
							('".$_POST['PanelistID']."','".$_POST['ConID']."')";
				$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			}
			$query="Update CPDB_Invite
					Set `InviteState` = 'Responded'
					where `InviteID` = '".$_POST['InviteID']."'";
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			return "";
		}
	} else {
		return $errors;
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


function save_avail()
{
	global $CFG;
#	debug_array($_POST['Availability']);
	#### Determine which Availability hours to Insert or Delete
	$query="Select * from CPDB_Availability where PanelistID = '".$_POST['PanelistID']."'and `ConID` = '".$_POST['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$AVAIL[$row['AvailHour']] = $row['AvailHour'];
	}
	$AVAIL['garbage']='';
	$AVAIL2=$_POST['Availability'];
	$AVAIL2['garbage']='';
	$AVAIL1=array_flip($AVAIL2);
	$DEL['Garbage']=0;
	$INS['Garbage']=0;
	foreach($AVAIL as $val) {
		if(!array_key_exists($val,$AVAIL1)) $DEL[] = $val;
	}
	foreach($AVAIL2 as $val) {
		if(!array_key_exists($val,$AVAIL)) $INS[] = $val;
	}
	foreach($DEL as $val) {
		if (!($val==0)) {
			$query="delete from CPDB_Availability
					where PanelistID = '".$_POST['PanelistID']."'and `ConID` = '".$_POST['ConID']."' and `AvailHour` = '".$val."'";
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		}
	}
	foreach($INS as $key => $val) {
		#<
		if (!($val==0)) {
			$query = "insert into CPDB_Availability
						(`PanelistID`,`ConID`,`AvailHour`)
						values
						('".$_POST['PanelistID']."','".$_POST['ConID']."','".$val."')";
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		}
	}

	###
	foreach($_POST as $key=> $value) {
		#<

		if (strpos($key,"MaxPanels_")==0) {
			$nVal = substr($key,10,10);
			$query="select * from CPDB_MaxPanels where PanelistID = '".$_POST['PanelistID']."'and `ConID` = '".$_POST['ConID']."' and `Date` = '".$nVal."'";
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			$row = mysql_fetch_assoc($sql);
			$query="Update CPDB_MaxPanels
					Set `MaxPanels` = '".$value."'
					where `MPID` = '".$row['MPID']."'";
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		}
	}
	$query = "Update CPDB_PanelistCon
				Set `SchedReqs` = '".$_POST['SchedReqs']."' ,
					`PhysReqs` = '".$_POST['PhysReqs']."',
					`listme` = '".$_POST['listme']."',
					`sharephone` = '".$_POST['sharephone']."',
					`sharemail` = '".$_POST['sharemail']."',
					`shareemail` = '".$_POST['shareemail']."'
				where PanelistID = '".$_POST['PanelistID']."'and `ConID` = '".$_POST['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}


function save_guest()
{
	global $CFG;
	$query="select * from CPDB_Guest
			where PanelistID = '".$_POST['PanelistID']."'and `ConID` = '".$_POST['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);

	$query = "Update CPDB_Guest
				Set
				`GOPanelistName` = '".$_POST['GOPanelistName']."',
				`GOPanelistBadgeName` = '".$_POST['GOPanelistBadgeName']."',
				`GOPanelistAddress` = '".$_POST['GOPanelistAddress']."',
				`GOPanelistCity` = '".$_POST['GOPanelistCity']."',
				`GOPanelistState` = '".$_POST['GOPanelistState']."',
				`GOPanelistZip` = '".$_POST['GOPanelistZip']."',
				`GOPanelistPhone` = '".$_POST['GOPanelistPhone']."',
				`GOPanelistEmail` = '".$_POST['GOPanelistEmail']."'
				where `GuestID` = '".$row['GuestID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}


function save_suggest()
{
	global $CFG;
	$errors='';
	$err = 0;
	if (!array_key_exists('CatID',$_POST)) {
		$errors .= "<font color='red'>A Category Must be Selected</font><br>";
		$err += 1;
	}
	if (strlen($_POST['PanelTitle'])<2) {
		$errors .= "<font color='red'>You must enter a Title</font><br>";
		$err +=1;
	}
	if (strlen($_POST['PanelDescription'])<2) {
		$errors .= "<font color='red'>You must enter a Description</font><br>";
		$err +=1;
	}
	if ((($_POST['Action']=='Prev') || ($_POST['Action']=='Next')) && ($err>1)) {
		#<
		return;
	}
	if ($err==0) {
		$PanelNotes = "PanelType = ".$_POST['PanelType']."\r\n";
		$PanelNotes.= "========================================\r\n";
		$PanelNotes = "PanelLayout = ".$_POST['PanelLayot']."\r\n";
		$PanelNotes.= "========================================\r\n";
		$PanelNotes.= "Technical Requirements = \r\n" . $_POST['TechReqs']."\r\n";
		$PanelNotes.= "========================================\r\n";
		$PanelNotes.= "Suggested Panelists = \r\n" . $_POST['SuggPanelist']."\r\n";
		$PanelNotes.= "========================================\r\n";
		$PanelNotes.= "I want on this panel (0=no, 1=yes) = \r\n" . $_POST['PlaceMe']."\r\n";
		$PanelNotes.= "I want to Moderate (0=no, 1=yes) = \r\n" . $_POST['ModerateMe']."\r\n";
		$PanelNotes.= "Suggested By = \r\n" . $_POST['PanelistPubName']." : ".$_POST['PanelistID']."\r\n";



		$query = "Insert into CPDB_Panels
					(`ConID`, `CatID`, `PanelTitle`, `PanelDescription`, `PanelNotes`, `PanelApproved`, `PanelSuggestBy`)
					values
					('".$_POST['ConID']."',
					'".$_POST['CatID']."',
					'".mysql_escape_string($_POST['PanelTitle'])."',
					'".mysql_escape_string($_POST['PanelDescription'])."',
					'".mysql_escape_string($PanelNotes)."',
					'0',
					'".$_POST['PanelistID']."')";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		# now clear the values so we do not populate the next form
			unset($_POST['CatID']);
			unset($_POST['PanelTitle']);
			unset($_POST['PanelDescription']);
			unset($_POST['PanelType']);
			unset($_POST['TechReqs']);
			unset($_POST['SuggPanelist']);
			unset($_POST['PlaceMe']);
			unset($_POST['ModerateMe']);
		return ;
	} else {
		return $errors;
	}
}


function save_select()
{
	global $CFG;
	foreach($_POST as $key=> $value) {
		#<
		if (substr($key,0,5)=='Rank_') {
			$nVal = substr($key,5,strlen($key)-5);
			#print "RANK===".$nVal." = ".$value."<br>";
			$RANK[$nVal] = $value;
		}
		if (substr($key,0,4)=='Mod_') {
			$nVal = substr($key,4,strlen($key)-4);
			#print "MOD===".$nVal." = ".$value."<br>";
			$MODS[$nVal] = $value;
		}
	}
	$query="select * from CPDB_PanelRanking
			where `ConID` = '".$_POST['ConID']."'
			and `PanelistID` = '".$_POST['PanelistID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$REQS[$row['PanelID']] = -1;
	}
	$MODS['garbage']=$RANK['garbage']=$REQS['garbage']='';
	#$FINLST = array_merge_recursive($MODS,$RANK,$REQS);
	foreach($MODS as $key => $value){
		#<
		$FINLST[$key]=1;
	}
	foreach($RANK as $key => $value){
		#<
		$FINLST[$key]=1;
	}
	foreach($REQS as $key => $value){
		#<
		$FINLST[$key]=1;
	}
	unset($FINLST['garbage']);
	foreach($FINLST as $key => $value) {
		#<
		if ($key=='garbage') break;
		if (!array_key_exists($key,$MODS)) $MODS[$key]=0;
		if (!array_key_exists($key,$RANK)) $RANK[$key]=0;
		$query ="select * from CPDB_PanelRanking
				Where `PanelID`='".$key."'
					and `ConID` = '".$_POST['ConID']."'
					and `PanelistID` = '".$_POST['PanelistID']."'";
		#print_qry($query);
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

		if (mysql_num_rows($sql)==0) {
			#this is an Insert
			$query1="Insert into CPDB_PanelRanking
					(`PanelistID`,`PanelID`,`ConID`,`Rank`,`Moderate`)
					values
					('".$_POST['PanelistID']."',
					'".$key."',
					'".$_POST['ConID']."',
					'".$RANK[$key]."',
					'".$MODS[$key]."')";
		} else {
			if (($RANK[$key]==0) && ($MODS[$key]==0)) {
				# This is a Delete
				$query1= "Delete from CPDB_PanelRanking
							Where `PanelID` = '".$key."'
								and `ConID` = '".$_POST['ConID']."'
								and `PanelistID` = '".$_POST['PanelistID']."'";
			} else {
				#This then has to be a Update
				$query1 = "Update CPDB_PanelRanking
						Set `Rank` = '".$RANK[$key]."',
							`Moderate` = '".$MODS[$key]."'
						Where `PanelID` = '".$key."'
							and `ConID` = '".$_POST['ConID']."'
							and `PanelistID` = '".$_POST['PanelistID']."'";
			}
		}
		$QRYS[] = $query1;
	}
	$QRYS['garbage']='';
	foreach($QRYS as $query){
		if (!$query==''){
#			print_qry($query);
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		}
	}
}


function debug()
{
	global $CFG;
	if ($CFG['debug']==1){

	debug_array($_POST);
	debug_CFG();
	}
}

function print_qry($qry)
{
	global $qrydbg;
	if ($qrydbg==1) print "<font color='green'>".$qry."</font><br>";
}

function debug_CFG() {
	global $CFG;
	if ($CFG['debug']==1){
		print "<table border=1 bgcolor='#ff9999'><tr><th>KEY</th><th>Value</th></tr>";
		foreach($CFG as $key => $value ) {
			#<
			print "<tr><td>";
			print $key;
			print "</td><td>";
			print $value;
			print "</td></tr>";
		}
		print "</table></font>";
	}
}

function debug_array($myarray)

{
	print "<table border=1 bgcolor='#ff9999'><tr><th>KEY</th><th>Value</th></tr>";
	foreach($myarray as $key => $value ) {
		#<
		print "<tr><td>";
		print $key;
		print "</td><td>";
		print $value;
		print "</td></tr>";
	}
	print "</table></font>";



}
?>