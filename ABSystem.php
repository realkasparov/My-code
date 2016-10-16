<?php
/**
 * Основная библиотека для взаимодействия с Мастер-Бонус
 * Обращается к удаленному серверу, парсит ответ, выдает нужные данные в виде массива
 *
 */
class ABSystem {
	/**
	 * Код операции: форма логина
	 */
	const OPER_LOGIN 					= 1;

	/**
	 * Код операции: полный список призов (логин не требуется)
	 */
	const OPER_FULL_PRIZE_LIST 	= 3;

	/**
	 * Код операции: отправка заказа
	 */
	const OPER_SEND_ORDER 			= 4;

	/**
	 * Код операции: список филиалов (логин не требуется)
	 */
	const OPER_BRANCH_OFFICE_LIST = 6;

	/**
	 * Код операции: подтверждение заказа
	 */
	const OPER_ORDER_CONFIRM 		= 7;
	/**
	 * Код операции: баланс
	 *
	 */
	const OPER_BALANCE 	= 13;
	/**
	 * Код операции: общий список призов для авторизованных
	 * и неавторизованных пользователей (новый)
	 *
	 */
	const OPER_COMMON_PRIZE_LIST 	= 14;
	const SEND_SMS 					= 12;


	public static function getOfficeNameList() {
		$CI 				= get_instance();
		$officeList 	= array();
		/**
		 * Формирование запроса к АБС
		 */
		$ABSUrl 			= new ABSUrl();
		$ABSUrl->setOperationId(self::OPER_BRANCH_OFFICE_LIST);
		$url				= $ABSUrl->createUrl();
		$mbonusUrl		= $CI->config->item("mbonus_url");
		$parsedUrl		= parse_url($url);
		$messedContent = FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);
		//Запрос к АБС сформирован

		$officeList		= ABSContentParse::getTextBetweenTags($messedContent, 'nmbranch');

		if (count($officeList) > 0) {
			foreach ($officeList as $key => $value) {
				$officeList[$key] = mb_strtolower($value, 'utf-8');
			}
		}
		return $officeList;
	}

	public static function getOfficeFullList() {
		$CI 							= get_instance();
		$associativeOfficeList = array();
		/**
		 * Формирование запроса к АБС
		 */
		$ABSUrl 			= new ABSUrl();
		$ABSUrl->setOperationId(self::OPER_BRANCH_OFFICE_LIST);
		$url				= $ABSUrl->createUrl();
		$mbonusUrl		= $CI->config->item("mbonus_url");
		$parsedUrl		= parse_url($url);
		$messedContent = FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);
		//Запрос к АБС сформирован

		$officeMessedList	= ABSContentParse::getTextBetweenTags($messedContent, 'document');
		Common::printVar('officeMessedList');
		Common::printVar($officeMessedList);

		if (!$officeMessedList || !isset($officeMessedList[0])) {
			return null;
		}
		$officeList			= ABSContentParse::xml_to_array($officeMessedList[0]);
		$branchesList = isset($officeList['branches']['branch']) && !empty($officeList['branches']['branch'])
							? $officeList['branches']['branch']
							: (isset($officeList['branch']) && !empty($officeList['branch'])
									? $officeList['branch']
									: null);
		if (!$branchesList) {
			return null;
		}

		foreach ($branchesList as $key => $value) {
			$associativeOfficeList[$value['idbranch']] = $value['nmbranch'];
		}

		return $associativeOfficeList;
	}

	public static function getPrizeCategoryList() {
		$CI 				= get_instance();
		$officeList 	= array();
		/**
		 * Формирование запроса к АБС
		 */
		$ABSUrl 			= new ABSUrl();
		$ABSUrl->setOperationId(self::OPER_BRANCH_OFFICE_LIST);
		$url				= $ABSUrl->createUrl();
		$mbonusUrl		= $CI->config->item("mbonus_url");
		$parsedUrl		= parse_url($url);
		$messedContent = FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);
		//Запрос к АБС сформирован

		$officeList		= ABSContentParse::getTextBetweenTags($messedContent, 'nmbranch');

		if (count($officeList) > 0) {
			foreach ($officeList as $key => $value) {
				$officeList[$key] = mb_strtolower($value, 'utf-8');
			}
		}
		return $officeList;
	}

	public static function getFullCatList() {
		$CI 							= get_instance();
		$associativeOfficeList = array();
		/**
		 * Формирование запроса к АБС
		 */
		$ABSUrl 			= new ABSUrl();
		$ABSUrl->setOperationId(self::OPER_BRANCH_OFFICE_LIST);
		$url				= $ABSUrl->createUrl();
		$mbonusUrl		= $CI->config->item("mbonus_url");
		$parsedUrl		= parse_url($url);
		$messedContent = FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);
		//Запрос к АБС сформирован

		$officeMessedList	= ABSContentParse::getTextBetweenTags($messedContent, 'document');
		Common::printVar($officeMessedList);

		if (!$officeMessedList || !isset($officeMessedList[0])) {
			return null;
		}
		$officeList			= ABSContentParse::xml_to_array($officeMessedList[0]);
		if (!isset($officeList['program']['kategs']) || empty($officeList['program']['kategs'])) {
			return null;
		}
		foreach ($officeList['program']['kategs'] as $key => $value) {
			if (is_array($value) && isset($value[0])) {
				foreach ($value as $katKey => $kat) {
					$associativeOfficeList[$kat['katid']] = $kat['katname'];
				}
			}
		}

		return $associativeOfficeList;
	}
	/**
	 * За один запрос выдаст и список категорий призов, и список филиалов
	 *
	 */
	public static function getPrizeFilterParams() {
		Common::printVar('----getPrizeFilterParams');
		$CI 							= get_instance();
		$associativeOfficeList 	= array();
		/**
		 * Формирование запроса к АБС
		 */
		$ABSUrl 				= new ABSUrl();
		$ABSUrl->setOperationId(self::OPER_BRANCH_OFFICE_LIST);
		$url					= $ABSUrl->createUrl();
		$mbonusUrl			= $CI->config->item("mbonus_url");
		$parsedUrl			= parse_url($url);
		Common::printVar($mbonusUrl);
		Common::printVar($parsedUrl['query']);
		$messedContent 	= FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);
		Common::printVar($messedContent);
		//Запрос к АБС сформирован
		$filterArray		= array('branches' => array(), 'kat' => array());
		$officeMessedList	= ABSContentParse::getTextBetweenTags($messedContent, 'document');
		Common::printVar($officeMessedList);
		if (!$officeMessedList || !isset($officeMessedList[0])) {
			return null;
		}
		Common::printVar($officeMessedList);
		$officeList			= ABSContentParse::xml_to_array($officeMessedList[0]);
		//Офисы
		if (isset($officeList['branches']['branch']) && sizeof($officeList['branches']['branch']) > 0) {
			foreach ($officeList['branches']['branch'] as $key => $value) {
				$filterArray['branches'][$value['idbranch']] = $value['nmbranch'];
			}
		}
		if (isset($officeList['program']['kategs']) && sizeof($officeList['program']['kategs']) > 0) {
			foreach ($officeList['program']['kategs'] as $key => $value) {
				if (is_array($value) && isset($value[0])) {
					foreach ($value as $katKey => $kat) {
						$filterArray['kat'][$kat['katid']] = $kat['katname'];
					}
				}
			}
		}
		return $filterArray;

	}

	/**
	 * Получает полный список призов (доступно в т.ч. неавторизованному пользователю)
	 *
	 * @return array
	 */
	public static function getFullPrizeList($user = null, $idOffice, $idCat) {
		$CI 					= get_instance();
		$prizeList 			= array();

		/**
		 * Формирование запроса к АБС
		 */
		$ABSUrl 				= new ABSUrl();
		$ABSUrl->setOperationId(self::OPER_FULL_PRIZE_LIST);
		$url					= $ABSUrl->createUrl();
		$parsedUrl			= parse_url($url);
		$mbonusUrl			= $CI->config->item("mbonus_url");
		if ($idOffice) {
			$parsedUrl['query'] .= '&PIDBRANCH='.$idOffice;
		}
		if ($idCat) {
			$parsedUrl['query'] .= '&KATID='.$idCat;
		}
		Common::printVar("Полный список призов");
		Common::printVar($parsedUrl['query']);
		$messedContent 	= FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);
		
		//Запрос к АБС сформирован
		$prizeData			= ABSContentParse::getTextBetweenTags($messedContent, 'program');
		if (!$prizeData || count($prizeData) < 1 || !isset($prizeData[0])) {
			return false;
		}
		$prizeDataArray 	= ABSContentParse::xml_to_array($prizeData[0]);

		$prizeList			= isset($prizeDataArray['goods']) ? $prizeDataArray['goods'] : null;
		if (!$prizeList) {
			return null;
		}

		$result['prize_list'] 	= $prizeList;
		$result['list_remark'] 	= isset($prizeDataArray['list_remark']) ? $prizeDataArray['list_remark'] : null;

		return $result;
	}

	public static function getBalance($user) {
		$CI 					= get_instance();
		$prizeList 			= array();
		/**
		 * Формирование запроса к АБС
		 */
		$ABSUrl 				= new ABSUrl();
		$ABSUrl->setOperationId(self::OPER_BALANCE);
		$url					= $ABSUrl->createUrl();
		$parsedUrl			= parse_url($url);
		$mbonusUrl			= $CI->config->item("mbonus_url");
		$account 			= $user ? $CI->Mbonus->getAccountMounted($user) : null;

		if ($account) {
			$accountDetails = $CI->Mbonus->getAccountDetails($user);
			foreach ($accountDetails as $key => $value) {
				$parsedUrl['query'] .= '&'.$value->field_name.'='.$value->field_value;
			}
		} else {
			return false;
		}
		$messedContent		= FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);

		$documentContent	= ABSContentParse::getTextBetweenTags($messedContent, 'document');
		$pageContentArray = ABSContentParse::xml_to_array($documentContent[0]);
		return $pageContentArray;
	}
	/**
	 * Получает доступный пользователю список призов
	 *
	 * @return array
	 */
	public static function getAuthPrizeList($user, $idCat = null) {

		$CI 					= get_instance();
		$prizeList 			= array();

		/**
		 * Формирование запроса к АБС
		 */
		$ABSUrl 				= new ABSUrl();
		$ABSUrl->setOperationId(self::OPER_LOGIN);
		$url					= $ABSUrl->createUrl();
		$parsedUrl			= parse_url($url);
		$mbonusUrl			= $CI->config->item("mbonus_url");

		$account 			= $user ? $CI->Mbonus->getAccountMounted($user) : null;

		if ($account) {
			$accountDetails = $CI->Mbonus->getAccountDetails($user);
			foreach ($accountDetails as $key => $value) {
				$parsedUrl['query'] .= '&'.$value->field_name.'='.$value->field_value;
			}
		} else {
			return false;
		}

		if ($idCat) {
			$parsedUrl['query'] .= '&KATID='.$idCat;
		}

		Common::printVar($parsedUrl['query']);

		$messedContent 	= FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);

		//Запрос к АБС сформирован
		$prizeData			= ABSContentParse::getTextBetweenTags($messedContent, 'document');
		if (!$prizeData || count($prizeData) < 1 || !isset($prizeData[0])) {
			return false;
		}
		$prizeDataArray 	= ABSContentParse::xml_to_array($prizeData[0]);

		return $prizeDataArray;
	}


	public static function getAuthorisationResults($postParams) {
		$CI = get_instance();

		$ABSUrl 			= new ABSUrl();
		$ABSUrl->setOperationId(ABSystem::OPER_LOGIN);
		$url 				= $ABSUrl->createUrl();
		$parsedUrl 		= parse_url($url);
		$mbonusUrl		= $CI->config->item("mbonus_url");
		$postParams		= http_build_query($postParams);

		$messedContent		= FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query'].'&'.$postParams);

		$documentContent	= ABSContentParse::getTextBetweenTags($messedContent, 'document');
		$pageContentArray = ABSContentParse::xml_to_array($documentContent[0]);
		return $pageContentArray;
	}

	public static function getOrderConfirmContent($user, $gid) {
		$CI = get_instance();
		$ABSUrl 			= new ABSUrl();
		$ABSUrl->setOperationId(ABSystem::OPER_ORDER_CONFIRM);
		$url 				= $ABSUrl->createUrl();
		$parsedUrl 		= parse_url($url);
		$mbonusUrl		= $CI->config->item("mbonus_url");
		$account 		= $user ? $CI->Mbonus->getAccountMounted($user) : null;
		if ($account) {
			$accountDetails = $CI->Mbonus->getAccountDetails($user);
			foreach ($accountDetails as $key => $value) {
				$parsedUrl['query'] .= '&'.$value->field_name.'='.$value->field_value;
			}
			$parsedUrl['query'] .= '&PPG_ID='.$gid.'&TYPE=0';
		} else {
			return false;
		}

		$messedContent		= FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);


		$documentContent	= ABSContentParse::getTextBetweenTags($messedContent, 'document');
		$pageContentArray = ABSContentParse::xml_to_array($documentContent[0]);
		$data = array('url' => 'order_confirm', 'answer' => Common::returnPrintVar($pageContentArray));
		return $pageContentArray;
	}
	/**
	 * Формирует URL для создания заказа, отправляет заказ, разбирает ответ
	 *
	 * @param unknown_type $user
	 * @param unknown_type $orderParams
	 */
	public static function getOrderSendContent($user, $orderParams) {
		$CI 					= get_instance();
		$ABSUrl 				= new ABSUrl();
		$ABSUrl->setOperationId(ABSystem::OPER_SEND_ORDER);
		$url 					= $ABSUrl->createUrl();
		$parsedUrl 			= parse_url($url);
		$mbonusUrl			= $CI->config->item("mbonus_url");
		$account 			= $user ? $CI->Mbonus->getAccountMounted($user) : null;
		if (!$account) {
			return false;
		}

		$accountDetails 	= $CI->Mbonus->getAccountDetails($user);

		foreach ($accountDetails as $key => $value) {
			$parsedUrl['query'] .= '&'.$value->field_name.'='.$value->field_value;
		}
		foreach ($orderParams as $key => $value) {
			$parsedUrl['query']	.= '&'.$key.'='.$value;
		}
		$messedContent		= FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);
		$documentContent	= ABSContentParse::getTextBetweenTags($messedContent, 'document');
		$pageContentArray = ABSContentParse::xml_to_array($documentContent[0]);
		return $pageContentArray;
	}

	public static function getCommonPrizeList($user = null, $otherParams = array()) {
		$CI 					= get_instance();
		$ABSUrl 				= new ABSUrl();
		$ABSUrl->setOperationId(ABSystem::OPER_COMMON_PRIZE_LIST);
		$url 					= $ABSUrl->createUrl();
		$parsedUrl 			= parse_url($url);
		$mbonusUrl			= $CI->config->item("mbonus_url");
		$account 			= $user ? $CI->Mbonus->getAccountMounted($user) : null;
		if ($account) {
			$accountDetails 	= $CI->Mbonus->getAccountDetails($user);
			foreach ($accountDetails as $key => $value) {
				$parsedUrl['query'] .= '&'.$value->field_name.'='.$value->field_value;
			}
		}
		if (sizeof($otherParams) > 0) {
			foreach ($otherParams as $key => $value) {
				$parsedUrl['query'] .= '&'.$key.'='.$value;
			}
		}
		$messedContent		= FileGetContent::SimplePost($mbonusUrl, $parsedUrl['query']);

		Common::printVar("идем на");
		Common::printVar($mbonusUrl.'?'.$parsedUrl['query']);
		$documentContent	= StringFunctions::getTextBetweenTags($messedContent, 'document');
		$pageContentArray = ABSContentParse::xml_to_array($documentContent);

		return isset($pageContentArray['document']) ? $pageContentArray['document'] : array();

	}
	public static function getSms($url) {
		$CI 					= get_instance();
		$mbonusUrl			= $CI->config->item("mbonus_url");

		$messedContent		= FileGetContent::SimplePost($mbonusUrl, $url);
		Common::printVar('==============================================================');
		Common::printVar($messedContent);
		Common::printVar('==============================================================');
		$isSuccess 			= true;
		$errNum 				= StringFunctions::getTextBetweenTags($messedContent, 'err_num', false);
		$errText 			= StringFunctions::getTextBetweenTags($messedContent, 'err_txt', false);
		Common::printVar($errNum);
		Common::printVar($errText);
		Common::printVar('==============================================================');
		if ($errNum || $errText) {
			$isSuccess = false;
		}
		return array('is_success' => $isSuccess, 'ERR_NUM' => $errNum, 'ERR_TEXT' => $errText);
	}
} //ABSystem
?>