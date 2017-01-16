<?php

/*
** to do
qndo convida treinador, nao exibe os dados atualizados

*/

class ControllerAtivocoachAtivocoach extends Controller {
	public function index()	{

		if ($this->customer->getGroupId() != '3') {
			if ($this->customer->getGroupId() != '2') {
				$this->response->redirect($this->url->link('ativocoach/details','token=' . $this->session->data['token'].'&aid=0&sid='.$this->customer->getId() . $url, 'SSL'));
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
		
		$this->load->model('ativocoach/ativocoach');
		$filter_data = array(
			'start'		=> ($page - 1) * $limit,
			'limit'		=> $limit,
			'my_id'		=> $this->customer->getId(),
			'my_group'	=> $this->customer->getGroupId(),
			'sid'		=> (!empty($this->request->get['sid'])) ? $this->request->get['sid'] : ''
			);
		$sid_txt = (!empty($this->request->get['sid'])) ? $this->request->get['sid'] : $this->customer->getId();
		$checkPermission = $this->model_ativocoach_ativocoach->getPermission($filter_data);
		// if ($checkPermission) {
			$ativocoach_total = $this->model_ativocoach_ativocoach->getTotalAtivocoachs($filter_data);
			$results = $this->model_ativocoach_ativocoach->getAtivocoachs($filter_data);
			foreach ($results as $result) {
				if ($this->customer->getGroupId() == '3') {
					if (!empty($this->request->get['sid'])) {
						$url_details = $this->url->link('ativocoach/details', '&sid='.$result['student_id'].'&aid='.$result['ativocoach_id']);
					} else {
						$url_details = $this->url->link('ativocoach/ativocoach', '&sid='.$result['student_id'].'&aid='.$result['ativocoach_id']);
					}
				} else {
					$url_details = $this->url->link('ativocoach/details', '&sid='.$result['student_id'].'&aid='.$result['ativocoach_id']);
				}
				$data['ativocoachs'][] = array(
					'ativocoach_id' 	=> $result['ativocoach_id'],
					'my_id'				=> $this->customer->getId(),
					'coach_id'			=> $result['coach_id'],
					'student_id'		=> $result['student_id'],
					'student_email'		=> (!empty($result['student_email'])) ? $result['student_email'] : strip_tags(html_entity_decode($result['email'], ENT_QUOTES, 'UTF-8')),
					'student_name'		=> (!empty($result['student_name'])) ? $result['student_name'] : strip_tags(html_entity_decode($result['firstname'], ENT_QUOTES, 'UTF-8')),
					'student_accepted'	=> $result['student_accepted'],
					'url_details'		=> $url_details,
					'url_disable'		=> $this->url->link('ativocoach/ativocoach/disableInvite', '&aid='.$result['ativocoach_id'].'&cid_txt='.$sid_txt.'&sid='.$result['student_id'].'&cid='.$result['coach_id'].'&token='.$this->session->data['token'], 'SSL')
					);
				$data['coach_id'] = $result['coach_id'];
			}
		// } 
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
		if (empty($data['coach_id'])) $data['coach_id'] = $this->customer->getId();

		if (file_exists(DIR_TEMPLATE. $this->config->get('config_template').'/template/ativocoach/ativocoach.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template').'/template/ativocoach/ativocoach.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/ativocoach/ativocoach.tpl', $data));
		}
	}
		public function invite() {
			//to do : send invite by email
			$this->load->language('ativocoach/ativocoach');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->model('ativocoach/ativocoach');
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				$filter_uid = array (
					'coach_id'		=> $this->customer->getId(),
					'cid_txt'		=> $this->request->post['coach_id'],
					'student_name'	=> $this->request->post['student_name'],
					'language_id'	=> (int)$this->config->get('config_language_id'),
					'student_email'	=> $this->request->post['student_email']
					);
				if ($this->model_ativocoach_ativocoach->inviteAtivocoach($filter_uid)) {
					$this->session->data['success'] = $this->language->get('text_invite_student_success');
				} else {
					$this->session->data['error_warning'] = $this->language->get('text_invite_student_error');
				}
				$url='';
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				$this->response->redirect($this->url->link('ativocoach/ativocoach','sid='.$this->request->post['coach_id'].'&token=' . $this->session->data['token'] . $url, 'SSL'));
			}
		}
		public function disableInvite(){
			$this->load->language('ativocoach/ativocoach');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->model('ativocoach/ativocoach');
			if (($this->request->server['REQUEST_METHOD'] == 'GET')) {
				$filter_uid = array (
					'ativocoach_id'	=> $this->request->get['aid'],
					'my_id'			=> $this->customer->getId(),
					'group_id'		=> $this->customer->getGroupId(),
					'coach_id'		=> $this->request->get['cid'],
					'cid_txt'		=> $this->request->get['cid_txt'],
					'student_id'	=> $this->request->get['sid'],
					'language_id'	=> (int)$this->config->get('config_language_id')
					);
				$returnDisable = $this->model_ativocoach_ativocoach->disableInviteAtivocoach($filter_uid);
				if ($returnDisable) {
					$this->session->data['success'] = $this->language->get('text_disableInvite_student_success');
				} else {
					$this->session->data['error_warning'] = $this->language->get('text_disableInvite_student_error');
				}
				$url='';
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				$this->response->redirect($this->url->link('ativocoach/ativocoach','sid='.$this->request->get['cid_txt'].'&token=' . $this->session->data['token'] . $url, 'SSL'));
			}
		}
}