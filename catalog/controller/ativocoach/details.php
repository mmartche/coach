<?php

class ControllerAtivocoachDetails extends Controller {
	public function index()	{
		$this->load->language('ativocoach/ativocoach');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('ativocoach_description'));
		$this->document->setKeywords($this->language->get('ativocoach_keywords'));
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_empty'] = $this->language->get('text_empty');
		$data['button_continue'] = $this->language->get('button_continue');

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
			);
		$data['breadcrumbs'][] = array (
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('ativocoach/ativocoach')
			);
		$data['breadcrumbs'][] = array (
			'text' => $this->language->get('heading_title_details'),
			'href' => $this->url->link('ativocoach/details')
			);
		$data['ativocoachs'] = array();
		
		$filter_data = array (
			'sid' => (!empty($this->request->get['sid'])) ? $this->request->get['sid'] : $this->customer->getId(),
			'aid' => (!empty($this->request->get['aid'])) ? $this->request->get['aid'] : '0'
			);
		$this->load->model('ativocoach/ativocoach');
		$results = $this->model_ativocoach_ativocoach->getAtivocoach($filter_data);
		foreach ($results as $result) {
			$data['ativocoachs'][] = array(
				'student_email'	=> (!empty($result['email'])) ? strip_tags(html_entity_decode($result['email'], ENT_QUOTES, 'UTF-8')) : strip_tags(html_entity_decode($result['student_email'], ENT_QUOTES, 'UTF-8')),
				'student_name'	=> (!empty($result['firstname'])) ? strip_tags(html_entity_decode($result['firstname'], ENT_QUOTES, 'UTF-8')) : strip_tags(html_entity_decode($result['student_name'], ENT_QUOTES, 'UTF-8'))
				);
		}

		$data['continue'] = $this->url->link('common/home');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$data['client_logged']  = ($this->customer->isLogged()) ? "1" : "0";
		$data['client_id']		= ($this->customer->getId()) ? $this->customer->getId() : "0";
		$data['client_name']	= ($this->customer->getFirstName()) ? $this->customer->getFirstName() : "No Name";

		if (file_exists(DIR_TEMPLATE. $this->config->get('config_template').'/template/ativocoach/details.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template').'/template/ativocoach/details.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/ativocoach/details.tpl', $data));
		}
	}
}