<?php
/**
 * работа со строкой ответа удаленного сервера
 * ответ приходит не в формате
 *
 */
class ABSContentParse {
	/**
	 * Парсит строку, вытаскивает контент, находящийся внутри тега $tagName
	 *
	 * @param string $string
	 * @param string $tagName
	 */
	public static function getTextBetweenTags($string, $tagName) {
		$pregString = "/<".$tagName.">(.*)<\/".$tagName.">/U";
		preg_match_all ($pregString, $string, $patArray);
		if (!$patArray || count($patArray) < 2 || !$patArray[1]) {
			return null;
		}
		return $patArray[1];
	}

	public static function xml_to_array($XML)
	{
		// Clean up white space
		$XML 			= trim($XML);
		$XML 			= str_replace("\r\n",'',$XML);
		$XML 			= str_replace("\n",'',$XML);

		$returnVal 	= $XML; // Default if just text;


		// Expand empty tags
		$emptyTag 	= '<(.*)/>';
		$fullTag 	= '<\\1></\\1>';
		$XML 			= preg_replace ("|$emptyTag|", $fullTag, $XML);

		preg_match_all('|<(.*)>(.*)</\\1>|Ums', trim($XML), $matches);

		if ($matches)
		{
			if (count($matches[1]) > 0) $returnVal = array(); // If we have matches then return an array else just text
			foreach ($matches[1] as $index => $outerXML)
			{
				$attribute = $outerXML;
				$value = self::xml_to_array($matches[2][$index]);
				if (! isset($returnVal[$attribute])) $returnVal[$attribute] = array();
				$returnVal[$attribute][] = $value;
			}
		}

		// Bring un-indexed singular arrays to a non-array value.
		if (is_array($returnVal)) foreach ($returnVal as $key => $value)
		{
			if (is_array($value) && count($value) == 1 && key($value) === 0)
			{
				$returnVal[$key] = $returnVal[$key][0];
			}
		}
		return $returnVal;
	}
}
?>
