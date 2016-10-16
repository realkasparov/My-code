<?php
header('Content-Type: text/html; charset=windows-1251');

// Ôàéë ñ òåêñòîì
define('F', 'war_and_peace.txt');

// Íàñòðîéêè áàçû
$server 	= 'localhost';
$user 		= 'root';
$password 	= 'usbw';
$db 		= 'seopult';
/*******************************************************************************************************************/
// Íàñòðîéêè ÒÎÏ
$top_letters_limit	= 20;
$top_words_limit	= 20;

$portion 		= 1000;
$char_len_limit	= 3;

/*******************************************************************************************************************/
/***********************************************  ÔÓÍÊÖÈÈ  *********************************************************/
/*******************************************************************************************************************/


/***********************************************   ÁÓÊÂÛ   *********************************************************/
/**
 * Îáðàáîòêà ñòðîêè. Ïðîõîäèòñÿ ïî ñòðîêå, ÷èñòèò ñòðîêó îò çíàêîâ ïðåïèíàíèÿ,
 * öèôð, ëèøíèõ ïðîáåëîâ. Ïåðåâîäèò â íèæíèé ðåãèñòð.
 * @param $line_short
 * @return $line_short
 **/
function ClearFunc($line_short){
	$symStr 	= '!,."\'?:;()123456-7890*';
	$symArr 	= str_split($symStr);
	$line_short = str_replace($symArr, ' ', $line_short);
	$line_short = str_replace("\n", ' ', $line_short);
	$line_short = preg_replace("/\s{2,}/",' ',$line_short);
	$line_short = mb_strtolower($line_short, 'windows-1251');
	return $line_short;
}
/**
 * Îáðàáîòêà ñòðîêè. Ïðîõîäèòñÿ ïî ñòðîêå, ðàñïèõèâàåò áóêâû â àññîöèàòèâíûé ìàññèâ,
 * â êîòîðîì êëþ÷è - áóêâû, à çíà÷åíèÿ - êîëè÷åñòâî âêëþ÷åíèé.
 * @param $line_short
 * @return $aLetters
 **/
function GetChars($line_short){
	$aLetters 	= array();
	$count 		= strlen($line_short);
	for($i = 0; $i < $count; $i++){
		if(preg_match('/^[à-ÿ]+$/', $line_short{$i})) {
			if(isset($aLetters[$line_short{$i}])) {
				$aLetters[$line_short{$i}]++;
			} else {
				$aLetters[$line_short{$i}] = 1;
			}
		}
	}
	return $aLetters;
}
/**
 * Ñôîðìèðîâàòü çàïðîñ ê áàçå äàííûõ.
 * @param $aLetters
 * @return $query_string
 **/
function InsertLettersIntoDb($aLetters){
	$query_string = "INSERT INTO letter (`letter`, `qty`) VALUES ";
	foreach($aLetters as $key => $value) {
		$query_string .= "('".$key ."', ".$value."), ";
	}
	$query_string 	= substr($query_string, 0, strlen($query_string)-2); 
	$query_string .= ";";
	$query_string 	= iconv('windows-1251', 'utf-8', $query_string);
	$result 		= mysql_query($query_string);
	return $result;
}
/**
 * Âûáîðêà ÒÎÏ20 áóêâ.
 **/
function PrintTopLetters($top_letters_limit){
	$query_string	= 'SELECT `letter`, SUM(`qty`) as `all_qty` FROM `letter` GROUP BY `letter` ORDER BY `all_qty` DESC LIMIT '. $top_letters_limit;
	$result	= mysql_query($query_string);
	echo '<br>';
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$row1 = iconv('utf-8', 'windows-1251', $row["letter"]); 
		$row2 = $row["all_qty"];
        printf ("Letter: %s  Quantity: %s", $row1, $row2);
		echo '<br>';
    }
    mysql_free_result($result);
	mysql_query("
	INSERT INTO word_2 (`word`, `qty`)
	SELECT `word`, SUM(`qty`) as `qty` FROM `word_1` GROUP BY `word`;");
	$del_word_1 = iconv('windows-1251', 'utf-8', 'DELETE FROM word_1;');
	mysql_query($del_word_1);
}

function parseChars($line_short) {
	$aLetters 		= GetChars($line_short);
	$parseResult 	= InsertLettersIntoDb($aLetters);
	return $parseResult;
}

/***********************************************   ÑËÎÂÀ   *********************************************************/
/**
 * Óäàëÿåì èç ñòðîêè ñëîâà èç 1-2 áóêâ.
 * @param $line_short
 * @return $explode_words
 **/
function ClearMinWords($line_short) {
	$explode_words = explode(' ', $line_short);
	foreach($explode_words as $key => $explode_word) {
		$word_len = strlen($explode_words[$key]);
		if ($word_len < 3) {
			unset($explode_words[$key]);
		}
	}
	return $explode_words;
}
/**
 * Çàïîëíÿåì òàáëèöó word_1 ñëîâàìè.
 * @param $clear_text
 * @return $result
 **/
function InsertWordsIntoDb($clear_text) {
	$query_string 	= "INSERT INTO word_1 (`word`) VALUES ";
	foreach($clear_text as $key => $value) {
		$query_string .= "('".$value ."'), ";
	}
	$query_string 	= substr($query_string, 0, strlen($query_string)-2); 
	$query_string .= ";";
	$query_string 	= iconv('windows-1251', 'utf-8', $query_string);
	$result 		= mysql_query($query_string);
	return $result;
}

function ParseWords($line_short){
	$clear_text 		= ClearMinWords($line_short);
	$parseWordsResult 	= InsertWordsIntoDb($clear_text);	
}
/**
 * Âûáîðêà ÒÎÏ20 ñëîâ.
 **/
function PrintTopWords ($top_words_limit){
	$query_string	= 'SELECT `word`, SUM(`qty`) as `all_qty` FROM `word_2` GROUP BY `word` ORDER BY `all_qty` DESC LIMIT '.
	$top_words_limit;
	$query_string 	= iconv('windows-1251', 'utf-8', $query_string);
	$result			= mysql_query($query_string);
	echo '<br>';
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$row1 = iconv('utf-8', 'windows-1251', $row["word"]); 
		$row2 = $row["all_qty"];
        printf ("Word: %s  Quantity: %s", $row1, $row2);
		echo '<br>';
    }
    mysql_free_result($result);
	mysql_query("
	INSERT INTO word_2 (`word`, `qty`)
	SELECT `word`, SUM(`qty`) as `qty` FROM `word_1` GROUP BY `word`;");
	$del_word_1 = iconv('windows-1251', 'utf-8', 'DELETE FROM word_1;');
	mysql_query($del_word_1);
}

/*******************************************************************************************************************/
/**********************************************  /ÔÓÍÊÖÈÈ  *********************************************************/
/*******************************************************************************************************************/

// Ïîïûòêà óñòàíîâèòü ñîåäèíåíèå ñ MySQL:

if (!mysql_connect($server, $user, $password)) {
	echo "Îøèáêà ïîäêëþ÷åíèÿ ê ñåðâåðó MySQL";
	exit;
}

// Ñîåäèíèëèñü, òåïåðü âûáèðàåì áàçó äàííûõ:
mysql_select_db($db);
$del_letter = iconv('windows-1251', 'utf-8', 'delete from `letter`;');
$del_word_1 = iconv('windows-1251', 'utf-8', 'delete from `word_1`;');
$del_word_2 = iconv('windows-1251', 'utf-8', 'delete from `word_2`;');
mysql_query($del_letter);
mysql_query($del_word_1);
mysql_query($del_word_2);

/************************************************************************************************************************************/
/************************************************************************************************************************************/
/************************************************************************************************************************************/

$startÐos 	 = 0;
$snoskaFlag  = 0;
$isLastCycle = 0;

for($i = 0; !$isLastCycle; $i++) {
	/************************************************************************************************************************************/
	/*******************************************   ÒÎÏ 20 ÁÓÊÂ ÐÓÑÑÊÎÃÎ ÀËÔÀÂÈÒÀ   ******************************************************/
	/************************************************************************************************************************************/
	$line 	= file_get_contents(F, NULL, NULL, $startÐos, $portion);
	$strpos = strrpos($line, ' ');
	
	if(strlen($line) == $portion){
		$line_short = substr($line, 0, $strpos);
	} else {
		$line_short = $line;
		$isLastCycle = 1;
	}
	
	if($snoskaFlag == 0){
		$snoskaPos = strpos($line_short, 'ÑÍÎÑÊÈ');
		if($snoskaPos || $snoskaPos === 0) {
			$line_short = substr($line_short, 0, $snoskaPos);
			$startÐos += $snoskaPos;
			$snoskaFlag = 1;
		} else {
			$startÐos += $strpos;
		}
		//echo '  ÐÀÁÎÒÀÅÌ!!!  ';
		$line_short 	= ClearFunc($line_short);
		$parseResult	= parseChars($line_short);
	} else {
		$chapterPos = strpos($line_short, '×ÀÑÒÜ');
		if($chapterPos || $chapterPos === 0){
			$line_short = substr($line_short, $chapterPos);
			$startÐos += $strpos;
			//echo '  ÐÀÁÎÒÀÅÌ!!!  ';
			$line_short 	= ClearFunc($line_short);
			$parseResult	= parseChars($line_short);
			$snoskaFlag = 0;
		} else {
			$startÐos += $strpos;
		}
	}
	/************************************************************************************************************************************/
	/*******************************************   ÒÎÏ 20 ÏÎÏÓËßÐÍÛÕ ÑËÎÂ   *************************************************************/
	/************************************************************************************************************************************/
	$parseWordsResult = ParseWords($line_short);
}
PrintTopLetters($top_letters_limit);
PrintTopWords($top_words_limit);

/************************************************************************************************************************************/
/************************************************************************************************************************************/
/************************************************************************************************************************************/




?>
