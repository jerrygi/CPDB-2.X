<?php
$query="Select * from `CPDB_User` where `UserName` = '".$CFG['USERNAME']."' and `UserPass` = '".$CFG['hash']."'";
$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
$sqlrows= mysql_num_rows($sql);
if ($sqlrows == 0) {
	#########################
	#handle Login Stuff here#
	#########################
	print "<table align='center'><tr><td><center><form method='post'><input type='hidden' name='Action' value='Login'>
			<table><tr><td colspan=2><center>CPDB</center></td></tr>
			<tr><td>User Name</td><td><input type='text' name='User'></td></tr>
			<tr><td>Password</td><td><input type='password' name='Hash'></td></tr>
			<tr><td colspan=2><input type='submit' value='Login'></td></tr>
			</table></form></td></tr></table>";
	exit(0);
} else {
	print "<table width='1024px' border=1><tr>";
	print "<td colspan=7><center>Logged on as : <font color='green'>".$CFG['USERNAME']."</font></center></td>";
	print "</tr><tr>";
	print "<td><center><form method='post' action='cpdb.php'><input type='submit' name='Programming Tool' value='Programming Tool'></form></center></td>";
	print "<td><center><form method='post' action='portfolio.php'><input type='submit' name='Programming Tool' value='Panelist Portfolio'></form></center></td>";
	print "<td><center><form method='post' action='invite.php'><input type='submit' name='Invite Manager' value='Invite Manager'></form></center></td>";
	print "<td><center><form method='post' action='publications.php'><input type='submit' name='Publications Manager' value='Publications Manager'></form></center></td>";
	print "<td><center><form method='post' action='facilities.php'><input type='submit' name='Facilities Manager' value='Facilities Manager'></form></center></td>";
	print "<td><center><form method='post' action='admin.php'><input type='submit' name='System Admin' value='System Admin'></form></center></td>";
	print "<td rowspan=2><center><form method='post' action='cpdb.php'><input type='submit' name='Action' value='Logout'></form><br><form method='post' action='changepass.php'><input type='submit' name='change' Value='Change Password'><input type='hidden' name='Action' value='request'></form></center></td>";
	print "</tr><tr>";
	print "<td><center><form method='post' action='registration.php'><input type='submit' name='Registration' value='Registration'></form></center></td>";
	print "<td><center><form method='post' action='dealers.php'><input type='submit' name='Dealers' value='dealers'></form></center></td>";
	print "<td><center><form method='post' action='artshow.php'><input type='submit' name='Art Show' value='Art Show'></form></center></td>";
}	print "</tr></table>";

?>