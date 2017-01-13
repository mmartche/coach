<?php

class ModelCatalogFeedback extends Model {
	public function addFeedback ($data) {
		$this->event->trigger('pre.admin.feedback.add',$data);
		$this->db->query("INSERT INTO " . DB_PREFIX . "feedback SET status = '" . (int) $data['status'] . "'");
		$feedback_id = $this->db->getLastId();
		foreach ($data['feedback_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO ". DB_PREFIX . "feedback_description SET feedback_id = '" . (int) $feedback_id . "', language_id = '" . (int) $language_id . "', author = '" . $this->db->escape($value['author']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		if (isset($data['feedback_store'])) {
			foreach ($data['feedback_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_to_store SET feedback_id = '" . (int) $feedback_id . "', store_id = '" . (int) $store_id . "'");
			}
		}
		if (isset($data['feedback_layout'])) {
			foreach ($data['feedback_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_to_layout SET feedback_id = '" . (int) $feedback_id . "', store_id = '" . (int) $store_id . "', layout_id = '" . (int) $layout_id . "'");
			}
		}
		$this->event->trigger('post.admin.feedback.add',$feedback_id);
		return $feedback_id;
	}
	public function editFeedback($feedback_id, $data) {
		$this->event->trigger('pre.admin.feedback.edit', $data);
		$this->db->query("UPDATE " . DB_PREFIX . "feedback SET status = '" . (int) $data['status'] . "' WHERE feedback_id = '" . (int) $feedback_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_description WHERE feedback_id = '" . (int) $feedback_id . "'");
		foreach ($data['feedback_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_description SET feedback_id = '" . (int) $feedback_id . "', language_id = '" . (int) $language_id . "', author = '" . $this->db->escape($value['author']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_to_store WHERE feedback_id = '" . (int) $feedback_id . "'");
		if (isset($data['feedback_store'])) {
			foreach ($data['feedback_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_to_store SET feedback_id = '" . (int) $feedback_id . "', store_id = '" . (int) $store_id . "'");
			}
		}
		$this->db->query(" DELETE FROM " . DB_PREFIX . "feedback_to_layout WHERE feedback_id = '" . (int) $feedback_id . "'");
		if (isset($data['feedback_layout'])) {
			foreach ($data['feedback_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "feedback_to_layout SET feedback_id = '" . (int) $feedback_id . "', store_id = '" . (int) $store_id . "', layout_id = '" . (int) $layout_id . "'");
			}
		}
		$this->event->trigger('post.admin.feedback.edit', $feedback_id);
	}
	public function deleteFeedback($feedback_id) {
		$this->event->trigger('pre.admin.feedback.delete',$feedback_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback WHERE feedback_id = '" . (int) $feedback_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_description WHERE feedback_id = '" . (int) $feedback_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_to_store WHERE feedback_id = '" . (int) $feedback_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "feedback_to_layout WHERE feedback_id = '" . (int) $feedback_id . "'");
		$this->event->trigger('post.admin.feedback.delete',$feedback_id);
	}
	public function getFeedback($feedback_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback WHERE feedback_id = '" . (int) $feedback_id . "'");
		return $query;
	}
	public function getFeedbacks($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "feedback f LEFT JOIN " . DB_PREFIX . "feedback_description fd ON (f.feedback_id = fd.feedback_id) WHERE fd.language_id = '" . (int) $this->config->get('config_language_id') . "' LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
			$query = $this->db->query($sql);
			return $query->rows;
		} else {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback f LEFT JOIN " . DB_PREFIX . "feedback_description fd ON (f.feedback_id = fd.feedback_id) WHERE fd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY f.date_added DESC");
			$feedback_data = $query->rows;
			return $feedback_data;
		}
	}
	public function getFeedbackDescriptions($feedback_id) {
		$feedback_description_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback_description WHERE feedback_id = '" . (int) $feedback_id . "'");
		foreach ($query->rows as $result) {
			$feedback_description_data[$result['language_id']] = array(
				'author' 		=> $result['author'],
				'description'	=> $result['description']
				);
		}
		return $feedback_description_data;
	}
	public function getFeedbackStores ($feedback_id) {
		$feedback_store_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback_to_store WHERE feedback_id = '" . (int) $feedback_id . "'");
		foreach ($query->rows as $result) {
			$feedback_store_data[] = $result['store_id'];
		}
		return $feedback_store_data;
	}
	public function getFeedbackLayouts ($feedback_id) {
		$feedback_layout_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "feedback_to_layout WHERE feedback_id = '" . (int) $feedback_id . "'");
		foreach ($query->rows as $result) {
			$feedback_layout_data[$result['store_id']] = $result['layout_id'];
		}
		return $feedback_layout_data;
	}
	public function getTotalFeedbacks() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "feedback");
		return $query->row['total'];
	}
	public function getTotalFeedbackByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "feedback_to_layout WHERE layout_id = '" . (int) $layout_id . "'");
		return $query->row['total'];
	}
}