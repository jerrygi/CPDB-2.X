<?php
##############################################################
# Configuration values for Convention Panelist Survey System #
##############################################################

###################################
# Handle Cookie Creation          #
# or Distruction when appropriate #
###################################
$CFG['ApplicationName']= substr($_SERVER['SCRIPT_NAME'] ,strrpos($_SERVER['SCRIPT_NAME'],"/")+1);
$NoCookieApps = array('img.php','survey.php','matrix.php','tn_img.php','tentcard.php');

if (!(in_array($CFG['ApplicationName'], $NoCookieApps))){
	if ($_POST['Action'] == 'Login'){
		setcookie("user",$_POST['User'],time()+3600,"/cpdb/admin");
		setcookie("hash",md5($_POST['Hash']),time()+3600,"/cpdb/admin");
		$_COOKIE['user'] = $_POST['User'];
		$_COOKIE['hash'] = md5($_POST['Hash']);
		$CFG['USERNAME']=$_COOKIE['user'];
		$CFG['hash']=$_COOKIE['hash'];
	} else if ($_POST['Action'] == 'Logout'){
		setcookie("user","",time()-3600,"/cpdb/admin");
		setcookie("hash","",time()-3600,"/cpdb/admin");
		$CFG['USERNAME'] = "";
		$CFG['hash'] = "";
		#exit(0);
	} else {
		################################
		# set/reset cookies for 1 hour #
		################################
		$CFG['USERNAME']=$_COOKIE['user'];
		$CFG['hash']=$_COOKIE['hash'];
		setcookie("user",$CFG['USERNAME'],time()+3600,"/cpdb/admin");
		setcookie("hash",$CFG['hash'],time()+3600,"/cpdb/admin");
	}
}

####################
# Data Base Values #
####################
include_once ('/home/rustycon/Private/CPDB_Sec.php');
# DB login are stored outside of public_html for security reasons

global $MailHost;

#####################
# connect to SQL DB #
#####################
global $Database;
$dbIdx='Panelist';
$conn = $Database [$dbIdx];
$db = mysql_connect($conn ['host'], $conn ['user'], $conn ['pass']);
mysql_select_db($conn ['db'], $db);

#############################################
# Load / override CFG from DB				#
# overides applied in the following order	#
# Global		Global						#
# Global		App							#
# UserLevel		Global						#
# UserLevel		App							#
# ConYear Overides							#
#############################################

#$CFG['USERNAME'] = $_SERVER['PHP_AUTH_USER'];
$CFG1['init']='';
$CFG2['init']='';
$CFG3['init']='';
$CFG4['init']='';
	$query="select * from CPDB_User where UserName = '".$CFG['USERNAME']."' ";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$CFG['CfgLvl']=$row['CfgLvl'];
	$CFG['UserActive']=$row['Active'];
	$CFG['ConID']= $row['ConID'];
	$CFG['UserID']=$row['UserID'];

	$query="Select * from CPDB_CfgLvl where `CfgLvlID` = '".$CFG['CfgLvl']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$CFG['USERLEVEL'] = $row['CfgName'];

	$query="select * from CPDB_CfgLvl where CfgName='GLOBAL'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$CGLOBAL = $row['CfgLvlID'];

	$query="select * from CPDB_Apps where `AppName` = '".$CFG['ApplicationName']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$CFG['APPID'] = $row['AppID'];

	$query="select * from CPDB_Apps where `AppName` = 'GLOBAL'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	$AGLOBAL = $row['AppID'];

	$query="select * from `CPDB_Config` where CfgLvlID = '".$CGLOBAL."' and CFG_APP = '".$AGLOBAL."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		if (array_key_exists($row['CFG_Variable'], $CFG1)) {
			$CFG1[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
		} else {
			$CFG1[$row['CFG_Variable']] = $row['CFG_Value'];
		}
	}

	$query="select * from `CPDB_Config` where CfgLvlID = '".$CGLOBAL."' and CFG_APP = '".$CFG['APPID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		if (array_key_exists($row['CFG_Variable'], $CFG2)) {
			$CFG2[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
		} else {
			$CFG2[$row['CFG_Variable']] = $row['CFG_Value'];
		}
	}

	$query="select * from `CPDB_Config` where CfgLvlID = '".$CFG['CfgLvl']."' and CFG_APP = '".$AGLOBAL."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		if (array_key_exists($row['CFG_Variable'], $CFG3)) {
			$CFG3[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
		} else {
			$CFG3[$row['CFG_Variable']] = $row['CFG_Value'];
		}
	}

	$query="select * from `CPDB_Config` where CfgLvlID = '".$CFG['CfgLvl']."' and CFG_APP = '".$CFG['APPID']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		if (array_key_exists($row['CFG_Variable'], $CFG4)) {
			$CFG4[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
		} else {
			$CFG4[$row['CFG_Variable']] = $row['CFG_Value'];
		}
	}

	$CatList = array();
	$query="select `CatID` from `CPDB_UserCat` where `UserID` = '".$CFG['UserID']."' order by `CatID`";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_assoc($sql)) {
		array_push($CatList,$row['CatID']);
	}
	$CFG['CategoryList'] = implode(',',$CatList);

	foreach ($CFG1 as $key => $value) {
		#<
		unset($CFG[$key]);
		$CFG[$key]=$value;
	}

	foreach ($CFG2 as $key => $value) {
		#<
		unset($CFG[$key]);
		$CFG[$key]=$value;
	}

	foreach ($CFG3 as $key => $value) {
		#<
		unset($CFG[$key]);
		$CFG[$key]=$value;
	}

	foreach ($CFG4 as $key => $value) {
		#<
		unset($CFG[$key]);
		$CFG[$key]=$value;
	}
	if ($CFG['Debug']==2) {
		print"###1###<br>";
		print"<br>\r\n";
		print_r (array_keys($CFG1));
		print"<br>\r\n";
		print_r (array_values($CFG1));

		print"###2###<br>";
		print"<br>\r\n";
		print_r (array_keys($CFG2));
		print"<br>\r\n";
		print_r (array_values($CFG2));

		print"###3###<br>";
		print"<br>\r\n";
		print_r (array_keys($CFG3));
		print"<br>\r\n";
		print_r (array_values($CFG3));

		print"###4###<br>";
		print"<br>\r\n";
		print_r (array_keys($CFG4));
		print"<br>\r\n";
		print_r (array_values($CFG4));

		print"###FINAL###<br>";
		print"<br>\r\n";
		print_r (array_keys($CFG));
		print"<br>\r\n";
		print_r (array_values($CFG));

	}
#	if (!($CFG['USERNAME']=='')) {
		$query="select * from CPDB_Convention where `ConID` = '".$CFG['ConID']."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		if (!(mysql_num_rows($sql)==0)) {
			$row = mysql_fetch_assoc($sql);
			foreach($row as $key => $value) {
				#<
				$CFG[$key]=$row[$key];
			}
		}
#	}
	$CFG['constartdate']  = $CFG['ConDate'];
	$CFG['conrundays']  = $CFG['ConDays'];

	#exit();
	if ($CFG['SurveyStage']==1){
		#Survey round 1
		# Invite Survey Pages
		# 1 means page is active
		# 0 means that page is inactive and not shown
		$PAGE['1']=1;	#Intro
		$PAGE['2']=1;	#Contact info
		$PAGE['3']=1;	#Image
		$PAGE['4']=0;	#Availability
		$PAGE['5']=0;	#Guest
		$PAGE['6']=0;	#Guest reg URL and cupon code Code
		$PAGE['7']=1;	#Pannel Suggestions
		$PAGE['8']=0;	#Paneling Notes
		$PAGE['9']=0;	#Pannel Selection
		$PAGE['10']=1;	#Thank you
		$PAGE['11']=0;
		$PAGE['12']=0;
	}
	if ($CFG['SurveyStage']==2){
		#Survey Round 2
		# Invite Survey Pages
		# 1 means page is active
		# 0 means that page is inactive and not shown
		$PAGE['1']=1;	#Intro
		$PAGE['2']=1;	#Contact info
		$PAGE['3']=1;	#Image
		$PAGE['4']=1;	#Availability
		$PAGE['5']=0;	#Guest
		$PAGE['6']=1;	#Guest reg URL and cupon code Code
		$PAGE['7']=0;	#Pannel Suggestions
		$PAGE['8']=0;	#Paneling Notes
		$PAGE['9']=1;	#Pannel Selection
		$PAGE['10']=1;	#Thank you
		$PAGE['11']=0;
		$PAGE['12']=0;
	}




?>
