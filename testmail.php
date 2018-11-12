<?php
	$eol="\r\n";
	$now=time();
	$headers = 'From: '.$ConName.'Programming Department<rustycon27programming@rustycon.com> RET=FULL ENVID=QQ1234567'.$eol;
	#$headers .= 'BCC:jerrygi@verizon.net'.$eol;
	$headers .= 'Reply-To:Rustycon 27 Programming Department<rustycon27programming@rustycon.com>'.$eol;
	$headers .= 'Return-Path: rustycon27programming@rustycon.com'.$eol;    // these two to set reply address
	$headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
	$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
	$headers .= 'MIME-Version: 1.0'.$eol;
	$headers .= "Content-type: text/html\r\n";

	$body = "This is a test,this is only a test";
	$subj="This is a test mail";


	mail('mac_the_fallen99999999999999999999@hotmail.com', $subject, $body, $headers, '-frustycon27programming@rustycon.com');
	print "SENT ".date('Y-M-d G:i:s',time());
?>