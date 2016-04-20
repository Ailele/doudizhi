<?php
	function array_del($parent, $sub)
	{
		foreach($parent as $key => $value)
		{
			foreach($sub as $subKey => $subValue )
			{
				if($value == $subValue)
				{
					unset($parent[$key]);
					unset($sub[$subKey]);
					break;
				}
			}
		}
		sort($parent);
		return $parent;
	}
