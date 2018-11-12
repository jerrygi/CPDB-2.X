<?php

include ("SMTPClientClass.php");


if ($_POST["SendMail"])
{

$SmtpServer=$_POST["SMTPServe"];
$SmtpPort=$_POST["SMTPPort"];
$SmtpUser=$_POST["SMTPUser"];
$SmtpPass=$_POST["SMTPPass"];
$from=$_POST["FROM"];
$to=$_POST["TO"];
$subject=$_POST["SUBJECT"];
$body=$_POST["BODY"];



$SMTPMail = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $subject, '' ,$body);

$SMTPChat = $SMTPMail->SendMail();

echo "<h1>Talking with the SMTP Server</h1>";
echo "<p>The server response:</p>";
echo $SMTPChat["hello"]."<br />";
echo $SMTPChat["res"]."<br />";
echo $SMTPChat["user"]."<br />";
echo $SMTPChat["pass"]."<br />";
echo $SMTPChat["From"]."<br />";
echo $SMTPChat["To"]."<br />";
echo $SMTPChat["data"]."<br />";
echo $SMTPChat["send"]."<br />";

}else{

?>


<form action="SMTPClient.php" method="post">

    <table>

        <tr>
        <td>SMTP Server:</td><td><input type="text" name="SMTPServe" value=""> Port: <input type="text" name="SMTPPort" value="" size="5"> [25 by default]</td>
        </tr>


        <tr>
        <td>SMTP User:</td><td><input type="text" name="SMTPUser" value=""></td>
        </tr>

        <tr>
        <td>SMTP Password :</td><td><input type="text" name="SMTPPass" value=""></td>
        </tr>


        <tr>
        <td>FROM:</td><td><input type="text" name="FROM" value=""></td>
        </tr>

        <tr>
        <td>TO:</td><td><input type="text" name="TO" value=""></td>
        </tr>

        <tr>
        <td>SUBJECT:</td><td><input type="text" name="SUBJECT" value=""></td>
        </tr>

        <tr>
        <td>BODY:</td><td><textarea name="BODY" rows="10" cols="50"></textarea></td>
        </tr>

        <tr>
        <td></td><td><input type="submit" name="SendMail" value="Send Mail" /></td>
        </tr>

</table>


</form>


<? } ?>