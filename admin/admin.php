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
$CFG['TimeZone']=8;
debug();

if (!array_key_exists("Action",$_POST)) $_POST['Action']="";


	Display_Header_Options();

if ($_POST["Action"] == 'Add Category'){
	Data_Insert_Category();
	$_POST['Action'] = 'Categories';
}

if ($_POST["Action"] == 'Toggle Category State'){
	Data_Toggle_State();
	$_POST['Action'] = 'Categories';
}

if ($_POST["Action"] == 'Add User'){
	Data_Insert_User();
	$_POST['Action'] = 'Users';
}

if ($_POST["Action"] == 'Update User'){
	Data_Update_User();
	$_POST['Action']='User Details';
}

if ($_POST["Action"] == 'UnAssign Category'){
	Data_UnAssign_Category();
	$_POST['Action']='User Details';
}

if ($_POST["Action"] == 'Assign Category'){
	Data_Assign_Category();
	$_POST['Action']='User Details';
}

if ($_POST["Action"] == 'User Details'){
	Display_User_Details();
	Display_User_Categories();

}

if ($_POST["Action"] == 'Edit Config'){
	$_POST['SubAction'] = $_POST['Action'];
	$_POST['Action']='Configurations';
}
if ($_POST["Action"] == 'Delete Config'){
	Data_Delete_Config();
	$_POST['Action']='Configurations';

}
if ($_POST["Action"] == 'Update Config'){
	Data_Update_Config();
	$_POST['Action']='Configurations';
	$_POST['CFGID']=-1;
}

if ($_POST["Action"] == 'Insert Config'){
	Data_Insert_Config();
	$_POST['Action']='Configurations';
}

if ($_POST["Action"] == 'Edit Con'){
	$_POST['SubAction'] = $_POST['Action'];
	$_POST['Action']='Conventions';
}

if ($_POST["Action"] == 'Update Con'){
	Data_Update_Con();
	$_POST['Action']='Conventions';
	$_POST['ConID']=-1;
}

if ($_POST["Action"] == 'Add Con'){
	Data_Insert_Con();
	$_POST['Action']='Conventions';
	$_POST['ConID']=-1;
}

if ($_POST["Action"] == 'Users'){
	Display_New_User_Form();
	Display_Users();
}

if ($_POST["Action"] == 'Categories'){
	Display_All_Categories();
}

if ($_POST["Action"] == 'Configurations'){
	Display_Configs();
}

if ($_POST["Action"] == 'Conventions'){
	Display_Add_Cons();
	Display_Cons();
}

function Display_Cons()
{
	global $CFG;

	$query= "select * from CPDB_Convention order by ConID";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	print "<table border=1><tr><th>Convention name</th>
					<th>Start Date</th>
					<th>Length</th>
					<th>First Day Grid Start</th>
					<th>Last Day Grid End</th>
					<th>Daily Grid Start</th>
					<th>Daily Grid End</th>
					<td></td></tr>";
	while ($row = mysql_fetch_assoc($sql)) {
		if ($row['ConID']==$_POST['ConID'])
		{
			# we are editing a Con
			print "<tr> <form method='post'>
						<input type='hidden' name='ConID' value='".$row['ConID']."'>
						<input type='hidden' name='Action' value='Update Con'>
						<input type='hidden' name='ConSurveyCFG' value='0'>
						<td><input type='text' name='ConName' value='".$row['ConName']."'></td>
						<td><input type='text' name='ConDate' value='".$row['ConDate']."'></td>
						<td><select name='ConDays'>".days_select($row['ConDays'])."</select></td>
						<td><select name='ConStartHour'>".time_select($row['ConStartHour'])."</select></td>
						<td><select name='ConEndHour'>".time_select($row['ConEndHour'])."</select></td>
						<td><select name='FirstDailyHour'>".time_select($row['FirstDailyHour'])."</select></td>
						<td><select name='LastDailyHour'>".time_select($row['LastDailyHour'])."</select></td>
						<td><input type='submit' name='Edit' value='Save'></td>
						</form>
						</tr>";

		}else {
			#we are displaying a con
			print "<tr><td>".$row['ConName']."</td>
						<td>".$row['ConDate']."</td>
						<td>".$row['ConDays']."</td>
						<td>".date('ga',($row['ConStartHour']+8)*60*60)."</td>
						<td>".date('ga',($row['ConEndHour']+8)*60*60)."</td>
						<td>".date('ga',($row['FirstDailyHour']+8)*60*60)."</td>
						<td>".date('ga',($row['LastDailyHour']+8)*60*60)."</td>
						<form method='post'>
						<input type='hidden' name='Action' value='Edit Con'>
						<input type='hidden' name='ConID' value='".$row['ConID']."'>
						<td><input type='submit' name='Edit' value='Edit'></td>
						</form>
						</tr>";
		}
	}
	print "</table>";
}

function time_select($hour)
{
	global $CFG;
	$myoption = '';
	for ($i=0;$i<=23;$i++){
		$myoption .= "<option value='".$i."'";
		if ($i==$hour) $myoption .=" selected ";
		$myoption .=">".date('ga',($i+$CFG['TimeZone'])*60*60)."</option>";
	}
	return $myoption;

}

function days_select($day)
{
	global $CFG;
	$myoption = '';
	for ($i=1;$i<=10;$i++){
		$myoption .= "<option value='".$i."'";
		if ($i==$day) $myoption .=" selected ";
		$myoption .=">".$i."</option>";
	}
	return $myoption;

}

function Display_Add_Cons()
{
	global $CFG;
	print"<table border=1><tr><td colspan=2><b>Add Convention</b></td></tr>";
	print "<tr> <form method='post'>
				<input type='hidden' name='Action' value='Add Con'>
	 			<td>Convention Name<br><input type='text'name='ConName'></td>
				<td>Start Date<br><input type='text'name='ConDate' value='yyyy-mm-dd'></td>
				<td>Length<br><select name='ConDays'>".days_select('0')."</select></td>
				<td>First Day Grid Start<br><select name='ConStartHour'>".time_select('0')."</select></td>
				<td>Last Day Grid End<br><select name='ConEndHour'>".time_select('0')."</select></td>
				<td>First Daily Grid Start<br><select name='DailyConStart'>".time_select('0')."</select></td>
				<td>First Daily Grid end<br><select name='DailyConEnd'>".time_select('0')."</select></td>
				<td><input type='submit' name='Add Con' Value = 'Add Convention'>
				<input type='hidden' name='ConSurveyCFG' value=''>
				</form>

				</tr>";
	print "</table>";



}

function Display_Configs()
{
	global $CFG;
	if (!array_key_exists("FilterCFG",$_POST)) $_POST['FilterCFG']='1';
	if (!array_key_exists("FilterAPP",$_POST)) $_POST['FilterAPP']='1';
	if (!array_key_exists("FilterCFGD",$_POST)) $_POST['FilterCFGD']='2';
	if (!array_key_exists("FilterAPPD",$_POST)) $_POST['FilterAPPD']='2';
	$appSel = "";
	$query="select * from CPDB_Apps";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$appSel.="<option value='".$row['AppID']."'";
		if ($_POST['FilterAPP']==$row['AppID']) $appSel.=" Selected ";
		$appSel.=">".$row['AppName']."</option>";
	}
	$cfgSel="";
	$query="select * from CPDB_CfgLvl";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$cfgSel.="<option value='".$row['CfgLvlID']."'";
		if ($_POST['FilterCFG']==$row['CfgLvlID']) $cfgSel.=" Selected ";
		$cfgSel.=">".$row['CfgName']."</option>";
	}

	$cfgSelD="";
	$query="select * from CPDB_CfgLvl where CfgLvlID > 1";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$cfgSelD.="<option value='".$row['CfgLvlID']."'";
		if ($_POST['FilterCFGD']==$row['CfgLvlID']) $cfgSelD.=" Selected ";
		$cfgSelD.=">".$row['CfgName']."</option>";
	}

	$appSelD="";
	$query="select * from CPDB_Apps where AppID > 1";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$appSelD.="<option value='".$row['AppID']."'";
		if ($_POST['FilterAPPD']==$row['AppID']) $appSelD.=" Selected ";
		$appSelD.=">".$row['AppName']."</option>";
	}
	print "<table width='1024px' border=1><tr>";
	print "<td>
			<table width=100%>
			<tr><th colspan=2>Configuration Components</th></tr>
			<tr><form method='post'>
			<td><center>Configuration Name<br>
			<select name='FilterCFG'>".$cfgSel."</select></td>
			<td><center>Application  Name<br>
			<select name='FilterAPP'>".$appSel."</select></td><tr>
			<tr><td colspan=2><center><input type='submit' name='Filter'value='Filter'>
			<input type='hidden' name='Action' value='Configurations'>
			<input type='hidden' name='FilterAPPD' value='".$_POST['FilterAPP']."'>
			<input type='hidden' name='FilterCFGD' value='".$_POST['FilterCFG']."'>
			</center>
			</td></form></tr></table>";

	print "<table border=1 width=50%><tr><th>Variable</th><th>Value</th><td></td></tr>";
	$query="Select * from CPDB_Config where `CfgLvlID` = '".$_POST['FilterCFG']."' and `CFG_APP` = '".$_POST['FilterAPP']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		if ($row['CFGID'] == $_POST['CFGID']) {
			print "<tr><td>".$row['CFG_Variable']."</td>
					<form method='post'>
					<td><input type='text' name='CFG_Value' value='".$row['CFG_Value']."'></td>
					<input type='hidden' name='Action' value='Update Config'>
					<input type='hidden' name='FilterAPP' value='".$_POST['FilterAPP']."'>
					<input type='hidden' name='FilterCFG' value='".$_POST['FilterCFG']."'>
					<input type='hidden' name='FilterAPPD' value='".$_POST['FilterAPPD']."'>
					<input type='hidden' name='FilterCFGD' value='".$_POST['FilterCFGD']."'>
					<input type='hidden' name='CFGID' value='".$row['CFGID']."'>
					<input type='hidden' name='CFG_Variable' value='".$row['CFG_Variable']."'>
					<input type='hidden' name='CfgLvlID' value='".$row['CfgLvlID']."'>
					<input type='hidden' name='CFG_APP' value='".$row['CFG_APP']."'>
					<td><input type='submit' value='Save' name='Save'></td></form>
					<form method='post'>
					<input type='hidden' name='Action' value='Delete Config'>
					<input type='hidden' name='FilterAPP' value='".$_POST['FilterAPP']."'>
					<input type='hidden' name='FilterCFG' value='".$_POST['FilterCFG']."'>
					<input type='hidden' name='FilterAPPD' value='".$_POST['FilterAPPD']."'>
					<input type='hidden' name='FilterCFGD' value='".$_POST['FilterCFGD']."'>
					<input type='hidden' name='CFGID' value='".$row['CFGID']."'>
					<td><input type='submit' value='Delete' name='Delete'></td></form>
					</tr>";

		} else {
			print "<tr><td>".$row['CFG_Variable']."</td>
					<td>".$row['CFG_Value']."</td>
					<form method='post'>
					<input type='hidden' name='Action' value='Edit Config'>
					<input type='hidden' name='FilterAPP' value='".$_POST['FilterAPP']."'>
					<input type='hidden' name='FilterCFG' value='".$_POST['FilterCFG']."'>
					<input type='hidden' name='FilterAPPD' value='".$_POST['FilterAPPD']."'>
					<input type='hidden' name='FilterCFGD' value='".$_POST['FilterCFGD']."'>
					<input type='hidden' name='CFGID' value='".$row['CFGID']."'>
					<td><input type='submit' value='Edit' name='Edit'></td></form>
					<form method='post'>
					<input type='hidden' name='Action' value='Delete Config'>
					<input type='hidden' name='FilterAPP' value='".$_POST['FilterAPP']."'>
					<input type='hidden' name='FilterCFG' value='".$_POST['FilterCFG']."'>
					<input type='hidden' name='FilterAPPD' value='".$_POST['FilterAPPD']."'>
					<input type='hidden' name='FilterCFGD' value='".$_POST['FilterCFGD']."'>
					<input type='hidden' name='CFGID' value='".$row['CFGID']."'>
					<td><input type='submit' value='Delete' name='Delete'></td></form>
					</tr>";
		}
	}
	print "<tr><form method='post'><td>
			<input type='text' name='CFG_Variable'>
			</td><td>
			<input type='text' name='CFG_Value'>
			</td><td colspan=2>
			<input type='submit' name='Add' value='Add'>
			</td>
			<input type='hidden' name='Action' value='Insert Config'>
			<input type='hidden' name='CfgLvlID' value='".$_POST['FilterCFG']."'>
			<input type='hidden' name='CFG_APP' value='".$_POST['FilterAPP']."'>
			</form>
			</tr>";
	print "</table></td><td width=50%>";
	print "<table width=100% valign='top'><tr><th colspan=2>Derived Configuration</th></tr>";
	print "<tr><form method='post'>
			<input type='hidden' name='Action' value='Configurations'>
			<td><center>Configuration Name<br>
			<select name='FilterCFGD'>".$cfgSelD."</select></td>
			<td><center>Application Name<br>
			<select name='FilterAPPD'>".$appSelD."</select></td></tr>
			<tr><td colspan=2><center><input type='submit' name='Filter' value='Filter'></td>
			<input type='hidden' name='FilterAPP' value='".$_POST['FilterAPP']."'>
			<input type='hidden' name='FilterCFG' value='".$_POST['FilterCFG']."'>
			</form></tr></table>";
		$CFGD1 = array();
		$CFGD2 = array();
		$CFGD3 = array();
		$CFGD4 = array();
		$query1="select * from CPDB_Config where CfgLvlID=1 and CFG_APP=1";
		$query2="select * from CPDB_Config where CfgLvlID=1 and CFG_APP='".$_POST['FilterAPPD']."'";
		$query3="select * from CPDB_Config where CfgLvlID='".$_POST['FilterCFGD']."' and CFG_APP=1";
		$query4="select * from CPDB_Config where CfgLvlID='".$_POST['FilterCFGD']."' and CFG_APP='".$_POST['FilterAPPD']."'";

		$sql=mysql_query($query1) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_assoc($sql)) {
			if (array_key_exists($row['CFG_Variable'], $CFGD1)) {
				$CFGD1[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
			} else {
				$CFGD1[$row['CFG_Variable']] = $row['CFG_Value'];
			}
		}

		$sql=mysql_query($query2) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_assoc($sql)) {
			if (array_key_exists($row['CFG_Variable'], $CFGD2)) {
				$CFGD2[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
			} else {
				$CFGD2[$row['CFG_Variable']] = $row['CFG_Value'];
			}
		}

		$sql=mysql_query($query3) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_assoc($sql)) {
			if (array_key_exists($row['CFG_Variable'], $CFGD3)) {
				$CFGD3[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
			} else {
				$CFGD3[$row['CFG_Variable']] = $row['CFG_Value'];
			}
		}

		$sql=mysql_query($query4) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_assoc($sql)) {
			if (array_key_exists($row['CFG_Variable'], $CFGD4)) {
				$CFGD4[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
			} else {
				$CFGD4[$row['CFG_Variable']] = $row['CFG_Value'];
			}
		}


	foreach ($CFGD1 as $key => $value) {
		#<
		unset($CFG[$key]);
		$CFGD[$key]=$value;
	}

	foreach ($CFGD2 as $key => $value) {
		#<
		unset($CFG[$key]);
		$CFGD[$key]=$value;
	}

	foreach ($CFGD3 as $key => $value) {
		#<
		unset($CFG[$key]);
		$CFGD[$key]=$value;
	}

	foreach ($CFGD4 as $key => $value) {
		#<
		unset($CFG[$key]);
		$CFGD[$key]=$value;
	}
	print "<table width=100% border=1><tr><th>Variable</th><th>Value</th></tr>";
	foreach ($CFGD as $key => $value) {
		#<
		print "<tr><td>".$key."</td><td>".$value."</td></tr>";
	}
	print "</table>";
	print "<Center><table border=1><tr><th>Derived Configuration<br>Applies to</td></tr>";
	$query="Select * from CPDB_User where `CfgLvl` = '".$_POST['FilterCFGD']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['UserName']."</td></tr>";
	}
	print "</table>";






	print "</table>";



}
function Display_Header_Options()
{
	global $CFG;
	print "<table width='1024px' border=1><tr>";
	print "<td><center><form method='post'><input type='submit' name='Action' value='Users'></form></td>";
	print "<td><center><form method='post'><input type='submit' name='Action' value='Configurations'></form></td>";
	print "<td><center><form method='post'><input type='submit' name='Action' value='Categories'></form></td>";
	print "<td><center><form method='post'><input type='submit' name='Action' value='Conventions'></form></td>";
	print "</tr></table>";

}


function Display_All_Categories()
{
	global $CFG;
	$query="SELECT C.Category, C.CatID, C.Active, count(X.UserID) as Tally
			FROM `CPDB_UserCat` as X
			right outer Join CPDB_Category as C
			on C.CatID = X.CatID
			Group by C.CatID
			order by C.Category";
	print "<Table border=1 width='1024px'>";
	print "<tr><th>Category</th><th>Users</th><th>State</th></tr>";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['Category']."</td>";
		print "<td>".$row['Tally']."</td>";
		if ($row['Active']==1) {
			print "<form method='post'>
				<td bgcolor='#00ff00'>
				<input type='hidden' name='Action' value='Toggle Category State'>
				<input type='hidden' name='CatID' value='".$row['CatID']."'>
				<input type='submit' name='Disable' value='Disable'>
				</td>
				</form>";
		} else {
			print "<form method='post'>
				<td bgcolor='#ff0000'>
				<input type='hidden' name='Action' value='Toggle Category State'>
				<input type='hidden' name='CatID' value='".$row['CatID']."'>
				<input type='submit' name='Disable' value='Activate'>
				</td>
				</form>";
		}
		print "</tr>";
	}
	print "<tr>
			<form method='post'>
			<input type='hidden' name='Action' value='Add Category'>
			<td><input type='text' name='Category'></td>
			<td><input type='radio' name='Active' value=0 Checked>Disabled
			<br><input type='radio' name='Active' value=1 Checked>Active
			</td>
			<td><input type='submit' name='Add' value='Add'>
			</td></tr></form>";
	print "</table>";
}

function Display_New_User_Form()
{
	global $CFG;
	$options="";
	$query="select * from CPDB_CfgLvl where `CfgName` <> 'GLOBAL'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$options.="<option value=".$row['CfgLvlID'].">".$row['CfgName']."</option>";
	}
	$query="select * from CPDB_Convention";
	$cons='';
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row2 = mysql_fetch_assoc($sql)) {
		$cons.="<option value=".$row2['ConID'];
		if ($row2['ConID']==$row['ConID']) $cons .= " Selected ";
		$cons.=">".$row2['ConName']."</option>";
	}
	print "<Table border=1 width='1024px'>";
	print "<tr><th>Add New User</th><td> <form method='post'>User Name<br><input name='UserName' type='text'></br>";
	print "<td>User Configuration<br><select name='CfgLvl'>".$options."</select></td>";
	print "<td>Convention<br><select name='ConID'>".$cons."</select></td>";
	print "<td>Password<br><input type='password' name='NewPW1'></td>";
	print "<td><input type='radio' name='Active' value=1>Active<br><input type='radio' name='Active' value=0 CHECKED>Disabled</td>";
	print "</tr><tr>";
	print "<td colspan=6><center><input type='submit' name='submit' value='Add User'>";
	print "<input type='hidden' name='Action' value='Add User'></form></td></tr>";
	print "</table>";
}

function Display_Users ()
{
	global $CFG;
	print "<Table border=1 width='1024px'><tr><td width=10%></td><th width=20%>User Name</th><th width=20%>Configuration Level</th><th width=5%>Category Count</th><th width=10%>Convention</th><th width=5%>State</th></tr>";
	$query = "SELECT U.`UserID`, U.`UserName` , U.`Active` , C.`CFGName` , E.`ConName`,  count( CatID ) AS tally
				FROM `CPDB_UserCat` AS X
				right Outer JOIN CPDB_User AS U ON U.UserID = X.UserID
				Inner JOIN CPDB_CfgLvl AS C ON U.CfgLvl = C.CfgLvlID
				inner join CPDB_Convention as E on U.ConID = E.ConID
				GROUP BY U.UserID";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr>";
		print "<td><form method='post'>
				<input type='hidden' name='Action' value='User Details'>
				<input type='submit' name='details' value='details'>
				<input type='hidden' name='UserID' value='".$row['UserID']."'>
				</form>
				</td>";
		print "<td>".$row['UserName']."</td>";
		print "<td>".$row['CFGName']."</td>";
		print "<td>".$row['tally']."</td>";
		print "<td>".$row['ConName']."</td>";
		if ($row['Active']==1) {
			print "<td>Active</td>";
		} else {
			print "<td>Disabled</td>";
		}
		print "</tr>";

	}
	print "</table>";
}


function Display_User_Details()
{
	global $CFG;
	$query = "SELECT U.`UserID`, U.`UserName` , U.`Active` ,U.`ConID`, C.`CFGName`,C.`CfgLvlID` , count( CatID ) AS tally
				FROM `CPDB_UserCat` AS X
				right Outer JOIN CPDB_User AS U ON U.UserID = X.UserID
				Inner JOIN CPDB_CfgLvl AS C ON U.CfgLvl = C.CfgLvlID
				GROUP BY U.UserID
				having U.UserID = '".$_POST['UserID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$query="select * from CPDB_CfgLvl where `CfgName` <> 'GLOBAL'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row1 = mysql_fetch_assoc($sql)) {
		$options.="<option value=".$row1['CfgLvlID'];
		if ($row1['CfgLvlID']==$row['CfgLvlID']) $options .= " Selected ";
		$options.=">".$row1['CfgName']."</option>";
	}
	$query="select * from CPDB_Convention";
	$cons='';
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row2 = mysql_fetch_assoc($sql)) {
		$cons.="<option value=".$row2['ConID'];
		if ($row2['ConID']==$row['ConID']) $cons .= " Selected ";
		$cons.=">".$row2['ConName']."</option>";
	}
	print "<Table border=1 width='1024px'>";
	print "<TR><th>Modify User</th><td><form method='post'>User Name<br><input type='text' name='UserName' value='".$row['UserName']."'></td>";
	print "<td>Configuration<br><select name='CfgLvl'>".$options."</select></td>";
	print "<td>Convention<br><select name='ConID'>".$cons."</select></td>";
	$ra0=$ra1="";
	if ($row['Active']==0) {
		$ra0='Checked';
	} else {
		$ra1='Checked';
	}
	print "<td><input type='radio' name='Active' value=1 ".$ra1.">Active<br><input type='radio' name='Active' value=0 ".$ra0.">Disabled</td>";
	print "</tr><tr><td colspan=5><center>";
	print "<input type='hidden' name='Action' value='Update User'>
			<input type='submit' value='Update User'>
			<input type='hidden' name='UserID' value='".$row['UserID']."'>
			</form>";
	print "</table>";
}


function Display_User_Categories()
{
	global $CFG;
	print "<table border=1>";
	print "<tr><th>Category</th><td></td><td></td></tr>";
	$query="select * from CPDB_UserCat where UserID='".$_POST['UserID']."' order by CatID";

	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$CATS[$row['CatID']]=1;
	}

	$query="select * from CPDB_Category order by CatID";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print"<tr><td>".$row['Category']."</td>";
		if ($row['Active']==1) {
			if ($CATS[$row['CatID']] ==1) {
				print "<td>
						<form method='post'>
						<input type='hidden' name='UserID' value='".$_POST['UserID']."'>
						<input type='hidden' name='CatID' value='".$row['CatID']."'>
						<input type='hidden' name='Action' Value='UnAssign Category'>
						<input type='submit' name='Remove' value='Remove'>
						</td></form><td></td>";
			} else {
				print "<td></td><td>
						<form method='post'>
						<input type='hidden' name='UserID' value='".$_POST['UserID']."'>
						<input type='hidden' name='CatID' value='".$row['CatID']."'>
						<input type='hidden' name='Action' Value='Assign Category'>
						<input type='submit' name='Add' value='Add'>
						</td></form>";
			}
		} else {
			print "<td colspan=2>Disabled</td>";
		}
		print "</tr>";
	}

	print "</table>";





}
function Data_Insert_User()
{
	global $CFG;
	$query="insert into CPDB_User (`CfgLvl`,`UserName`,`Active`,`ConID`,`UserPass`) value ('".$_POST['CfgLvl']."','".$_POST['UserName']."','".$_POST['Active']."','".$_POST['ConID']."','".md5($_POST['NewPW1'])."')";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

}

function Data_Update_Con()
{
	global $CFG;
	$query="update `CPDB_Convention`
			Set `ConName`	= '".$_POST['ConName']."',
			`ConDate`		= '".$_POST['ConDate']."',
			`ConDays`		= '".$_POST['ConDays']."',
			`ConStartHour`	= '".$_POST['ConStartHour']."',
			`ConEndHour`	= '".$_POST['ConEndHour']."',
			`FirstDailyHour`= '".$_POST['FirstDailyHour']."',
			`LastDailyHour` = '".$_POST['LastDailyHour']."',
			`ConSurveyCFG` 	= '".$_POST['ConSurveyCFG']."'
			where `ConID` = '".$_POST['ConID']."'
			";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}
function Data_Insert_Con()
{
	global $CFG;
	$query="insert into `CPDB_Convention`
				(`ConName`,
				`ConDate`,
				`ConDays`,
				`ConStartHour`,
				`ConEndHour`,
				`FirstDailyHour`,
				`LastDailyHour`,
				`ConSurveyCFG`)
				values
				('".$_POST['ConName']."',
				'".$_POST['ConDate']."',
				'".$_POST['ConDays']."',
				'".$_POST['ConStartHour']."',
				'".$_POST['ConEndHour']."',
				'".$_POST['FirstDailyHour']."',
				'".$_POST['LastDailyHour']."',
				'".$_POST['ConSurveyCFG']."'
				)
			";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Update_User()
{
	global $CFG;
	$query="update CPDB_User
			Set `CfgLvl` = '".$_POST['CfgLvl']."',
			`UserName` = '".$_POST['UserName']."',
			`Active` = '".$_POST['Active']."',
			`ConID` = '".$_POST['ConID']."'
			where UserID='".$_POST['UserID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Assign_Category()
{
	global $CFG;
	$query="Insert into CPDB_UserCat (`UserID`, `CatID`) value ('".$_POST['UserID']."','".$_POST['CatID']."')";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_UnAssign_Category()
{
	global $CFG;
	$query="delete from CPDB_UserCat where `UserID` = '".$_POST['UserID']."' and `CatID` = '".$_POST['CatID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

}

function Data_Insert_Category()
{
	global $CFG;
	$query="Select * from CPDB_Category where `Category` = '".$_POST['Category']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$numRows =mysql_num_rows($sql);
	if ($numRows == 0){
		$query="Insert into CPDB_Category (`Category`, `Active`) value ('".$_POST['Category']."','".$_POST['Active']."')";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	} else {
		print "<font color='red'>Duplicate Category, can not be added</font><br>";
	}

}

function Data_Toggle_State()
{
	global $CFG;
	$query="Select * from CPDB_Category where CatID = '".$_POST['CatID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	if ($row['Active']==1) {
		$newstate=0;
	} else {
		$newstate=1;
	}
	$query="Update CPDB_Category set `Active` = '".$newstate."' where CatID = '".$_POST['CatID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Delete_Config()
{
	global $CFG;
	$query="delete from CPDB_Config where CFGID = '".$_POST['CFGID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Insert_Config()
{
	global $CFG;
	$query="Insert into CPDB_Config (`CfgLvlID`,`CFG_APP`,`CFG_Variable`,`CFG_Value`) values
			('".$_POST['CfgLvlID']."','".$_POST['CFG_APP']."','".$_POST['CFG_Variable']."','".$_POST['CFG_Value']."')";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
}

function Data_Update_Config()
{
	global $CFG;
	$query="Update CPDB_Config
			Set`CfgLvlID` = '".$_POST['CfgLvlID']."',
			`CFG_APP` = '".$_POST['CFG_APP']."',
			`CFG_Variable` = '".$_POST['CFG_Variable']."',
			`CFG_Value` = '".$_POST['CFG_Value']."'
			where `CFGID` = '".$_POST['CFGID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

}

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

