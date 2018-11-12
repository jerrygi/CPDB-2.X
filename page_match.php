<?php

function page_header()
{
	global $CFG;
	print"<head><title>Panelist Survey for ".$CFG['ConName']."</title><LINK REL=StyleSheet HREF='".$CFG['CSS']."'>
	<style type='text/css'>
	<!--
	a:link {
	color: #990000;
	text-decoration: none;
	}
	a:visited {
	text-decoration: none;
	color: #663300;
	}
	a:hover {
	text-decoration: underline;
	color: #990000;
	}
	a:active {
	text-decoration: none;
	color: #FF0000;
	}
	body {
	background-color: #000000;
	color:white;
	}
	body,td,th {
	font-family: calibri, century gothic, Arial, Helvetica, sans-serif;
	}
	th{
	text-align: left;
	cursor: pointer;
	border: 1;
	color: TEAL;
	}
	table tbody tr td{
	padding-left: 0px;
	}

-->
</style>
<script type='text/javascript'>
var count = '".$CFG['BiographySize']."';
function limiter(){
	var tex = document.survey.Biography.value;
	var len = tex.length;
	if(len > count){
		tex = tex.substring(0,count);
		document.survey.Biography.value =tex;
		return false;
	}
	document.survey.limit.value = count-len;
}

function showHide(obj){
var tbody = obj.parentNode.parentNode.parentNode.getElementsByTagName('tbody')[0];
var old = tbody.style.display;
tbody.style.display = (old == 'none'?'':'none');
}

function hideall()
{
  locl = document.getElementsByTagName('tbody');
  for (i=0;i<locl.length;i++)
  {
	 locl[i].style.display='none';
  }

}

</script>
	</head>";
}

function page_top()
{
	global $CFG;
	print "<body onload=limiter() LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>
	<TABLE WIDTH=900 BORDER=0 align='center' CELLPADDING=0 CELLSPACING=0 bgcolor='#C3C3C3'>

	<TR>
		<td colspan=3><img src='http://www.rustycon.com/img/RustyDates33.png' width='900' alt='Rustycon 33'>
	</TR>";
}

function page_footer()
{
	print "<!--</table>-->
	<TR>
		<TD COLSPAN=3>
		<span class='style4' style='color:#000000 '> <span class='style10'><center>Copyright &copy; 2009-2015 Rustycon, All rights reserved</span>. </span> </div></TD>
	</TR>
</TABLE>
";
}

function page_left()
{
	print "<tr><td>";
	###################
	#pretty stuff here
	###################
	print "</td>";
}

function page_right()
{
	print "<td>";
	###################
	#pretty stuff here
	###################
	print "</td>";
}


?>