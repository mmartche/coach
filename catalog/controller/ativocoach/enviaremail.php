<?php

class ControllerAtivocoachEnviaremail extends Controller {
	public function index()	{
		$this->load->language('ativocoach/ativocoach');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('ativocoach_description'));
		$this->document->setKeywords($this->language->get('ativocoach_keywords'));
		if (!empty($this->request->post['token'])) {
			$checkData = (!empty($this->request->post['email'])) ? $this->request->post['email'] : '';
			if ($checkData) {

				$subject = "assunto";
				$message = "manesadasod";
				$mail = new Mail();
				$mail->setTo($checkData);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($this->config->get('config_name'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();


				$filter_data = array (
					'email'	=> $this->request->post['email']
				);
				$this->load->model('ativocoach/ativocoach');
				$results = $this->model_ativocoach_ativocoach->sendEmailAtivocoach($filter_data);
				if ($results) {
					// print_r('{"Success":true}');
					// foreach ($results as $result => $key) {
						print_r(json_encode($results));
					// }
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