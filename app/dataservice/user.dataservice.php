<?php

class UserDataService extends BaseDataService {

	public function getUsers() {

    $request = $this->getRequestData();
    
    $userId = isset($request->userId) ? $request->userId : 'ALL';
    $activeYn = isset($request->activeYn) && strtoupper($request->activeYn) === 'Y' ? 'Y' : 'N';

		$query = <<<EOF
			SELECT DISTINCT u.id, u.first_name, u.last_name, u.email, u.gender_mf,
      u.birth_date, u.auth0_user_id, u.avatar, u.created_time, u.active_yn,
      MD5(CONCAT(u.id, UNIX_TIMESTAMP(u.created_time))) AS token,
      r.id AS role_id, r.role AS role_title, r.bit_mask AS role_bit_mask
			FROM user u
      JOIN user_role ur ON u.id = ur.user_id
      JOIN role r ON ur.role_id = r.id
      WHERE u.active_yn = :active_yn
      AND ('ALL' = :user_id1 OR u.id = :user_id2)
      ORDER BY u.last_name, u.first_name
EOF;

    $GLOBALS['log']->info('query', 'UserDataService', 'getUsers', array('query'=>$query,'request'=>$request));

		$bind_params = array(
  		':user_id1' => $userId,
  		':user_id2' => $userId,
  		':active_yn' => $activeYn
  	);
		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			$users = array();
			foreach ($result as $row) {
				$user = new User($row);
				$users[] = $user;
			}
			return $users;
		}

		return $result;
	}

	public function getUser($id) {

		$query = <<<EOF
			SELECT u.id, u.first_name, u.last_name, u.email, u.password, u.avatar, u.active_yn,
      MD5(CONCAT(u.id, UNIX_TIMESTAMP(u.created_time))) AS token,
      u.created_time, SUBSTRING_INDEX(u.auth0_user_id, '|', 1) as auth0_user_id,
			r.id AS role_id, r.role AS role_title, r.bit_mask AS role_bit_mask
			FROM user u
			JOIN user_role ur ON u.id = ur.user_id
      JOIN role r ON ur.role_id = r.id
			WHERE u.id = :id
EOF;

		$bind_params = array(':id' => $id);
		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			$user = FALSE;
			foreach ($result as $row) {
				$user = new User($row);
			}

			return $user;
		}

		return $result;
	}

	public function getUserByAuth0Id($id) {

		$query = <<<EOF
			SELECT u.id, u.first_name, u.last_name, u.email, u.avatar, u.active_yn,
      MD5(CONCAT(u.id, UNIX_TIMESTAMP(u.created_time))) AS token,
      u.created_time, u.auth0_user_id, 
      -- SUBSTRING_INDEX(u.auth0_user_id, '|', 1) as auth0_user_id,
			r.id AS role_id, r.role AS role_title
			FROM user u
			JOIN user_role ur ON u.id = ur.user_id
      JOIN role r ON ur.role_id = r.id
			WHERE u.auth0_user_id = :id
EOF;

		$bind_params = array(':id' => $id);
		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			$user = FALSE;
			foreach ($result as $row) {
				$user = new User($row);
			}

			return $user;
		}

		return $result;
	}

	public function addUser() {

		$request = $this->getRequestData();

		$user = new User();

		$user->firstName = $request->firstName;
		$user->lastName = $request->lastName;
		$user->email = $request->email;
		$user->password = $request->password;

		if (isset($request->role)) {
			$role = new Role();
			$role->id = $request->role;
		}
		$user->role = $role;

		return $this->addUserFromObject($user);
	}

	public function addUserFromObject($user) {

		// check to see if the email address already exists
		$query = <<<EOF
			SELECT COUNT(email) AS email_exists
			FROM user
			WHERE email = :email
EOF;

		$bind_params = array(':email' => $user->email);
		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		$continue = FALSE;
		if (is_array($result)) {
			if (isset($result[0]['email_exists']) && $result[0]['email_exists'] < 1) {
				$continue = TRUE;
			} else {
				Utilities::returnJsonResult(FALSE, 'Unable to create user. The email entered already exists.', TRUE);
			}
		}

		if ($continue) {
			$query = <<<EOF
				INSERT INTO user
				(first_name, last_name, email, password)
				VALUES
				(:first_name, :last_name, :email, :password)
EOF;

			$bind_params = array(
				':first_name' => $user->firstName,
				':last_name' => $user->lastName,
				':email' => $user->email,
				':password' => password_hash($user->password, PASSWORD_BCRYPT)
			);

			$params = array('bind_params' => $bind_params);
			$result = $this->alterQuery($query, $params);

			if ($result) {
				$userId = $this->database->lastInsertId();

				if (isset($userId) && $userId > 0) {
					$query = <<<EOF
						INSERT INTO user_role
						(user_id, role_id)
						VALUES
						(:user_id, :role_id)
EOF;

					$bind_params = array(
						':user_id' => $userId,
						':role_id' => $user->role->id,
					);
					$params = array('bind_params' => $bind_params);
					$result = $this->alterQuery($query, $params);

					if ($result) {
						$result = $this->getUser($userId);
					}
				}
			}
		}

		return $result;
	}

	public function getUserAvatar($uniqueId) {

		$query = <<<EOF
			SELECT avatar_data
			FROM user
			WHERE avatar = :uniqueId
EOF;

		$bind_params = array(':uniqueId' => $uniqueId);
		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		if ($result && $result[0]['avatar_data'] && $result[0]['avatar_data']) {
			$obj = new stdClass();
	    $obj->avatarData = $result[0]['avatar_data'];

	    return $obj;
		}

		return FALSE;
	}

  public function updateAccount() {
    $request = $this->getRequestData();

    $id = $request->userId;
    $firstName = isset($request->firstName) ? trim($request->firstName) : '';
    $lastName = isset($request->lastName) ? trim($request->lastName) : '';
    $passwordCurrent = isset($request->passwordCurrent) ? trim($request->passwordCurrent) : '';
    $passwordNew = isset($request->passwordNew) ? trim($request->passwordNew) : '';
    $passwordConfirm = isset($request->passwordConfirm) ? trim($request->passwordConfirm) : '';

    $bind_params = array(
    	':id' => $id
    );

    $errorObj = new stdClass();
    $errorObj->success = FALSE;

    if (!strlen($id)) {
    	$errorObj->msg = 'Id not found.';
			return $errorObj;
    }

    if (strlen($passwordCurrent)) {
    	if (!password_verify($passwordCurrent, $GLOBALS['current_user']->password)) {
      	$errorObj->msg = 'Your current password is not correct.';
				return $errorObj;
      }
    } else {
    	$errorObj->msg = 'Your current password is not correct.';
			return $errorObj;
    }

    $updateFirstName = '';
    if (strlen($firstName)) {
      $bind_params[':firstName'] = $firstName;
      $updateFirstName = ', first_name = :firstName';
    }

    $updateLastName = '';
    if (strlen($lastName)) {
      $bind_params[':lastName'] = $lastName;
      $updateLastName = ', last_name = :lastName';
    }

    $updatePassword = '';
    if (strlen($passwordNew) && strlen($passwordConfirm)) {
      if ($passwordNew !== $passwordConfirm) {
      	$errorObj->msg = 'Your confirm password does not match your new password.';
				return $errorObj;
      }
      $bind_params[':password'] = password_hash($passwordNew, PASSWORD_BCRYPT);
      $updatePassword = ', password = :password';
    }

    $query = <<<EOF
      UPDATE user
      SET updated_time = NOW()
      		$updateFirstName
          $updateLastName
          $updatePassword 
      WHERE id = :id
EOF;

    $params = array('bind_params' => $bind_params);

    $update_result = $this->alterQuery($query, $params);
    if ($update_result) {
      $resp = $this->getUser($id);
      return $resp;
    }
    return $update_result;

  }

}

