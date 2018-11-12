<?php


require_once("../config.php"); # load configuration file
print"<head><title>Convention Registration for ".$CFG['ConName']."</title><LINK REL=StyleSheet HREF='base.css'></head>";
;
print "<div class='main_menu'>";
print "<center><font size=5>Convention Registration Utilities for ".$CFG['ConName']."</font></center>";
require_once("GlobalMenu.php"); # load Global Menu

if (!(strtoupper($CFG['Access'])=='GRANT')) {
	print "<center><font color='red' size=6>Registration System Access Denied</font></center>";
	exit();
}
#$CFG['debug']=1;
debug();
debug_CFG();

if (!array_key_exists("Action",$_POST)) $_POST['Action']="RatesSetup";

if ($_POST['Action']=='EditRates') {
	Form_EditRates();
	$_POST['Action']='RatesSetup';
}

if ($_POST['Action']=='RatesSetup') {
	RatesSetup();
}

function Form_EditRates()
{
	global $CFG;
	$query="Select * from CREG_RegRates where RateID = '".$_POST['RateID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$rate = mysql_fetch_assoc($sql);
	$regset = "";
	$query="Select * from CREG_RegSet where ConID = '".$CFG['ConID']."' and RegAvail = 1";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$regset .= "<option value='".$row['RegSet']."'";
		if ($row['RegSet']==$rate['RegSet'])  $regset .= " selected ";
		$regset .=">".$row['RateExp']."</option>";
	}
	$rategrp = "";
	$query="Select * from CREG_RateGrp";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$rategrp .= "<option value='".$row['RateGrp']."'";
		if ($row['RateGrp']==$rate['RateGrp']){
			$rategrp .= " selected ";
		}
		$rategrp .=">".$row['GroupName']."</option>";
	}
	print "<table><tr><th colspan=4><center>Modify Registration Rate</th></tr>";
	print "<tr><th>Group Name</th><th>Group Rate</th><th>Rate Text</th><th>Rate Expiration</th></tr>";
	print "<tr><form method='Post'>
			<input type='hidden' name='Action' value='UpdateRate'>
			<input type='hidden' name='RateID' value='".$rate['RateID']."'>
			<td><select name='RateGrp'>".$rategrp."</select></td>
			<td><input type='text' name='Rate' value='".$rate['Rate']."'></td>
			<td><input type='text' name='RateText' value='".$rate['RateText']."'></td>
			<td><select name='RegSet'>".$regset."</select></td></tr>
			<tr><td colspan=4><center><input type='submit' name='Update' value='Update'></td></tr></form></table>";

}

function RatesSetup()
{
	global $CFG;
	print "<table><tr><td>";
	Table_CurrentRates();
	print "</td><td>";
	Table_FullRates();
	print "</td></tr></table>";
}

function Table_FullRates()
{
	global $CFG;
	$query="Select *
			from CREG_RegRates as R
			inner join CREG_RegSet as S
			on S.RegSet = R.RegSet
			inner join CREG_RateGrp as G
			on R.RateGrp = G.RateGrp
			where ConID = '".$CFG['ConID']."'
			order by G.GrpType, G.GroupName , S.RateExp";
	print "<table border=1><tr><th colspan=4><center>All (online) Registration Rates</th></tr><tr><th>Group Name</th><th>Group Rate</th><th>Rate Text</th><th>Rate Expires on</th><td> </td></tr>";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$mbgcolor='00ff00';
		if ($row['GrpType']==1) $mbgcolor='ff0000';
		print "<tr><td bgcolor='#".$mbgcolor."'>".$row['GroupName']."</td><td>".$row['Rate']."</td><td>".$row['RateText']."</td><td>".$row['RateExp']."</td><form method='post'><input  type='hidden' Name='Action' Value='EditRates'><input type='hidden' name='RateID' value='".$row['RateID']."'><td><input type='submit' value='Edit'></td></form></tr>";
	}
	print "</table>";
}

function Table_CurrentRates ()
{
	global $CFG;
	$query="Select * from (
				Select R.RateID,  R.RegSet,  R.RateGrp,  R.Rate,  R.RateText,  S.RateExp,  S.ConID,  S.RegAvail
				from CREG_RegRates as R
				inner join CREG_RegSet as S
				on S.RegSet = R.RegSet
				where S.RateExp >= curdate()
				) as D
			inner join CREG_RateGrp as G
			on D.RateGrp = G.RateGrp
			group by D.RateGrp
			Having min(RateExp)";
	print "<table border=1><tr><th colspan=4><center>Current (online) Registration Rates</th></tr><tr><th>Group Name</th><th>Group Rate</th><th>Rate Text</th><th>Rate Expires on</th></tr>";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$mbgcolor='00ff00';
		if ($row['GrpType']==1) $mbgcolor='ff0000';
		print "<tr><td bgcolor='#".$mbgcolor."'>".$row['GroupName']."</td><td>".$row['Rate']."</td><td>".$row['RateText']."</td><td>".$row['RateExp']."</td></tr>";
	}
	print "</table>";

}

function debug() {
	global $CFG;
	if ($CFG['debug']==1){
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

?>