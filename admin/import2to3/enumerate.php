<?php

include_once ('/home/rustycon/Private/system.php');
# DB login are stored outside of public_html for security reasons

#####################
# connect to SQL DB #
#####################
global $Database;
$dbIdx='Panelist';
$conn = $Database [$dbIdx];
#print_r ($conn);

$db = mysql_connect( $conn['host'], $conn['user'], $conn['pass'], $conn['db']);
mysql_select_db($conn ['db'], $db);

$CFG['debug']=0;
debug();

$x= $_REQUEST['id'];
enumerate_element($x);

function enumerate_element($elementid) {
	$query="select 0 as CatID, 0 as Class, 'Element ID' as Type, convert (ElID , char) as Value from CPDB3_Elements where ELID='".$elementid."'
union
select 0 as CatID, 0 as Class, 'Element Type ID' as Type, convert (ElTyID , char) as  Value from CPDB3_Elements where ELID='".$elementid."'
union
select 0 as CatID, 0 as Class, 'Element Type' as Type, convert (T.ElementType , char) as  Value from CPDB3_Elements as E inner join CPDB3_ElementTypes as T on E.ElTyID = T.ElTyID where ELID='".$elementid."'
union
select 0 as CatID, 0 as Class, 'Element Created' as Type, convert (Created , char) as Value from CPDB3_Elements where ELID='".$elementid."'
union
select EDCatID as CatID, 'ID' as Class, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_ID as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'
union
select EDCatID as CatID, 'INT' as Class, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_Int as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'
union
select EDCatID as CatID, 'Time' as Class, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_Time as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'
union
select EDCatID as CatID, 'Strings' as Class, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_Strings as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'
union
select EDCatID as CatID, 'Text' as Class, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_Text as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'
union
select EDCatID as CatID, 'Bin' as Class, C.CatName as Type, convert (EDVal , char) as Value from CPDB3_Elements_Bin as E inner join CPDB3_Categorization as C on E.EdCatID = C.CatID where E.ELID='".$elementid."'";

	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	print "<table border=1><th>CatIhD</th><th>Type</th><th>Value</th></tr>";
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['CatID']."</td><td>".$row['Type']."</td><td>";
		if ($row['Class']=='ID'){
			print "<a href='enumerate.php?id=".$row['Value']."'>".$row['Value']."</a></td></tr>";
		} else {
			print $row['Value']."</td></tr>";
		}
	}
	print "</table>";

	$query="SELECT ElementType, ELID  FROM `CPDB3_Elements_ID` as A
			inner join CPDB3_V_Categorization as C
			on EDCatID = C.CatID
 			WHERE `EDVal`  = '".$elementid."'";
	$sql=mysql_query($query) or die('Query failed: ' . mysql_error());
	print "<table border=1><tr><th>Element Type</th><th>Element ID</th></tr>";
	while ($row = mysql_fetch_assoc($sql)) {
		print "<tr><td>".$row['ElementType']."</td><td><a href='enumerate.php?id=".$row['ELID']."'>".$row['ELID']."</td></tr>";
	}
	print "</table>";

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

?>