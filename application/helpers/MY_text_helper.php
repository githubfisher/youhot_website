<?php 

	function ellipsize($str, $max_length, $position = 1, $ellipsis = '&hellip;', $charset="UTF-8")
	{
		// Strip tags
		$str = trim(strip_tags($str));

		// Is the string long enough to ellipsize?
		if (strlen($str) <= $max_length)
		{
			return $str;
		}
		
		mb_internal_encoding($charset);

		$beg = mb_strcut($str, 0, floor($max_length * $position));

		$position = ($position > 1) ? 1 : $position;

		if ($position === 1)
		{
			$end = mb_strcut($str, 0, -($max_length - strlen($beg)));
		}
		else
		{
			$end = mb_strcut($str, -($max_length - strlen($beg)));
		}

		return $beg.$ellipsis.$end;
	}

