<?php
require_once("config.php"); # load configuration file
my_header();
if(!(array_key_exists('mode',$_GET))){
	$_GET['mode']='participants';
}

if ($_GET['mode']=='panel'){
	if(!(array_key_exists('id',$_GET))){
		$_GET['mode']='participants';
	} else {
		Show_Panel();
	}
}

if ($_GET['mode']=='panelist'){
	if(!(array_key_exists('id',$_GET))){
		$_GET['mode']='participants';
	} else {
		Show_Panelist();
	}
}

if ($_GET['mode']=='grid'){
	if(!(array_key_exists('start',$_GET))){
		$_GET['mode']='participants';
	} else {
		if(!(array_key_exists('dur',$_GET))){
			$_GET['mode']='participants';
		} else {
			Show_Grid();
		}
	}
}

if($_GET['mode']=='participants') {
	List_Panelists();
}

function Show_Panel() {
	global $CFG;
	where_am_i();

	$query="select * from CPDB_Panels as P
	inner join CPDB_PTR as L
	on P.PanelID = L.PanelID
	inner join CPDB_Room as R
	on L.RoomID = R.RoomID
	where P.PanelID = '".$_GET['id']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	print "<table border=1 width='475'>
			<tr><th>Title</th><td>".$row['PanelTitle']."</td></tr>
			<tr><th>Description</th><td>".stripslashes(stripslashes($row['PanelDescription']))."</td></tr>
			<tr><th>Start</th><td>".$row['Start']."</td></tr>
			<tr><th>End</th><td>".$row['End']."</td></tr>
			<tr><th>Room</th><td>".$row['RoomName']."</td></tr>
			<tr><th colspan=2><center>Panelists</th></tr>";
	$query="select * from CPDB_P2P as L
			inner join CPDB_Panelist as P
			on L.PanelistID = P.PanelistID
			where L.PanelID = '".$_GET['id']."'
			order by Moderator desc";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$link="xgrid.php?mode=panelist&id=".$row['PanelistID'];
		print "<tr><td colspan=2><center>";
		if ($row['Moderator']==1) print "<b>Moderator</b><br>";
		print "<a href='".$link."'>".right_name($row)."</a></td></tr>";
	}
	print "</table>";



}

function Show_Panelist(){
	global $CFG;
	where_am_i();

	$query="select * from CPDB_Panelist where PanelistID=".$_GET['id'];
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$displayname=$row['PanelistPubName'];
	#if ($displayname=='') $displayname=$row['PanelistBadgeName'];
	if ($displayname=='') $displayname=$row['PanelistFirstName']." ".$row['PanelistLastName'];
	print "<table border=1 width='475'>
			<tr><th>Name</th></tr>
			<tr><td><center>".$displayname."</td></tr>
			<tr><th>Biography</th></tr>
			<tr><td>".$row['Biography']."</td></tr>
			<tr><td><center><img src='img.php?pid=".$_GET['id']."'</td></tr>
			";
	$query="Select * from CPDB_P2P as L
			inner join CPDB_Panels as P
			on L.PanelID = P.PanelID
			where L.PanelistID = '".$_GET['id']."'
			and L.ConID = '".$CFG['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$link='xgrid.php?mode=panel&id='.$row['PanelID'];
		print "<tr><td><a href='".$link."'>".$row['PanelTitle']."</a></td></tr>";
	}
	print "</table>";
#	print $query;

}

function List_Panelists() {
	global $CFG;
	where_am_i();
	$query="Select * from CPDB_PanelistCon as C
			inner join CPDB_Panelist as P
			on P.PanelistID = C.PanelistID
			where C.ConID = '".$CFG['ConID']."'
			and ((PanelistPubName <> '') or (PanelistLastName <> '') or (PanelistFirstName <> ''))
			order by PanelistLastName";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#	print $query."<br>";
	print "<table border=2>";
	$col=1;
	while ($row = mysql_fetch_assoc($sql)) {
		$displayname=$row['PanelistPubName'];
		#if ($displayname=='') $displayname=$row['PanelistBadgeName'];
		if ($displayname=='') $displayname=$row['PanelistFirstName']." ".$row['PanelistLastName'];
		$link="xgrid.php?mode=panelist&id=".$row['PanelistID'];
		if ($col==1) {
			$col=2;
			print "<tr><td>";
			print "<table border=1 width='200'><thead><tr onClick='showHide(this)'><td><img src='./p-m.JPG'>".right_name($row)." </td></tr></thead>";
			print "<tbody style='display:none';><tr><th>Biography</th></tr>
			<tr><td>".$row['Biography']."</td></tr>
			<tr><td><center><img src='img.php?pid=".$row['PanelistID']."'";
			print "</td></tr>
			</tbody>";
			print "</table>";
			#print "<br><a href='".$link."'>".right_name($row)."</a>"
			print "</td>";
		} else if ($col==2){
			$col=3;
			print "<Td>";
			print "<table border=1 width='200'><thead><tr onClick='showHide(this)'><td><img src='./p-m.JPG'>".right_name($row)."</td></tr></thead>";
			print "<tbody style='display:none';><tr><th>Biography</th></tr>
			<tr><td>".$row['Biography']."</td></tr>
			<tr><td><center><img src='img.php?pid=".$row['PanelistID']."'";
			print "</td></tr>
			</tbody>";
			print "</table>";
			#print "<br><a href='".$link."'>".right_name($row)."</a>";
			print "</td>";
		} else {
			$col=1;
			print "<Td>";
			print "<table border=1 width='200'><thead><tr onClick='showHide(this)'><td><img src='./p-m.JPG'>".right_name($row)."</td></tr></thead>";
			print "<tbody style='display:none';><tr><th>Biography</th></tr>
			<tr><td>".$row['Biography']."</td></tr>
			<tr><td><center><img src='img.php?pid=".$row['PanelistID']."'";
			print "</td></tr>
			</tbody>";
			print "</table>";
			#print "<br><a href='".$link."'>".right_name($row)."</a>";
			print "</td></tr>";
		}
		#print "<br><a href='".$link."'>".right_name($row)."</a>";
	}
	if ($col==2) print "<td>&nbsp;</td><td>&nbsp;</td></tr>";
	if ($col==3) print "<td>&nbsp;</td></tr>";
	print "</table>";
}

function right_name($row){
	global $CFG;

		$displayname=$row['PanelistPubName'];
		#if ($displayname=='') $displayname=$row['PanelistBadgeName'];
		if ($displayname=='') $displayname=$row['PanelistFirstName']." ".$row['PanelistLastName'];
	return ucwords(strtolower($displayname));
	#return $displayname;
}

function where_am_i(){
	#var_dump($_GET);

}

function my_header(){
	global $CFG;
	$CFG['Duration']=4;
	print "<head>
			<script type='text/javascript'>
			var count = '".$CFG['BiographySize']."';
			function limiter(){
				var tex = document.survey.Biography.value;
				var len = tex.length;
				if(len > count){
					tex = tex.substring(0,count);
					document.survey.Biography.value =tex;
					return false;
				}
				document.survey.limit.value = count-len;
			}

			function showHide(obj){
			var tbody = obj.parentNode.parentNode.parentNode.getElementsByTagName('tbody')[0];
			var old = tbody.style.display;
			tbody.style.display = (old == 'none'?'':'none');
			}

			function hideall()
			{
			  locl = document.getElementsByTagName('tbody');
			  for (i=0;i<locl.length;i++)
			  {
				 locl[i].style.display='none';
			  }

			}

		</script></head>";
	print "<body bgcolor='#c3c3c3'><table width=475><tr><td><a href='xgrid.php?mode=participants'>List Panelists</a></td>";
	$query="select * from CPDB_Convention
			where ConID = '".$CFG['ConID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$BaseDate=strtotime($row['ConDate']);
	print "<td><h2>Show Grid For</h2><table border=0>";
	for ($i=1;$i<=$row['ConDays'];$i++){
		if ($i==1) {
			$starthour=$row['ConStartHour'];
			$endhour=$row['LastDailyHour'];
		} else {
			if ($i==$row['ConDays']){
				$starthour=$row['FirstDailyHour'];
				$endhour=$row['ConEndHour'];
			} else {
				$starthour=$row['FirstDailyHour'];
				$endhour=$row['LastDailyHour'];
			}
		}
		if ($endhour < $starthour) $endhour +=24;
		print "<tr><td colspan=2><br>".date("l M j",$BaseDate +(($i-1)*24*60*60))."</td></tr>";
		for ($h=$starthour;$h<$endhour;$h=$h+$CFG['Duration']){

			$blockstart=$BaseDate +(($i-1)*24*60*60) + $h*60*60;
			$blockend = $BaseDate +(($i-1)*24*60*60) + ($h + $CFG['Duration'])*60*60;
			print"<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td><td>";
			print "<a href='xgrid.php?mode=grid&start=".date("Y-m-d H:i:s",$blockstart)."&dur=".$CFG['Duration']."'>";
			print date("g:i A",$blockstart);
			print " to ";
			print date("g:i A",$blockend);
			print "</a></td></tr>";


		}

	}
	print "</table>
				</td></tr></table>";

}

function Show_Grid(){
	global $CFG;

	$grid=array();
	$PanelTitle=array();

	$StartTime = strtotime($_GET['start']);
	$StartDate = $StartTime;
	$EndTime = $StartTime + (60*60*$_GET['dur']);
	#Select rooms for current convention that are not hidden
	$query="select * from `CPDB_Room` where `ConID` = '".$CFG['ConID']."' and `RoomHideGrid` = 0";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	#Build Grid based on Hours selected and displayable rooms, padding with 0
	while ($row = mysql_fetch_assoc($sql)) {
		for ($i = $StartTime - 1800;$i < $EndTime + 1800; $i = $i + 1800){
			$grid[$i][$row['RoomID']]=0;
		}
	}

	$query = "select `Start`, `End`, PanelTitle, L.RoomID as RID, L.PanelID as PID from `CPDB_PTR` as L
			   inner join CPDB_Room as R
			   on L.RoomID = R.RoomID
			   inner join CPDB_Panels as P
			   on L.PanelID = P.PanelID
				where L.ConID = '".$CFG['ConID']."'
				and `RoomHideGrid` = 0
				and PanelHidePublic = 0
				and (`Start` between '".date("Y-m-d H:i:s",$StartTime)."' and '".date("Y-m-d H:i:s",$EndTime-1)."'
				  or `End` between '".date("Y-m-d H:i:s",$StartTime+1)."' and '".date("Y-m-d H:i:s",$EndTime)."')
				or (`Start` < '".date("Y-m-d H:i:s",$StartTime)."' and `End` > '".date("Y-m-d H:i:s",$EndTime)."')
				order by `Start`, R.RoomID";
#	print $query."<br>";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$PanelTitle[$row['PID']]=$row['PanelTitle'];
		if (strtotime($row['Start']) < $StartTime) $grid[$StartTime-1800][$row['RID']] = -1;
		if (strtotime($row['End']) > $EndTime) $grid[$EndTime+1800][$row['RID']] = -1;
		#<
		for ($i=strtotime($row['Start']);$i< strtotime($row['End']);$i+=1800) {
			if (($i>=$StartTime) && ($i <=$EndTime)){
				$grid[$i][$row['RID']]= $row['PID'];
#				print "Enumerating ".date("H:i:s",$i)." in Room ".$row['RID']." For Panel ".$row['PID']."<br>";
			} else {
#				print "Ignoring ".date("H:i:s",$i)." in Room ".$row['RID']." For Panel ".$row['PID']."<br>";
			}
		}

	}

#	var_dump ($grid);
	#Start displaying the Grid
	$x=($_GET['dur']*2)+3;
	print "<table border=1 width='475'><tr><th colspan='".$x."'>".date("l F j",$StartTime)."</th></tr>";
	print "<tr><th width='16%'>Room</th><td width='2%'></td>";
	for ($i = $StartTime; $i < $EndTime; $i = $i + 1800){
		print "<th width='10%'>".date("g:i A",$i)."</th>";
	}
	print "<td width='2%'></td></tr>";
	$query="select * from `CPDB_Room`
			where `ConID` = '".$CFG['ConID']."'
			and `RoomHideGrid` = 0
			order by `RoomOrder`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['RoomName']."</td>";
		if ($grid[$StartTime-1800][$row['RoomID']]==0){
			print "<td></td>";
		} else {
			print "<td>&lt;</td>";
		}
		for ($i = $StartTime; $i < $EndTime; $i = $i + 1800){
			if ($grid[$i][$row['RoomID']]==0) {
				print "<td></td>";
			} else {
				$ctr=0;
				for ($j=$i;$j<$EndTime;$j+=1800){
					if ($grid[$i][$row['RoomID']]==$grid[$j][$row['RoomID']]) {
						$ctr+=1;
					} else {
						break;
					}
				}
#				print "<td colspan=".$ctr.">".$grid[$i][$row['RoomID']]."</td>";
				print "<td colspan=".$ctr.">
				<a href='xgrid.php?mode=panel&id=".$grid[$i][$row['RoomID']]."'>
				".$PanelTitle[$grid[$i][$row['RoomID']]]."</a></td>";
				$i+=(1800*($ctr-1));
			}

		}
		if ($grid[$EndTime+1800][$row['RoomID']]==0){
			print "<td></td>";
		} else {
			print "<td>&gt;</td>";
		}
		print "</tr><tr>";
	}
	print "<td></td><td></td>";
	for ($i = $StartTime; $i < $EndTime; $i = $i + 1800){
		print "<th>".date("g:i A",$i)."</th>";
	}
	print "<td></td></tr></table>";
#	var_dump ($PanelTitle);



}


?>