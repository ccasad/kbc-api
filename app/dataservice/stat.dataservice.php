<?php

class StatDataService extends BaseDataService {
	
	public function getStats() {
		
		$query = <<<EOF
			SELECT *
			FROM stat
			ORDER BY name
EOF;

    $result = $this->selectQuery($query);

		if (is_array($result)) {
			$stats = array();
			foreach ($result as $row) {
				$stat = new Stat($row);
				$stats[] = $stat;
			}
			return $stats;
		}

		return $result;
	}
	
	public function getStat($id) {

		$query = <<<EOF
			SELECT *
			FROM stat s
			WHERE s.id = :id
EOF;

		$bind_params = array(':id' => $id);
		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			$stat = FALSE;
			foreach ($result as $row) {
				$stat = new Stat($row);
			}

			return $stat;
		}

		return $result;
	}

	public function getUserStats($userId) {
		
		$query = <<<EOF
			SELECT *
			FROM user_stat us
			JOIN stat s ON us.stat_id = s.id
			WHERE us.user_id = :user_id
			ORDER stat_date DESC
EOF;

    $bind_params = array(':user_id' => $userId);
		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			$stats = array();
			foreach ($result as $row) {
				$stat = new Stat($row);
				$stats[] = $stat;
			}
			return $stats;
		}

		return $result;
	}
	
	public function getUserStat($userId, $userStatId) {

		$query = <<<EOF
			SELECT *
			FROM user_stat us
			JOIN stat s ON us.stat_id = s.id
			WHERE us.user_id = :user_id
			AND us.id = :user_stat_id
EOF;

		$bind_params = array(
			':user_id' => $userId,
			':user_stat_id' => $userStatId
		);
		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			$stat = FALSE;
			foreach ($result as $row) {
				$stat = new Stat($row);
			}

			return $stat;
		}

		return $result;
	}

	public function getUserStatGoal($userId, $userStatId) {

		$query = <<<EOF
			SELECT goal
			FROM user_stat_goal us
			WHERE us.user_id = :user_id
			AND us.stat_id = :user_stat_id
EOF;

		$bind_params = array(
			':user_id' => $userId,
			':user_stat_id' => $userStatId
		);
		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			$goal = '';
			foreach ($result as $row) {
				$goal = $row['goal'];
			}

			return array('goal'=>$goal);
		}

		return $result;
	}

	public function getAllUserStats() {
		
		$request = $this->getRequestData();
    
    $userId = isset($request->userId) ? $request->userId : '';
    $statId = isset($request->statId) ? $request->statId : '';
    $orderBy = isset($request->orderBy) ? $request->orderBy : 'stat_date';
    $orderDir = isset($request->orderDir) ? $request->orderDir : 'DESC';
    $prOnly = (isset($request->prOnly) && $request->prOnly == 'true') ? true : false;

    $bind_params = array();

    $userSql = '';
    $prSql = '';
    if (strlen($userId)) {
    	$userSql = ' AND u.id = :user_id';
    	$bind_params[':user_id'] = $userId;
    	//$prSql = ', IF(us.stat_value = ( SELECT IF(us.stat_id NOT IN (4, 5, 10, 12, 13, 14, 15), MAX(stat_value), MIN(stat_value)) FROM user_stat us2 WHERE user_id = :user_id2 AND stat_id = us.stat_id ), \'true\', \'false\') AS is_personal_record';
    	
    	$prSql = <<<EOF
				, IF(us.stat_value = 
					( SELECT 
						IF(us2.stat_id NOT IN (4, 10, 12, 13, 14, 15), 
							MAX(CAST(us2.stat_value AS DECIMAL(5,1))), 
				            IF(us2.stat_id IN (4, 15), 
								MIN(CAST(REPLACE(us2.stat_value, ':', '.') AS UNSIGNED)), 
				                IF(us2.stat_id IN (10, 11, 12, 13, 14),
									MIN(CAST(us2.stat_value AS DECIMAL(5,1))),
				                    MIN(CAST(us2.stat_value AS UNSIGNED))))) 
						FROM user_stat us2 
						WHERE us2.user_id = :user_id2
				        AND us2.stat_id = us.stat_id ), 'true', 'false') AS is_personal_record
EOF;
    	$bind_params[':user_id2'] = $userId;
    }

    $statSql = '';
    if (strlen($statId)) {
    	$statSql = ' AND s.id = :stat_id';
    	$bind_params[':stat_id'] = $statId;
    }
    
    switch (strtolower($orderBy)) {
    	case 'statname':
    		$orderBy = 'stat_name';
    		break;
    	case 'statdate':
    		$orderBy = 'stat_date';
    		break;
    	case 'statvalue':
    		$orderBy = 'stat_value';
    		break;
    }

		$query = <<<EOF
			SELECT u.*, us.*, us.id AS user_stat_id, s.name AS stat_name
			{$prSql}
			FROM user_stat us
			JOIN user u ON us.user_id = u.id {$userSql}
			JOIN stat s ON us.stat_id = s.id {$statSql}
			ORDER BY {$orderBy} {$orderDir}
EOF;

		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			$objs = array();
			foreach ($result as $row) {
				$obj = new stdClass();
		    $obj->userId = $row['user_id'];
		    $obj->userFirstName = $row['first_name'];
		    $obj->userLastName = $row['last_name'];
		    $obj->userFullName = $row['first_name'].' '.$row['last_name'];
		    $obj->avatar = $row['avatar'];
		    $obj->userStatId = $row['user_stat_id'];
		    $obj->statDate = $row['stat_date'];
		    $obj->statValue = $row['stat_value'];
		    $obj->statInfo = $row['stat_info'];

		    $obj->statValueInfo = $obj->statValue . ' (' . $row['stat_info'] . ')';

		    $obj->statId = $row['stat_id'];
		    $obj->statName = $row['stat_name'];

		    $obj->isPersonalRecord = (isset($row['is_personal_record']) && $row['is_personal_record'] == 'true') ? true : false;
		    
		    if ($prOnly) {
		    	if ($obj->isPersonalRecord) {
		    		$objs[] = $obj;
		    	}
		    } else {
		    	$objs[] = $obj;
		    }
			}
			return $objs;
		}

		return $result;
	}

	public function addUserStat() {
		
		$errorObj = new stdClass();
    $errorObj->success = FALSE;

		$request = $this->getRequestData();
    
    $userId = isset($request->userId) ? $request->userId : '';
    $statId = isset($request->statId) ? $request->statId : '';
    $statDate = isset($request->statDate) ? $request->statDate : '';
    $statValue = isset($request->statValue) ? $request->statValue : '';
    $statInfo = isset($request->statInfo) ? $request->statInfo : '';
    $statComment = isset($request->statComment) ? $request->statComment : '';

    $query = <<<EOF
			SELECT COUNT(*) AS stat_exists
			FROM user_stat us
			WHERE user_id = :userId
			AND stat_id = :statId 
			AND stat_date = CAST(:statDate AS DATE)
			AND stat_value = :statValue
EOF;

		$params = array(
      'bind_params' => array(
        ':userId' => $userId,
        ':statId' => $statId,
        ':statDate' => $statDate,
        ':statValue' => $statValue
      )
    );
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			if ($result[0]['stat_exists'] > 0) {
				$errorObj->msg = 'User stat already exists.';
				return $errorObj;
			}
		}

    $query = <<<EOF
    	INSERT INTO user_stat (stat_id, user_id, stat_date, stat_value, stat_info, stat_comment)
      VALUES (:statId, :userId, CAST(:statDate AS DATE), :statValue, :statInfo, :statComment)
EOF;
    
    $params = array(
      'bind_params' => array(
        ':userId' => $userId,
        ':statId' => $statId,
        ':statDate' => $statDate,
        ':statValue' => $statValue,
        ':statInfo' => $statInfo,
        ':statComment' => $statComment,
      )
    );

    $result = $this->alterQuery($query, $params);
    
    if($result) {
			$id = $this->lastInsertId();
		} else {
			$errorObj->msg = 'Unable to create user stat id.';
			return $errorObj;
		}
		
		return array('newUserStatId' => $id);
  }

  public function updateUserStat() {
		
		$errorObj = new stdClass();
    $errorObj->success = FALSE;

		$request = $this->getRequestData();
    
    $userStatId = isset($request->userStatId) ? $request->userStatId : '';
    $statId = isset($request->statId) ? $request->statId : '';
    $statDate = isset($request->statDate) ? $request->statDate : '';
    $statValue = isset($request->statValue) ? $request->statValue : '';
    $statInfo = isset($request->statInfo) ? $request->statInfo : '';
    $statComment = isset($request->statComment) ? $request->statComment : '';

    $result = FALSE;

    if (strlen($userStatId)) {
	    $query = <<<EOF
	    	UPDATE user_stat 
	    	SET stat_id = :statId,  
	    			stat_date = CAST(:statDate AS DATE), 
	    			stat_value = :statValue, 
	    			stat_info = :statInfo, 
	    			stat_comment = :statComment
	    	WHERE id = :userStatId
EOF;
	    
	    $params = array(
	      'bind_params' => array(
	        ':userStatId' => $userStatId,
	        ':statId' => $statId,
	        ':statDate' => $statDate,
	        ':statValue' => $statValue,
	        ':statInfo' => $statInfo,
	        ':statComment' => $statComment,
	      )
	    );

	    $result = $this->alterQuery($query, $params);
    }
    
    if(!$result) {
			$errorObj->msg = 'Unable to update user stat id.';
			return $errorObj;
		}
		
		return true;
  }

  public function updateUserStatGoal() {
		
		$errorObj = new stdClass();
    $errorObj->success = FALSE;

		$request = $this->getRequestData();
    
    $statId = isset($request->statId) ? $request->statId : '';
    $userId = isset($request->userId) ? $request->userId : '';
    $goal = isset($request->goal) ? $request->goal : '';
    
    $result = FALSE;

    $query = <<<EOF
			SELECT COUNT(*) AS stat_exists
			FROM user_stat_goal usg
			WHERE user_id = :userId
			AND stat_id = :statId 
EOF;

		$params = array(
      'bind_params' => array(
        ':userId' => $userId,
        ':statId' => $statId,
      )
    );
		$result = $this->selectQuery($query, $params);

		$exists = FALSE;
		if (is_array($result)) {
			if ($result[0]['stat_exists'] > 0) {
				$exists = TRUE;
			}
		}

    if ($exists) {
	    $query = <<<EOF
	    	UPDATE user_stat_goal
	    	SET goal = :goal,
	    			updated_time = CURRENT_TIMESTAMP
	    	WHERE stat_id = :statId
	    	AND user_id = :userId
EOF;
	  } else {
	  	$query = <<<EOF
	  		INSERT INTO user_stat_goal (user_id, stat_id, goal) 
	  			VALUES (:userId, :statId, :goal)
EOF;
	  }

	  $params = array(
      'bind_params' => array(
        ':userId' => $userId,
        ':statId' => $statId,
        ':goal' => $goal,
      )
    );
	  $result = $this->alterQuery($query, $params);
    
    if(!$result) {
			$errorObj->msg = 'Unable to update user stat goal.';
			return $errorObj;
		}
		
		return array('goal' => $goal);
  }

  public function deleteUserStat($userStatId, $userId) {
		
		$errorObj = new stdClass();
    $errorObj->success = FALSE;

    $result = FALSE;

    if (strlen($userStatId) && strlen($userId)) {
	    $query = <<<EOF
	    	DELETE FROM user_stat 
	    	WHERE id = :userStatId
	    	AND user_id = :userId
EOF;
	    
	    $params = array(
	      'bind_params' => array(
	        ':userStatId' => $userStatId,
	        ':userId' => $userId
	      )
	    );

	    $result = $this->alterQuery($query, $params);
    }
    
    if(!$result) {
			$errorObj->msg = 'Unable to delete user stat id.';
			return $errorObj;
		}
		
		return true;
  }

  public function addStat() {		
		$errorObj = new stdClass();
    $errorObj->success = FALSE;

		$request = $this->getRequestData();
    
    $statName = isset($request->statName) ? $request->statName : '';
    $statDescription = isset($request->statDescription) ? $request->statDescription : '';
    $statType = isset($request->statType) ? $request->statType : '';

    $query = <<<EOF
			SELECT COUNT(*) AS stat_exists
			FROM stat s
			WHERE UPPER(name) = :statName 
EOF;

		$params = array(
      'bind_params' => array(
        ':statName' => strtoupper($statName),
      )
    );
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			if ($result[0]['stat_exists'] > 0) {
				$errorObj->msg = 'Stat already exists.';
				return $errorObj;
			}
		}

    $query = <<<EOF
    	INSERT INTO stat (name, description, form_element_id)
      VALUES (:statName, :statDescription, :statType)
EOF;
    
    $params = array(
      'bind_params' => array(
        ':statName' => $statName,
        ':statDescription' => $statDescription,
        ':statType' => $statType,
      )
    );

    $result = $this->alterQuery($query, $params);
    
    if($result) {
			$id = $this->lastInsertId();
		} else {
			$errorObj->msg = 'Unable to create stat';
			return $errorObj;
		}
		
		return array('newStatId' => $id);
  }

  public function updateStat() {
		
		$errorObj = new stdClass();
    $errorObj->success = FALSE;

		$request = $this->getRequestData();
    
    $statId = isset($request->statId) ? $request->statId : '';
    $statName = isset($request->statName) ? $request->statName : '';
    $statDescription = isset($request->statDescription) ? $request->statDescription : '';
    $statType = isset($request->statType) ? $request->statType : '';

    $result = FALSE;

    if (strlen($statId)) {
	    $query = <<<EOF
	    	UPDATE stat 
	    	SET name = :statName, 
	    			description = :statDescription, 
	    			form_element_id = :statType
	    	WHERE id = :statId
EOF;
	    
	    $params = array(
	      'bind_params' => array(
	        ':statId' => $statId,
	        ':statName' => $statName,
        	':statDescription' => $statDescription,
        	':statType' => $statType,
	      )
	    );

	    $result = $this->alterQuery($query, $params);
    }
    
    if(!$result) {
			$errorObj->msg = 'Unable to update stat';
			return $errorObj;
		}
		
		return true;
  }
}
