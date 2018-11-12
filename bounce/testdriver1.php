<h1>bounce_driver.class.php -- Version 3</h1>
<P>
06/08/2006
<HR>
<a href="php.bouncehandler.v3.zip">Download source code</a>
<HR>

<P>
This bounce handler Attempts to parse Multipart reports for hard bounces, according to <a href='http://www.faqs.org/rfcs/rfc1892.html'>RFC1892</a> (RFC 1892 - The Multipart/Report Content Type for the Reporting of Mail System Administrative Messages) and <a href='http://www.faqs.org/rfcs/rfc1894.html'>RFC1894</a> (RFC 1894 - An Extensible Message Format for Delivery Status Notifications).  We can reuse this for any well-formed bounces. </P>
<P>
If the bounce is not well formed, it tries to extract some useful information anyway.  Currently Postfix and Exim are supported, partially.  You can edit the function <code>get_the_facts()</code> if you want to add a parser for your own busted MTA.  Please forward any useful & reuseable code to the keeper of this class.  <a href="http://cfortune.kics.bc.ca/">Chris Fortune</a></P>
<?
require_once("bounce_driver.class.php");


if($_GET['eml']){
    echo "<HR><P><B>".$_GET['eml']."</B>  --  ";
    echo "<a href=\"testdriver1.php\">View a different bounce</a></P>";
    $bounce = file_get_contents("eml/".$_GET['eml']);
    echo "<P>Quick and dirty bounce handler:<BR>
        useage:
        <blockquote><code>
        require_once(\"bounce_driver.class.php\");<br>
        \$multiArray = Bouncehandler::get_the_facts(\$strEmail);</code>
        </blockquote>
        returns a 2D associative array of bounced recipient addresses and their SMTP status codes (if available)<P>";
    $multiArray = Bouncehandler::get_the_facts($bounce);
    echo "<TEXTAREA COLS=100 ROWS=".(count($multiArray)*8).">";
    print_r($multiArray);
    echo "</TEXTAREA>";

    $bounce = BounceHandler::init_bouncehandler($bounce, 'string');
    list($head, $body) = preg_split("/\r\n\r\n/", $bounce, 2);
}
else{
    print "select a bounce email to view the parse";
    if ($handle = opendir('eml')) {
       echo "<P>Files:</P>\n";

       /* This is the correct way to loop over the directory. */
       while (false !== ($file = readdir($handle))) {
           if($file=='.' || $file=='..') continue;
           echo "<a href=\"".$_SERVER['PHP_SELF']."?eml=".urlencode($file)."\">$file</a><br>\n";
       }

       closedir($handle);
    }
    exit;
}

echo "<P>Will return recipient's email address, the RFC1893 error code, and the action.  Action can be one of the following:
<UL>
<LI>'transient'(temporary problem),
<LI>'failed' (permanent problem),
<LI>'autoreply' (a vacation auto-response), or
<LI>'' (nothing -- not classified).</UL>";

echo "<P>You could throw it into a 'for loop', for example...</P>
<PRE>
    foreach(\$multiArray as \$the){
        switch(\$the['action']){
            case 'failed':
                //do something
                kill_him(\$the['recipient']);
                break;
            case 'transient':
                //do something else
                \$num_attempts  = delivery_attempts(\$the['recipient']);
                if(\$num_attempts  > 10){
                    kill_him(\$the['recipient']);
                }
                else{
                    insert_into_queue(\$the['recipient'], (\$num_attempts+1));
                }
                break;
            case 'autoreply':
                //do something different
                postpone(\$the['recipient'], '7 days');
                break;
            default:
                //don't do anything
                break;
        }
    }
</PRE>";
echo "<P>That's all you need to know, but if you want to get more complicated you can.</P><BR>";


echo "<hr><h2>Here is the parsed head</h2>\n";
$head_hash = BounceHandler::parse_head($head);
echo "<TEXTAREA COLS=100 ROWS=".(count($head_hash)*2.7).">";
print_r($head_hash);
echo "</TEXTAREA>";

if (BounceHandler::is_RFC1892_multipart_report($head_hash) === TRUE){
    print "<h2><font color=red>Looks like an RFC1892 multipart report</font></H2>";
}
else {
    print "<h2><font color=red>Not an RFC1892 multipart report</font></H2>";
    echo "<TEXTAREA COLS=100 ROWS=100>";
    print_r($body);
    echo "</TEXTAREA>";
    exit;
}


echo "<h2>Here is the parsed report</h2>\n";
echo "<P>Postfix adds an appropriate X- header (X-Postfix-Sender:), so you do not need to create one via phpmailer.  RFC's call for an optional Original-recipient field, but mandatory Final-recipient field is a fair substitute.</P>";
$boundary = $head_hash['Content-type']['boundary'];
$mime_sections = BounceHandler::parse_body_into_mime_sections($body, $boundary);
$rpt_hash = BounceHandler::parse_machine_parsable_body_part($mime_sections['machine_parsable_body_part']);
echo "<TEXTAREA COLS=100 ROWS=".(count($rpt_hash)*16).">";
print_r($rpt_hash);
echo "</TEXTAREA>";



echo "<h2>Here is the error status code</h2>\n";
echo "<P>It's all in the status code, if you can find one.</P>";
for($i=0; $i<count($rpt_hash['per_recipient']); $i++){
    echo "<P>Report #".($i+1)."<BR>\n";
    echo BounceHandler::get_recipient($rpt_hash['per_recipient'][$i]);
    $scode = $rpt_hash['per_recipient'][$i]['Status'];
    echo "<PRE>$scode</PRE>";
    echo BounceHandler::fetch_status_messages($scode);
    echo "</P>\n";
}

echo "<h2>The Diagnostic-code</h2> <P>is not the same as the reported status code, but it seems to be more descriptive, so it should be extracted (if possible).";
for($i=0; $i<count($rpt_hash['per_recipient']); $i++){
    echo "<P>Report #".($i+1)." <BR>\n";
    echo BounceHandler::get_recipient($rpt_hash['per_recipient'][$i]);
    $dcode = $rpt_hash['per_recipient'][$i]['Diagnostic-code']['text'];
    if($dcode){
        echo "<PRE>$dcode</PRE>";
        echo BounceHandler::fetch_status_messages($dcode);
    }
    else{
        echo "<PRE>couldn't decode</PRE>";
    }
    echo "</P>\n";
}

echo "<H2>Grab original To: and From:</H2>\n";
echo "<P>Just in case we don't have an Original-recipient: field, or a X-Postfix-Sender: field, we can retrieve information from the (optional) returned message body part</P>\n";
$head = BounceHandler::get_head_from_returned_message_body_part($mime_sections);
echo "<P>From: ".$head['From']."<br>To: ".$head['To']."<br>Subject: ".$head['Subject']."</P>";


echo "<h2>Here is the body in RFC1892 parts</h2>\n";
echo "<P>Three parts: [first_body_part], [machine_parsable_body_part], and [returned_message_body_part]</P>";
echo "<TEXTAREA cols=100 rows=100>";
print_r($mime_sections);
echo "</TEXTAREA>";


/*
                $status_code = BounceHandler::format_status_code($rpt_hash['per_recipient'][$i]['Status']);
                $status_code_msg = BounceHandler::fetch_status_messages($status_code['code']);
                $status_code_remote_msg = $status_code['text'];
                $diag_code = BounceHandler::format_status_code($rpt_hash['per_recipient'][$i]['Diagnostic-code']['text']);
                $diag_code_msg = BounceHandler::fetch_status_messages($diag_code['code']);
                $diag_code_remote_msg = $diag_code['text'];
*/
?>
