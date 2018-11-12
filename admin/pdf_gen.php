<?php
require_once("../config.php"); # load configuration file

$d=$_GET['d'];
$PID=0;
$CID=0;
if (array_key_exists('cid',$_GET)){
	$CID=$_GET['cid'];
}
if (array_key_exists('pid',$_GET)){
	$PID=$_GET['pid'];
}

		$query="Select * from CPDB_Panelist where `PanelistID` = '".$PID."'";
		$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
		$row = mysql_fetch_assoc($sql);

if (!isset($demotext) || strlen($demotext)==0){
	$demotext='demo text';
}
include '../class.pdf.php/class.pdf.php';
include '../class.pdf.php/class.ezpdf.php';
$pdf = new Cezpdf('LETTER','landscape' );
$pdf->ezSetMargins(20,20,20,20);
$pdf->selectFont('./fonts/Helvetica');

$pdf->setLineStyle(1);
$pdf->line(0,153,792,153);
$pdf->line(0,306,792,306);
$pdf->line(0,459,792,459);



$fs=40;

$pdf->ezSetY(230);
$pdf->ezText($row['PanelistPubName'],$fs,array('justification'=>'full'));

$query="SELECT F.PanelTitle, L.Start, R.RoomName FROM `CPDB_P2P` as X
	inner join `CPDB_Panels` as F
	on F.PanelID = X.PanelID
	inner join `CPDB_PTR` as L
	on L.PanelID = F.PanelID
	inner join `CPDB_Room` as R
	on R.RoomID = L.RoomID
	where X.PanelistID = '".$PID."'
	and F.ConID = '".$CID."'
	order by L.Start";
$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
$y=535;
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


$pdf->setLineStyle(1,'','',array(10,5));
$pdf->line(396,0,396,77);
$pdf->line(396,535,396,612);
$pdf->addText(400,77,8,"cut here",90);
$pdf->addText(392,535,8,"cut here",-90);






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
$pdf->stream();
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