<?php


require_once("../config.php"); # load configuration file
print"<head><title>Convention Dealer management for ".$CFG['ConName']."</title><LINK REL=StyleSheet HREF='base.css'></head>";
;
print "<div class='main_menu'>";
print "<center><font size=5>Convention Dealer Management for ".$CFG['ConName']."</font></center>";
require_once("GlobalMenu.php"); # load Global Menu

if (!(strtoupper($CFG['Access'])=='GRANT')) {
	print "<center><font color='red' size=6>Dealers System Access Denied</font></center>";
	exit();
}
#$CFG['debug']=1;
debug();
debug_CFG();

if (!array_key_exists("Action",$_POST)) $_POST['Action']="DealerList";

if ($_POST['Action']=='DealerList') {
	Display_DealerList();
}

function Display_DealerList()
{
	global $CFG;
	print "<table border=1>";
	print "<tr><th>Dealer</th><th>Dealers Website</th><td></td><td></td></tr>";
	$query="SELECT *, count(T.TableID) as TableCount
			from CREG_Dealer as D
			Left outer join CREG_DealerTable as T
			on T.DealerID = D.DealerID
			group by D.DealerID
			having D.ConID = '".$CFG['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['DealerName']."</td>";
		print "<td>".$row['DealerLink']."</td>";
		print "<td><form method='post'>
				<input type='hidden' name='Action' value='DealerList'>
				<input type='hidden' name='DealerID' value='".$row['DealerID']."'>
				<input type='submit' name='select' value='Tables'>
				</form></td>";
		print "<td><form method='post'>
				<input type='hidden' name='Action' value='DealerEdit'>
				<input type='hidden' name='DealerID' value='".$row['DealerID']."'>
				<input type='submit' name='select' value='Edit'>
				</form></td></tr>";
		if ($_POST['DealerID']==$row['DealerID']) {
			print "<tr><td colspan=4><table border=1>";
			print "<tr><th>Table#</th>
					<th>Table Status</th>
					<th>Maximum # Helpers</th>
					<th># Helpers registered offline</th>
					<th>Table Membership</th>
					<th>Online Helper Count</th>
					<th>Available Helper Slots (online)</th><td></td></tr>\r\n";
			$query1 = "SELECT *, count(*) as tally, T.TableID as TTableID
						from CREG_DealerTable as T
						left outer join CREG_DealerTableReg as R
						on R.TableID = T.TableID
						group by T.TableID, R.Type
						having T.DealerID = '".$row['DealerID']."'
						order by T.TableID, R.Type";

			$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
			while ($row1 = mysql_fetch_assoc($sql1)) {

				$grid[$row1['TTableID']]['TableID']=$row1['TTableID'];
				$grid[$row1['TTableID']]['TableMode']=$row1['TableMode'];
				$grid[$row1['TTableID']]['MaxHelpers']=$row1['MaxHelpers'];
				$grid[$row1['TTableID']]['OffLineHelpers']=$row1['OffLineHelpers'];
				if ($row1['Type']=='Table') {
					$grid[$row1['TTableID']]['TableReg']=$row1['tally'];
				} else {
					$grid[$row1['TTableID']]['RegdHelpers']=$row1['tally'];
				}
				if (!array_key_exists("TableReg",$grid[$row1['TTableID']])) $grid[$row1['TTableID']]['TableReg']=0;
				if (!array_key_exists("RegdHelpers",$grid[$row1['TTableID']])) $grid[$row1['TTableID']]['RegdHelpers.kl,k']=0;
			}
			foreach($grid as $key=> $value) {
				#<
				$avail=$grid[$key]['MaxHelpers'] - $grid[$key]['OffLineHelpers'] - $grid[$key]['RegdHelpers'];
				print "<tr>";
				print "<td>".$grid[$key]['TableID']."</td>";
				print "<td>".$grid[$key]['TableMode']."</td>";
				print "<td>".$grid[$key]['MaxHelpers']."</td>";
				print "<td>".$grid[$key]['OffLineHelpers']."</td>";
				print "<td>".$grid[$key]['TableReg']."</td>";
				print "<td>".$grid[$key]['RegdHelpers']."</td>";
#				print "<td>".$grid[$key]['TableID']."</td>";
				print "<td>".$avail."</td>";
				print "<td><form method='post'>
							<input type='hidden' name='Action' value='EditTable'>
							<input type='hidden' name='TableID' value='".$key."'>
							<input type='submit' name='submit' value='Edit'>
							</form>
							</td>";
				print "</tr>\r\n";
			}
			print "</table>";
			print "</td></tr>";
		}


	}
	print "</table>";


}
function Display_DealerList_old()
{
	global $CFG;
	$query="SELECT *,
			concat('M',old_password(concat_ws(',', DealerID, `ConID`, `DealerName`))) as code
			FROM `CREG_dealer`
			Where ConID = '".$CFG['ConID']."'
			order by `DealerName`, `ParentTable`";
	print "<table border=1>";
	print "<tr><th>Registered Online</th><th>Dealer Name</th><th>Dealer Website</th><th>Dealer Reg State</th><th>Max Helpers</th><th>Off Line Helpers</th><th>Shoping Cart Code<th></th></tr>";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		if ($row['RegID']=='') {
			print "<tr bgcolor='#8888ff'><td>No</td>";
		} else {
			print "<tr bgcolor='#00ff00'><td>Yes</td>";
		}
		print "<td>".$row['DealerName']."</td><td>".$row['DealerLink']."</td><td>".$row['CodeStat']."</td><td>".$row['MaxHelpers']."</td><td>".$row['OffLineHelpers']."</td><td>".$row['code']."</td><form method='post'><input  type='hidden' Name='Action' Value='EditDealer'><input type='hidden' name='DealerID' value='".$row['DealerID']."'><td><input type='submit' value='Edit'></td></form></tr>";
	}
	print "</table>";





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