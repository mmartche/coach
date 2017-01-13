<?php

class ControllerAtivocoachAtivocoach extends Controller {
	public function index()	{

		if ($this->customer->getGroupId() != '3') {
			if ($this->customer->getGroupId() != '2') {
				$this->response->redirect($this->url->link('ativocoach/details','token=' . $this->session->data['token'].'&aid=0&sid='.$this->customer->getGroupId() . $url, 'SSL'));
			} else {
				// $this->response->redirect($this->url->link('ativocoach/ativocoach','token=' . $this->session->data['token'], 'SSL'));
			}
		}

		$this->load->language('ativocoach/ativocoach');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('ativocoach_description'));
		$this->document->setKeywords($this->language->get('ativocoach_keywords'));
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_empty'] = $this->language->get('text_empty');
		$data['button_continue'] = $this->language->get('button_continue');
		$data['text_accepted_success'] = $this->language->get('text_accepted_success');
		$data['text_accepted_error'] = $this->language->get('text_accepted_error');
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
			'href' => $this->url->link('ativocoach/ativocoach')
			);
		$data['ativocoachs'] = array();
		$limit = $this->config->get('theme_default_product_limit');
		
		if ($this->customer->getGroupId() == '3') {
			//check if is assess
		}

		$filter_data = array(
			'start'	=> ($page - 1) * $limit,
			'limit'	=> $limit,
			'my_id'	=> $this->customer->getId(),
			'tid'	=> (!empty($this->request->get['tid'])) ? $this->request->get['tid'] : ''
			);
		$this->load->model('ativocoach/ativocoach');
		$data['check_permission'] = $this->model_ativocoach_ativocoach->getPermission($this->customer->getId());
		$ativocoach_total = $this->model_ativocoach_ativocoach->getTotalAtivocoachs($filter_data);
		$results = $this->model_ativocoach_ativocoach->getAtivocoachs($filter_data);
		foreach ($results as $result) {
			$data['ativocoachs'][] = array(
				'ativocoach_id' 	=> $result['ativocoach_id'],
				'coach_id'			=> $result['coach_id'],
				'student_id'		=> $result['student_id'],
				'student_email'		=> (!empty($result['student_email'])) ? $result['student_email'] : strip_tags(html_entity_decode($result['email'], ENT_QUOTES, 'UTF-8')),
				'student_name'		=> (!empty($result['student_name'])) ? $result['student_name'] : strip_tags(html_entity_decode($result['firstname'], ENT_QUOTES, 'UTF-8')),
				'student_accepted'	=> $result['student_accepted'],
				'url_details'		=> ($this->customer->getGroupId() == '3') ? $this->url->link('ativocoach/ativocoach', '&sid='.$result['student_id'].'&aid='.$result['ativocoach_id']) : $this->url->link('ativocoach/details', '&sid='.$result['student_id'].'&aid='.$result['ativocoach_id']),
				'url_disable'		=> $this->url->link('ativocoach/ativocoach/disableInvite', '&aid='.$result['ativocoach_id'].'&sid='.$result['student_id'], 'SSL')
				);
		}
		$url = '';
		if (isset($this->request->get['limit'])) {
			$url .= '&limit='.$this->request->get['limit'];
		}
		$pagination = new Pagination();
		$pagination->total = $ativocoach_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('ativocoach/ativocoach', $url.'&page={page}');
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf(
			$this->language->get('text_pagination'), 
			($ativocoach_total) ? (($page - 1) * $limit) + 1 : 1,
			((($page - 1) * $limit) > ($ativocoach_total - $limit)) ? $ativocoach_total : ((($page - 1) * $limit) + $limit),
			$ativocoach_total, 
			ceil($ativocoach_total / $limit));
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

		$data['url_details'] = $this->url->link('ativocoach/details');
		$data['action'] = $this->url->link('ativocoach/ativocoach/invite', $url, 'SSL');

		$data['groupId'] = $this->customer->getGroupId();
		$groupData = array(
			'group_id' => (int)$this->customer->getGroupId(),
			'language_id' => (int)$this->config->get('config_language_id')
			);
		$data['groupName'] = $this->model_ativocoach_ativocoach->getGroupName($groupData);
		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (file_exists(DIR_TEMPLATE. $this->config->get('config_template').'/template/ativocoach/ativocoach.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template').'/template/ativocoach/ativocoach.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/ativocoach/ativocoach.tpl', $data));
		}
	}
		public function invite() {
			$this->load->language('ativocoach/ativocoach');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->model('ativocoach/ativocoach');
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				$filter_uid = array (
					'coach_id'		=> $this->customer->getId(),
					'student_name'	=> $this->request->post['student_name'],
					'language_id'	=> (int)$this->config->get('config_language_id'),
					'student_email'	=> $this->request->post['student_email']
					);
				$this->model_ativocoach_ativocoach->inviteAtivocoach($filter_uid);
				$this->session->data['success'] = $this->language->get('text_invite_student_success');
				$url='';
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				$this->response->redirect($this->url->link('ativocoach/ativocoach','token=' . $this->session->data['token'] . $url, 'SSL'));
			}
		}
		public function disableInvite(){
			$this->load->language('ativocoach/ativocoach');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->model('ativocoach/ativocoach');
			if (($this->request->server['REQUEST_METHOD'] == 'GET')) {
				$filter_uid = array (
					'ativocoach_id'	=> $this->request->get['aid'],
					'coach_id'		=> $this->customer->getId(),
					'student_id'	=> $this->request->get['sid'],
					'language_id'	=> (int)$this->config->get('config_language_id')
					);
				$returnDisable = $this->model_ativocoach_ativocoach->disableInviteAtivocoach($filter_uid);
				var_dump($returnDisable);
				if ($returnDisable) {
					$this->session->data['success'] = $this->language->get('text_disableInvite_student_success');
				} else {
					$this->session->data['error_warning'] = $this->language->get('text_disableInvite_student_error');
				}
				$url='';
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				$this->response->redirect($this->url->link('ativocoach/ativocoach','token=' . $this->session->data['token'] . $url, 'SSL'));
			}
		}
}