<?php

/**
* 
*/
class ControllerFeedbackFeedback extends Controller {
	public function index()	{
		$this->load->language('feedback/feedback');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('feedback_description'));
		$this->document->setKeywords($this->language->get('feedback_keywords'));
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_empty'] = $this->language->get('text_empty');
		$data['button_continue'] = $this->language->get('button_continue');
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
			);
		$data['breadcrumbs'][] = array (
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('feedback/feedback')
			);
		$data['feedbacks'] = array();
		$limit = $this->config->get('theme_default_product_limit');
		// var_dump($this->config);
		
		$filter_data = array(
			'start' => ($page - 1) * $limit,
			'limit' => $limit
			);
		$this->load->model('feedback/feedback');
		$feedback_total = $this->model_feedback_feedback->getTotalFeedbacks($filter_data);
		$results = $this->model_feedback_feedback->getFeedbacks($filter_data);
		foreach ($results as $result) {
			$data['feedbacks'][] = array(
				'feedback_id' 	=> $result['feedback_id'],
				'author' 		=> $result['author'],
				'url'			=> $this->url->link('feedback/details', '&uid='.$result['feedback_id']),
				'description' 	=> strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))
				);
		}
		$url = '';
		if (isset($this->request->get['limit'])) {
			$url .= '&limit='.$this->request->get['limit'];
		}
		$pagination = new Pagination();
		$pagination->total = $feedback_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('feedback/feedback', $url.'&page={page}');
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf(
			$this->language->get('text_pagination'), 
			($feedback_total) ? (($page - 1) * $limit) + 1 : 1,
			((($page - 1) * $limit) > ($feedback_total - $limit)) ? $feedback_total : ((($page - 1) * $limit) + $limit),
			$feedback_total, 
			ceil($feedback_total / $limit));
		$data['limit'] = $limit;

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

		$data['url_details'] = $this->url->link('feedback/details');
		$data['action'] = $this->url->link('feedback/feedback/add', 'token='.$this->session->data['token'].$url, 'SSL');


		if (file_exists(DIR_TEMPLATE. $this->config->get('config_template').'/template/feedback/feedback.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template').'/template/feedback/feedback.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/feedback/feedback.tpl', $data));
		}
	}
		public function add() {
			$this->load->language('feedback/feedback');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->model('feedback/feedback');
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				$filter_uid = array (
					'uid_description' 	=> $this->request->post['uid_description'],
					'language_id'			=> (int)$this->config->get('config_language_id')
					);
				$this->model_feedback_feedback->addFeedback($filter_uid);
				$this->session->data['success'] = $this->language->get('text_success');
				$url='';
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				$this->response->redirect($this->url->link('feedback/feedback','token=' . $this->session->data['token'] . $url, 'SSL'));
			}
		}
}