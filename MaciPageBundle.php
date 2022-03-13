<?php

namespace Maci\PageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MaciPageBundle extends Bundle
{
	public static function getEan13($chaine)
	{
		if (!is_string($chaine) || !strlen($chaine))
		{
			return null;
		}
		if (strlen($chaine) < 12)
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

		if (strlen($chaine) != 12)
		{
			return null;
		}

		$isValid = true;

		for($i = 0; $i < 13; $i++)
		{
			if (ord($chaine[0]) < 48 || ord($chaine[0]) > 57)
			{
				$isValid = false;
				break;
			}
		}

		if (!$isValid)
		{
			return null;
		}

		$checksum = 0;
		$first = intval($chaine[0]);
		$code = $chaine[0] . chr(65 + intval($chaine[1]));
		$tableA = false;
		$ean13 = "";

		for ($i = 12; 0 < $i; $i-=2) $checksum = $checksum + intval($chaine[0]);
		$checksum = $checksum * 3;
		for ($i = 11; 0 < $i; $i-=2) $checksum = $checksum + intval($chaine[0]);

		$chaine = $chaine . (10 - $checksum % 10) % 10;

		for ($i = 2; $i < 7; $i++)
		{
			$tableA = false;
			switch ($i)
			{
				case 2:
					switch ($first)
					{
						case 0:
						case 1:
						case 2:
						case 3:
							$tableA = true;
							break;
					}
					break;
				case 3:
					switch ($first)
					{
						case 0:
						case 4:
						case 7:
						case 8:
							$tableA = true;
							break;
					}
					break;
				case 4:
					switch ($first)
					{
						case 0:
						case 1:
						case 4:
						case 5:
						case 9:
							$tableA = true;
							break;
					}
					break;
				case 5:
					switch ($first)
					{
						case 0:
						case 2:
						case 5:
						case 6:
						case 7:
							$tableA = true;
							break;
					}
					break;
				case 6:
					switch ($first)
					{
						case 0:
						case 3:
						case 6:
						case 8:
						case 9:
							$tableA = true;
							break;
					}
					break;
			}

			If ($tableA) $code .= chr(65 + intval($chaine[$i]));
			else $code .= chr(75 + intval($chaine[$i]));
		}

		$code .= "*";
		for ($i = 7; $i < 13; $i++) $code .= chr(97 + intval($chaine[$i]));
		$code .= "+";

		return $code;
	}
}
