<?php
/**
 * Генерит URL для обращения к системе
 * @deprecated partly
 *
 */
class ABSUrl {
	/**
	 * Общие параметры
	 *
	 */
	/**
	 * ID бонусной программы
	 */
	const PARAM_BONUS_PROGRAM_ID 		= 'PBP_ID';
	/**
	 * Код бонусной программы
	 */
	const PARAM_BONUC_PROGRAM_CODE 	= 'progr';
	/**
	 * ID операции
	 * (определяет ответ АБС)
	 */
	const PARAM_OPERATION_ID			= 'nmProc';
	/**
	 * Язык
	 */
	const PARAM_LANGUAGE					= 'pLang';
	/**
	 * База данных
	 */
	const PARAM_DATABASE					= 'base';
	/**
	 * Основная часть URL внешнего сервиса
	 *
	 * @var string
	 */
	private $url;
	private $bonusProgramId 	= null;
	private $bonusProgramCode 	= null;
	private $operationId 		= null;
	private $language 			= null;
	private $database 			= null;
	/**
	 * Параметры пользователя
	 *
	 */
	const PARAM_USERNAME 		= 'PUSERNAME';
	const PARAM_PASSWORD 		= 'PPASSWORD';
	const PARAM_BRANCH_ID 		= 'PIDBRANCH';
	private $username 			= null;
	private $password 			= null;
	private $branchId 			= null;
	/**
	 * Параметры заказа
	 *
	 */
	const PARAM_ORDER_ITEM_ID = 'PPG_ID';
	private $orderItemId			= null;
	public function __construct() {
		$this->database 			= ABSConfig::getDatabase();
		$this->bonusProgramCode = ABSConfig::getProgramCode();
		$this->bonusProgramId 	= ABSConfig::getProgramId();
		$this->url					= ABSConfig::getUrl();
		$this->language			= ABSConfig::getDefaultLanguage();
		$this->operationId		= ABSConfig::getDefaultOperationId();
	}
	/**
	 * Генерит URL по параметрам запроса
	 *
	 * @return string
	 */
	public function createUrl() {
		$url							= $this->url;
		if (!$this->database) {
			return null;
		}
		$url 		.= '?'.self::PARAM_DATABASE.'='.$this->database;

		if ($this->bonusProgramCode) {
			$url 	.= '&'.self::PARAM_BONUC_PROGRAM_CODE.'='.$this->bonusProgramCode;
		}
		if ($this->language) {
			$url 	.= '&'.self::PARAM_LANGUAGE.'='.$this->language;
		}
		if ($this->operationId) {
			$url 	.= '&'.self::PARAM_OPERATION_ID.'='.$this->operationId;
		}
		if ($this->bonusProgramId) {
			$url 	.= '&'.self::PARAM_BONUS_PROGRAM_ID.'='.$this->bonusProgramId;
		}
		/**
		 * При авторизации
		 */
		if ($this->username) {
			$url 	.= '&'.self::PARAM_USERNAME.'='.$this->username;
		}
		if ($this->password) {
			$url 	.= '&'.self::PARAM_PASSWORD.'='.$this->password;
		}
		if ($this->branchId) {
			$url 	.= '&'.self::PARAM_BRANCH_ID.'='.$this->branchId;
		}
		if ($this->orderItemId) {
			$url 	.= '&'.self::PARAM_ORDER_ITEM_ID .'='.$this->orderItemId;
		}
		return $url;
	}

	public function setOperationId($operationId) {
		$operationId 				= (int)$operationId;
		if ($operationId) {
			$this->operationId 	= $operationId;
		}
	}

	public function setUsername($value) {
		$this->username = $value;
	}
	public function setPassword($value) {
		$this->password = $value;
	}
	public function setBranchId($value) {
		$value = (int)$value;
		if ($value) {
			$this->branchId = $value;
		}
	}
	public function setBonusProgramId($value) {
		$value = (int)$value;
		if ($value) {
			$this->bonusProgramId = $value;
		}
	}
	public function setOrderItemId($value) {
		$value = (int)$value;
		if ($value) {
			$this->orderItemId = $value;
		}
	}
 }
?>