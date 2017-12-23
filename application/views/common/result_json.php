<?php
$output_array =  array();
$output_array[bp_result_field]= $res;

if(!empty($hint)){
	$output_array[bp_result_hint_field]	= $hint;
}
if(isset($timeused)) {
 	$output_array['timeused'] = $timeused;
}
if(isset($data)){
	$output_array['data'] = $data;
}
if(isset($id)){
	$output_array['id'] = $id;
}
if(isset($class)){
	$output_array['class'] = $class;
}
if(isset($classes)){
	$output_array['classes'] = $classes;
}
if(isset($student)){
	$output_array['student'] = $student;
}
if(isset($students)){
	$output_array['students'] = $students;
}
if(isset($teacher)){
	$output_array['teacher'] = $teacher;
}
if(isset($total)){
	$output_array['total'] = $total;
}
if(isset($offset)){
	$output_array['offset'] = $offset;
}
if(isset($count)){
	$output_array['count'] = $count;
}

if(isset($audio_url)){
 $output_array['audio_url'] = $audio_url;
}
$callback = $this->input->get_post('callback');
if(!empty($callback)){

	header ( 'Content-Type: text/html; charset=utf-8' );
	echo '<script>'.htmlspecialchars($callback).'(';
	echo json_encode($output_array);
	echo ')</script>';
}else{
	echo_json($output_array);
}
