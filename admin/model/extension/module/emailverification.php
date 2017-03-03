<?php 

/**
* 
*/
class ModelExtensionModuleEmailVerification extends Model
{
	
	public function install ()
	{
		$result_length = $this->db->query("SHOW COLUMNS FROM " .DB_PREFIX . "customer LIKE 'verified'");
		if ($result_length->num_rows==0) {
			$this->db->query("ALTER TABLE " .DB_PREFIX . "customer ADD verified INT NOT NULL DEFAULT 0 AFTER approved");
			$this->db->query("UPDATE ".DB_PREFIX ."customer SET `verified` = 1 ");
		}
	}

	public function uninstall ()
	{
		$result_length = $this->db->query("SHOW COLUMNS FROM " .DB_PREFIX . "customer LIKE 'verified'");
		if ($result_length->num_rows > 0) {
			$this->db->query("ALTER TABLE " .DB_PREFIX . "customer DROP verified ");
		}
	}
}