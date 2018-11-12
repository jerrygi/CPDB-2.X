<?php
##############################################################
# Configuration values for Convention Panelist Survey System #
##############################################################
####################
# Data Base Values #
####################
include_once ('/home/rustycon/Private/system.php');
# DB login are stored outside of public_html for security reasons

#####################
# connect to SQL DB #
#####################
global $Database;
$dbIdx='Panelist';
$conn = $Database [$dbIdx];
#$db = mysql_connect($conn ['host'], $conn ['user'], $conn ['pass']);
#mysql_select_db($conn ['db'], $db);

$dblink1 = mysqli_connect( $conn['host'], $conn['user'],$conn['pass'] , $conn['db']);

###################################
# Handle Cookie Creation          #
# or Distruction when appropriate #
###################################
#$NoCookieApps = array('img.php','survey.php','matrix.php','tn_img.php','tentcard.php');
$CFG['ApplicationName']= substr($_SERVER['SCRIPT_NAME'] ,strrpos($_SERVER['SCRIPT_NAME'],"/")+1);

$query="select * from `CPDB_Apps` where `AppName` = '".$CFG['ApplicationName']."'";
#$sql=mysql_query($query) or die('Query failed: ' . mysqli_error());
$sql = mysqli_query( $dblink1, $query );
if (mysqli_num_rows($sql) == 0){
	$row['Public'] = 0;
} else {
	$row = mysqli_fetch_assoc($sql);
}

if ($row['Public']==0){
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

global $MailHost;



#####################################################
# Trim & Escape all inputs to prevent SQL injection #
#####################################################

if ($CFG['ApplicationName']=='survey.php'){
	$_GET = array_map('trim', $_GET);
	$_POST = array_map('trim', $_POST);
	$_COOKIE = array_map('trim', $_COOKIE);
	$_REQUEST = array_map('trim', $_REQUEST);
	if(get_magic_quotes_gpc()):
		$_GET = array_map('stripslashes', $_GET);
		$_POST = array_map('stripslashes', $_POST);
		$_COOKIE = array_map('stripslashes', $_COOKIE);
		$_REQUEST = array_map('stripslashes', $_REQUEST);
	endif;
	$_GET = array_map('mysqli_real_escape_string', $_GET);
	$_POST = array_map('mysqli_real_escape_string', $_POST);
	$_COOKIE = array_map('mysqli_real_escape_string', $_COOKIE);
	$_REQUEST = array_map('mysqli_real_escape_string', $_REQUEST);
}

##############################
# Convention Specific Values #
##############################



$ProgrammingAddress = 'Jerrygi@verizon.net,bobbiedu@verizon.net';
					# email address to send reports to reguarding filled out surveys

$RegistrationAddress = 'Jerrygi@verizon.net,bobbiedu@verizon.net';
					# email address to send reports to reguarding memberships

$srcEmail = "rustycon26programming@rustycon.com";
					# email address that all mails will originate from

$replyToEmail = "bobbiedu@verizon.net";
					#Replies to sent emails will go here

$helpEmail = "jerrygi@verizon.net,bobbiedu@verizon.net";
					# email address users will mailto for questions or help

$webpath = "http://www.rustycon.com/cpdb/";
					# Location of the web pages

$PanelistListSort = "`PanelistName`";
#$PanelistListSort = "`PanelistBadgeName`";
$PanelistListFilter = "";

$PanelListSort = '`PanelCategory`,`PanelTitle`';
$PanelListFilter = '';

$invitemode = 1;	# 0 is open, 1 is invite only
$panelsurvey = 0;	# 0 is open, 1 is invite only

############################
# Display Formating Values #
############################

$TimeFormat = "g:i a";	#12 hour format no seconds
#$TimeFormat = "G:i";	#24 hour format no seconds
#$TimeFormat = "g:i:s a";	#12 hour format with seconds
#$TimeFormat = "G:i:s";		#24 hour format with seconds


####################
# Debugging Values #
####################

$Debug=2;	# display debugging information
$debugreportsphp=1;

$dispID=1;	#display ID values

$dispRanks=1;	# display Panelist Selected Rankings on Pane list

#####################################
# Move all variables into CFG Array #
#####################################
$CFG['dbhost']=$dbhost;
$CFG['dbusername']=$dbusername;
$CFG['dbpasswd']=$dbpasswd;
$CFG['database_name']=$database_name;
$CFG['dbPrefix']=$dbPrefix;

$CFG['ConName']=$ConName;
$CFG['ProgrammingAddress']=$ProgrammingAddress;
$CFG['RegistrationAddress']=$RegistrationAddress;
$CFG['srcEmail']=$srcEmail;
$CFG['replyToEmail']=$replyToEmail;
$CFG['helpEmail']=$helpEmail;
$CFG['webpath']=$webpath;
$CFG['constartdate']=$constartdate;
$CFG['conrundays']=$conrundays;
$CFG['TimeFormat']=$TimeFormat;
$CFG['debug']=$debug;
$CFG['dispID']=$dispID;
$CFG['dispRanks']=$dispRanks;
$CFG['PanelistListSort']=$PanelistListSort;
$CFG['PanelistListFilter'] = $PanelistListFilter;
$CFG['PanelListSort']=$PanelListSort;
$CFG['PanelListFilter'] = $PanelListFilter;

$CFG['InviteMode']=$invitemode;
$CFG['PanelSurvey']=$panelsurvey;

$CFG['debug.reports.php'] = $debugreportsphp;

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
	$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
	$row = mysqli_fetch_assoc($sql);
	$CFG['CfgLvl']=$row['CfgLvl'];
	$CFG['UserActive']=$row['Active'];
	$CFG['ConID']= $row['ConID'];
	$CFG['UserID']=$row['UserID'];

	$query="Select * from CPDB_CfgLvl where `CfgLvlID` = '".$CFG['CfgLvl']."'";
	$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
	$row = mysqli_fetch_assoc($sql);
	$CFG['USERLEVEL'] = $row['CfgName'];

	$query="select * from CPDB_CfgLvl where CfgName='GLOBAL'";
	$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
	$row = mysqli_fetch_assoc($sql);
	$CGLOBAL = $row['CfgLvlID'];

	$query="select * from CPDB_Apps where `AppName` = '".$CFG['ApplicationName']."'";
	$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
	$row = mysqli_fetch_assoc($sql);
	$CFG['APPID'] = $row['AppID'];

	$query="select * from CPDB_Apps where `AppName` = 'GLOBAL'";
	$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
	$row = mysqli_fetch_assoc($sql);
	$AGLOBAL = $row['AppID'];

	$query="select * from `CPDB_Config` where CfgLvlID = '".$CGLOBAL."' and CFG_APP = '".$AGLOBAL."'";
	$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
	while ($row = mysqli_fetch_assoc($sql)) {
		if (array_key_exists($row['CFG_Variable'], $CFG1)) {
			$CFG1[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
		} else {
			$CFG1[$row['CFG_Variable']] = $row['CFG_Value'];
		}
	}

	$query="select * from `CPDB_Config` where CfgLvlID = '".$CGLOBAL."' and CFG_APP = '".$CFG['APPID']."'";
	$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
	while ($row = mysqli_fetch_assoc($sql)) {
		if (array_key_exists($row['CFG_Variable'], $CFG2)) {
			$CFG2[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
		} else {
			$CFG2[$row['CFG_Variable']] = $row['CFG_Value'];
		}
	}

	$query="select * from `CPDB_Config` where CfgLvlID = '".$CFG['CfgLvl']."' and CFG_APP = '".$AGLOBAL."'";
	$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
	while ($row = mysqli_fetch_assoc($sql)) {
		if (array_key_exists($row['CFG_Variable'], $CFG3)) {
			$CFG3[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
		} else {
			$CFG3[$row['CFG_Variable']] = $row['CFG_Value'];
		}
	}

	$query="select * from `CPDB_Config` where CfgLvlID = '".$CFG['CfgLvl']."' and CFG_APP = '".$CFG['APPID']."'";
	$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
	while ($row = mysqli_fetch_assoc($sql)) {
		if (array_key_exists($row['CFG_Variable'], $CFG4)) {
			$CFG4[$row['CFG_Variable']] .= ", ".$row['CFG_Value'];
		} else {
			$CFG4[$row['CFG_Variable']] = $row['CFG_Value'];
		}
	}

	$CatList = array();
	$query="select `CatID` from `CPDB_UserCat` where `UserID` = '".$CFG['UserID']."' order by `CatID`";
	$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
	while ($row = mysqli_fetch_assoc($sql)) {
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
	$CFG['Debug']=2;
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
		$sql=mysqli_query($dblink1,$query) or die('Query failed: ' . mysqli_error());
		if (!(mysqli_num_rows($sql)==0)) {
			$row = mysqli_fetch_assoc($sql);
			foreach($row as $key => $value) {
				#<
				$CFG[$key]=$row[$key];
			}
		}
#	}
	$CFG['constartdate']  = $CFG['ConDate'];
	$CFG['conrundays']  = $CFG['ConDays'];

	#exit();
	# Invite Survey Pages
	# 1 means page is active
	# 0 means that page is inactive and not shown
	$PAGE['1']=1;	#Intro
	$PAGE['2']=1;	#Contact info
	$PAGE['3']=1;	#Image
	$PAGE['4']=1;	#Availability
	$PAGE['5']=1;	#Guest
	$PAGE['6']=0;	#Guest reg URL and cupon code Code
	$PAGE['7']=0;	#Pannel Suggestions
	$PAGE['8']=0;	#Paneling Notes
	$PAGE['9']=1;	#Pannel Selection
	$PAGE['10']=1;	#Thank you
	$PAGE['11']=0;
	$PAGE['12']=0;


?>
