<?php

class ControllerAtivocoachApi extends Controller {
	public function index()	{
		$this->load->language('ativocoach/ativocoach');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('ativocoach_description'));
		$this->document->setKeywords($this->language->get('ativocoach_keywords'));

		$checkData = (!empty($this->request->get['email'])) ? $this->request->get['email'] : '';
		if ($checkData) {
			$filter_data = array (
				'email'	=> $this->request->get['email']
			);
			$this->load->model('ativocoach/ativocoach');
			$results = $this->model_ativocoach_ativocoach->getAtivocoachApi($filter_data);
			if ($results) {
				foreach ($results as $result => $key) {
					var_dump($key);
					print_r("<hr />");
				}
			} else {
				print_r('{
					"Success":false,
					"Message":"Cliente não encontrado",
					Code: 0
						}');
			}
		} else {
			print_r('{
				"Success":false,
				"Message":"Insira o campo email",
				Code: 0
					}');
		}
	}
}