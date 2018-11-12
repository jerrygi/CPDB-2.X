<?php
require_once("../config.php"); # load configuration file
include 'class.pdf.php';
include 'class.ezpdf.php';
#################################3
# Generate Table tents for the given Panelist and Con
# Http Get variables are
# cid:	Con ID
# pid:	PanelistID (a comma seperate the list can be provided)
# d: 	debug
#			0=no debug
#			1=Debug
# file:	The name of the file to output to. If not provided, Output will go to the screen
#
# cid and pid are required
# d and file are optional
#################################
$d=$_GET['d'];
$PID=0;
$CID=0;
if (array_key_exists('cid',$_GET)){
	$CID=$_GET['cid'];
}
if (array_key_exists('pid',$_GET)){
#	$PID=$_GET['pid'];
	$PANELISTS = split (',',$_GET['pid']);
}
$page=0;
	$pdf = new Cezpdf('LETTER','landscape' );
	$pdf->ezSetMargins(20,20,20,20);
	$pdf->selectFont('./fonts/Helvetica');
foreach ($PANELISTS as $PID) {

	$query="Select * from CPDB_Panelist where `PanelistID` = '".$PID."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$row = mysql_fetch_assoc($sql);
	if ($page==0){
		$page=1;
	} else {
		$pdf->newPage();
	}

	$pdf->setLineStyle(1);
	#$pdf->line(0,153,792,153);
	$pdf->line(0,306,792,306);
	#$pdf->line(0,459,792,459);



	$fs=80;
	$tw=$pdf->getTextWidth($fs,$row['PanelistPubName']);

	if ($tw > 770) {
		# Text is to big for one line
		$len=strlen($row['PanelistPubName']);
		if ($len%2==1){
			$strmid=($len+1)/2;
		} else{
			$strmid=$len/2;
		}
		for ($i=0;$i<=$strmid;$i++) {
			$lstr= $strmid-$i;
			$rstr= $strmid+$i;
			if (substr($row['PanelistPubName'],$lstr,1)==' ') {
				$left=substr($row['PanelistPubName'],0,$lstr);
				$right=substr($row['PanelistPubName'],$lstr,$len-$lstr);
				break;
			}
			if (substr($row['PanelistPubName'],$rstr,1)==' ') {
				$left=substr($row['PanelistPubName'],0,$rstr);
				$right=substr($row['PanelistPubName'],$rstr,$len-$rstr);
				break;
			}
		}
		$tw=$pdf->getTextWidth($fs,$left);
		$pdf->addText(792-(792-$tw)/2,400,$fs,$left,180);
		$tw=$pdf->getTextWidth($fs,$right);
		$pdf->addText(792-(792-$tw)/2,500,$fs,$right,180);


	} else {
		$pdf->addText(792-(792-$tw)/2,382,$fs,$row['PanelistPubName'],180);
	}

	#$pdf->ezSetY(382);
	#$pdf->ezText($row['PanelistPubName'],$fs,array('justification'=>'full'));
	#$tw="$tw";

	$query="select * from CPDB_V_PanelistPanelRoomTime
		where PanelistID = '".$PID."'
		and ConID = '".$CID."'
		order by Start";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	$y=306;
	$x=20;
	$pdf->setLineStyle(1,'','','');
	$pdf->line(20,$y,772,$y);
	$y-=8;
	$pdf->addText($x,$y,8,"Start Time");
	$pdf->addText($x+100,$y,8,"Location");
	$pdf->addText($x+300,$y,8,"Title");
	$y-=5;
	while ($row = mysql_fetch_assoc($sql)) {
		$pdf->line(20,$y,772,$y);
		$y-=8;
		$pdf->addText($x,$y,8,$row['Start']);
		$pdf->addText($x+100,$y,8,$row['RoomName']);
		$pdf->addText($x+300,$y,8,$row['PanelTitle']);
		$y-=5;
	}
	##################
	# Cut Here Lines
	##################
	#$pdf->setLineStyle(1,'','',array(10,5));
	#$pdf->line(396,0,396,77);
	#$pdf->line(396,535,396,612);
	#$pdf->addText(400,77,8,"cut here",90);
	#$pdf->addText(392,535,8,"cut here",-90);


}



if (isset($d) && $d){
	$pdfcode = $pdf->output(1);
	#$end_time = getmicrotime();
	$pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
	echo '<html><body>';
	print $query;
	echo trim($pdfcode);
	$CFG['debug']=1;
	debug();
	print_r (array_keys($data));
	print "<br>";
	print_r (array_values($data));

	$data = array(
	array('num'=>1,'name'=>'gandalf','type'=>'wizard')
	,array('num'=>2,'name'=>'bilbo','type'=>'hobbit','url'=>'http://www.ros.co.
	nz/pdf/')
	,array('num'=>3,'name'=>'frodo','type'=>'hobbit')
	,array('num'=>4,'name'=>'saruman','type'=>'bad
	dude','url'=>'http://sourceforge.net/projects/pdf-php')
	,array('num'=>5,'name'=>'sauron','type'=>'really bad dude')
	);
	print "<br>";
	print "<br>";
	print_r (array_keys($data));
	print "<br>";
	print_r (array_values($data));
	echo '</body></html>';

} else {

	if (array_key_exists('file',$_GET)){
		#Output to file
		$pdffile=$pdf->output();
		$dir = '../pdf';
		$fname = $dir.'/'.$_GET['file'];
		$fp = fopen($fname,'w');
		fwrite($fp,$pdffile);
		fclose($fp);

		echo '<html>
		<head>
		<SCRIPT LANGUAGE="JavaScript"><!--
		function go_now () { window.location.href = "'.$fname.'"; }
		//--></SCRIPT>
		</head>
		<body onLoad="go_now()"; >
		<a href="'.$fname.'">click here</a> if you are not re-directed.
		</body>
		</html>
		';

	} else {
		$pdf->stream();
	}

}



function debug() {
	global $CFG;
	if ($CFG['debug']==1){
	print $_SERVER['PHP_AUTH_USER'];
		print "<br><font color='red'>".$mySelect."<br>";
		print_r (array_keys($_GET));
		print"<br>\r\n";
		print_r (array_values($_GET));
		print"<br>\r\n";
		print $_SERVER["QUERY_STRING"];
		print "</font><br>";
	}
}
?>