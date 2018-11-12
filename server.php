<?php
print "<table border=1>";
foreach($_SERVER as $key=> $value) {
	print "<tr><td>".$key."</td><td>".$value."</td></tr>";
}
phpinfo();


?>