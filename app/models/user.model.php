<?php

class User {

	public $id;
	public $firstName;
	public $lastName;
	public $password;
	public $email;
	public $title;
	public $genderMf;
	public $birthDate;
	public $token;
  public $activeYn;
	public $role;
	public $auth0UserId;
	public $avatar;
  public $createdTime;

	public function __construct($arr=null) {
		if (isset($arr) && is_array($arr)) {
			$this->populate($arr);
		}
	}

	public function isAdmin() {
		return $this->isRole(Role::ROLE_ADMIN);
	}

	public function isUser() {
		return $this->isRole(Role::ROLE_USER);
	}

	public function isRole($role) {
		if (isset($this->role) && $this->role->id == $role) {
			return true;
		}
		return false;
	}

	public function populate($arr) {

		$this->id = (isset($arr['id']) && strlen($arr['id'])) ? $arr['id'] : '';
		$this->firstName = (isset($arr['first_name']) && strlen($arr['first_name'])) ? ucfirst($arr['first_name']) : '';
		$this->lastName = (isset($arr['last_name']) && strlen($arr['last_name'])) ? ucfirst($arr['last_name']) : '';
		$this->email = (isset($arr['email']) && strlen($arr['email'])) ? $arr['email'] : '';
		$this->password = (isset($arr['password']) && strlen($arr['password'])) ? $arr['password'] : '';
		$this->genderMf = (isset($arr['gender_mf']) && strlen($arr['gender_mf'])) ? $arr['gender_mf'] : '';
		$this->birthDate = (isset($arr['birth_date']) && strlen($arr['birth_date'])) ? $arr['birth_date'] : '';
		$this->token = (isset($arr['token']) && strlen($arr['token'])) ? $arr['token'] : '';
    $this->activeYn = (isset($arr['active_yn']) && strlen($arr['active_yn'])) ? $arr['active_yn'] : '';
		$this->auth0UserId = (isset($arr['auth0_user_id']) && strlen($arr['auth0_user_id'])) ? $arr['auth0_user_id'] : '';
    $this->avatar = (isset($arr['avatar']) && strlen($arr['avatar'])) ? $arr['avatar'] : '';
    $this->createdTime = (isset($arr['created_time']) && strlen($arr['created_time'])) ? $arr['created_time'] : 0;

		if (isset($arr['role_id']) && strlen($arr['role_id']) && $arr['role_id'] > 0) {
			$role = new Role();
			$role->id = $arr['role_id'];
			$role->title = (isset($arr['role_title']) && strlen($arr['role_title'])) ? $arr['role_title'] : '';
			$role->bitMask = (isset($arr['role_bit_mask']) && strlen($arr['role_bit_mask'])) ? $arr['role_bit_mask'] : '';

			$this->role = $role;
		}

	}

}