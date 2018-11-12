#!/usr/local/bin/php
#<?php
################################################################
## Check incoming mail
## If mail is a bounce, Mark Mailpump record with Bounce Info
##      This routiene uses the Bounce_Driver.Class module from www.phpclasses.org
## V 1.0.0
## Created on 3/6/2009
## Created By Jerry Gieseke
## Created For ConProgDB V2.0.0.0
#################################################################
#require_once("../config.php");
#require_once("bounce_driver.class.php");
#
#$connection = mysql_pconnect("$dbhost","$dbusername","$dbpasswd")
#	or die ("Couldn't connect to server.");
#
#
#$CFG['debug']=0;
#
$fp = fopen('data.txt', 'a');
fwrite($fp, '\r\n');
fwrite($fp, date('Y-M-d G:i:s',time()));
fclose($fp);
print date('Y-M-d G:i:s',time());

#########################################
## Get the Email from stdin
## and place it in the $mail variable
#########################################
#	$mail = "";
#	$fp = fopen("php://stdin", "r");
#	while (!feof($fp))
#	{
#			$mail.= fgets($fp,4096);
#	}
#	fclose($fp);
#
###############################
## Pass the @Mail variable
## to the BounceHandler
## and getresults in the
## $multiArray variable
###############################
#	$multiArray = Bouncehandler::get_the_facts($mail);
#
#
#    foreach($multiArray as $the){
#        switch($the['action']){
#            case 'failed':
#            case 'autoreply':
#            	####################################
#				# do the same action for
#				# failed, transient and autoreply
#				# actions
#				####################################
#            case 'transient':
#                ####################################
#                # update latest Mailpump record
#                # for the recipient
#                ####################################
#                $query = "select max(`SendDate`),`MailID` from CPDB_MailPump
#                			group by '".$the['recipient']."'
#                			having `SendEmail` = '".$the['recipient']."'";
#				$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#				$row = mysql_fetch_assoc($sql);
#				$query="update CPDB_MailPump
#						Set `StateMsg = '".$the['action']."',
#						`StateCode` = '".$the['status']."',
#						`ReturnBody` = '".$mail."'
#						where `MailID` = '".$row['MailID']."'";
#				$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
#                break;
#            default:
#                #################################
#                # don't do anything             '
#                #################################
#                break;
#        }
#    }
#
?>