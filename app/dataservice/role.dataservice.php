<?php

class RoleDataService extends BaseDataService {
	
	public function getRoleByToken($token) {
		
		// grab a random tip
		$query = <<<EOF
			SELECT *
			FROM role
			WHERE MD5(CONCAT(id, bit_mask)) = :token
EOF;

		$bind_params = array(
      ':token' => $token
    );

		$params = array('bind_params' => $bind_params);
    $result = $this->selectQuery($query, $params);
	
		if (is_array($result) && isset($result[0])) {
			$role = new Role($result[0]);

			return $role;
		}
	
		return $result;
	}
	
	public function tokenizeRole($roleId) {

		$query = <<<EOF
			SELECT MD5(CONCAT(id, bit_mask)) AS roletoken
			FROM role r
			WHERE id = :roleId
EOF;

		$bind_params = array(
      ':roleId' => $roleId
    );

		$params = array('bind_params' => $bind_params);
    $result = $this->selectQuery($query, $params);

    if (isset($result) && is_array($result) && isset($result[0])) {
    	$row = $result[0];
			if (isset($row['roletoken']) && strlen($row['roletoken'])) {
				return $row['roletoken'];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
}