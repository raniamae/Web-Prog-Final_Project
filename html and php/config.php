<?php
    $host="sql301.infinityfree.com";
    $user="if0_40953391";
	$password="Aa20060327";
	$database="if0_40953391_sinerate";

	$conn=mysqli_connect($host,$user,$password,$database);

	if(!$conn){
        die("connection failed:" .mysqli_connect_error());   
    }
	?>