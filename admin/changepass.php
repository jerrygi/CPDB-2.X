<?php
require_once("../config.php"); # load configuration file
debug();

print"<head><title>Convention Scheduling for ".$CFG['ConName']."</title><LINK REL=StyleSheet HREF='base.css'></head>";
;
print "<div class='main_menu'>";
print "<center><font size=5>Convention Programming Utilities for ".$CFG['ConName']."</font></center>";
require_once("GlobalMenu.php"); # load Global Menu

if ($_POST["Action"] == 'Validate'){
	ValidatePwChangeRequest();
}
if ($_POST["Action"] == 'request'){
	ChangePasswordForm();
}

function ValidatePwChangeRequest()
{
	global $CFG;
	if((md5($_POST['OldPW']) == $CFG['hash']) && ($_POST['NewPW1'] == $_POST['NewPW2'])) {
		# Old pwd matches record and new pwds are identicle
		$query="update `CPDB_User`
				Set `UserPass` = '".md5($_POST['NewPW1'])."'
				where `UserID` = '".$CFG['UserID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		print "<font color='green'> Password has been updated</font><br>";

	} else {
		print "<br><font color='red'>Error<br>";
		if (!(md5($_POST['OldPW']) == $CFG['hash'])) {
			print "Old password does not match<br>";
		}
		if (!($_POST['NewPW1'] == $_POST['NewPW2'])) {
			print "New Passwords do not match<br>";
		}
		print "</font>";
		$_POST['Action']='request';
	}

}

function ChangePasswordForm()
{
	global $CFG;
	print "<table><tr><td colspan=2> Password Change for ".$CFG['USERNAME']."</td></tr>";
	print "<tr><td>Old Password</td><td><form method='post' Action='changepass.php'><input type='password' name='OldPW'></td></tr>";
	print "<tr><td>New Password</td><td><input type='password' name='NewPW1'></td></tr>";
	print "<tr><td>Re-Enter New Password</td><td><input type='password' name='NewPW2'></td></tr>";
	print "<tr><td colspan=2><input type='submit' name='Change Password' value='Change Password'><input type='hidden' name='Action' value='Validate'></form></td></tr>";

}

function debug() {
	global $CFG;
	if ($CFG['debug']==1){
		print "DEBUG<br><Br>";
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