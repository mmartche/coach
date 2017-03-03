<?php

class ControllerAtivocoachSendtoapi extends Controller {
	public function index()	{
		$this->load->language('ativocoach/ativocoach');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('ativocoach_description'));
		$this->document->setKeywords($this->language->get('ativocoach_keywords'));

		$filter_data = array (
			'email'	=> (!empty($this->request->get['email'])) ? $this->request->get['email'] : ""
		);
		$this->load->model('ativocoach/ativocoach');
		$results = $this->model_ativocoach_ativocoach->getDataToApi($filter_data);
		if ($results) {
			foreach ($results as $result => $key) {
				print_r(json_encode($key));
				print_r("<hr />");
			}
		} else {
			print_r('{
				"Success": false,
				"Message": "Sem lista de Clientes Modificados",
				"Code": 0
					}');
		}
	}
}