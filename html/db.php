<?php
$con = mysql_connect("imyur.ceyo3ivnp7zs.us-east-1.rds.amazonaws.com","ruymi","T1dd3rRuYm1");
if(!$con){
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("imyur", $con);
$sql = "INSERT INTO urls (url,hostname) value ('http://www.hawidu.com/','98.250.88.173')";
mysql_query($sql,$con);
$result = mysql_query("SELECT * FROM urls");
while($row = mysql_fetch_array($result)){
  echo $row['uid'] . " " . $row['url'] . " " . $row['hostname'] . " " . $row['timestamp'];
  echo "<br />";
  }
mysql_close($con);