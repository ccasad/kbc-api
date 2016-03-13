<?php

class FormDataService extends BaseDataService {
	
	public function getForms() {

		$query = <<<EOF
			SELECT *
			FROM weekly_form wf
			ORDER BY form_date DESC
EOF;

		$result = $this->selectQuery($query);

		if (is_array($result)) {
			$forms = array();
			foreach ($result as $row) {
				$form = new Form($row);
				$forms[] = $form;
			}

			return $forms;
		}

		return $result;
	}

	public function getForm($id) {

		$query = <<<EOF
			SELECT *
			FROM weekly_form wf
			WHERE wf.id = :id
EOF;

		$bind_params = array(':id' => $id);
		$params = array('bind_params' => $bind_params);
		$result = $this->selectQuery($query, $params);

		if (is_array($result)) {
			$form = FALSE;
			foreach ($result as $row) {
				$form = new Form($row);
			}

			return $form;
		}

		return $result;
	}

	public function getFormStats($formId) {
		
		$query = <<<EOF
			SELECT *
			FROM weekly_form_stat wfs
			JOIN stat s ON wfs.stat_id = s.id
			WHERE wfs.form_id = :form_id
EOF;

    $bind_params = array(':form_id' => $formId);
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

}
