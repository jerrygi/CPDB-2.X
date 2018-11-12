<?php
require_once("../config.php"); # load configuration file

$d=$_GET['d'];
$PID=0;
$CID=0;

if (array_key_exists('cid',$_POST)){
	$CID=$_POST['cid'];
}
if (array_key_exists('pid',$_POST)){
	$PID=$_POST['pid'];
}
if (array_key_exists('cid',$_GET)){
	$CID=$_GET['cid'];
}
if (array_key_exists('pid',$_GET)){
	$PID=$_GET['pid'];
}



if (!isset($demotext) || strlen($demotext)==0){
	$demotext='demo text';
}
include '../class.pdf.php/class.pdf.php';
include '../class.pdf.php/class.ezpdf.php';
$pdf = new Cezpdf('LETTER','landscape' );

		$query="Select Distinct P.`PanelistID`,`PanelistPubName`
				from CPDB_Panelist as P
				inner join `CPDB_P2P` as L
				on P.PanelistID = L.PanelistID
				where L.ConID='".$CID."'
				and P.`IsEquip` = 0 ";
		if (!($PID==0)) {
			$query.= " and P.`PanelistID` = '".$PID."'";
		}
		$query.=" order by `PanelistPubName`";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($row = mysql_fetch_assoc($sql)) {
			BuildPage($row['PanelistPubName'],$row['PanelistID'],$CID);
		}
if (isset($d) && $d){
	DebugOutput();
} else {
	$pdf->stream();
}


function BuildPage($PanelistPubName, $PanelistID, $ConID){
	global $pdf;
	$pdf->ezSetMargins(20,20,20,20);
	$pdf->selectFont('./fonts/Helvetica');

	$pdf->setLineStyle(1);
	$pdf->line(0,153,792,153);
	$pdf->line(0,306,792,306);
	$pdf->line(0,459,792,459);



	$fs=80;

	$pdf->ezSetY(230);
	$pdf->ezText($PanelistPubName,$fs,array('justification'=>'full'));

	#<
	$query1="SELECT F.PanelTitle, L.Start, R.RoomName
		FROM `CPDB_P2P` as X
		inner join `CPDB_Panels` as F
		on F.PanelID = X.PanelID
		inner join `CPDB_PTR` as L
		on L.PanelID = F.PanelID
		inner join `CPDB_Room` as R
		on R.RoomID = L.RoomID
		where X.PanelistID = '".$PanelistID."'
		and F.ConID = '".$ConID."'
		order by L.Start";
	$sql1=mysql_query($query1) or die('Query failed: ' . mysql_error());
	$y=535;
	$x=20;
	$pdf->setLineStyle(1,'','','');
	$pdf->line(20,$y,772,$y);
	$y-=8;
	$pdf->addText($x,$y,8,"Start Time");
	$pdf->addText($x+100,$y,8,"Location");
	$pdf->addText($x+300,$y,8,"Title");
	$y-=5;
	while ($row1 = mysql_fetch_assoc($sql1)) {
		$pdf->line(20,$y,772,$y);
		$y-=8;
		$pdf->addText($x,$y,8,$row1['Start']);
		$pdf->addText($x+100,$y,8,$row1['RoomName']);
		$pdf->addText($x+300,$y,8,$row1['PanelTitle']);
		$y-=5;
	}


	$pdf->setLineStyle(1,'','',array(10,5));
	$pdf->line(396,0,396,77);
	$pdf->line(396,535,396,612);
	$pdf->addText(400,77,8,"cut here",90);
	$pdf->addText(392,535,8,"cut here",-90);
	$pdf->ezNewPage();
}





function debug() {
	global $CFG;
	if ($CFG['debug']==1){
	print $_SERVER['PHP_AUTH_USER'];
		print "<br><font color='red'>".$mySelect."<br>";
		print_r (array_keys($_POST));
		print"<br>\r\n";
		print_r (array_values($_POST));
		print"<br>\r\n";
		print $_SERVER["QUERY_STRING"];
		print "</font><br>";
	}
}

function DebugOutput(){
	global $CFG;
	global $pdf;
	global $query;
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
}
?>