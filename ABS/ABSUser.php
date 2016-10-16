<?php
/**
 * @deprecated
 *
 */
class ABSUser {
	private $username 		= null;
	private $password 		= null;
	private $branchOfficeId = null;
	private $bonusProgramId	= null;

	public function __construct() {

		/**
		 * Проверка существования пользователя в сессии
		 */
		$this->username 			= isset($_SESSION['mb_username']) 	? $_SESSION['mb_username'] 	: null;
		$this->password 			= isset($_SESSION['mb_password']) 	? $_SESSION['mb_password'] 	: null;
		if (!$this->username || !$this->password) {
			return null;
		}
		$this->username 			= trim($this->username);
		$this->password 			= trim($this->password);
		/**
		 * Если пользователь существует - проверка, соответствуют ли логин-пароль требованиям
		 */
		if (!StringFunctions::isAlphaNumeric($this->username) || !StringFunctions::isAlphaNumeric($this->password)) {
			return null;
		}
		$this->branchOfficeId 	= isset($_SESSION['mb_branch']) 	? (int)($_SESSION['mb_branch']*1) 	: null;

		/**
		 * Определение ID бонусной программы
		 */
		$this->bonusProgramId 	= isset($_SESSION['mb_program']) 	? (int)($_SESSION['mb_branch']*1) 	: ABSConfig::getProgramId();
	}
	/****************************************
	 * Геттеры
	 *
	 ****************************************/
	public function getUsername() {
		return $this->username;
	}
	public function getPassword() {
		return $this->password;
	}
	public function getBranchOfficeId() {
		return $this->branchOfficeId;
	}
	public function getBonusProgramId() {
		return $this->bonusProgramId;
	}
	/****************************************
	 * Сеттеры
	 *
	 ****************************************/
	public function setUsername($value) {
		return $this->username = $value;
	}
	public function setPassword($value) {
		return $this->password = $value;
	}
	public function setBranchOfficeId($value) {
		return $this->branchOfficeId = $value;
	}
	public function setBonusProgramId($value) {
		return $this->bonusProgramId = $value;
	}
}
?>
