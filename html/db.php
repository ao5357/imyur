<?php
$con = mysql_connect("imyur.ceyo3ivnp7zs.us-east-1.rds.amazonaws.com","ruym1","T1dd3rRuYm1");
if(!$con){
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("imyur", $con);
$sql = "CREATE TABLE urls(uid int(11) AUTO_INCREMENT,url varchar(2048),hostname varchar(128),timestamp timestamp())";
mysql_query($sql,$con);
mysql_close($con);