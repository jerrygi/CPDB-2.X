<?php
require_once("../config.php"); # load configuration file
#if (!array_key_exists("pswd",$_POST) return;
#if (!array_key_exists("user",$_POST) return;
if (!(strtoupper($CFG['Access'])=='GRANT')) {
	print "<center><font color='red' size=6>Access Denied</font></center>";
} else {
	################################################################
	# change the next line to match the path to your password file #
	################################################################
	$_POST['PaTh'] = "/home/rustycon/.htpasswds/public_html/cpdb/admin/passwd";



	print"<head><title>Convention Scheduling for ".$CFG['ConName']."</title><LINK REL=StyleSheet HREF='base.css'></head>";
	;
	print "<div class='main_menu'>";
	print "<center><font size=5>Convention Programming Utilities for ".$CFG['ConName']."</font></center>";
	require_once("GlobalMenu.php"); # load Global Menu
	debug();
	if (!array_key_exists("Action",$_POST)) $_POST['Action']='';

	if ($_POST['Action'] == 'Change') {
		if ($_POST['pswd1'] == $_POST['pswd2']) {
			Password_Change();
		} else {
			print "<font color='red'>Passwords do not match, please try again,<font><br>";
		}
		$_POST['Action']='';
	}

	if ($_POST['Action'] == '') {
		Password_Change_Form();
	}
}

function Password_Change(){
	#$line = genLine($_POST['user'], $_POST['pswd1']);
	#print "<br>".$line."<br>";
	#writeFile($_POST['PaTh'],$line);
	exec("htpasswd -b ".$_POST['PaTh']." ".$_POST['user']." ".$_POST['pswd1'],$output);
	array_table($output);
}

function Password_Change_Form(){
	global $CFG;
	$query="Select * from `CPDB_CfgLvl` where `CfgLvlID` = '".$CFG['CfgLvl']."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	if ($row['CfgName']=='Admin') {
		$query="Select * from `CPDB_User`";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		$username = "<select name='user'>";
		while ($row = mysql_fetch_assoc($sql)){
			$username .= "<option value='".$row['UserName']."'>".$row['UserName']."</option>";
		}
		$username .= "</select>";

	} else {
		$username = "<input type='hidden' name='user' value='".$CFG['USERNAME']."'>".$CFG['USERNAME']."" ;
	}
	print "<form method='post'>
	<input type='hidden' name='Action' value='Change'>
	<table border=1><tr><td colspan=2>Password reset tool</td></tr>
	<tr><td>User Name</td><td>".$username."</td></tr>
	<tr><td>Password</td><td><input type='password' name='pswd1'></td></tr>
	<tr><td>Re Enter</td><td><input type='password' name='pswd2'></td></tr>
	<tr><td colspan=2><center><input type='submit' value='Change'></td></tr>
	</table></form>";
}


    // Encrypts given password
    function encryptPW($thePW){
        $thePW = crypt(trim($thePW),base64_encode(CRYPT_STD_DES));
         $thePW = crypt(trim($thePW));
         $thePW = md5($thePW);
        return $thePW;
    }

    // Calls the encryptPW function, generates the line for writing
    function genLine($username,$password){
        $encrypted_password = encryptPW($password);
        return "$username:$encrypted_password";
    }

    // Writes data to the file
    function writeFile($theFile,$theLine){
        $fp = fopen($theFile, "a");
        $orig_size = filesize($theFile);

        // Trims all whitespace
        $theContents = file_get_contents($theFile);
        $strippedContents = str_replace(" ", "", $theContents);

        ftruncate($fp, 0);

        fwrite($fp, $strippedContents);

        // Sets file pointer to the end of the file
        fseek($fp, filesize($theFile));

        // If this is the first entry in the file, do not add a new line before writing
        if($orig_size == 1){
            fwrite($fp, "$theLine");
        }else{
            // If this is not the first entry, write the data on a new line in the file
            fwrite($fp, "$theLine");
        }
        fclose($fp);
    }


function debug() {
	global $CFG;
	if ($CFG['debug']==1){

#			print_r (array_keys($_POST));
#			print"<br>\r\n";
#			print_r (array_values($_POST));
#			print"<br>\r\n";
		$message = "User ID = ".$_SERVER['PHP_AUTH_USER']."<br>Exploding '$ _POST'";
		array_table($_POST, "99ff99",1,$message);
		print "</font><br>";
	}
}


function array_table($myarray, $bgcolor="ff9999", $cols=3,$note="")
{
	print "<br><font color='red'><B>Begin Debug Info</B><br>";
	print $note."<br>";
	$ttlrows=count($myarray);
	$wrkrows=$ttlrows-($ttlrows%$cols);
	$rows=($wrkrows/$cols)+2;
	print "<table border=1 bgcolor='#".$bgcolor."'><tr>";

	$row=1;
	uksort($myarray, "cmp");
	foreach($myarray as $key => $value ) {
		#<
		if ($row==1) {
			print "<td valign='top'><table border=1><tr><th>KEY</th><th>VALUE</TH></tr>";
		}
		print "<TR><td>".$key."</td><td>";
		if (count($value)==1){
			print $value;
		} else {
			array_table($value);
		}
		print "</td></tr>";
		$row++;
		if ($row==$rows){
			$row=1;
			print "</table></td>";
		}
	}
	if (!($row==$rows)){
		print "</tr></table>";
	}
	print "</tr></table>";
	print "<B>End Debug Info</B></font>";
}
function cmp($a, $b)
{
    return strcasecmp($a, $b);
}

function Display_Query($query){
	global $CFG;
	if ($CFG['print_query']==1) {
		print "<br><font color='green'>".$query."</font><br><br>";
	}
}


?>