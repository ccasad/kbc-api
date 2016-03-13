<?php

class ReportDataService extends BaseDataService {
	
	public function getReports() {

		$query = <<<EOF
			SELECT *
			FROM lookup_report
			ORDER BY title
EOF;

		$result = $this->selectQuery($query);

		if (is_array($result)) {
			$reports = array();
			foreach ($result as $row) {
				$report = new Report($row);
				$reports[] = $report;
			}

			return $reports;
		}

		return $result;
	}

	public function getReport($id) {

		$result = FALSE;

		switch($id) {
			case 1:
				$result = $this->getListOfParticipants();
		}

		return $result;
	}

	public function getListOfParticipants() {
		$query = <<<EOF
			SELECT id, first_name, last_name, gender_mf, email, created_time
			FROM user 
			WHERE active_yn='Y'
			AND id NOT IN (3)
			ORDER BY last_name, first_name
EOF;

		$result = $this->selectQuery($query);

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
}

