<?php 
$_res = array();
//var_dump($_interface);
foreach ($_interface as $value) {
	if(isset($$value))
	{
		$_res[$value]	= $$value;
	}

}
echo_json($_res);



//end