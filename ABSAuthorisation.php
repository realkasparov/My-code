<?php
/**
 * @deprecated
 * отвечал за авторизацию пользователя в старой системе
 *
 */
class ABSAuthorisation {
	private $user 				= null;
	public $aErrors 			= array();
	private $pageContent 	= null;
	public function __construct() {

	}
	/**
	 * Юзер забирается из сессии
	 *
	 * @return ABSUser
	 */
	public function getUser() {
		if (!$this->user) {
			$this->user = new ABSUser();
		}
		return $this->user;
	}
	/**
	 * Возвращает содержимое страницы после какого-либо действия методов класса
	 *
	 * @return string
	 */
	public function getPageContent() {
		return $this->pageContent;
	}
	/**
	 * Пытается подконнектиться к АБС с параметами пользователя
	 * Если ошибка - возвращает список ошибок
	 *
	 * Если успешно - генерит юзера, сессию и возвращает пустой список
	 *
	 * @param string $requestParam
	 * @return string
	 */
	public function authorize($requestParam) {
		/**
		 * Проверка параметров реквеста
		 */
		if (!$this->isRequestValid($requestParam)) {
			return false;
		}
		/**
		 * Генерим URL для обращения
		 */
		$ABSUrl 			= new ABSUrl();
		$ABSUrl->setOperationId(ABSystem::OPER_LOGIN);

		$ABSUrl->setUsername($requestParam['mb_username']);
		$ABSUrl->setPassword($requestParam['mb_password']);
		$ABSUrl->setBranchId($requestParam['mb_branch']);
		$ABSUrl->setBonusProgramId($requestParam['mb_program']);

		$url							= $ABSUrl->createUrl();

		$this->pageContent 		= ABSystem::getAuthorisationResults($url);
		if (isset($this->pageContent['branch'])) {
			$this->aErrors['auth'] 	= 'Неправильный логин или пароль';
			$this->user					= null;

			return false;
		} else {
			/**
			 * Генерация юзера
			 */
			$this->user					= new ABSUser();
			$this->user->setUsername($requestParam['mb_username']);
			$this->user->setPassword($requestParam['mb_password']);
			$this->user->setBranchOfficeId($requestParam['mb_branch']);
			$this->user->setBonusProgramId($requestParam['mb_program']);
			/**
			 * Установка сессионных переменных
			 */
			$_SESSION['mb_username'] 	= $requestParam['mb_username'];
			$_SESSION['mb_password']	= $requestParam['mb_password'];
			$_SESSION['mb_branch'] 		= $this->user->getBranchOfficeId();
			$_SESSION['mb_program'] 	= $this->user->getBonusProgramId();
			return true;
		}
		return false;
	}

	private function isRequestValid($requestParam) {
		if (!isset($requestParam['mb_username']) || empty($requestParam['mb_username'])) {
			$this->aErrors['fields']['mb_username'] = 'Поле обязательно к заполнению';
		} elseif (!StringFunctions::isAlphaNumeric($requestParam['mb_username'])) {
			$this->aErrors['fields']['mb_username'] = 'Допускаются только символы латиницы и цифры';
		}

		if (!isset($requestParam['mb_password']) || empty($requestParam['mb_password'])) {
			$this->aErrors['fields']['mb_password'] = 'Поле обязательно к заполнению';
		} elseif (!StringFunctions::isAlphaNumeric($requestParam['mb_password'])) {
			$this->aErrors['fields']['mb_password'] = 'Допускаются только символы латиницы и цифры';
		}
		return empty($this->aErrors['fields']);
	}

	public function getContentByUser() {
		if ($this->user->getUsername() && $this->user->getPassword()) {
			$ABSUrl 						= new ABSUrl();
			$ABSUrl->setOperationId(ABSystem::OPER_LOGIN);

			$ABSUrl->setUsername($this->user->getUsername());
			$ABSUrl->setPassword($this->user->getPassword());
			$ABSUrl->setBranchId($this->user->getBranchOfficeId());
			$ABSUrl->setBonusProgramId($this->user->getBonusProgramId());

			$url							= $ABSUrl->createUrl();
			$this->pageContent 		= ABSystem::getAuthorisationResults($url);
			if (isset($this->pageContent['branch'])) {
				self::logout();
				return false;
			} else {
				return true;
			}
		} else {
			self::logout();
			return false;
		}
	}
	public static function logout() {
		/**
		* Установка сессионных переменных
		*/
		$_SESSION['mb_username'] 	= null;
		$_SESSION['mb_password']	= null;
		$_SESSION['mb_branch'] 		= null;
		$_SESSION['mb_program'] 	= null;
		return true;
	}

	public function getOrderContent($orderItemId) {
		$orderItemId = (int)$orderItemId;
		if ($this->user->getUsername() && $this->user->getPassword() && $orderItemId) {
			$ABSUrl 						= new ABSUrl();
			$ABSUrl->setOperationId(ABSystem::OPER_ORDER_CONFIRM);

			$ABSUrl->setUsername($this->user->getUsername());
			$ABSUrl->setPassword($this->user->getPassword());
			$ABSUrl->setBranchId($this->user->getBranchOfficeId());
			$ABSUrl->setBonusProgramId($this->user->getBonusProgramId());
			$ABSUrl->setOrderItemId($orderItemId);

			$url							= $ABSUrl->createUrl();
			$this->pageContent 		= ABSystem::getOrderConfirmContent($url);
			if (!isset($this->pageContent['ARENA'])) {
				self::logout();
				return false;
			}
			return true;
		} else {
			return false;
		}
	}

	public function getOrderApproveContent($officeId, $orderItemId) {
		if ($this->user->getUsername() && $this->user->getPassword() && $orderItemId && $officeId) {
			$ABSUrl 						= new ABSUrl();
			$ABSUrl->setOperationId(ABSystem::OPER_SEND_ORDER);

			$ABSUrl->setUsername($this->user->getUsername());
			$ABSUrl->setPassword($this->user->getPassword());
			$ABSUrl->setBranchId($this->user->getBranchOfficeId());
			$ABSUrl->setBonusProgramId($this->user->getBonusProgramId());
			$ABSUrl->setOrderItemId($orderItemId);

			$url							= $ABSUrl->createUrl();
			$this->pageContent 		= ABSystem::getOrderConfirmContent($url);
			if (!isset($this->pageContent['order']) || !isset($this->pageContent['order']['status']) || $this->pageContent['order']['status'] != 1) {
				self::logout();
				return false;
			}
			return true;
		} else {
			return false;
		}
	}
}
?>