<?php 
/**
 * 
appointmentres：<appointment-result：0表示操作成功，其他表示失败，后续版本可以具体定义错误码>，hint：<hint：操作失败的提示原因，成功时不需要这个域>
 */
//var_dump($student_info);
$output_array =  array();


$output_array[bp_appointment_result_field]		= $appointmentres;
if(isset($hint) && $hint!='')
{
	$output_array[bp_appointment_hint_field]		= $hint;
	
}

echo_json($output_array);
//end