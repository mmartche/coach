<?php

class ControllerAtivocoachAlterardados extends Controller {
	public function index()	{
		$this->load->language('ativocoach/ativocoach');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('ativocoach_description'));
		$this->document->setKeywords($this->language->get('ativocoach_keywords'));
		if (!empty($this->request->post['token'])) {
			$checkData = (!empty($this->request->post['email'])) ? $this->request->post['email'] : '';
			if ($checkData) {
				$filter_data = array (
					'email'	=> $this->request->post['email']
				);
				$this->load->model('ativocoach/ativocoach');
				$results = $this->model_ativocoach_ativocoach->changeClientData($filter_data);
				if (!empty($results)) {
					foreach ($results as $result => $key) {
						print_r(json_encode($key));
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
		} else {
			print_r('{
				"Success":false,
				"Message":"Informe Token (utilize parametro via post token=ativo)",
				Code: 0
					}');
		}
	}
}