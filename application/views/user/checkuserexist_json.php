<?php
echo_json(array(
	bp_result_field =>($userexistres?bp_operation_verify_fail:bp_operation_ok),
	bp_result_hint_field => "username exist"
));