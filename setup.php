<?php
	print "<h1> Convention Programming System Setup</h1><br>";
	$fullpath=$_SERVER['DOCUMENT_ROOT'];
	$secpath = substr($fullpath,0,strrpos($fullpath,'/'))."/Private";
//	$secpath = $fullpath;
	$apppath= $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
	$webpath = "http://".substr($apppath,0,strrpos($apppath,'/'));
if (!(array_key_exists('SetupStep', $_POST)))
	{
	$_POST['SetupStep']=1;
	}
if ($_POST['SetupStep']==1)
	{
	#print_r($_SERVER);
	print $webpath;
	print"
	<form method='post'>
	<B>Database Host</b><br>
	<input type='text' name='DBHost' value='localhost'><br><br>
	<B>Database Name</b><br>
	<input type='text' name='DBName'><br><br>
	<B>DB User</B><br>
	<input type='text' name='DBUser'><br><br>
	<B>DB Password</b><br>
	<input type='text' name='DBPass'><br><br>
	<input type='submit' name='submit'>
	<input type='hidden' name='SetupStep' value='2'>
	</form>";
	}
if ($_POST['SetupStep']==2)
	{
	$FileName = "CPDB_Sec.php";
	$FileContent="<?php
	$"."Database = array (
		'Panelist'=>array ('host'=>'".$_POST['DBHost']."', 'user'=>'".$_POST['DBUser']."', 'pass'=>'".$_POST['DBPass']."', 'db'=>'".$_POST['DBName']."')
	);
	?>";
	$handle = fopen($FileName,"a");
	fwrite($handle,$FileContent);
	fclose($handle);
	print "Please move <b>$FileName</b> from install directory to <B>$secpath</B> setting permisions on the file to <b>0644</b>, and press continue when done.<br>
	make sure that <B>$FileName</B> is removed from the install directory as this can pose a security issue.<br>
	<br><br>If you can not place this file above the public_html directory, change line 8 in config.php and lines 3-5 of setup.php to reflect the actual location of this file.<br><br>

	<form method='post'>
	<input type='hidden' name='SetupStep' value='3'>
	<input type='submit' name='Continue' value = 'continue'>
	</form>";
	}
if ($_POST['SetupStep'] >= 3)
	{
	#<
	include_once ($secpath."/CPDB_Sec.php");
	global $Database;
	$dbIdx='Panelist';
	$conn = $Database [$dbIdx];
	print_r($conn);
	$db = mysql_connect($conn ['host'], $conn ['user'], $conn ['pass']);
	mysql_select_db($conn ['db'], $db);
	print "Connected to the DB<br>";

	}
if ($_POST['SetupStep'] == 3)
	{
	#DB Creation Begins Here
	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Apps` (
	  `AppID` int(11) NOT NULL auto_increment,
	  `AppName` varchar(20) NOT NULL,
	 `Public` int(11) NOT NULL DEFAULT `0` COMMENT `1 if public facing App`,
	  PRIMARY KEY  (`AppID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Apps";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Availability` (
	  `AvailID` int(11) NOT NULL auto_increment,
	  `PanelistID` int(11) NOT NULL,
	  `ConID` int(11) NOT NULL,
	  `AvailHour` datetime NOT NULL COMMENT 'The Panelist is Available for the DT lised here',
	  PRIMARY KEY  (`AvailID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Availability";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Category` (
	  `CatID` int(11) NOT NULL auto_increment,
	  `Category` varchar(30) NOT NULL,
	  `Active` tinyint(1) NOT NULL default '1',
	  PRIMARY KEY  (`CatID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Category";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_CfgLvl` (
	  `CfgLvlID` int(11) NOT NULL auto_increment,
	  `CfgName` varchar(30) NOT NULL,
	  PRIMARY KEY  (`CfgLvlID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_CfgLvl";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_CompLevel` (
	  `CompID` int(11) NOT NULL auto_increment,
	  `CompLevel` varchar(30) NOT NULL,
	  PRIMARY KEY  (`CompID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_ACompLevel";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Config` (
	  `CFGID` int(11) NOT NULL auto_increment,
	  `CfgLvlID` int(11) NOT NULL,
	  `CFG_APP` int(11) NOT NULL,
	  `CFG_Variable` varchar(30) NOT NULL,
	  `CFG_Value` varchar(128) NOT NULL,
	  PRIMARY KEY  (`CFGID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Config";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Convention` (
	  `ConID` int(11) NOT NULL auto_increment,
	  `ConName` varchar(30) NOT NULL,
	  `ConDate` date NOT NULL,
	  `ConDays` int(11) NOT NULL,
	  `ConStartHour` int(11) NOT NULL,
	  `ConEndHour` int(11) NOT NULL,
	  `FirstDailyHour` int(11) NOT NULL,
	  `LastDailyHour` int(11) NOT NULL,
	  `ConSurveyCFG` int(11) NOT NULL default '0',
	  PRIMARY KEY  (`ConID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Convention";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Guest` (
	  `GuestID` int(10) unsigned NOT NULL auto_increment,
	  `PanelistID` int(11) NOT NULL,
	  `OldPanelistID` int(10) default NULL,
	  `ConID` int(10) default NULL,
	  `GOPanelistName` varchar(255) default NULL,
	  `GOPanelistBadgeName` varchar(255) default NULL,
	  `GOPanelistAddress` varchar(255) default NULL,
	  `GOPanelistCity` varchar(255) default NULL,
	  `GOPanelistState` varchar(255) default NULL,
	  `GOPanelistZip` varchar(255) default NULL,
	  `GOPanelistPhone` varchar(255) default NULL,
	  `GOPanelistEmail` varchar(255) default NULL,
	  `GOPanelistGuest` int(11) default NULL,
	  PRIMARY KEY  (`GuestID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Guest";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Image` (
	  `image_id` int(10) unsigned NOT NULL auto_increment,
	  `OldImageID` int(11) NOT NULL,
	  `PanelistID` int(10) default NULL,
	  `image_type` varchar(50) NOT NULL default '',
	  `image` longblob NOT NULL,
	  `image_size` bigint(20) NOT NULL default '0',
	  `image_name` varchar(255) NOT NULL default '',
	  `image_date` datetime NOT NULL default '0000-00-00 00:00:00',
	  `ConID` int(10) default NULL,
	  UNIQUE KEY `image_id` (`image_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Image";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Invite` (
	  `InviteID` int(11) NOT NULL auto_increment,
	  `InviteGUID` varchar(100) default NULL,
	  `InviteState` varchar(100) default NULL,
	  `InviteDate` datetime NOT NULL default '0000-00-00 00:00:00',
	  `PanelistID` int(11) NOT NULL default '0',
	  `ConID` int(11) default NULL,
	  `GuestState` int(11) NOT NULL default '0',
	  `InviteClass` int(11) NOT NULL default '0',
	  PRIMARY KEY  (`InviteID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ; ";
	print "<br>Creating SQL Table CPDB_Invite";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Invite_Status` (
	  `InviteID` int(11) NOT NULL,
	  `PageID` int(11) NOT NULL,
	  `SurveyState` int(11) NOT NULL,
	  `TimeStamp` datetime NOT NULL,
	  UNIQUE KEY `StateEntry` (`InviteID`,`PageID`,`SurveyState`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
	print "<br>Creating SQL Table CPDB_Invite_Status";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_MaxPanels` (
	  `MPID` int(11) NOT NULL auto_increment,
	  `ConID` int(11) NOT NULL,
	  `PanelistID` int(11) NOT NULL,
	  `Date` date NOT NULL,
	  `MaxPanels` int(11) NOT NULL,
	  PRIMARY KEY  (`MPID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_MaxPanels";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_P2P` (
	  `P2PID` int(11) NOT NULL auto_increment,
	  `PanelistID` int(11) NOT NULL default '0',
	  `PanelID` int(11) NOT NULL default '0',
	  `Moderator` tinyint(1) NOT NULL default '0',
	  `ConID` int(11) default NULL,
	  PRIMARY KEY  (`P2PID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_P2P";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_PanelEdits` (
	  `EditID` int(11) NOT NULL auto_increment,
	  `EditTime` datetime NOT NULL,
	  `EditBy` varchar(30) NOT NULL,
	  `PanelID` int(11) NOT NULL,
	  `PanelTitle` varchar(100) NOT NULL,
	  `PanelDescription` longtext NOT NULL,
	  `PanelNotes` text NOT NULL,
	  `CatID` int(11) NOT NULL,
	  PRIMARY KEY  (`EditID`),
	  UNIQUE KEY `EditInstance` (`EditTime`,`EditBy`,`PanelID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_PanelEdits";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Panelist` (
	  `PanelistID` int(10) unsigned NOT NULL auto_increment,
	  `OldPanelistID` int(10) default NULL COMMENT 'Can Remove',
	  `PanelistName` varchar(255) default NULL,
	  `PanelistLastName` varchar(255) default NULL,
	  `PanelistFirstName` varchar(255) default NULL,
	  `PanelistPubName` varchar(255) default NULL,
	  `PanelistBadgeName` varchar(255) default NULL,
	  `PanelistAddress` varchar(255) default NULL,
	  `PanelistCity` varchar(255) default NULL,
	  `PanelistState` varchar(255) default NULL,
	  `PanelistZip` varchar(255) default NULL,
	  `PanelistPhoneDay` varchar(255) default NULL,
	  `PanelistPhoneEve` varchar(255) default NULL,
	  `PanelistPhoneCell` varchar(255) default NULL,
	  `PanelistEmail` varchar(255) default NULL,
	  `GuestID` int(10) default NULL COMMENT 'Can Remove',
	  `GroupName` varchar(255) default NULL,
	  `GroupEvent` varchar(255) default NULL,
	  `listme` varchar(255) default NULL,
	  `sharephone` varchar(255) default NULL,
	  `shareemail` varchar(255) default NULL,
	  `sharemail` varchar(255) default NULL,
	  `ImageID` varchar(255) default NULL COMMENT 'Can Remove',
	  `Biography` longtext,
	  `SchedReqs` longtext,
	  `PhysReqs` longtext,
	  `IsEquip` tinyint(1) NOT NULL default '0',
	  `SubmittedDTS` datetime default '0000-00-00 00:00:00',
	  `ConID` int(10) default NULL COMMENT 'Can Remove',
	  `DNI` int(11) NOT NULL default '0' COMMENT 'Do not Invite if 1',
	  PRIMARY KEY  (`PanelistID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Panelist";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_PanelistCon` (
	  `PcID` int(11) NOT NULL auto_increment,
	  `PanelistID` int(11) NOT NULL,
	  `ConID` int(11) NOT NULL,
	  `SchedReqs` longtext,
	  `PhysReqs` longtext,
	  `listme` int(11) NOT NULL default '0',
	  `sharephone` int(11) NOT NULL default '0',
	  `shareemail` int(11) NOT NULL default '0',
	  `sharemail` int(11) NOT NULL default '0',
	  `comped` int(11) NOT NULL default '1' COMMENT '0 not compd, 1 comped',
	  PRIMARY KEY  (`PcID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_PanelistCon";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_PanelRanking` (
	  `PanelRankID` int(11) NOT NULL auto_increment,
	  `PanelistID` int(11) NOT NULL default '0',
	  `PanelID` int(11) NOT NULL default '0',
	  `Rank` int(11) NOT NULL default '0',
	  `Moderate` tinyint(4) NOT NULL,
	  `ConID` int(11) default NULL,
	  PRIMARY KEY  (`PanelRankID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_PanelRanking";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Panels` (
	  `PanelID` int(11) NOT NULL auto_increment,
	  `ConID` int(10) default NULL,
	  `CatID` int(11) NOT NULL,
	  `OldPanelID` int(10) default NULL,
	  `PanelCategory` varchar(20) NOT NULL default '',
	  `PanelTitle` varchar(100) NOT NULL default '',
	  `PanelDescription` longtext NOT NULL,
	  `PanelNotes` text,
	  `PanelHidePublic` tinyint(1) NOT NULL default '0',
	  `PanelHideSurvey` tinyint(1) NOT NULL default '0',
	  `PanelLocked` tinyint(1) NOT NULL default '0',
	  `PanelSolo` tinyint(1) NOT NULL default '0',
	  `PanelApproved` tinyint(1) NOT NULL default '1',
	  `PanelTech` tinyint(1) NOT NULL default '0',
	  `PanelSuggestBy` int(11) NOT NULL default '0',
	  `PanelCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY  (`PanelID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Panels";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_PTR` (
	  `PTRID` int(11) NOT NULL auto_increment,
	  `PanelID` int(11) NOT NULL default '0',
	  `RoomID` int(11) NOT NULL default '0',
	  `SetID` int(11) NOT NULL,
	  `Start` datetime NOT NULL default '0000-00-00 00:00:00',
	  `End` datetime NOT NULL default '0000-00-00 00:00:00',
	  `SchedNotes` text,
	  `ConID` int(11) default NULL,
	  PRIMARY KEY  (`PTRID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_PTR";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Room` (
	  `RoomID` int(11) NOT NULL auto_increment,
	  `OldRoomID` int(11) default NULL,
	  `ConID` int(11) default NULL,
	  `RoomName` varchar(50) default NULL,
	  `RoomSqr` int(11) default NULL,
	  `RoomChild1ID` int(11) NOT NULL default '0',
	  `RoomChild2ID` int(11) NOT NULL default '0',
	  `RoomChild3ID` int(11) NOT NULL default '0',
	  `RoomChild4ID` int(11) NOT NULL default '0',
	  `RoomChild5ID` int(11) NOT NULL default '0',
	  `RoomChild6ID` int(11) NOT NULL default '0',
	  `RoomChild7ID` int(11) NOT NULL default '0',
	  `RoomChild8ID` int(11) NOT NULL default '0',
	  `RoomChild9ID` int(11) NOT NULL default '0',
	  `RoomChild10ID` int(11) NOT NULL default '0',
	  `RoomNotes` longtext,
	  `RoomOrder` int(11) NOT NULL default '1',
	  `RoomHideGrid` int(11) NOT NULL default '0',
	  `RoomZone` int(11) NOT NULL default '1',
	  PRIMARY KEY  (`RoomID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Room";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_RoomSets` (
	  `SetID` int(11) NOT NULL auto_increment,
	  `SetName` varchar(30) NOT NULL,
	  `SetDescription` longtext NOT NULL,
	  `ConID` int(11) NOT NULL,
	  PRIMARY KEY  (`SetID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_RoomSets";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_User` (
	  `UserID` int(11) NOT NULL auto_increment,
	  `CfgLvl` int(11) NOT NULL,
	  `UserName` varchar(24) NOT NULL,
	  `UserPass` varchar(64) NOT NULL,
	  `Active` tinyint(1) NOT NULL default '1',
	  `ConID` int(11) NOT NULL,
	  PRIMARY KEY  (`UserID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_User";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_UserCat` (
	  `UCID` int(11) NOT NULL auto_increment,
	  `UserID` int(11) NOT NULL,
	  `CatID` int(11) NOT NULL,
	  PRIMARY KEY  (`UCID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_UserCat";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE TABLE IF NOT EXISTS `CPDB_Zone` (
	  `ZoneID` int(11) NOT NULL auto_increment,
	  `ZoneName` varchar(30) NOT NULL,
	  `ConID` int(11) NOT NULL,
	  PRIMARY KEY  (`ZoneID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	print "<br>Creating SQL Table CPDB_Zone";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE VIEW `CPDB_V_ActiveCouponCode` AS 
		select 	`I`.`InviteID` AS `InviteID`,
			`I`.`ConID` AS `ConID`,
			`I`.`InviteClass` AS `InviteClass`,
			`I`.`GuestState` AS `GuestState`,
			concat(_utf8'G',old_password(concat_ws(_utf8',',`I`.`InviteID`,`I`.`ConID`,`I`.`InviteGUID`))) AS `Code` 
			from (`CPDB_Invite` `I` 
			join `CPDB_Panelist` `P` 
			on((`I`.`PanelistID` = `P`.`PanelistID`))) 
			order by concat(_utf8'G',old_password(concat_ws(_utf8',',`I`.`InviteID`,`I`.`ConID`,`I`.`InviteGUID`)));";
	print "<br>Creating SQL View CPDB_V_ActiveCouponCode";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE VIEW `CPDB_V_Config` AS select `C`.`CFGID` AS `CFGID`,`L`.`CfgName` AS `CfgName`,`A`.`AppName` AS `AppName`,`C`.`CFG_Variable` AS `CFG_Variable`,`C`.`CFG_Value` AS `CFG_Value` from ((`CPDB_Config` `C` join `CPDB_Apps` `A` on((`C`.`CFG_APP` = `A`.`AppID`))) join `CPDB_CfgLvl` `L` on((`C`.`CfgLvlID` = `L`.`CfgLvlID`)));";
	print "<br>Creating SQL View CPDB_V_Config";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE VIEW `CPDB_V_CouponCode` AS select `P`.`PanelistName` AS `PanelistName`,`I`.`ConID` AS `ConID`,concat(_utf8'G',old_password(concat_ws(_utf8',',`I`.`InviteID`,`I`.`ConID`,`I`.`InviteGUID`))) AS `Code` from (`CPDB_Invite` `I` join `CPDB_Panelist` `P` on((`I`.`PanelistID` = `P`.`PanelistID`))) order by `P`.`PanelistName`;";
	print "<br>Creating SQL View CPDB_V_CouponCode";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE VIEW `CPDB_V_Panelist2Con` AS select `CPDB_Availability`.`PanelistID` AS `PanelistID`,`CPDB_Availability`.`ConID` AS `ConID` from `CPDB_Availability` union select `CPDB_MaxPanels`.`PanelistID` AS `PanelistID`,`CPDB_MaxPanels`.`ConID` AS `ConID` from `CPDB_MaxPanels` union select `CPDB_PanelistCon`.`PanelistID` AS `PanelistID`,`CPDB_PanelistCon`.`ConID` AS `ConID` from `CPDB_PanelistCon`;";
	print "<br>Creating SQL View CPDB_V_Panelist2Con";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE VIEW `CPDB_V_PanelistPanelRoomTime` AS select `F`.`PanelCategory` AS `PanelCategory`,`F`.`PanelTitle` AS `PanelTitle`,`F`.`PanelDescription` AS `PanelDescription`,`L`.`Start` AS `Start`,`L`.`End` AS `End`,`R`.`RoomName` AS `RoomName`,`G`.`PanelistPubName` AS `PanelistPubName`,`F`.`ConID` AS `ConID`,`X`.`PanelistID` AS `PanelistID`,`F`.`PanelID` AS `PanelID` from ((((`CPDB_P2P` `X` join `CPDB_Panels` `F` on((`F`.`PanelID` = `X`.`PanelID`))) join `CPDB_Panelist` `G` on((`G`.`PanelistID` = `X`.`PanelistID`))) join `CPDB_PTR` `L` on((`L`.`PanelID` = `F`.`PanelID`))) join `CPDB_Room` `R` on((`R`.`RoomID` = `L`.`RoomID`))) order by `L`.`Start`;";
	print "<br>Creating SQL View CPDB_V_PanelistPAnelRoomTime";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	CREATE VIEW `CPDB_V_UserCategories` AS select `U`.`UserName` AS `UserName`,`X`.`UserID` AS `UserID`,`C`.`Category` AS `Category`,`X`.`CatID` AS `CatID` from ((`CPDB_UserCat` `X` join `CPDB_User` `U` on((`U`.`UserID` = `X`.`UserID`))) join `CPDB_Category` `C` on((`C`.`CatID` = `X`.`CatID`))) where ((`C`.`Active` = 1) and (`U`.`Active` = 1));";
	print "<br>Creating SQL View CPDB_V_UserCategories";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="INSERT INTO `CPDB_Apps` (`AppID`, `AppName`, `Public`) VALUES
	(1, 'GLOBAL', '0'),
	(2, 'reports.php', '0'),
	(5, 'publications.php', '0'),
	(4, 'admin.php', '0'),
	(6, 'cpdb.php', '0'),
	(7, 'invite.php', '0'),
	(8, 'portfolio.php', '0'),
	(9, 'survey.php', '1'),
	(10, 'config.php', '0'),
	(11, 'facilities.php', '0'),
	(12, 'mypassmgr.php', '0'),
	(13, 'matrix.php', '0'),
	(14, 'changepass.php', '0');";
	print "Inserting Applications into CPDB_Apps";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	INSERT INTO `CPDB_CfgLvl` (`CfgLvlID`, `CfgName`) VALUES
	(1, 'GLOBAL'),
	(2, 'Admin'),
	(3, 'history'),
	(4, 'Programming Exec'),
	(5, 'Track Lead'),
	(6, 'Facilities'),
	(7, 'Publications');";
	print "Inserting Configuration Levels into CPDB_CfgLvl";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$opt_list = "<option value=0>12 am</option>
				 <option value=1>1 am</option>
				 <option value=2>2 am</option>
				 <option value=3>3 am</option>
				 <option value=4>4 am</option>
				 <option value=5>5 am</option>
				 <option value=6>6 am</option>
				 <option value=7>7 am</option>
				 <option value=8>8 am</option>
				 <option value=9>9 am</option>
				 <option value=10>10 am</option>
				 <option value=11>11 am</option>
				 <option value=12>12 pm</option>
				 <option value=13>1 pm</option>
				 <option value=14>2 pm</option>
				 <option value=15>3 pm</option>
				 <option value=16>4 pm</option>
				 <option value=17>5 pm</option>
				 <option value=18>6 pm</option>
				 <option value=19>7 pm</option>
				 <option value=20>8 pm</option>
				 <option value=21>9 pm</option>
				 <option value=22>10 pm</option>
				 <option value=23>11 pm</option>
	";
	print "<form method='post'>
	Please enter the following information about your convention
	<br><br><b>Convention name</b><br>
	<input type='Text' name='ConName'><br>
	<br><b>Con Start Date</b><br>
	<input type='text' name='ConDate' value='yyyy-mm-dd'><br>
	<br><b>Number of Days the convnetion runs</b><br>
	<br><select name='ConDays'> <option value=1>1</option>
								<option value=2>2</option>
								<option value=3>3</option>
								<option value=4>4</option>
								<option value=5>5</option>
								<option value=6>6</option>
								<option value=7>7</option>
								</select><br>
	<br><b>What is the first hour that paneling will occure onthe first day of the Convention</b><br>
	<select name='ConStartHour'>$opt_list</select><br>
	<br><b>What is the hour that paneling will start on all other days</b><br>
	<select name='FirstDailyHour'>$opt_list</select><br>
	<br><b>What is the last hour paneling will occure on the last day of the convention</b><br>
	<select name='ConEndHour'>$opt_list</select><br>
	<br><b>What is the last hour that paneling will start on all other days</b></br>
	<select name='LastDailyHour'>$opt_list</select><br>
	<br><Br><br><b>Please enter a password for the Administrator account</b><br>
	<input type='text' name='AdminPwd'><br>
	<input type='hidden' name='SetupStep' value='4'>
	<input type='submit' name='Continue' value = 'continue'>
	</form>";

	}
if ($_POST['SetupStep'] == 4)
	{
	$query="
	INSERT INTO `CPDB_User` (`UserID`, `CfgLvl`, `UserName`, `UserPass`, `Active`, `ConID`) VALUES
		(1, 2, 'admin', md5('".$_POST['AdminPwd']."'), 1, 1);";
	print "Creating Administrators Account<br>";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	INSERT INTO `CPDB_Convention` (`ConID`, `ConName`, `ConDate`, `ConDays`, `ConStartHour`, `ConEndHour`, `FirstDailyHour`, `LastDailyHour`, `ConSurveyCFG`) VALUES
	(1, '".$_POST['ConName']."', '".$_POST['ConDate']."', ".$_POST['ConDays'].", ".$_POST['ConStartHour'].", ".$_POST['ConEndHour'].", ".$_POST['FirstDailyHour'].", ".$_POST['LastDailyHour'].", 0);";
	print "Creating First Convention Record<br>";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	$query="
	INSERT INTO `CPDB_Config` (`CFGID`, `CfgLvlID`, `CFG_APP`, `CFG_Variable`, `CFG_Value`) VALUES
		(101, 2, 7, 'debug', '0'),
		(2, 1, 1, 'ProgrammingAddress', '##'),
		(3, 1, 1, 'ProgrammingAddress', '##'),
		(4, 1, 1, 'RegistrationAddress', '##'),
		(5, 1, 1, 'RegistrationAddress', '##'),
		(6, 1, 1, 'srcEmail', '##'),
		(7, 1, 1, 'replyToEmail', '##'),
		(8, 1, 1, 'helpEmail', '##'),
		(9, 1, 1, 'helpEmail', '##'),
		(102, 2, 15, 'Access', 'GRANT'),
		(96, 1, 1, 'BCC_Address', '##'),
		(12, 1, 1, 'conrundays', '3'),
		(13, 1, 1, 'TimeFormat', 'g:i a'),
		(14, 1, 1, 'debug', '1'),
		(15, 1, 1, 'dispID', '1'),
		(16, 1, 1, 'dispRanks', '1'),
		(17, 1, 1, 'PanelistListSort', '`PanelistName`'),
		(18, 1, 1, 'PanelistListFilter', ''),
		(19, 1, 1, 'PanelListSort', '`PanelCategory`,`PanelTitle`'),
		(20, 1, 1, 'PanelListFilter', ''),
		(21, 1, 1, 'InviteMode', '1'),
		(22, 1, 2, 'debug', '0'),
		(26, 1, 1, 'dispRankAgg', '0'),
		(30, 1, 1, 'TrackMgr', 'All'),
		(23, 2, 2, 'debug', '0'),
		(27, 2, 1, 'dispRankAgg', '1'),
		(24, 3, 1, 'dbPrefix', 'Deprecated'),
		(25, 3, 1, 'ConName', '##'),
		(28, 4, 1, 'dispRankAgg', '1'),
		(29, 4, 1, 'dispRanks', '0'),
		(32, 1, 1, 'Access', 'Deny'),
		(33, 1, 4, 'Access', 'Deny'),
		(34, 2, 4, 'Access', 'Grant'),
		(35, 2, 6, 'Access', 'Grant'),
		(36, 1, 1, 'ViewContact', 'Deny'),
		(37, 2, 1, 'ViewContact', 'GRANT'),
		(38, 4, 1, 'ViewContact', 'GRANT'),
		(39, 2, 1, 'Access', 'GRANT'),
		(41, 1, 7, 'Letter', '##_Invite_1.txt'),
		(42, 1, 7, 'Access', 'Deny'),
		(43, 4, 7, 'Access', 'G'),
		(44, 2, 1, 'debug', '0'),
		(45, 1, 1, 'webpath', '$webpath'),
		(47, 1, 1, 'Con', '##'),
		(48, 2, 6, 'debug', '0'),
		(49, 1, 1, 'ViewGuest', 'Deny'),
		(53, 1, 1, 'EditPanelist', 'Deny'),
		(51, 2, 1, 'ViewGuest', 'GRANT'),
		(82, 1, 5, 'edit color base', '00ff00'),
		(54, 2, 1, 'EditPanelist', 'GRANT'),
		(73, 7, 5, 'Access', 'GRANT'),
		(71, 7, 1, 'Access', 'Deny'),
		(74, 1, 5, 'edit time 1', '1'),
		(69, 1, 9, 'MaintenanceCode', 'xyzzy'),
		(67, 2, 6, 'print_query', '0'),
		(68, 1, 9, 'maintenance', '0'),
		(66, 1, 9, 'debug', '0'),
		(76, 1, 5, 'edit time 3', '3'),
		(77, 1, 5, 'edit color 1', 'ff0000'),
		(78, 1, 5, 'edit color 2', 'ff8000'),
		(79, 1, 5, 'edit color 3', 'ffff00'),
		(80, 1, 5, 'Access', 'Deny'),
		(81, 1, 5, 'edit time 2', '2'),
		(83, 1, 5, 'edit color highlight', '00ffff'),
		(84, 5, 6, 'Access', 'Grant'),
		(85, 1, 12, 'Access', 'Deny'),
		(86, 2, 12, 'Access', 'grant'),
		(87, 5, 12, 'Access', 'Grant'),
		(88, 1, 12, 'debug', '1'),
		(89, 2, 12, 'debug', '1'),
		(90, 1, 13, 'ConID', '1'),
		(91, 1, 13, 'ConName', '##'),
		(92, 2, 6, 'BulkMail', '1'),
		(93, 1, 6, 'BulkMail', '0'),
		(94, 1, 6, 'Itinerarie_Letter', '##Itinerary_letter.txt'),
		(99, 1, 14, 'debug', '1'),
		(98, 1, 6, 'debug', '1'),
		(103, 2, 16, 'Access', 'GRANT'),
		(104, 2, 16, 'ConID', '1'),
		(105, 1, 9, 'ConID', '1'),
		(106, 1, 1, 'BiographySize', '1000'),
		(107, 1, 1, 'SurveyStage', '1');
		";
	print "Initial Configurations have been completed<br>";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());

	print "Please go to <a href='$webpath/admin/admin.php' target='new'>the Admin Tool</a><br>
	Select System Admin, and Configurations, and update the following values<br>
	Global : Global : ProgrammingAddress<br>
	Global : Global : RegistrationAddress<br>
	Global : Global : srcEmail<br>
	Global : Global : replyToEmail<br>
	Global : Global : helpEmail<br>
	Global : Global : BCC_Address<br>
	Global : Global : Con<br>
	Global : matrix.php : ConName<br>
	Global : cpdb.php : Itinerarie_Letter<br>
	Global : invite.php : Letter<br>
	history : GLOBAL : ConName<br>
	";

	}
if ($_POST['SetupStep'] == 5)
	{
	}
?>