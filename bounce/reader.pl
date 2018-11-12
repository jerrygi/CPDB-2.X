@email = <STDIN>; 
open (LOGGING, ">>/temp.txt"); 
print LOGGING "@email\n"; 
close LOGGING; 