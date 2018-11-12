<?php


require_once("../config.php"); # load configuration file
include ("../SMTPClientClass.php");
include_once("phpReportGen.php");

print"<head><title>Convention Scheduling for ".$CFG['ConName']."</title><LINK REL=StyleSheet HREF='base.css'>
<LINK REL=StyleSheet HREF='".$CFG['CSS']."'>
	<script type='text/javascript' src='javascript/common.js'></script>
	<script type='text/javascript' src='javascript/css.js'></script>
	<script type='text/javascript' src='javascript/standardista-table-sorting.js'></script>
	<!--<script src='sorttable.js'></script>-->

</head><body>";
$query='';
print "<form method='post'><table border=1><tr>
		<td>Panelist 1<br>We will keep this contact information</td>
		<td><input type='text' name='PanelistID1'></td>
		</tr><tr>
		<td>Panelist 2<br>Contact information will be lost</td>
		<td><input type='text' name='PanelistID2'></td>
		</tr><tr>
		<td colspan=2><input type='submit' name='Action' value='Merge'></td>
		</tr></table></form><br>";

if ($_REQUEST['Action']=='Merge'){
	$query1="update CPDB_PanelRanking set PanelistID='".$_REQUEST['PanelistID1']."' where PanelistID ='".$_REQUEST['PanelistID2']."'";
	$query2="update CPDB_P2P set PanelistID='".$_REQUEST['PanelistID1']."' where PanelistID ='".$_REQUEST['PanelistID2']."'";
	$query3="delete from CPDB_Availability where PanelistID ='".$_REQUEST['PanelistID2']."'";
	$query4="delete from CPDB_Invite where PanelistID ='".$_REQUEST['PanelistID2']."'";
	$query5="delete from CPDB_PanelistCon where PanelistID ='".$_REQUEST['PanelistID2']."'";
	$query6="delete from CPDB_Panelist where PanelistID ='".$_REQUEST['PanelistID2']."'";
}


if ($query1<>''){
	#<
	$sql=mysql_query($query1) or die('Query failed: ' . mysql_error());
	$sql=mysql_query($query2) or die('Query failed: ' . mysql_error());
	$sql=mysql_query($query3) or die('Query failed: ' . mysql_error());
	$sql=mysql_query($query4) or die('Query failed: ' . mysql_error());
	$sql=mysql_query($query5) or die('Query failed: ' . mysql_error());
	$sql=mysql_query($query6) or die('Query failed: ' . mysql_error());
}


?>