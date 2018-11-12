<?php
session_start();
require_once("config.php");
require_once('recaptchalib.php');
global $Recaptcha;
$debug=0;
$captcha=0;

print '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<head>
	<style type="text/css">
	<!--
	.mybody {
	  font-family : arial;
	  font-size : 12pt;
	  z-index : auto;
	}

	-->

	</style>
	</head>
	<body>
	<div class="mybody">
';

$myerrors['1']=array("color"=>'#ffffff',msg=>"",valu=>"myself"); # memtype
$myerrors['2']=array("color"=>'#ffffff',msg=>"",valu=>"yes"); # member
$myerrors['3']=array("color"=>'#ffffff',msg=>"",valu=>""); # submitemail
$myerrors['4']=array("color"=>'#ffffff',msg=>"",valu=>""); # PanelistName
$myerrors['5']=array("color"=>'#ffffff',msg=>"",valu=>""); # PanelistEmail
$myerrors['6']=array("color"=>'#ffffff',msg=>"",valu=>""); # Topics
$myerrors['7']=array("color"=>'#ffffff',msg=>"",valu=>""); # Comments
$myerrors['8']=array("color"=>'#ffffff',msg=>"",valu=>""); # Webpage
$myerrors['9']=array("color"=>'#ffffff',msg=>"",valu=>""); # chapt
#<

if (array_key_exists('Submit',$_POST)){
	########################
	# Move submited values into array, to populate the form for resubmission if there are errors
	########################
	$myerrors['1']['valu'] = $_POST['memtype'];
	$myerrors['2']['valu'] = $_POST['member'];
	$myerrors['3']['valu'] = $_POST['submitemail'];
	$myerrors['4']['valu'] = $_POST['PanelistName'];
	$myerrors['5']['valu'] = $_POST['PanelistEmail'];
	$myerrors['6']['valu'] = $_POST['Topics'];
	$myerrors['7']['valu'] = $_POST['Comments'];
	$myerrors['8']['valu'] = $_POST['Webpage'];
	$myerrors['9']['valu'] = $_POST['chapt'];

	########################
	# Time to Validate
	########################
	$errocount=0;
	if ($captcha == 1) {
		$resp = recaptcha_check_answer ($Recaptcha['Private'],
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);
		if (!$resp->is_valid) {
			$errorcount=1;
			$myerrors['9']['msg'] = 'You must enter a valid CAPTCHA';
		}
	}
	#########################
	# Validate Conditional Fields
	#########################
	if ($myerrors['1']['valu']==''){
		$myerrors['1']['color'] = '#ff0000';
		$myerrors['1']['msg'] = 'You must specify if you are submiting yourself or someone else';
		$errorcount=1;
	} else if ($myerrors['1']['value']=='myself'){
		if ($myerrors['2']['valu']==''){
			$myerrors['2']['color'] = '#ff0000';
			$myerrors['2']['msg'] = 'You must specify your current membership status';
			$errorcount=1;
		}
	} else if ($myerrors['1']['value']=='other'){
		if (!(validEmail($myerrors['3']['valu']))) {
			$myerrors['3']['color'] = '#ff0000';
			$myerrors['3']['msg'] = 'You must eneter a valid email address for yourself';
			$errorcount=1;
		}
	}


	#########################
	# Validate joint fields
	#########################

	if (strlen($myerrors['4']['valu'])<1) {
		$myerrors['4']['color'] = '#ff0000';
		$myerrors['4']['msg'] = 'You must eneter a name for the Potential Panelist';
		$errorcount=1;
	}

	if (!(validEmail($myerrors['5']['valu']))) {
		$myerrors['5']['color'] = '#ff0000';
		$myerrors['5']['msg'] = 'You must eneter a valid email address for the potential panelist';
		$errorcount=1;
	}

	if (strlen($myerrors['6']['valu'])<1) {
		$myerrors['6']['color'] = '#ff0000';
		$myerrors['6']['msg'] = 'You must eneter a Topics that the potential panelist has expertise with';
		$errorcount=1;
	}

	if (strlen($myerrors['7']['valu'])<1) {
		$myerrors['7']['color'] = '#ff0000';
		$myerrors['7']['msg'] = 'You must eneter a comments about the potential panelist';
		$errorcount=1;
	}
	if (strlen($myerrors['8']['valu'])<1) {
		$myerrors['8']['color'] = '#ff0000';
		$myerrors['8']['msg'] = 'You must eneter the website fo the potential panelist';
		$errorcount=1;
	}
	if ($errorcount==1){
		#$select = build_select($myerrors['1']['valu']);
	} else {

		###################333
		# Write it to the DB
		#####################
		if ($myerrors['1']['valu']=="myself") {
			$notes = "Self Submitted from Web Site<br>";
			if ($myerrors['2']['valu']=="yes") {
				$notes .= "I have my membership<br>";
			} else {
				$notes .= "I do not have my membership yet<br>";
			}
		} else {
			$notes = "I am submitting someone else via the web site.<br>
					My Email is ".$myerrors['3']['valu']."<br>";
		}
		$notes .= "<br>=========================== <br>
					I can speak on the following Topics <br>".$myerrors['6']['valu'].
				 "<br>=========================== <br>
				 Some Brief Comments <br>".$myerrors['7']['valu'].
				 "<br>=========================== <br>
				 My Web Page is<br>".$myerrors['8']['valu'].
				 "<br>=========================== <br>
				 Suggested on ". date('M-d-Y');
		$query = "insert into `CPDB_Panelist` (`PanelistName`,`PanelistEmail`,`Biography`,`SubmittedDTS`) values ('".$myerrors['4']['valu']."','".$myerrors['5']['valu']."','".$notes."',now())";
#		print $query;
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());


		$myerrors['1']=array("color"=>'#ffffff',msg=>"",valu=>"myself"); # memtype
		$myerrors['2']=array("color"=>'#ffffff',msg=>"",valu=>"yes"); # member
		$myerrors['3']=array("color"=>'#ffffff',msg=>"",valu=>""); # submitemail
		$myerrors['4']=array("color"=>'#ffffff',msg=>"",valu=>""); # PanelistName
		$myerrors['5']=array("color"=>'#ffffff',msg=>"",valu=>""); # PanelistEmail
		$myerrors['6']=array("color"=>'#ffffff',msg=>"",valu=>""); # Topics
		$myerrors['7']=array("color"=>'#ffffff',msg=>"",valu=>""); # Comments
		$myerrors['8']=array("color"=>'#ffffff',msg=>"",valu=>""); # Webpage
		$myerrors['9']=array("color"=>'#ffffff',msg=>"",valu=>""); # chapt
		print "<h2><font color='green'>Successfully added</font></h2><br>";
	}

	display_form();
} else {
	display_form();
}

function display_form(){
global $myerrors;
global $select;
global $Recaptcha;
global $debug;
global $captcha;
print '<script>
		function check_radio()
		{
			for(var i=0;i<document.forms[0].elements.length;i++)
			{
				var e = document.forms[0].elements[i];
				var row1 = document.getElementById("x1");
				var row2 = document.getElementById("x2");
				var row3 = document.getElementById("x3");
				var row4 = document.getElementById("x4");
				var row5 = document.getElementById("x5");
				var row6 = document.getElementById("x6");
				if(e.name=="memtype" && e.checked)
				{
					if(e.value=="myself")
					{
						//document.forms[0].member[0].disabled=true;
						//document.forms[0].member[1].disabled=true;
						//row1.style.display = "none";
						row2.style.display = "";
						row3.style.display = "";
						row4.style.display = "";
						row5.style.display = "none";
						//row6.style.display = "none";
					} else
					if(e.value=="other"){
						//document.forms[0].member[0].disabled=false;
						//document.forms[0].member[1].disabled=false;
						//row1.style.display = "";
						row2.style.display = "none";
						row3.style.display = "none";
						row4.style.display = "none";
						row5.style.display = "";
						//row6.style.display = "";
					}
					break;
				}
			}
		}
		</script>';

print "<body onload='check_radio()'>";


if ($debug==1) {
	print "1". $myerrors['1']['valu']   ."<br>";
	print "2". $myerrors['2']['valu']   ."<br>";
	print "3". $myerrors['3']['valu']   ."<br>";
	print "4". $myerrors['4']['valu']   ."<br>";
	print "5". $myerrors['5']['valu']   ."<br>";
	print "6". $myerrors['6']['valu']   ."<br>";
	print "7". $myerrors['7']['valu']   ."<br>";
	print "8". $myerrors['8']['valu']   ."<br>";
	print "9". $myerrors['9']['valu']   ."<br>";
}


print "<h2>Submit a panelist suggestion</h2><br>";
print "We will be sending out invitations to panelists beginning in the fall of 2011.<br>";
print "If you have not heard from us by the beginning of 2012 please drop a line to<br>";
print "programming @ Chicon.org. However, if you would like to let us know you are<br>";
print "interested in participating in the programming, complete this short Program<br>";
print "Volunteer Form and we will consider you.  We cannot guarantee to include you,<br>";
print "but we are always happy to hear from potential Programming Participants.<br>";

print "<form method='post'><table width='45%'>";
$myself = $other = $memby = $membn = '';
if ($myerrors['1']['valu'] == 'myself') {$myself = 'checked';}
if ($myerrors['1']['valu'] == 'other') {$other = 'checked';}
if ($myerrors['2']['valu'] == 'yes') {$memby = 'checked';}
if ($myerrors['2']['valu'] == 'no') {$membn = 'checked';}

print "<tr><th width='75%'>I am submitting my name</th><td>     <input type='radio' name='memtype' value='myself'  onclick='check_radio()' ".$myself."></td></tr>";
print "<tr><th>I am submitting someone else</th><td><input type='radio' name='memtype' value='other'   onclick='check_radio()' ".$other."><br><font color='red'>".$myerrors['1']['msg']. "</font></td></tr>";
print "<tr id='x1'><td colspan=2><hr></td></tr>";
print "<tr id='x2'><th>I am an Attending member of Chicon.</th><td><input type='radio' name='member' value='yes' ".$memby."></td></tr>";
print "<tr id='x3'><th>I am not yet an attending member, <br>I understand I need to buy a membership to attend</th><td><input type='radio' name='member' value='no'  ".$membn."><br><font color='red'>".$myerrors['2']['msg']. "</font></td></tr>";
print "<tr id='x4'><td colspan=2>Check our online member search if you are not sure of your status</td></tr>";
print "<tr id='x5'><th>Your (submitters) email address</th><td><input type='text' name='submitemail' value='".$myerrors['3']['valu']."'><br><font color='red'>".$myerrors['3']['msg']. "</font></td</tr>";
#print "<tr id='x6'><td colspan=2><hr></td></tr>";
print "<tr><th>Full Name of the potential panelist</th><td><input type='text' name='PanelistName'  value='".$myerrors['4']['valu']."'><br><font color='red'>".$myerrors['4']['msg']. "</font></td></tr>";
print "<tr><th>Email Address of the potential panelist</th><td> <input type='text' name='PanelistEmail' value='".$myerrors['5']['valu']."'><br><font color='red'>".$myerrors['5']['msg']."</font></td></tr>";
print "<tr><th>Topics: <br>(list examples of the topics the potential panelist has expertise with)</th><td><textarea name='Topics' height=5 width=30 value='".$myerrors['6']['valu']."'>".$myerrors['6']['valu']."</textarea><br><font color='red'>".$myerrors['6']['msg']."</font></td></tr>";
print "<tr><th>Brief Comments</th><td><textarea name='Comments' height=5 width=30 value='".$myerrors['7']['valu']."'>".$myerrors['7']['valu']."</textarea><br><font color='red'>".$myerrors['7']['msg']."</font></td></tr>";
print "<tr><th>Potential panelist web page</th><td><input type='text' name='Webpage' value='".$myerrors['8']['valu']."'><br><font color='red'>".$myerrors['8']['msg']."</font></td></tr>";
if ($captcha == 1) {
	print "<tr><td colspan=2>";
	echo recaptcha_get_html($Recaptcha['Public']);
	print "</td><td><font color='red'>".$myerrors['9']['msg']."</font></td></tr>";
}
print"<tr><td colspan=3><center><input type='Submit' name='Submit' value='Submit'></center></td></tr>
	   ";
print "</table></form><div>";
}


function validEmail($email)
{
	#checks email formating against RFC 1035, 2234, 2821, 2822, 3696
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}

?>