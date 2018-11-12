<?php


require_once("../config.php"); # load configuration file
print"<head><title>Convention Scheduling for ".$CFG['ConName']."</title><LINK REL=StyleSheet HREF='base.css'></head>";
;
print "<div class='main_menu'>";
print "<center><font size=5>Convention Programming Utilities for ".$CFG['ConName']."</font></center>";
require_once("GlobalMenu.php"); # load Global Menu

#if (!array_key_exists("Access",$CFG)) $CFG['Access']="DENY";
if (!(strtoupper($CFG['Access'])=='GRANT')) {
	print "<center><font color='red' size=6>portfolio Access Denied</font></center>";
	exit();
}
#$CFG['debug']=1;
debug();

if (!array_key_exists("Action",$_POST)) $_POST['Action']="";



print "<table width=100%><tr><td width='25%'>";
	panelist_selection_list();
	print "</td><td valign='top' width=75%>";
	Panelist_Detail();
	print "<center><font size=4 color='blue'>Panel History</font></center>";
	Panelist_History();
	print "</td></tr></table>";





function debug() {
	global $CFG;
	if ($CFG['debug']==1){
	print $_SERVER['PHP_AUTH_USER'];
		print "<br><font color='red'>".$mySelect."<br>";
		print_r (array_keys($_POST));
		print"<br>\r\n";
		print_r (array_values($_POST));
		print"<br>\r\n";
		print $_SERVER["QUERY_STRING"];
		print "</font><br>";
	}
}

function panelist_selection_list() {
	global $CFG;
	print"<table border=1><tr><th>Panelist Name</th><th>Portfolio</th></tr>";
	$query="select * from CPDB_Panelist order by PanelistName";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['PanelistName']."</td>
				<form method='post'>
				<td><input type='submit' name='View' value='View'></td>
				<input type='hidden' name='Action' value='View Portfolio'>
				<input type='hidden' name='PanelistID' value='".$row['PanelistID']."'>
				</form>
				</tr>";
	}
	print "</table>";


}

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
			WHERE J.`PanelistID` = '".$_POST['PanelistID']."'
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
	if (!array_key_exists('PanelistID',$_POST)) {
		print "No Panelist Selected";
	} else {
		$query="Select * from CPDB_Panelist where `PanelistID` = '".$_POST['PanelistID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		$row = mysql_fetch_assoc($sql);
		print "<table border=1><tr><td colspan=3><center><font color='blue' size=6>".$row['PanelistName']."</center></font></td></tr>";
		print "<tr><td>First Name</td><td>".$row['PanelistFirstName']."</td><td width='210px' rowspan=12><img src='../img.php?pid=".$_POST['PanelistID']."'></td></tr>";
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
		print "<tr><td colspan=3>".$row['Biography']."</td></tr><table>";
	}
}