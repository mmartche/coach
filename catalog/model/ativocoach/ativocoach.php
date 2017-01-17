<?php

/**
* 
*/
class ModelAtivocoachAtivocoach extends Model {
	public function getPermission($data = array()){
		$query = $this->db->query("SELECT COUNT(DISTINCT c.ativocoach_id) as id FROM " . DB_PREFIX . "ativocoach c WHERE c.coach_id = '".$data['my_id']."' and c.student_id = '".$data['sid']."'");
		if ($query->num_rows) {
			return true;
		} else {
			return false;
		}
	}
	public function getGroupName($data = array()){
		$query = $this->db->query("SELECT description FROM " . DB_PREFIX . "customer_group_description WHERE customer_group_id = '".$data['group_id']."' and language_id = '".$data['language_id']."'");
		if ($query->num_rows) {
			return $query->row['description'];
		} else {
			return false;
		}
	}
	public function getAtivocoachs($data = array()) {
		if ($data['my_group'] == 3 && !empty($data['sid'])) {
			$query = $this->db->query("SELECT b.*, c.* FROM " . DB_PREFIX . "ativocoach a left join " . DB_PREFIX ."ativocoach b on (a.student_id = b.coach_id) left join oc_customer c
		on b.student_id = c.customer_id WHERE b.status = '1' and a.coach_id = '".$data['my_id']."' and a.student_id = '".$data['sid']."' ORDER BY a.date_added DESC LIMIT ".(int)$data['start'].",".(int)$data['limit']."");
		} else {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ativocoach f left join " . DB_PREFIX ."customer c on (f.student_id = c.customer_id) WHERE f.status = '1' and f.coach_id = '".$data['my_id']."' ORDER BY f.date_added DESC LIMIT ".(int)$data['start'].",".(int)$data['limit']."");
		}
		return $query->rows;
	}
	public function getAtivocoach($data = array()) {
		//TO DO : checkPermission()
		if ($data['sid'] != 0) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer c WHERE c.customer_id = '".$data['sid']."'");
			return $query->rows;
		} elseif ($data['aid'] != 0) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ativocoach c WHERE c.ativocoach_id = '".$data['aid']."'");
			return $query->rows;
		} elseif ($data['my_id'] != 0) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer c WHERE c.customer_id = '".$data['my_id']."'");
			return $query->rows;
		} else {
			return false;
		}
	}
	public function getTotalAtivocoachs ($data = array()) {
		$query = $this->db->query("SELECT COUNT(DISTINCT f.ativocoach_id) AS total FROM " . DB_PREFIX . "ativocoach f WHERE f.status = '1' and f.coach_id = '".$data['my_id']."'");
		return $query->row['total'];
	}
	public function inviteAtivocoach ($data = array()) {
		if ($data['coach_id'] || $data['coach_id'] != 0) {
			if ($data['coach_id'] != $data['cid_txt']) {
				$query = $this->db->query("SELECT student_id FROM " . DB_PREFIX . "ativocoach WHERE coach_id = '".$data['coach_id']."' and student_id = '".$data['cid_txt']."'");
				if ($query->num_rows) {
					$coach_id = $query->row['student_id'];
				} else {
					return false;
				}
			} else {
				$coach_id = $data['coach_id'];
			}
			$query_student = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "customer where email = '".$data['student_email']."'");
			if ($query_student->num_rows) {
				$student_id = $query_student->row['customer_id'];
			} else {
				$student_id = 0;
			}
			$query_id = $this->db->query("INSERT INTO " . DB_PREFIX . "ativocoach SET status = '1', coach_id = '".$coach_id."', student_id = '".$student_id."', student_email = '".$data['student_email']."', student_name = '".$data['student_name']."'");
			// $ativocoach_id = $this->db->getLastId();
			return true;
		} else {
			return false;
		}

	}
	public function disableInviteAtivocoach ($data = array()) {
		if (!empty($data['ativocoach_id']) || !empty($data['my_id']) || !empty($data['student_id'])) {
			if (($data['my_id'] != $data['coach_id']) && ($data['group_id'] == 3)) {
				$query_check = $this->db->query("SELECT b.ativocoach_id as id FROM " . DB_PREFIX ."ativocoach a LEFT JOIN " . DB_PREFIX ."ativocoach b ON (a.student_id = b.coach_id) WHERE a.student_id = '".$data['coach_id']."' and a.coach_id = '".$data['my_id']."'");
				if ($query_check->num_rows) {
					$query = $this->db->query("UPDATE " . DB_PREFIX . "ativocoach SET status='0' WHERE ativocoach_id='".$data['ativocoach_id']."' and coach_id = '".$data['cid_txt']."' and student_id = '".$data['student_id']."'");
					if ($this->db->countAffected() > 0){
						return true;
					} else {
						return false;
					}
				}
			} else {
				$query = $this->db->query("UPDATE " . DB_PREFIX . "ativocoach SET status='0' WHERE ativocoach_id='".$data['ativocoach_id']."' and coach_id = '".$data['my_id']."' and student_id = '".$data['student_id']."'");
				if ($this->db->countAffected() > 0){
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}
	public function getCoachs($data = array()){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ativocoach a LEFT JOIN " . DB_PREFIX . "customer c ON (a.coach_id = c.customer_id) WHERE a.student_email = '".$data['my_email']."'");
		return $query->rows;
	}
	public function confirmInvite($data = array()) {
		$query_check = $this->db->query("SELECT * FROM " . DB_PREFIX . "ativocoach WHERE ativocoach_id = '".$data['ativocoach_id']."' and (student_id = '".$data['my_id']."' or student_email = '".$data['email']."') and coach_id = '".$data['coach_id']."'");
		if ($query_check) {
			// return("UPDATE " . DB_PREFIX . "ativocoach SET student_accepted = '1' and student_id = '".$data['my_id']."' WHERE ativocoach_id = '".$query_check->row['ativocoach_id']."'");
			$query_update_invite = $this->db->query("UPDATE " . DB_PREFIX . "ativocoach SET student_accepted = '1', student_id = '".$data['my_id']."' WHERE ativocoach_id = '".$query_check->row['ativocoach_id']."'");
			// $updateCoachId = $this->db->getLastId();
			if ($this->db->countAffected() > 0) { return true; }
			else { return false; }
		}
		return false;
	}
}























