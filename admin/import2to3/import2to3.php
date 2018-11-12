<?php

include_once ('/home/rustycon/Private/system.php');
include ('/home/rustycon//public_html/cpdb/admin/import2to3/room_config.php');
# DB login are stored outside of public_html for security reasons

#####################
# connect to SQL DB #
#####################
global $Database;
$dbIdx='Panelist';
$conn = $Database [$dbIdx];
#print_r ($conn);

$db = mysql_connect( $conn['host'], $conn['user'], $conn['pass'], $conn['db']);
mysql_select_db($conn ['db'], $db);
#$dblink1 = @mysqli_connect( 'localhost', 'rustycon_conpro','prodb', 'rustycon_conpro');
#print "mysqli_connect( ".$conn['host'].", ".$conn['user'].", ".$conn['pass'].", ".$conn['db'].")";

$CFG['debug']=0;
debug();

purge();

room_block_1();

$mapping = array();
$mapping['ConName']='Convention Name';
$mapping['ConDate']='Convention Start Date';
$mapping['ConDays']='Convention run days';
$mapping['FirstDailyHour']='Grid Start Hour';
$mapping['LastDailyHour']='Grid End hour';
$mapping['ConStartHour']='First Day Grid Starts at';
$mapping['ConEndHour']='Last Day Grid ends at';
#$mapping['']='Active Category';
$mapping['ConID']='Old ID';
$v2Table = 'CPDB_Convention';
$v3Element = 'Convention';
element_import($v2Table,$v3Element,$mapping);

room_block_2();

$mapping = array();
$mapping['AppName']='AppName';
$mapping['Public']='Public';
$mapping['AppID']='Old ID';
$v2Table = 'CPDB_Apps';
$v3Element = 'Apps';
element_import($v2Table,$v3Element,$mapping);


$mapping = array();
$mapping['CfgName']='Access Level Name';
$mapping['CfgLvlID']='Old ID';
$v2Table = 'CPDB_CfgLvl';
$v3Element = 'ConfigLevel';
element_import($v2Table,$v3Element,$mapping);


$mapping = array();
$mapping['CFG_Variable']='CFG_Variable';
$mapping['CFG_Value']='CFG_Value';
$mapping['CfgLvlID']=array('method'=>1,'type'=>'ConfigLevel','cat'=>'Old ID', 'newcat'=>'Config LevelID'); #<
$mapping['CFG_APP']=array('method'=>1,'type'=>'Apps','cat'=>'Old ID', 'newcat'=>'Config AppID'); #<
$mapping['CFGID']='Old ID';
$v2Table = 'CPDB_Config';
$v3Element = 'Config';
element_import($v2Table,$v3Element,$mapping);

$mapping = array();
$mapping['Category']='Category Name';
$mapping['CatID']='Old ID';
$v2Table = 'CPDB_Category';
$v3Element = 'Category';
element_import($v2Table,$v3Element,$mapping);

$mapping = array();
$mapping['PanelistLastName']='Last name';
$mapping['PanelistFirstName']='First Name';
$mapping['PanelistPubName']='Publishing name';
$mapping['PanelistName']='Panelist Sorting Name';
$mapping['PanelistBadgeName']='Badge Name';
$mapping['PanelistAddress']='Address Line 1';
$mapping['PanelistCity']='City';
$mapping['PanelistState']='State/Province';
$mapping['PanelistZip']='Zip/Postal Code';
$mapping['PanelistPhoneDay']='Phone';
$mapping['PanelistPhoneEve']='Phone';
$mapping['PanelistPhoneCell']='Phone';
$mapping['PanelistEmail']='Email';
$mapping['']='Image';
$mapping['Biography']='Biography';
$mapping['PanelistID']='Old ID';
$mapping['Notes']='Notes';
$mapping['DNI']='DNI';
$v2Table = 'CPDB_Panelist';
$v3Element = 'Panelist';
$condition = 'Where `IsEquip` = 0';
element_import($v2Table,$v3Element,$mapping,$condition);

$mapping = array();
$mapping['PanelistName']='Equipment Name';
$mapping['PanelistID']='Old ID';
$v2Table = 'CPDB_Panelist';
$v3Element = 'Equipment';
$condition = 'Where `IsEquip` = 1';
element_import($v2Table,$v3Element,$mapping,$condition);

$mapping = array();
$mapping['CatID']=array('method'=>1,'type'=>'Category','cat'=>'Old ID', 'newcat'=>'Panel Category'); #<
$mapping['PanelTitle']='Panel Title';
$mapping['PanelDescription']='Panel Description';
$mapping['PanelNotes']='Panel Notes';
$mapping['PanelSuggestBy']='Suggested by';
$mapping['PanelCreated']='Created';
$mapping['ConID']=array('method'=>1,'type'=>'Convention','cat'=>'Old ID', 'newcat'=>'Convention'); #<
$mapping['PanelID']='Old ID';
$mapping['PanelHidePublic']='Visability Public';
$mapping['PanelHideSurvey']='Visability Survey';
$mapping['PanelHighlited']='Visability Highlite';
$mapping['PanelLocked']='State Locked';
$mapping['PanelSolo']='State Solo';
$mapping['PanelApproved']='State Approved';
$v2Table = 'CPDB_Panels';
$v3Element = 'Panel';
$condition = '';
element_import($v2Table,$v3Element,$mapping,$condition);

$mapping = array();
$mapping['PanelistID']=array('method'=>1,'type'=>'Panelist','cat'=>'Old ID', 'newcat'=>'Panelist ID'); #<
$mapping['ConID']=array('method'=>1,'type'=>'Convention','cat'=>'Old ID', 'newcat'=>'Convention ID'); #<
$mapping['SchedReqs']='Scheduling Requests';
$mapping['PhysReqs']='Physical Notes';
$mapping['listme']='List me in Publications';
$mapping['PcID']='Old ID';
$v2Table = 'CPDB_PanelistCon';
$v3Element = 'Panelist_Convention';
$condition = '';
element_import($v2Table,$v3Element,$mapping,$condition);

$mapping = array();
$mapping['XX']=array('method'=>2,  'type'=>'Panelist_Convention','newcat'=>'PanelistConveintionID'); #<
$mapping['Date']='Date';
$mapping['MaxPanels']='Max Panels';
$mapping['MPID']='Old ID';
$v2Table = 'CPDB_MaxPanels';
$v3Element = 'Panelist_Convention_Max';
$condition = '';
element_import($v2Table,$v3Element,$mapping,$condition);

$mapping = array();
$mapping['PanelistID']=array('method'=>1,'type'=>'Panelist','cat'=>'Old ID', 'newcat'=>'PanelistID'); #<
$mapping['PanelID']=array('method'=>1,'type'=>'Panel','cat'=>'Old ID', 'newcat'=>'PanelID'); #<
$mapping['Rank']='Ranking';
$mapping['Moderate']='Will Moderate';
$mapping['PanelRankID']='Old ID';
$v2Table = 'CPDB_PanelRanking';
$v3Element = 'Ranking';
$condition = '';
element_import($v2Table,$v3Element,$mapping,$condition);

$mapping = array();
$mapping['PanelistID']=array('method'=>1,'type'=>'Panelist','cat'=>'Old ID', 'newcat'=>'PanelistID'); #<
$mapping['PanelID']=array('method'=>1,'type'=>'Panel','cat'=>'Old ID', 'newcat'=>'PanelID'); #<
$mapping['Moderator']='Panelist Type';
$mapping['P2PID']='Old ID';
$v2Table = 'CPDB_P2P';
$v3Element = 'Panelist_Panel';
$condition = '';
element_import($v2Table,$v3Element,$mapping,$condition);

$mapping = array();
$mapping['RoomID']=array('method'=>1,'type'=>'Room_Con','cat'=>'Old ID', 'newcat'=>'Room_Con ID'); #<
$mapping['PanelID']=array('method'=>1,'type'=>'Panel','cat'=>'Old ID', 'newcat'=>'PanelID'); #<
$mapping['Start']='Panel Start';
$mapping['End']='Panel end';
$mapping['SchedNotes']='Scheduling notes';
$mapping['PTRID']='Old ID';
$v2Table = 'CPDB_PTR';
$v3Element = 'Panel_Room';
$condition = '';
element_import($v2Table,$v3Element,$mapping,$condition);

$mapping = array();
$mapping['UserName']='User Name';
$mapping['UserPass']='Password';
$mapping['UserID']='Old ID';
$v2Table = 'CPDB_User';
$v3Element = 'User';
$condition = '';
element_import($v2Table,$v3Element,$mapping,$condition);

$mapping = array();
$mapping['UserID']=array('method'=>1,'type'=>'User','cat'=>'Old ID', 'newcat'=>'UserID'); #<
$mapping['CfgLvl']='Config Level';
$mapping['ConID']=array('method'=>1,'type'=>'Convention','cat'=>'Old ID', 'newcat'=>'ConventionID'); #<
$v2Table = 'CPDB_User';
$v3Element = 'User_Con';
$condition = '';
element_import($v2Table,$v3Element,$mapping,$condition);

$mapping = array();
$mapping['InviteID']='Old ID';
$mapping['InviteGUID']='Invite GUID';
$mapping['ConID']=array('method'=>1,'type'=>'Convention','cat'=>'Old ID', 'newcat'=>'ConID'); #<
$mapping['PanelistID']=array('method'=>1,'type'=>'Panelist','cat'=>'Old ID', 'newcat'=>'PanelistID'); #<
$v2Table = 'CPDB_Invite';
$v3Element = 'Invite';
$condition = '';
element_import($v2Table,$v3Element,$mapping,$condition);

comp_types();

exit;




function element_generate($element, $mapping) {
	print "Creating Element ".$element."<br>";;
	$query="select `CatID`, `CatName`, `CatType`, `ElTyID` from `CPDB3_V_Categorization` where `ElementType` = '".$element."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$categorization[$row['CatName']] = array('CatID' => $row['CatID'], 'CatType' => $row['CatType']); #<
		$v3n=$row['ElTyID'];
	}
#	print_r ($categorization);
#	print "<br>";
	$ElementID = element_create($v3n);
	foreach ($mapping as $key => $value) { #<
		$newcat = $categorization[$key]['CatID'];
		if (is_array($value)) {
			$list = array();
			$query="insert into `CPDB3_Elements_".$categorization[$key]['CatType']."` (`ELID`,`EDDate`,`EDCatID`,`EDVal`) values ";
			foreach ($value as $subvalue) {
				array_push($list,"('".$ElementID."', now(), '".$newcat."', '".htmlentities($subvalue,ENT_QUOTES)."' )");
			}
			$query .= implode(", ",$list);
		} else {
			$query="insert into `CPDB3_Elements_".$categorization[$key]['CatType']."` (`ELID`,`EDDate`,`EDCatID`,`EDVal`)
					values ('".$ElementID."', now(), '".$newcat."', '".htmlentities($value,ENT_QUOTES)."' )";
		}
#		print $query."<br>";
		mysql_query($query) or die('Query failed: ' . mysql_error());
#		print "<hr>";
	}
#	print $ElementID." _ ".$mapping['Room Name']."<br>";
}


function element_import($v2,$v3s,$mapping,$condition=''){
	print "Importing Element[".$v3s."] from table[".$v2."]<br>";
	$query="select `CatID`, `CatName`, `CatType`, `ElTyID` from `CPDB3_V_Categorization` where `ElementType` = '".$v3s."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$categorization[$row['CatName']] = array('CatID' => $row['CatID'], 'CatType' => $row['CatType']); #<
		$v3n=$row['ElTyID'];
	}
	$query='Select * from `'.$v2.'` '.$condition;
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		$ElementID = element_create($v3n);
		foreach ($mapping as $key => $value){ #<
			if (is_array($value)) {
				if ($value['method']==1) {
					switch ($key) {
						case 'CatID':
							$oldval=$row['CatID'];
							break;
						case 'CFG_APP':
							$oldval=$row['CFG_APP'];
							break;
						case 'CfgLvlID':
							$oldval=$row['CfgLvlID'];
							break;
						case 'ConID':
							$oldval=$row['ConID'];
							break;
						case 'PanelID':
							$oldval=$row['PanelID'];
							break;
						case 'PanelistID':
							$oldval=$row['PanelistID'];
							break;
						case 'RoomID':
							$oldval=$row['RoomID'];
							break;
						default:
							$oldval=-1;
					}
					$query1="select * from CPDB3_V_Categorization where `ElementType` = '".$value['type']."' and `CatName` = '".$value['cat']."'";
#					print $query1."<br>";
					$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error() . "<br><b>".$query1."</b><br>");
					$row1 = mysql_fetch_assoc($sql1);
#					print_r ($row1);
#					print "<br>";
					$query2="select * from `CPDB3_Elements_".$row1['CatType']."` where `EDCatID` = '".$row1['CatID']."' and EDVal = '".$oldval."'";
#					print $query2."<br>";
					$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error() . "<br><b>".$query2."</b><br>");
					$row2 = mysql_fetch_assoc($sql2);
#					print_r ($row2);
#					print "<br>";
					$newcat = $categorization[$value['newcat']]['CatID'];
					$query3="insert into `CPDB3_Elements_".$categorization[$value['newcat']]['CatType']."` (`ELID`,`EDDate`,`EDCatID`,`EDVal`)
							values  ('".$ElementID."', now(), '".$newcat."', '".htmlentities($row2['ELID'],ENT_QUOTES)."' )";
#					print $query3."<hr>";
					$sql3=mysql_query($query3) or die('Insert failed: ' . mysql_error() . "<br><b>".$query3."</b><br>");

				} elseif ($value['method']==2){
					$query1="select A.ELID as ELID from `CPDB3_Elements_ID` as A
							inner join `CPDB3_Elements_Int` as B
							on A.EDVAL = B.ELID
							inner join `CPDB3_Elements_ID` as C
							on A.ELID = C.ELID
							inner join `CPDB3_Elements_Int` as D
							on C.EDVAL = D.ELID
							Where A.EdCATID=58 and B.EDVal = '".$row['ConID']."' and C.EdCATID=57 and D.EDVal = '".$row['PanelistID']."'";
#					print $query1."<br>";
					$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error() . "<br><b>".$query1."</b><br>");
					$row1 = mysql_fetch_assoc($sql1);
					$newcat = 65;
					$query2="insert into `CPDB3_Elements_ID` (`ELID`,`EDDate`,`EDCatID`,`EDVal`)
					values ('".$ElementID."', now(), '".$newcat."', '".$row1['ELID']."' )";
#					print $query2."<br>";
					$sql2=mysql_query($query2) or die('Insert failed: ' . mysql_error() . "<br><b>".$query2."</b><br>");

				} elseif ($value['method']==3){




				} else {
					print "<font color='red'>Unknown Method for ".$key."<br>";
					print_r ($value1);
					print "</font><br>";
				}
			} else {
				$value1 = $value;
				$val=$row[$key];
				$newcat = $categorization[$value1]['CatID'];
				if ($categorization[$value1]['CatType']=='Time') {
					if (is_null($val)) {
						$val='0000-00-00';
					}
					$query2="insert into `CPDB3_Elements_".$categorization[$value1]['CatType']."` (`ELID`,`EDDate`,`EDCatID`,`EDVal`)
					values ('".$ElementID."', now(), '".$newcat."', convert('".$val."',DATETIME) )";
#					print $query2."<hr>";
				} else {
					$query2="insert into `CPDB3_Elements_".$categorization[$value1]['CatType']."` (`ELID`,`EDDate`,`EDCatID`,`EDVal`)
					values ('".$ElementID."', now(), '".$newcat."', '".htmlentities($val,ENT_QUOTES)."' )";
				}
#				print $query2."<hr>";
				$sql2=mysql_query($query2) or die('Query failed: ' . mysql_error() . "<br><b>".$query2."</b><br>");
			}
		}
	}
}

function element_create ($element_type) {
	$query="insert into `CPDB3_Elements` (`ElTyID`,`Created`) values ('".$element_type."',now())";
	mysql_query($query);
	return mysql_insert_id();
}

function enumerate_element($elementid) {
	$query="select 0 as CatID, 'Element ID' as Type, convert (ElID , char) as Value from CPDB3_Elements where ELID='".$elementid."'
union
select 0 as CatID, 'Element Type ID' as Type, convert (ElTyID , char) as  Value from CPDB3_Elements where ELID='".$elementid."'
union
select 0 as CatID, 'Element Type' as Type, convert (T.ElementType , char) as  Value from CPDB3_Elements as E inner join CPDB3_ElementTypes as T on E.ElTyID = T.ElTyID where ELID='".$elementid."'
union
select 0 as CatID, 'Element Created' as Type, convert (Created , char) as Value from CPDB3_Elements where ELID='".$elementid."'
union
select EDCatID as CatID, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_ID as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'
union
select EDCatID as CatID, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_Int as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'
union
select EDCatID as CatID, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_Time as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'
union
select EDCatID as CatID, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_Strings as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'
union
select EDCatID as CatID, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_Text as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'
union
select EDCatID as CatID, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_Bin as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'";


}

function purge(){
	$query="TRUNCATE TABLE `CPDB3_Elements`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$query="TRUNCATE TABLE `CPDB3_Elements_ID` ";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$query="TRUNCATE TABLE `CPDB3_Elements_Int` ";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$query="TRUNCATE TABLE `CPDB3_Elements_Strings`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$query="TRUNCATE TABLE `CPDB3_Elements_Time`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$query="TRUNCATE TABLE `CPDB3_Elements_Bin`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$query="TRUNCATE TABLE `CPDB3_Elements_Text`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	print "<B>Tables Purged</B><hr>";
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

?>