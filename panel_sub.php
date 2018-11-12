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
	  font-size : 16pt;
	  z-index : auto;
	}

	-->

	</style>
	</head>
	<body>
	<div class="mybody">
';

$myerrors['1']=array("color"=>'#ffffff',msg=>"",valu=>""); # category
$myerrors['2']=array("color"=>'#ffffff',msg=>"",valu=>""); # title
$myerrors['3']=array("color"=>'#ffffff',msg=>"",valu=>""); # description
$myerrors['4']=array("color"=>'#ffffff',msg=>"",valu=>""); # notes
$myerrors['5']=array("color"=>'#ffffff',msg=>"",valu=>""); # contact
$myerrors['6']=array("color"=>'#ffffff',msg=>"",valu=>""); # chaptca
#<
$category = array();
$query="select * from `CPDB_Category` where `Active` = 1 order by Category asc";
$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_assoc($sql)) {
	$category = $category + array($row['CatID']=>$row['Category']);
#<
}

if (array_key_exists('Submit',$_POST)){
	########################
	# Move submited values into array, to populate the form for resubmission if there are errors
	########################
	$myerrors['1']['valu'] = $_POST['category'];
	$myerrors['2']['valu'] = $_POST['title'];
	$myerrors['3']['valu'] = $_POST['description'];
	$myerrors['4']['valu'] = $_POST['notes'];
	$myerrors['5']['valu'] = $_POST['suggestedby'];
	$myerrors['6']['valu'] = $_POST['chapt'];

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
			$myerrors['6']['msg'] = 'You must enter a valid CAPTCHA';
		}
	}
	if (!(validEmail($myerrors['5']['valu']))) {
		$myerrors['5']['color'] = '#ff0000';
		$myerrors['5']['msg'] = 'You must eneter a valid email address';
		$errorcount=1;
	}

	if (strlen($myerrors['2']['valu'])<1) {
		$myerrors['2']['color'] = '#ff0000';
		$myerrors['2']['msg'] = 'You must eneter a Title';
		$errorcount=1;
	}

	if (strlen($myerrors['3']['valu'])<1) {
		$myerrors['3']['color'] = '#ff0000';
		$myerrors['3']['msg'] = 'You must eneter a Description';
		$errorcount=1;
	}

	if ($errorcount==1){
		$select = build_select($myerrors['1']['valu']);
	} else {
		###################333
		# Write it to the DB
		#####################
		$notes = $myerrors['4']['valu']."<br>=========================== <br>Suggested By ".$myerrors['5']['valu'];
		$query = "insert into `CPDB_Panels` (`ConID`,`CatID`,`PanelTitle`,`PanelDescription`,`PanelNotes`) values('".$CFG['ConID']."','".$myerrors['1']['valu']."','".$myerrors['2']['valu']."','".$myerrors['3']['valu']."','".$notes."')";
#		print $query;
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());


		$myerrors['1']=array("color"=>'#ffffff',msg=>"",valu=>""); # category
		$myerrors['2']=array("color"=>'#ffffff',msg=>"",valu=>""); # title
		$myerrors['3']=array("color"=>'#ffffff',msg=>"",valu=>""); # description
		$myerrors['4']=array("color"=>'#ffffff',msg=>"",valu=>""); # notes
		$myerrors['5']=array("color"=>'#ffffff',msg=>"",valu=>""); # contact
		$myerrors['6']=array("color"=>'#ffffff',msg=>"",valu=>""); # chaptca
		$select = build_select(-1);
		print "<h2><font color='green'>Successfully added</font></h2><br>";
	}

	display_form();
} else {
	$select = build_select(-1);
	display_form();
}

function display_form(){
global $myerrors;
global $select;
global $Recaptcha;
global $captcha;

print "<h2>Submit a panel suggestion</h2><br>
It is never too early to add to the pool of possible program <br>
topics.  A good topic will include complete information.<br>
Please fill out the form as completely as possible.  We don’t<br>
promise to use every topic suggested but  we will do our best <br>
to make sure we cover the topics of highest interest to all.<br>
	   <form method='post'><table>";
print "<tr><th>Panel Category</th><td>".$select."</td><td><font color='red'>".$myerrors['1']['msg']. "</font></td></tr>";
print "<tr><th>Panel Title</th><td> <input type='text' name='title' value='".$myerrors['2']['valu']."'></td><td><font color='red'>".$myerrors['2']['msg']."</font></td></tr>";
print "<tr><th>Panel Description</th><td><textarea name='description' height=5 width=30 value='".$myerrors['3']['valu']."'>".$myerrors['3']['valu']."</textarea></td><td><font color='red'>".$myerrors['3']['msg']."</font></td></tr>";
print "<tr><th>Panel Notes</th><td><textarea name='notes' height=5 width=30 value='".$myerrors['4']['valu']."'>".$myerrors['4']['valu']."</textarea></td><td><font color='red'>".$myerrors['4']['msg']."</font></td></tr>";
print "<tr><th>Suggested by<br>(your email address)</th><td><input type='text' name='suggestedby' value='".$myerrors['5']['valu']."'></td><td><font color='red'>".$myerrors['5']['msg']."</font></td></tr>";
if ($captcha == 1) {
	print "<tr><td colspan=2>";
	echo recaptcha_get_html($Recaptcha['Public']);
	print "</td><td><font color='red'>".$myerrors['6']['msg']."</font></td></tr>";
}
print"<tr><td colspan=3><center><input type='Submit' name='Submit' value='Submit'></center></td></tr>";
print "</table></form></div>";
}

function build_select($idx){
global $category;
$output="<select name='category'>";
foreach ($category as $row=>$value){
	#<
	if ($idx==$row) {
		$output .="<option selected value='".$row."'>".$value."</option>";
	} else {
		$output .="<option value='".$row."'>".$value."</option>";
	}
}
$output .= "</select>";
return $output;
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