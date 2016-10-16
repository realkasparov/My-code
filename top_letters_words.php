<?php
header('Content-Type: text/html; charset=windows-1251');

// Файл с текстом
define('F', 'war_and_peace.txt');

// Настройки базы
$server 	= 'localhost';
$user 		= 'root';
$password 	= 'usbw';
$db 		= 'seopult';
/*******************************************************************************************************************/
// Настройки ТОП
$top_letters_limit	= 20;
$top_words_limit	= 20;

$portion 		= 1000;
$char_len_limit	= 3;

/*******************************************************************************************************************/
/***********************************************  ФУНКЦИИ  *********************************************************/
/*******************************************************************************************************************/


/***********************************************   БУКВЫ   *********************************************************/
/**
 * Обработка строки. Проходится по строке, чистит строку от знаков препинания,
 * цифр, лишних пробелов. Переводит в нижний регистр.
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
 * Обработка строки. Проходится по строке, распихивает буквы в ассоциативный массив,
 * в котором ключи - буквы, а значения - количество включений.
 * @param $line_short
 * @return $aLetters
 **/
function GetChars($line_short){
	$aLetters 	= array();
	$count 		= strlen($line_short);
	for($i = 0; $i < $count; $i++){
		if(preg_match('/^[а-я]+$/', $line_short{$i})) {
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
 * Сформировать запрос к базе данных.
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
 * Выборка ТОП20 букв.
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

/***********************************************   СЛОВА   *********************************************************/
/**
 * Удаляем из строки слова из 1-2 букв.
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
 * Заполняем таблицу word_1 словами.
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
 * Выборка ТОП20 слов.
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
/**********************************************  /ФУНКЦИИ  *********************************************************/
/*******************************************************************************************************************/

// Попытка установить соединение с MySQL:

if (!mysql_connect($server, $user, $password)) {
	echo "Ошибка подключения к серверу MySQL";
	exit;
}

// Соединились, теперь выбираем базу данных:
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

$startРos 	 = 0;
$snoskaFlag  = 0;
$isLastCycle = 0;

for($i = 0; !$isLastCycle; $i++) {
	/************************************************************************************************************************************/
	/*******************************************   ТОП 20 БУКВ РУССКОГО АЛФАВИТА   ******************************************************/
	/************************************************************************************************************************************/
	$line 	= file_get_contents(F, NULL, NULL, $startРos, $portion);
	$strpos = strrpos($line, ' ');
	
	if(strlen($line) == $portion){
		$line_short = substr($line, 0, $strpos);
	} else {
		$line_short = $line;
		$isLastCycle = 1;
	}
	
	if($snoskaFlag == 0){
		$snoskaPos = strpos($line_short, 'СНОСКИ');
		if($snoskaPos || $snoskaPos === 0) {
			$line_short = substr($line_short, 0, $snoskaPos);
			$startРos += $snoskaPos;
			$snoskaFlag = 1;
		} else {
			$startРos += $strpos;
		}
		//echo '  РАБОТАЕМ!!!  ';
		$line_short 	= ClearFunc($line_short);
		$parseResult	= parseChars($line_short);
	} else {
		$chapterPos = strpos($line_short, 'ЧАСТЬ');
		if($chapterPos || $chapterPos === 0){
			$line_short = substr($line_short, $chapterPos);
			$startРos += $strpos;
			//echo '  РАБОТАЕМ!!!  ';
			$line_short 	= ClearFunc($line_short);
			$parseResult	= parseChars($line_short);
			$snoskaFlag = 0;
		} else {
			$startРos += $strpos;
		}
	}
	/************************************************************************************************************************************/
	/*******************************************   ТОП 20 ПОПУЛЯРНЫХ СЛОВ   *************************************************************/
	/************************************************************************************************************************************/
	$parseWordsResult = ParseWords($line_short);
}
PrintTopLetters($top_letters_limit);
PrintTopWords($top_words_limit);

/************************************************************************************************************************************/
/************************************************************************************************************************************/
/************************************************************************************************************************************/




?>