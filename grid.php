<?php
require_once("config.php"); # load configuration file

if (array_key_exists('date',$_GET)){
	$_POST['date']=$_GET['date'];
}

$connection = mysql_pconnect($CFG['dbhost'],$CFG['dbusername'],$CFG['dbpasswd'])
	or die ("Couldn't connect to server.");

$db = mysql_select_db($CFG['database_name'], $connection) or die("Couldn't select database.");

print "<font size=5>Paneling Grid</font><br>";
print "<br><table border=1><tr><td><b>Key</b></td></tr>";
print "<tr><td bgcolor='lime'>Publicly displayable panel</td></tr>";
print "<tr><td bgcolor='#6666cc'>Space is Blocked by Parent/Child room Events<br>or is reserved for setup/teardown</td></tr>";
print "<tr><td bgcolor='aqua'>Open Time Slot, No pnale Scheduled</td></tr></table><br>";

$grid=array();
# pad grid with 0 (zero)
$StartTime = strtotime($CFG['constartdate']);
$StartDate = $StartTime;
$EndTime = $StartTime + (60*60*24*$CFG['conrundays']);
$query="select * from `CPDB_Room` where `ConID` = '".$CFG['ConID']."'";
$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_assoc($sql)) {
	for ($i = $StartTime;$i < $EndTime; $i = $i + 1800){
		$grid[$i][$row['RoomID']]=0;
	}
}
$query = "select * from `CPDB_PTR`";
$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_assoc($sql)) {
	$StartDTS=strtotime($row['Start']);
	$EndDTS=strtotime($row['End']);

	$query1="select * from `CPDB_PTR` as S inner join `CPDB_Room` as R on S.`RoomID` = R.`RoomID` where `PanelID` = '".$row['PanelID']."' and R.`ConID` = '".$CFG['ConID']."'";
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
	$queryCol .= " and R.`ConID` = '".$CFG['ConID']."'";
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
while ($rowChild = mysql_fetch_assoc($sqlChild)) {
	$temp = $rowChild['ParentRoom'].":".$rowChild['RoomID'];
	$tempP = "0:".$rowChild['ParentRoom'];
	$tempC = $rowChild['RoomID'].":0";
	$PC[$rowChild['ParentRoom']][$temp]=$rowChild['RoomID'];
	$PC['0'][$tempP]=$rowChild['ParentRoom'];
	$CP[$rowChild['RoomID']][$temp]= $rowChild['ParentRoom'];
	$CP['0'][$tempC]=$rowChild['RoomID'];
}
#####
# Parents should mask Children when Scheduled
#####
$StartTime = strtotime($CFG['constartdate']);
$EndTime = $StartTime + (60*60*24*$CFG['conrundays']);
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
$queryRoom = "select * from `CPDB_Room` where `RoomHideGrid` = 0 and `ConID` = '".$CFG['ConID']."' order by `RoomOrder`,`RoomName`";
$sqlRoom=mysql_query($queryRoom) or die('Query failed: ' . mysql_error());
print "<tr><th></th>";

print "<br  clear=all style='page-break-before:always'>";

#######
# Finaly it`s time to display the grid
#######
$conlen=$CFG['conrundays'];
for ($d = 0;$d<$conlen;$d=$d+1){
	#######
	# if $_POST['date'] exists,
	# and it is the current date in loop
	# display grid
	#######
	#strtotime($CFG['constartdate']);
	if ((!array_key_exists('date',$_POST))or($StartDate+($d*60*60*24)==(strtotime($_POST['date'])))) {
	#print " : ".$_POST['date']." : ".strtotime($_POST['date'])." ";
		######
		# Start the Daily Loop
		######
		print "<table border=1><tr><td colspan=49><center>";
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
				$cols = ($j - $i)/1800 ;
				$cl = 'lime';
				if (($grid[$i][$rowRoom['RoomID']]==0)or ($grid[$i][$rowRoom['RoomID']]==-1)) {
					$cl='aqua';
					print "<td colspan='".$cols."' bgcolor='".$cl."'>&nbsp;</td>";
				} elseif ($grid[$i][$rowRoom['RoomID']]==-2) {
					$cl='#6666cc';
					$i1=$i - 1800;
					print "<td colspan='".$cols."' bgcolor='".$cl."'>&nbsp;</td>";
				} else {
					$queryPanel = "Select * from `CPDB_Panels` where `PanelID` = '".$grid[$i][$rowRoom['RoomID']]."'";
					$sqlPanel=mysql_query($queryPanel) or die('Query failed: ' . mysql_error());
					$rowPanel = mysql_fetch_assoc($sqlPanel);
					if ($rowPanel['PanelHidePublic']==1) {
						print "<td colspan=".$cols." bgcolor='#6666cc'>&nbsp;</td>";
					} else {
						print "<td colspan=".$cols." bgcolor='lime'>".$rowPanel['PanelTitle']."</td>";
					}
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
		for ($i = $StartDate+($d*60*60*24); $i < $StartDate+(($d+1)*60*60*24); $i=$i + 1800){
			print "<td colspan=1>".date($CFG['TimeFormat'],$i)."</td>";
			if (date("G:i",$i)=='23:30') print "<td></td>";
		}
	print "</tr>";
	print "</table>";
	print "<br  clear=all style='page-break-before:always'>";
	}
}

?>