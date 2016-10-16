<?php
/**
 * @todo перевести элементы конфигурации в файл config.php
 *
 */
class ABSConfig {
	/**
	 * Возвращает строчку URL
	 *
	 * @return string
	 */
	public static function getUrl() {
		$CI = get_instance();
		return $CI->config->item('mbonus_url');
	}
	/**
	 * DataBase name
	 *
	 * @return string
	 */
	public static function getDatabase() {
		$CI 		= get_instance();
		$myUrl 	= $CI->config->item('base_url');
		if (strpos($myUrl, 'dev') || strpos($myUrl, 'test') || strpos($myUrl, '.my')) {
			return 'test';
		} else {
			return 'boy';
		}
	}
	/**
	 * Program Code
	 *
	 * @return string
	 */
	public static function getProgramCode() {
		return '0002';
	}
	/**
	 * Default Program ID
	 *
	 * @return string
	 */
	public static function getProgramId() {
		return 1;
	}
	/**
	 * Default language ID (RUS)
	 *
	 * @return ing
	 */
	public static function getDefaultLanguage() {
		return self::handleLanguage();
	}
	private static function handleLanguage() {
		$CI 			= get_instance();
		$request 	= new csRequest();
		$post 		= $request->getPostParameter(spKeys::BACK_REQUEST);
		$language 	= isset($post[spKeys::LANGUAGE]) ? $post[spKeys::LANGUAGE] : $CI->config->item('default_lang');
		if ($language == $CI->config->item('lang_rus')) {
			return 1;
		} else {
			return 2;
		}
	}
	/**
	 * Default Operation (barnch offices list)
	 *
	 * @return int
	 */
	public static function getDefaultOperationId() {
		return 3;
	}
}
?>