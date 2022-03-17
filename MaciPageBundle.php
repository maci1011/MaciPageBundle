<?php

namespace Maci\PageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MaciPageBundle extends Bundle
{
	public static function getLabel($value, $haystack)
	{
		if(!is_array($haystack) || count($haystack) == 0) return null;
		if(!$value || $value == "") return array_search(array_values($haystack)[0], $haystack);
		$array = $haystack;
		$key = array_search($value, $array);
		if ($key) {
			return $key;
		}
		$str = str_replace('_', ' ', $value);
		return ucwords($str);
	}

	public static function getEan13($chaine)
	{
		if (!is_string($chaine) || !strlen($chaine))
		{
			return null;
		}

		if (strlen($chaine) == 12)
		{
			// Right Len!
		}
		else if (strlen($chaine) < 12)
		{
			while (strlen($chaine) < 12)
			{
				$chaine = '0' . $chaine;
			}
		}
		else if (strlen($chaine) == 13)
		{
			$chaine = substr($chaine, 1, 12);
		}
		else return null;

		$isValid = true;
		for($i = 0; $i < 13; $i++)
		{
			if (ord($chaine[0]) < 48 || ord($chaine[0]) > 57)
			{
				$isValid = false;
				break;
			}
		}
		if (!$isValid) return null;

		$checksum = 0;
		for ($i = 11; -1 < $i; $i-=2) $checksum += intval($chaine[$i]);
		$checksum *= 3;
		for ($i = 10; -1 < $i; $i-=2) $checksum += intval($chaine[$i]);
		$chaine .= (10 - $checksum % 10) % 10;

		$code = $chaine[0] . chr(65 + intval($chaine[1]));
		for ($i = 2; $i < 7; $i++) $code .= in_array(intval($chaine[0]), ([
			2 => [0,1,2,3],
			3 => [0,4,7,8],
			4 => [0,1,4,5,9],
			5 => [0,2,5,6,7],
			6 => [0,3,6,8,9]
		])[$i]) ? chr(65 + intval($chaine[$i])) : chr(75 + intval($chaine[$i]));

		$code .= "*";
		for ($i = 7; $i < 13; $i++) $code .= chr(97 + intval($chaine[$i]));

		return $code . "+";
	}
}
