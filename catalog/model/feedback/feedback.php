<?php

/**
* 
*/
class ModelFeedbackFeedback extends Model {
	public function getFeedbacks($data = array()) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback f LEFT JOIN " . DB_PREFIX . "feedback_description fd ON (f.feedback_id = fd.feedback_id) LEFT JOIN " . DB_PREFIX . "feedback_to_store f2s ON (f.feedback_id = f2s.feedback_id) WHERE fd.language_id = '".(int)$this->config->get('config_language_id')."' AND f2s.store_id = '".(int)$this->config->get('config_store_id')."' AND f.status = '1' ORDER BY f.date_added DESC LIMIT ".(int)$data['start'].",".(int)$data['limit']."");
		return $query->rows;
	}
	public function getFeedbackLayoutId($feedback_id) {
		$query = $this->db->query("SELECT * from ".DB_PREFIX."feedback_to_layout WHERE feedback_id = '".(int)$feedback_id."' and store_id = '".(int)$this->config->get('config_store_id')."'");
		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}
	public function getTotalFeedbacks ($data = array()) {
		$query = $this->db->query("SELECT COUNT(DISTINCT f.feedback_id) AS total FROM " . DB_PREFIX . "feedback f LEFT JOIN " . DB_PREFIX . "feedback_description fd on (f.feedback_id = fd.feedback_id) left join ".DB_PREFIX."feedback_to_store f2s on (f.feedback_id = f2s.feedback_id) WHERE fd.language_id = '".(int)$this->config->get('config_language_id')."' and f2s.store_id = '".(int)$this->config->get('config_store_id')."' and f.status = '1'");
		return $query->row['total'];
	}
	public function addFeedback ($data = array()) {
		if ($data['uid_description']) {
			$query_id = $this->db->query("INSERT INTO " . DB_PREFIX . "feedback SET status = '1'");
			$feedback_id = $this->db->getLastId();
			$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_to_layout SET feedback_id = '".$feedback_id."'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_to_store SET feedback_id = '".$feedback_id."'");
			$query = $this->db->query("INSERT INTO " . DB_PREFIX . "feedback_description SET feedback_id = '".$feedback_id."', language_id = '".$data['language_id']."', description = '" . (string) $data['uid_description'] . "'");
			$feedback_description_id = $this->db->getLastId();
			return $feedback_description_id;
		}
	}
	public function getFeedback($data=array()) {
		$query = $this->db->query("SELECT * from ". DB_PREFIX . "feedback_description WHERE feedback_id = '".$data['uid']."'");
		return $query->rows;
	}
}