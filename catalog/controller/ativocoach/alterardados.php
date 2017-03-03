<?php

class ControllerAtivocoachAlterardados extends Controller {
	public function index()	{
		$this->load->language('ativocoach/ativocoach');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('ativocoach_description'));
		$this->document->setKeywords($this->language->get('ativocoach_keywords'));
		if (!empty($this->request->post['token'])) {
			$checkEmail = (!empty($this->request->post['email'])) ? trim($this->request->post['email']) : '';
			$checkPassword = (!empty($this->request->post['password'])) ? trim($this->request->post['password']) : '';
			if ($checkEmail && $checkPassword) {
				$this->customer->logout();
				$this->customer->login($checkEmail, $checkPassword, $override=false);
				$logInData = $this->customer->isLogged();
				if ($logInData) {
					$filter_data = array (
						'email'			=> $this->request->post['email'],
						'customer_id'	=> $this->customer->getId()
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
							"Code": 0
								}');
					}
				} else {
					print_r('{
						"Success":false,
						"Message":"Login incorreto",
						"Code": 0
							}');
				}
			} else {
				print_r('{
					"Success":false,
					"Message":"Campo email ou senha não foram informados",
					"Code": 0
						}');
			}
		} else {
			print_r('{
				"Success":false,
				"Message":"Informe Token (utilize parametro via post token=ativo)",
				"Code": 0
					}');
		}
		$this->customer->logout();
	}
}