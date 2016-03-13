<?php
require_once('base.dataservice.php');

class AuthDataService extends BaseDataService {

  public function login() {
    $request = $this->getRequestData();
    $email = $request->email;
    $password = $request->password;

    $query = <<<EOF
      SELECT id, password
      FROM user u
      WHERE email = :email
EOF;

    $bind_params = array(':email' => $email);
    $params = array('bind_params' => $bind_params);
    $result = $this->selectQuery($query, $params);

    if (is_array($result)) {
      $user = false;
      if (isset($result[0]['id']) && strlen($result[0]['id']) && $result[0]['id'] > 0) {
        if (isset($result[0]['password']) && strlen($result[0]['password'])) {
          if (password_verify($password, $result[0]['password'])) {
            $userDataService = new UserDataService();
            $user = $userDataService->getUser($result[0]['id'], TRUE);
          }
        }
      }
      return $user;
    }
    return $result;
  }

  public function register() {
    $request = $this->getRequestData();

    $result = FALSE;
    $firstName = $request->firstName;
    $lastName = $request->lastName;
    $email = $request->email;
    $password = $request->password;

    // check to see if the email address is already in the database

    $query = <<<EOF
      SELECT id, password
      FROM user u
      WHERE email = :email
EOF;

    $bind_params = array(':email' => $email);
    $params = array('bind_params' => $bind_params);
    $result = $this->selectQuery($query, $params);

    $exists = FALSE;

    if (is_array($result)) {
      $user = false;

      foreach ($result as $row) {
        if (isset($row['id']) && strlen($row['id']) && $row['id'] > 0) {
          $exists = TRUE;  // account exists
        } else {
          $exists = FALSE;
        }       
      }

      if ($exists) {
        return 9999;
      } else {
        $user = new User();
        $user->firstName = $firstName;
        $user->lastName = $lastName;
        $user->email = $email;
        $user->password = $password;

        $role = new Role();
        $role->id = ROLE::ROLE_USER;
        $user->role = $role;

        $userDataService = new UserDataService();
        $user = $userDataService->addUserFromObject($user);

        Utilities::sendEmailRegistered($email);

        return $user;
      }
      return $user;
    }
    return $result;
  }

  public function resetPassword() {
    $request = $this->getRequestData();

    $success = FALSE;
    $email = $request->email;
    
    // check to see if the email exists and if it does 
    // then reset the password and then send an email

    $query = <<<EOF
      SELECT id
      FROM user u
      WHERE email = :email
EOF;

    $bind_params = array(':email' => $email);
    $params = array('bind_params' => $bind_params);
    $result = $this->selectQuery($query, $params);

    if (is_array($result)) {
      if (isset($result[0]['id']) && strlen($result[0]['id']) && $result[0]['id'] > 0) {
        $updateQuery = <<<EOF
          UPDATE user
          SET updated_time = NOW(),
              password = :password
          WHERE id = :id
EOF;

        $randomPassword = Utilities::getRandomString();

        $bind_params = array(
          ':id' => $result[0]['id'],
          ':password' => password_hash($randomPassword, PASSWORD_BCRYPT)
        );
        $params = array('bind_params' => $bind_params);
        $updateResult = $this->alterQuery($updateQuery, $params);

        if ($updateResult) {
          Utilities::sendEmailPasswordReset($email, $randomPassword);
          //return $randomPassword;
        }
      }
    }

    return $success;
  }

	public function verifyUserToken() {

    $isKeyValid = false;

    $user = $this->getUserByToken();

		if ($user && $user->id > 0) {
      $isKeyValid = true;
		}

		return $result;
  }

  public function getUserByToken($token=null) {

  	$query = <<<EOF
			SELECT *
			FROM user
			WHERE MD5(CONCAT(id, UNIX_TIMESTAMP(created_time))) = :token
EOF;

		if (!isset($token) && isset($_SERVER['HTTP_USERTOKEN'])) {
			$token = $_SERVER['HTTP_USERTOKEN'];
		}

    if (isset($token)) {
      $bind_params = array(':token' => $token);
      $params = array('bind_params' => $bind_params);

      $result = $this->selectQuery($query, $params);

      if (is_array($result)) {
        if (isset($result[0]['id']) && strlen($result[0]['id'])) {
          $userDataService = new UserDataService();
          $user = $userDataService->getUser($result[0]['id'], TRUE);
          return $user;
        }
      }
    }

		return false;
  }

  public function isAuthorized($userId) {

  	if (isset($GLOBALS['current_user']) && $GLOBALS['current_user']->id > 0) {
      // if current user is admin, allow to look at all data
      if(Utilities::isCurrentUserAdmin()) {
        return TRUE;
      }

      // if its a student just trying to look at their own data allow it
  		if ($GLOBALS['current_user']->id == $userId && ($GLOBALS['current_user']->role->id == Role::ROLE_USER)) {
  			return TRUE;
  		}
  	}

		return FALSE;
  }

}