<?php
require_once("config.php"); # load configuration file

ob_start ();	//	Begin caching output

if (array_key_exists('date',$_GET)){
	$_POST['date']=$_GET['date'];
}

if (array_key_exists('ConID',$_GET)){
	$_POST['ConID']=$_GET['ConID'];
}
if (array_key_exists('ConID',$_POST)){
	$CFG['ConID']=$_POST['ConID'];
}


#$connection = mysql_pconnect($CFG['dbhost'],$CFG['dbusername'],$CFG['dbpasswd'])
#	or die ("Couldn't connect to server.");

#$db = mysql_select_db($CFG['database_name'], $connection) or die("Couldn't select database.");

print "<font size=5>Paneling for ".$CFG['ConName']."</font><br>";
if (!(array_key_exists('mode',$_GET))){
	$_GET['mode']='grid';
}
if ($_GET['mode']=='grid'){
	grid();
}

if ($_GET['mode']=='panel'){
	panels();
}

if ($_GET['mode']=='panelist'){
	panelist();
}

# Footers #
	print "<div class='MatrixLink'><a href='matrix.php'>Full Grid</a>";
	print "<br><a href='matrix.php?date=1/15/2010'>Friday Grid</a>";
	print "<br><a href='matrix.php?date=1/16/2010'>Saturday Grid</a>";
	print "<br><a href='matrix.php?date=1/17/2010'>Sunday Grid</a></div>";

	$out = ob_get_clean ();
	$css = "<link rel='stylesheet' href='base.css' type='text/css'/>\n";
	$out = "<html><head>$css<body class='panel'>$out</body></head></html>";
	echo $out;


function panels(){
	global $CFG;
	$query="Select * from CPDB_Panels as P
			inner join CPDB_PTR as L
			on P.PanelID = L.PanelID
			inner join CPDB_Category as C
			on C.CatID = P.CatId
			inner join CPDB_Room as R
			On R.RoomID = L.RoomID
			where PanelhidePublic=0
			and P.conID = '".$CFG['ConID']."'
			and P.PanelID = '".$_GET['ID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	print "<table border=1 width=500>";
	print "<tr><th>Title</th><td>".$row['PanelTitle']."</td></tr>";
	print "<tr><th>Description</th><td>".$row['PanelDescription']."</td></tr>";
	print "<tr><th>Start</th><td>".$row['Start']."</td></tr>";
	print "<tr><th>End</th><td>".$row['End']."</td></tr>";
	print "<tr><th>Room</th><td>".$row['RoomName']."</td></tr>";

	$query1="Select * from CPDB_Panelist as P
			inner join CPDB_P2P as L
			on P.PanelistID = L.PanelistID
			inner join CPDB_Panels as T
			on T.PanelID = L.PanelID
			where P.IsEquip=0
			and L.PanelId = '".$_GET['ID']."'
			and T.ConID = '".$CFG['ConID']."'";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	print "<th colspan=2>Panelists</th></tr>";
	while ($row1 = mysql_fetch_assoc($sql1)) {
		$outname = $row1['PanelistPubName'];
		if ($outname == ""){
			$outname = $row1['PanelistName'];
		}
		print "<tr><td colspan=2><center><a href='matrix.php?mode=panelist&ID=".$row1['PanelistID']."'>".$outname."</a>";
		if ($row1['Moderator']==1) print " <b>Moderator</b> ";
		print "</td></tr>";
	}
	print "</table>";
}

function panelist(){
	global $CFG;
	$query="select * from CPDB_Panelist as P
			inner join CPDB_PanelistCon as C
			on P.PanelistID = C.PanelistID
			where C.ConID = '".$CFG['ConID']."'
			and P.PanelistId = '".$_GET['ID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$outname=$row['PanelistPubName'];
	if ($outname=="") $outname=$row['PanelistName'];
	print "<table border=1 width=500>";
	print "<tr><th>Name</th></tr><tr><td>".$outname."</td></tr>";
	print "<tr><th>Biography</th></tr><tr><td>".$row['Biography']."</td></tr>";
	print "<tr><td rowspan=2><center><img src='img.php?pid=".$_GET['ID']."'></td></tr>";
	print "<tr><th<center>Panels</th></tr>";
	$query1="select * from CPDB_Panels as P
			inner join CPDB_P2P as L
			on P.PanelID = L.PanelID
			inner join CPDB_PTR as S
			on S.PanelId = P.PanelID
			where L.PanelistID = '".$_GET['ID']."'
			and P.ConID = '".$CFG['ConID']."'";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	while ($row1 = mysql_fetch_assoc($sql1)) {
		print "<tr><td colspan=3><a href='matrix.php?mode=panel&ID=".$row1['PanelID']."'>".$row1['PanelTitle']."</a></td></tr>";
	}

	print "</table><br><br>";






}

function grid(){
	global $CFG;
	print "<br><table border=1><tr><td><b>Key</b></td></tr>";
	print "<tr><td class='public'>Publicly displayable panel</td></tr>";
	print "<tr><td class='reserved'>Space is Blocked by Parent/Child room Events<br>or is reserved for setup/teardown</td></tr>";
	print "<tr><td class='free'>Open time slot, no panel scheduled</td></tr></table><br>";

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
			print "<table border=1 class='Grid'><tr><td colspan=49><center>";
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
					$cl = 'public';
					if (($grid[$i][$rowRoom['RoomID']]==0)or ($grid[$i][$rowRoom['RoomID']]==-1)) {
						$cl='free';
						print "<td colspan='".$cols."' class='".$cl."'>&nbsp;</td>";
					} elseif ($grid[$i][$rowRoom['RoomID']]==-2) {
						$cl='reserved';
						$i1=$i - 1800;
						print "<td colspan='".$cols."' class='".$cl."'>&nbsp;</td>";
					} else {
						$queryPanel = "Select * from `CPDB_Panels` where `PanelID` = '".$grid[$i][$rowRoom['RoomID']]."'";
						$sqlPanel=mysql_query($queryPanel) or die('Query failed: ' . mysql_error());
						$rowPanel = mysql_fetch_assoc($sqlPanel);
						if ($rowPanel['PanelHidePublic']==1) {
							print "<td colspan=".$cols." class='reserved'>&nbsp;</td>";
						} else {
							print "<td colspan=".$cols." class='public'><a href='matrix.php?mode=panel&ID=".$rowPanel['PanelID']."'>".$rowPanel['PanelTitle']."</a></td>";
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
}

?>