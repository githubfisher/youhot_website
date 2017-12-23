<?php
$x=file_get_contents('/tmp/xxx');

$x =$y= json_decode($x, true);
//$x = json_decode($x);

print_r($x);

echo "----------------------------------------------------------\n";
$x['ps'] = json_decode($x['ps'], true);
print_r($x);
exit;
echo "----------------------------------------------------------\n";

foreach($x['ps'] AS $ps){
   foreach($ps AS $key => $p){
	$n[$key] = $p;
}
}
echo "----------------------------------------------------------\n";
$x['ps'] = Array();
$x['ps'][] = $n;
$x['ps'][] = $n;

print_r($x);

echo json_encode($x);
echo "\n----------------------------------------------------------\n";
echo json_encode($y);
echo "\n----------------------------------------------------------\n";
?>
