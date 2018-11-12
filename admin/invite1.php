<?php


require_once("../config.php"); # load configuration file
include ("../SMTPClientClass.php");
print"<head><title>Convention Scheduling for ".$CFG['ConName']."</title><LINK REL=StyleSheet HREF='base.css'>
<LINK REL=StyleSheet HREF='".$CFG['CSS']."'>
	<script type='text/javascript' src='javascript/common.js'></script>
	<script type='text/javascript' src='javascript/css.js'></script>
	<script type='text/javascript' src='javascript/standardista-table-sorting.js'></script>
	<!--<script src='sorttable.js'></script>-->

</head>";
;
print "<div class='main_menu'>";

#$CFG['debug']=1;
debug();
#debug_CFG();

if (!array_key_exists("Action",$_REQUEST)) {
	$_REQUEST['Action']="";
	#page header stuff
	print "<center><font size=5>Convention Programming Utilities for ".$CFG['ConName']."</font></center>";
	require_once("GlobalMenu.php"); # load Global Menu

	if (!(strtoupper($CFG['Access'])=='GRANT')) {
		print "<center><font color='red' size=6>Invitation System Access Denied</font></center>";
		exit();
	}
	print '<a href="invite1.php?Action=ShowStat" target="inviteright"><img src="./ui_images/icons/anchor.png">Stats</a>&nbsp;';
	print '<a href="invite1.php?Action=AddPerson" target="inviteright"><img src="./ui_images/icons/user_add.png">Add Panelist</a>&nbsp;';
	print '<a href="invite1.php?Action=MailBox" target="inviteright"><img src="./ui_images/icons/box.png">Mail Queue</a>&nbsp;';
	print '<a href="invite1.php?Action=ShowConfig" target="inviteright"><img src="./ui_images/icons/cog.png">Config</a>&nbsp;';


	#print '<FRAMESET COLS="*,*">
	#		<FRAME SRC="invite1.php?Action=ShowList" NAME="inviteleft">
	#		<FRAME SRC="invite1.php?Action=ShowStat" NAME="inviteright">
	#		</FRAMESET>';
	print "<table border=1 width=100%><tr>
			<td width=50%><iframe width='100%' height='600' SRC='?Action=ShowList' name='inviteleft'>iframe is not supported</iframe></td>
			<td width=50%><iframe width='100%' height='600' SRC='invite1.php?Action=ShowStat' name='inviteright'>iframe is not supported</iframe></td>
			</tr></table>";
} else {
	print"<head><LINK REL=StyleSheet HREF='base.css'>
	<LINK REL=StyleSheet HREF='".$CFG['CSS']."'>
		<script type='text/javascript' src='javascript/common.js'></script>
		<script type='text/javascript' src='javascript/css.js'></script>
		<script type='text/javascript' src='javascript/standardista-table-sorting.js'></script>
		<!--<script src='sorttable.js'></script>-->

	</head>";
}
if ($_REQUEST['Action']=='SavePerson') {
	SavePerson();
	$_REQUEST['Action']='DetailPanelist';
}
if ($_REQUEST['Action']=='ShowStat') {
	TallyBox();
}
if ($_REQUEST['Action']=='ShowList') {
	ShowList();
}
if ($_REQUEST['Action']=='DetailPanelist') {
	PanelistInfo();
}
if ($_REQUEST['Action']=='PanelistResponces') {
	PanelistResponce();
}
if ($_REQUEST['Action']=='PanelistRanking') {
	PanelistRanking();
}
if ($_REQUEST['Action']=='AddPerson') {
	PanelistInsertForm();
}

if ($_REQUEST['Action']=='Invite') {
	#Invite_Loop();
}
if ($_REQUEST['Action']=='AddInvite') {
	Data_Insert_Panelist();
}
if ($_REQUEST['Action']=='SetState') {
	Force_State();
}
if ($_REQUEST['Action']=='UpdatePanelist'){
	Data_Update_Panelist();
	$_REQUEST['PanelistID']=-1;
}


#################################33
#
# Main Body
#
###################################




#print "<table><tr><td valign='top'>";
#Form_Add_Panelist();

#print "<td valign='top'>";
#Form_Force_State();
#print "<td valign='top'>";
#Form_Edit_Panelist();
#print "</tr></table>";

function PanelistInsertForm(){
	# display PanelistAddForm
	global $CFG;
	print "<table width='100%' height='600'><tr><td>";
		print "<table border=1><tr><td colspan=3><center><font color='blue' size=6> Insert New Panelist</center></font></td></tr>";
		print "<tr><td>First Name</td><td>* <input type='text' name='PanelistFirstName']></td><td width='210px' rowspan=12><img src='../img.php?pid=".$_REQUEST['PanelistID']."'></td></tr>";
		print "<tr><td>Last Name</td><td>* <input type='text' name='PanelistLastName']></td></tr>";
		print "<tr><td>Pub Name</td><td> <input type='text' name='PanelistPubName']></td></tr>";
		print "<tr><td>Badge Name</td><td> <input type='text' name='PanelistBadgeName']></td></tr>";
		print "<tr><td>Address</td><td> <input type='text' name='PanelistAddress']></td></tr>";
		print "<tr><td>City</td><td> <input type='text' name='PanelistCity']></td></tr>";
		print "<tr><td>State</td><td> <input type='text' name='PanelistState']></td></tr>";
		print "<tr><td>Zip</td><td> <input type='text' name='PanelistZip']></td></tr>";
		print "<tr><td>Day Phone</td><td> <input type='text' name='PanelistPhoneDay']></td></tr>";
		print "<tr><td>Eve Phone</td><td> <input type='text' name='PanelistPhoneEve']></td></tr>";
		print "<tr><td>Cell Phone</td><td> <input type='text' name='PanelistPhoneCell']></td></tr>";
		print "<tr><td>Email</td><td>* <input type='text' name='PanelistEmail']></td></tr>";
		print "<tr><td>Notes</td><td colspan=2> <input type='text' name='Notes']></td></tr>";
		print "<tr><td colspan=3> <input type='text' name='Biography']></td></tr><table></td></tr></table>";


}
function PanelistRanking(){
	# display Which Panels
	global $CFG;
	print "<table width='100%' height='600'><tr><td>";
	$query="select * from CPDB_Panelist
			Where PanelistID = '".$_REQUEST['PanelistID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	print "<h3>Panels Ranked by ".$row['PanelistName']."</h3>";
	$query="select R.Rank, C.Category, P.PanelTitle from CPDB_Panels as P
			Inner join CPDB_PanelRanking as R
			On P.PanelId = R.PanelID
			inner join CPDB_Category as C
			on C.CatID = P.CatID
			where R.ConID = '".$CFG['ConID']."'
			and R.PanelistID = '".$_REQUEST['PanelistID']."'
			order by R.Rank, C.Category";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	print "<table border=1><tr><th>Rank</th><th>Category</th><th>Title</th></tr>";
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr>
				<td>".$row['Rank']."</td>
				<td>".$row['Category']."</td>
				<td>".$row['PanelTitle']."</td>
				</tr>";
	}
	print "</table>";
	print "</td></tr></table>";


}
function PanelistResponce(){
	# display when panelists last touched survey pages
	global $CFG;
	print "<table width='100%' height='600'><tr><td>";
	$query="select * from CPDB_Invite as I
	inner join CPDB_Invite_Status as S
	on I.InviteID = S.InviteID
	Where I.PanelistID = '".$_REQUEST['PanelistID']."'
	and I.ConID = '".$CFG['ConID']."'
	Order by PageID";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$responces="";
	while ($row = mysql_fetch_assoc($sql)) {
		$responces.="<tr><td>".$row['PageID']."</td><td>".$row['TimeStamp']."</td></tr>";
	}
	$query= "select * from CPDB_Panelist as P
			inner join CPDB_Invite as I
			on P.PanelistId = I.PanelistID
			where P.PanelistID = '".$_REQUEST['PanelistID']."'
			and I.ConID = '".$CFG['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$linkstring='<A href="'.$CFG['webpath'].'survey.php?InviteGUID='.$row['InviteGUID'].'&InviteID='.$row['InviteID'].'"><img src="./ui_images/icons/table_edit.png">Survey</a>';

	print "<center><h3>Invite Status for ".$row['PanelistName']."</h3></center>";
	print "<table border=1>
			<tr><th>Current State</th><td>".$row['InviteState']."</td></tr>
			<tr><th>Previous State</th><td>".$row['State1']."</td></tr>
			<tr><th>LastInviteSent</th><td>".$row['InviteDate']."</td></tr>
			<tr><th>Survey Link</th><td>".$linkstring."</td></tr>
			<tr><th colspan=2>Survey Responce Dates</th></tr>
			<tr><th>Survey Page</th><th>Last Viewed</th></tr>
			".$responces."
			</table>";
	print "</td></tr></table>";
}

function PanelistInfo(){
	global $CFG;
	print "<table width='100%' height='600'><tr><td>";
	Panelist_Detail();
	Panelist_History();
	print "</td></tr></table>";
}
function ShowList(){
	global $CFG;
	print "<form method='post'><table border=1  class='sortable'>";
	Invite_List();
	$letter=Letter_Exists();

	print "<tr><td colspan=5>";

	if ($letter==1) {
		print "<center><input type='submit' name='invite' value='Invite Selected'></center>";
		print "<input type='hidden' name='Action' value='Invite' target='inviteleft'>
				</form>";
	}else {

		print "<font color='red'>".$letter."</font>";
	}
	print "</tr></td></table>";
}

function TallyBox(){
	global $CFG;
	print "<table width='100%' height='600'><tr><td>";
	$query="SELECT count( * ) AS Tally
			FROM `CPDB_Panelist` ";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	print "<table border=1><tr><th>Category</th><th>Count</th></tr>";
	print "<tr><td>Total People in Sytem</td><td>".$row['Tally']."</td></tr>";
	$mytally=$row['Tally'];
	$query="SELECT DISTINCT InviteState, count( * ) as Tally
			FROM `CPDB_Invite`
			WHERE ConID =".$CFG['ConID']."
			GROUP BY InviteState";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['InviteState']."</td><td>".$row['Tally']."</td></tr>";
		$mytally-=$row['Tally'];
	}
	print "<tr><td>People Not Invited</td><td>".$mytally."</td></tr>";
	print "</table></td></tr></table>";
}
function Form_Edit_Panelist(){
	global $CFG;
	if ((!Array_key_exists('PanelistID',$_REQUEST))||($_REQUEST['PanelistID']==-1)){
		print "<form method='post'>";
		print "EditPanelist";
		print "<table border=1>";
		print "<tr><td><b>Panelist ID</b></td></tr>";
		print "<tr><td><input type='text' name='PanelistID'</td></tr>";
		print "<tr><td><input type='submit' name='invite' value='Edit'></td></tr>";
		print "</table><input type='hidden' name='Action' value='Edit'></form></td>";
	} else {
		$query="select * from `CPDB_Panelist` where `PanelistID` = '".$_REQUEST['PanelistID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		$row = mysql_fetch_assoc($sql);
		print "<form method='post'>";
		print "EditPanelist";
		print "<table border=1>";
		#print "<tr><td><b>Panelist ID</b></td></tr>";
		print "<tr><td><b>Last Name</b></td></tr>";
		print "<tr><td><input type='text' name='PanelistLastName' value='".$row['PanelistLastName']."'></td></tr>";
		print "<tr><td><b>First Name</b></td></tr>";
		print "<tr><td><input type='text' name='PanelistFirstName' value='".$row['PanelistFirstName']."'></td></tr>";
		print "<tr><td><b>Email Name</b></td></tr>";
		print "<tr><td><input type='text' name='PanelistEmail' value='".$row['PanelistEmail']."'></td></tr>";
		print "<tr><td><input type='hidden' name='PanelistID' value='".$_REQUEST['PanelistID']."'></td></tr>";
		print "<tr><td><input type='submit' name='invite' value='UpdatePanelist'></td></tr>";
		print "</table><input type='hidden' name='Action' value='UpdatePanelist'></form></td>";
	}
}


function Form_Force_State(){
	print "<form method='post'>";
	print "Force State";
	print "<table border=1>";
	print "<tr><td><b>Panelist ID</b></td></tr>";
	print "<tr><td><input type='text' name='PanelistID'</td></tr>";
	print "<tr><td><b>Desired State</b></td></tr>";
	print "<tr><td><select name='State'>
			<option value='Not Invited'>Not Invited</option>
			<option value='Invited'>Invited</option>
			<option value='Bounced'>Bounced</option>
			<option value='Responded'>Responded</option>
			<option value='Unavailable'>Unavailable</option>
			<option value='OptOut'>Delete</option>
			</select> </tr></td>";
	print "<tr><td><input type='submit' name='invite' value='Set State'></td></tr>";
	print "</table><input type='hidden' name='Action' value='SetState'></form></td>";
}

function Form_Add_Panelist(){
	print "<form method='post'>Add Panelist<table border=1>";
	print "<tr><td><b>Last Name</b></td></tr>";
	print "<tr><td><input type='text' name='PanelistLastName'</td></tr>";
	print "<tr><td><b>First Name</b></td></tr>";
	print "<tr><td><input type='text' name='PanelistFirstName'</td></tr>";
	print "<tr><td><b>Email Address</b></td></tr>";
	print "<tr><td><input type='text' name='PanelistEmail'</td></tr>";
	print "<tr><td><input type='submit' name='invite' value='Add to List'></td></tr>";
	print "</table><input type='hidden' name='Action' value='AddInvite'></form></td>";
}
function Invite_List()
{
	global $CFG;
	#					P.EmailGood as EmailGood,
	#					I.State1 as State1,
	$query = "select * from (
				(Select	P.PanelistName as PanelistName,
					P.PanelistID as PanelistID,
					P.PanelistEmail as PanelistEmail,
					I.inviteState as InviteState,
					NULL as EmailGood,
					NULL as State1,
					I.InviteDate as InviteDate,
					I.InviteGUID as InviteGUID,
					I.InviteID as InviteID,
					I.ConID as ConID
				from CPDB_Panelist as P
				inner join CPDB_Invite as I
				on P.PanelistID = I.PanelistID
				where 	I.ConID ='".$CFG['ConID']."'
					and P.isequip=0
					and P.DNI=0)
				union distinct
				(Select 	P.PanelistName as PanelistName,
					P.PanelistID as PanelistID,
					P.PanelistEmail as PanelistEmail,
					NULL as EmailGood,
					NULL as InviteState,
					NULL as State1,
					NULL as InviteDate,
					NULL as InviteGUID,
					NULL as InviteID,
					NULL as ConID
				from CPDB_Panelist as P
				where 	P.DNI=0
					and P.IsEquip=0))AS combined_table
				group by PanelistID
				";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#	print "<table border=1  class='sortable'>";
	print"<thead class='sortlink'><tr><th>&nbsp;</th><th></th><th>Name</th><th>Survey 1</th><th>Survey 2</th><th>Date</th><th>Panels <br> Ranked</th></tr></thead><tbody>";
	while ($row = mysql_fetch_assoc($sql)) {
		if ($row['InviteState']=='') {
				$row['InviteState']='Not Invited';
				if ($row['State1']==''){
					$linkstring="<img src='./ui_images/icons/table_error.png'>";
				} else {
					$linkstring='<A href="'.$CFG['webpath'].'survey.php?InviteGUID='.$row['InviteGUID'].'&InviteID='.$row['InviteID'].'"><img src="./ui_images/icons/table_edit.png"></a>';
				}
			} else {
				$linkstring='<A href="'.$CFG['webpath'].'survey.php?InviteGUID='.$row['InviteGUID'].'&InviteID='.$row['InviteID'].'"><img src="./ui_images/icons/table_edit.png"></a>';
			}
		$selDis = "";
		if ($row['InviteState']=='Unavailable') $selDis=" Disabled ";
		$query2="select count(*) as Tally from CPDB_PanelRanking where ConID = '".$CFG['ConID']."' and PanelistID = '".$row['PanelistID']."'";
		$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		if ($row['InviteDate']=='0000-00-00 00:00:00') $row['InviteDate']='';
		if ($row['EmailGood']==1) {
			$emlico= "<a href='mailto:".$row['PanelistEmail']."'><img src='./ui_images/icons/email_edit.png'></a>";
		} else {
			$emlico=  "<img src='./ui_images/icons/email_error.png'>";
		}

		print "<tr>
			<td><input type='checkbox' name='PanelistID[]' value='".$row['PanelistID']."'".$selDis."></td>
			<td>
				<a href='?Action=DetailPanelist&PanelistID=".$row['PanelistID']."' target='inviteright'><img src='./ui_images/icons/user_edit.png'></a>&nbsp;
				".$emlico."&nbsp;
				<a href='?Action=PanelistResponces&PanelistID=".$row['PanelistID']."' target='inviteright'><img src='./ui_images/icons/date.png'></a>&nbsp;
				<a href='?Action=PanelistRanking&PanelistID=".$row['PanelistID']."' target='inviteright'><img src='./ui_images/icons/flag_green.png'></a>&nbsp;
				".$linkstring."&nbsp;
			</td>
			<td>".$row['PanelistName']."</td>
			<td>".$row['State1']."</td>
			<td>".$row['InviteState']."</td>
			<td>".$row['InviteDate']."</td>
			<td>".$row2['Tally']."</td>
			</tr>";
	}
	print "</tbody><tfoot>";
	$query="Select * from CPDB_Panelist where DNI=1";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	print "<tr><td colspan=4><center>";
	print "There are ".mysql_num_rows($sql)." people on the Do Not Invite List<br>These people can not been displayed";
	print "</td></tr></tfoot>";
}

function Letter_Exists()
{
	global $CFG;
	$filePath='../text/';
	if (!Array_key_exists('Letter',$CFG)){
		return 'No Letter Defined';
	}
	if (!file_exists($filePath.$CFG['Letter'])){
		return 'No Letter Uploaded';
	}
	return 1;
}



function Invite_Loop()
{
	global $CFG;
	global $MailHost;
	if (!(array_key_exists('PanelistID',$_REQUEST))) {
		print "<center><font size=6 color='red'>You must select at least one person to invite</font></center>";
	} else {
		foreach($_REQUEST['PanelistID'] as $value){
			$query="Select * from `CPDB_Invite`
					where `ConID` = '".$CFG['ConID']."'
					and `PanelistID` = '".$value."'";
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			if (mysql_num_rows($sql)==0) {
				$query="insert into `CPDB_Invite`
					(`InviteGUID`,
					`InviteState`,
					`InviteDate`,
					`PanelistID`,
					`ConID`)
					values
					(UUID(),
					'Invite Queyed',
					now(),
					'".$value."',
					'".$CFG['ConID']."')";
			} else {
				$row = mysql_fetch_assoc($sql);
				$query="Update `CPDB_Invite`
						Set `InviteState` = 'Invite Queyed',
						`InviteDate` = now()
						where `InviteID` = '".$row['InviteID']."'";
			}
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		}
		$letter = file_get_contents('../text/'.$CFG['Letter']);
		foreach($_REQUEST['PanelistID'] as $value){
			$myinstr = $myinstr . "'".$value."',";
		}
		$mystr=rtrim($myinstr,",");
		$query= "Select PanelistName,PanelistFirstName, PanelistLastName,InviteID, InviteGUID, PanelistEmail
				from CPDB_Panelist as P
				inner join CPDB_Invite as I
					on P.PanelistID=I.PanelistID
				where P.PanelistID in (".$mystr.")
				and I.ConID = '".$CFG['ConID']."'";
		$query1="Update CPDB_Invite
					Set InviteState='Invited',
					InviteDate=now()
					where PanelistID in (".$mystr.")
					and ConID = '".$CFG['ConID']."'";
		$eolrn="\r\n";
		$eolr="\r";
		$eol=$eolr;
		$now=time();
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		$headers = "MIME-Version: 1.0".$eolrn;
		$headers .= "Content-type: text/html; charset=iso-8859-1 ".$eolrn;
		$headers .= "From: ".$CFG['ConName']." Programming Department <".$CFG['srcEmail'].">".$eol;
		$headers .= $eolrn."Bcc: ".$CFG['BCC_Address']."".$eol;
		$headers .= "Reply-To: ".$CFG['ConName']." Programming Department <".$CFG['srcEmail'].">".$eol;
		$headers .= "Return-Path: ".$CFG['srcEmail']." ".$eol;
		$headers .= "Message-ID: <".$now."TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
		$headers .= "X-Mailer: PHP v".phpversion().$eol;         // These two to help avoid spam-filters



		while ($row = mysql_fetch_assoc($sql)) {
			$wrkltr = $letter;
			$pos=strpos($wrkltr,"[SUBJECT]");
			$pos1=strpos($wrkltr,"[/SUBJECT]");
			$subject = substr($wrkltr,$pos + 9,$pos1-$pos - 9);
			if ($row['PanelistFirstName']=="" and $row['PanelistLastName']==""){
				$nametouse = $row['PanelistName'];
			} else {
				$nametouse = $row['PanelistFirstName']." ".$row['PanelistLastName'];
			}
			$wrkltr = str_replace("[InviteName]", $nametouse, $wrkltr);
			$wrkltr = str_replace("[SUBJECT]".$subject."[/SUBJECT]","",$wrkltr);
			$btn = '<A href="'.$CFG['webpath'].'survey.php?InviteGUID='.$row['InviteGUID'].'&InviteID='.$row['InviteID'].'">Survey</a>';
			$wrkltr = str_replace("[InviteButton]", $btn, $wrkltr);

#			if(imap_mail($row['PanelistEmail'], $subject, $wrkltr, $headers, "-f".$CFG['ConName']." Programming Department <".$CFG['srcEmail'].">")){
#				print "Mail Success";
#			} else {
#				print "Mail Failure";
#			}
			mail ($row['PanelistEmail'], $subject, $wrkltr, $headers, "-f".$CFG['srcEmail']);

#			$conn=$MailHost['Programming'];
##			$SMTPMail = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $subject, $body);
#			$SMTPMail = new SMTPClient ($conn['MailServer'], $conn['MailPort'], $conn['MailUser'], $conn['MailPass'], $CFG['srcEmail'], $row['PanelistEmail'], $subject, $headers, $wrkltr);
#			$SMTPChat = $SMTPMail->SendMail();
#			#print $row['PanelistEmail']. $subject. "<head></head><body>".$wrkltr. $headers. '-f'.$CFG['srcEmail']."<br><br>";
			print "<br>Mail Sent to ".$row['PanelistName']." at ".$row['PanelistEmail']."<br>";

		}
		$sql=mysql_query($query1) or die('Query failed: ' . mysql_error());







	}
#		foreach($_REQUEST['InviteID'] as $value){
#			$myinstr = $myinstr . "'".$value."',";
#		}
#		$mystr=rtrim($myinstr,",");
#		$query="select * from `".$dbPrefix."Invite`  where InviteID in (".$mystr.")";
#		$query1="update `".$dbPrefix."Invite`  set `InviteStat` = 'Invited', `InviteDate` = now() where InviteID in (".$mystr.")";
#		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#
#		if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
#		  $eol="\r\n";
#		} elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) {
#		  $eol="\r";
#		} else {
#		  $eol="\n";
#		}
#		$eol="\r\n";
#
#
#		# Common Headers
#		$headers .= 'From: '.$ConName.'Programming Department<'.$srcEmail.'>'.$eol;
#		$headers .= 'BCC:jerrygi@verizon.net'.$eol;
#		$headers .= 'Reply-To:'.$ConName.'Programming Department<'.$srcEmail.'>'.$eol;
#		#$headers .= 'Return-Path: '.$ConName.'Programming Department<'.$srcEmail.'>'.$eol;    // these two to set reply address
#		$headers .= 'Return-Path: '.$srcEmail.$eol;    // these two to set reply address
#		$headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
#		$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
#		$headers .= 'MIME-Version: 1.0'.$eol;
#		$headers .= "Content-type: text/html\r\n";
#
#
#		while ($row = mysql_fetch_assoc($sql)) {
#			$wrkltr = $_SESSION['Letter'];
#			$pos=strpos($wrkltr,"[SUBJECT]");
#			$pos1=strpos($wrkltr,"[/SUBJECT]");
#			$subject = substr($wrkltr,$pos + 9,$pos1-$pos - 9);
#			$wrkltr = str_replace("[InviteName]", $row['InviteName'], $wrkltr);
#			$wrkltr = str_replace("[SUBJECT]".$subject."[/SUBJECT]","",$wrkltr);
#			$btn = "<form method='post' action='".$webpath."index.php'>
#				<input type='hidden' name='InviteGUID' value='".$row['InviteGUID']."'>
#				<input type='hidden' name='PanelistID' value='".$row['PanelistID']."'>
#				<input type='hidden' name='InviteID'   value='".$row['InviteID']."'>
#				<input type='hidden' name='Action'     value='Invite_Submission'>
#				<input type='submit' name='submit' value='Survey'>
#				</form>";
#			$btn = '<A href="'.$webpath."index.php?InviteGUID=".$row['InviteGUID']."&PanelistID=".$row['PanelistID']."&InviteID=".$row['InviteID']."&Action=Invite_Submission".'">Survey</a>';
#			$wrkltr = str_replace("[InviteButton]", $btn, $wrkltr);
#
#			mail($row['InviteEmail'], $subject, "<head></head><body>".$wrkltr, $headers, '-f'.$srcEmail);
#			print "<br>Mail Sent to ".$row['InviteName']." at ".$row['InviteEmail']."<br>";
#		}
#		$sql=mysql_query($query1) or die('Query failed: ' . mysql_error());
#	}

}

function Data_Insert_Panelist(){
	global $CFG;
		$query="insert into `CPDB_Panelist` (
		`PanelistName`,
		`PanelistLastName`,
		`PanelistFirstName`,
		`PanelistEmail`
		) values (
		'".$_REQUEST["PanelistLastName"].", ".$_REQUEST["PanelistFirstName"]."',
		'".$_REQUEST["PanelistLastName"]."',
		'".$_REQUEST["PanelistFirstName"]."',
		'".$_REQUEST["PanelistEmail"]."'
		)";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Update_Panelist(){
	global $CFG;
		$query="update `CPDB_Panelist` set PanelistLastName = '".$_REQUEST['PanelistLastName']."',
											PanelistFirstName = '".$_REQUEST['PanelistFirstName']."',
											PanelistEmail = '".$_REQUEST['PanelistEmail']."',
											PanelistName = '".$_REQUEST['PanelistLastName'].", ".$_REQUEST['PanelistFirstName']."'
						where PanelistID = '".$_REQUEST['PanelistID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Force_State(){
	global $CFG;
	if ($_REQUEST['State']=='OptOut') {
		optout();
	} else {
		$query="select * from CPDB_Invite where `ConID` = '".$CFG['ConID']."' and `PanelistID` = '".$_REQUEST['PanelistID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		if (mysql_num_rows($sql) == 1) {
			#update
			$row = mysql_fetch_assoc($sql);
			$query1 = "update `CPDB_Invite`
						set `InviteState` = '".$_REQUEST['State']."'
						where `InviteID` = '".$row['InviteID']."'";
			#print $query1;
			$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		} else {
			#insert
			$query1 = "insert into `CPDB_Invite`
					(`InviteGUID`,
					`InviteDate`,
					`InviteState`,
					`PanelistID`,
					`ConID`
					) values (
					UUID(),
					now(),
					'".$_REQUEST['State']."',
					'".$_REQUEST['PanelistID']."',
					'".$CFG['ConID']."'
					)";
			#print $query1;
			$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
		}
		if ($_REQUEST['State'] == "Responded") {
			$query="select * from `CPDB_PanelistCon` where `PanelistID` = '".$_REQUEST['PanelistID']."' and `ConID` = '".$CFG['ConID']."'";
			#print $query;
			$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
			if (mysql_num_rows($sql) == 0) {
				$query1="Insert into `CPDB_PanelistCon`
						(`PanelistID`,
						`ConID`
						) values (
						'".$_REQUEST['PanelistID']."',
						'".$CFG['ConID']."'
						)";
				#print "<br><br>".$query1;
				$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
			}
		}
	}
}

function optout() {
	global $CFG;
	$query="select `InviteID` from `CPDB_Invite` where `PanelistID` = '".$_REQUEST['PanelistID']."' and `ConID` = '".$CFG['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$query="Update CPDB_Panelist
			set `DNI` = 1
			where `PanelistID` = '".$_REQUEST['PanelistID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$query="Update CPDB_Invite
			set `InviteState` = 'OptOut'
			where `InviteID` = '".$row['InviteID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function debug() {
	global $CFG;
	if ($CFG['debug']==1){
		$message = "User ID = ".$_SERVER['PHP_AUTH_USER']."<br>Exploding '$ _POST'";
		array_table($_REQUEST, "99ff99",1,$message);
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

function cmp($a, $b)
{
    return strcasecmp($a, $b);
}
function validEmail($email)
{
	#checks email formating against RFC 1035, 2234, 2821, 2822, 3696
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}
#<
function Panelist_History(){
	global $CFG;
	$query="SELECT C.ConName, X.Category, P.PanelTitle, P.PanelDescription
			FROM `CPDB_P2P` as J
			inner join CPDB_Panels as P
			on J.`PanelID` = P.`PanelID`
			inner join CPDB_Convention as C
			on P.ConID = C.ConID
			inner join `CPDB_Category` as X
			on X.CatID = P.CatID
			WHERE J.`PanelistID` = '".$_REQUEST['PanelistID']."'
			order by P.ConID, X.Category";
	print "<table border=1><tr>
			<th>Convention</th>
			<th>Category</th>
			<th>Title</th>
			<th>Panel Description</th></tr>";
	$sql1=mysql_query($query) or die('Query failed: ' . mysql_error());
	while($row1 = mysql_fetch_assoc($sql1)) {
			print "<tr><td>".$row1['ConName']."</td>";
			print "<td>".$row1['Category']."</td>";
			print "<td>".$row1['PanelTitle']."</td>";
			print "<td>".$row1['PanelDescription']."</td></tr>";
	}
	print "</table>";
}

function Panelist_Detail(){
	global $CFG;
	if (!array_key_exists('PanelistID',$_REQUEST)) {
		print "No Panelist Selected";
	} else {
		print "<table width='100%' height='600'><tr><td>";
		print "PanelistID = ".$_REQUEST['PanelistID']."<br>";
		$query="Select * from CPDB_Panelist where `PanelistID` = '".$_REQUEST['PanelistID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		$row = mysql_fetch_assoc($sql);
		print "<table border=1><tr><td colspan=3><center><font color='blue' size=6>".$row['PanelistName']."</center></font></td></tr>";
		print "<tr><td>First Name</td><td>".$row['PanelistFirstName']."</td><td width='210px' rowspan=12><img src='../img.php?pid=".$_REQUEST['PanelistID']."'></td></tr>";
		print "<tr><td>Last Name</td><td>".$row['PanelistLastName']."</td></tr>";
		print "<tr><td>Pub Name</td><td>".$row['PanelistPubName']."</td></tr>";
		print "<tr><td>Badge Name</td><td>".$row['PanelistBadgeName']."</td></tr>";
		if ($CFG['ViewContact']=='GRANT') {
			print "<tr><td>Address</td><td>".$row['PanelistAddress']."</td></tr>";
			print "<tr><td>City</td><td>".$row['PanelistCity']."</td></tr>";
			print "<tr><td>State</td><td>".$row['PanelistState']."</td></tr>";
			print "<tr><td>Zip</td><td>".$row['PanelistZip']."</td></tr>";
			print "<tr><td>Day Phone</td><td>".$row['PanelistPhoneDay']."</td></tr>";
			print "<tr><td>Eve Phone</td><td>".$row['PanelistPhoneEve']."</td></tr>";
			print "<tr><td>Cell Phone</td><td>".$row['PanelistPhoneCell']."</td></tr>";
			print "<tr><td>Email</td><td>".$row['PanelistEmail']."</td></tr>";
			print "<tr><td>Notes</td><td colspan=2>".$row['Notes']."</td></tr>";
		} else {
			print "<tr><td>Address</td>		<td><font color='red'>data restricted</font></td></tr>";
			print "<tr><td>City</td>		<td><font color='red'>data restricted</font></td></tr>";
			print "<tr><td>State</td>		<td><font color='red'>data restricted</font></td></tr>";
			print "<tr><td>Zip</td>			<td><font color='red'>data restricted</font></td></tr>";
			print "<tr><td>Day Phone</td>	<td><font color='red'>data restricted</font></td></tr>";
			print "<tr><td>Eve Phone</td>	<td><font color='red'>data restricted</font></td></tr>";
			print "<tr><td>Cell Phone</td>	<td><font color='red'>data restricted</font></td></tr>";
			print "<tr><td>Email</td>		<td><font color='red'>data restricted</font></td></tr>";
		}
		print "<tr><td colspan=3>".$row['Biography']."</td></tr><table></td></tr></table>";
	}
}
?>