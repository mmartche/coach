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
			'sid' 			=> (!empty($this->request->get['sid'])) ? $this->request->get['sid'] : '0',
			'my_id'			=> $this->customer->getId(),
			'student_email'	=> $this->customer->getEmail(),
			'aid' 			=> (!empty($this->request->get['aid'])) ? $this->request->get['aid'] : '0'
			);
		$this->load->model('ativocoach/ativocoach');
		$results = $this->model_ativocoach_ativocoach->getAtivocoach($filter_data);
		if ($results) {
			foreach ($results as $result) {
				$data['ativocoachs'][] = array(
					'student_email'	=> (!empty($result['email'])) ? strip_tags(html_entity_decode($result['email'], ENT_QUOTES, 'UTF-8')) : strip_tags(html_entity_decode($result['student_email'], ENT_QUOTES, 'UTF-8')),
					'student_name'	=> (!empty($result['firstname'])) ? strip_tags(html_entity_decode($result['firstname'], ENT_QUOTES, 'UTF-8')) : strip_tags(html_entity_decode($result['student_name'], ENT_QUOTES, 'UTF-8'))
					);
			}
		}
		$myData = array(
			'my_id' 	=> $this->customer->getId(),
			'my_email'	=> $this->customer->getEmail()
			);
		$data['inviteCoachs'] = array();
		$resultInviteCoachs = $this->model_ativocoach_ativocoach->getCoachs($myData);
		if ($resultInviteCoachs) {
			$tokenSession = (!empty($this->session->data['token'])) ? $this->session->data['token'] : '';
			foreach ($resultInviteCoachs as $inviteCoach) {
				$url_confirm = ($inviteCoach['student_accepted'] == 0) ? $this->url->link('ativocoach/details/confirmInvite', '&aid='.$inviteCoach['ativocoach_id'].'&cid='.$inviteCoach['coach_id'].'&token='.$tokenSession, 'SSL') : '';
				$data['inviteCoachs'][] = array(
					'firstname'		=> $inviteCoach['firstname'],
					'coach_id'		=> $inviteCoach['coach_id'],
					'aid'			=> $inviteCoach['ativocoach_id'],
					'url_confirm'	=> $url_confirm
					);
			}
		}
			$data['countCoach'] = count($resultInviteCoachs);

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

		if (file_exists(DIR_TEMPLATE. $this->config->get('config_template').'/template/ativocoach/details.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template').'/template/ativocoach/details.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/ativocoach/details.tpl', $data));
		}
	}
	public function confirmInvite(){
		$this->load->language('ativocoach/ativocoach');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('ativocoach/ativocoach');
		if (($this->request->server['REQUEST_METHOD'] == 'GET')) {
			$filter_uid = array (
				'ativocoach_id'	=> $this->request->get['aid'],
				'my_id'			=> $this->customer->getId(),
				'email'			=> $this->customer->getEmail(),
				'group_id'		=> $this->customer->getGroupId(),
				'coach_id'		=> $this->request->get['cid'],
				'language_id'	=> (int)$this->config->get('config_language_id')
				);
			$returnInvite = $this->model_ativocoach_ativocoach->confirmInvite($filter_uid);
			if ($returnInvite) {
				$this->session->data['success'] = $this->language->get('text_accepeted_coach_success');
			} else {
				$this->session->data['error_warning'] = $this->language->get('text_accepeted_coach_error');
			}
			$url='';
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$tokenSession = (!empty($this->session->data['token'])) ? $this->session->data['token'] : '';
			$this->response->redirect($this->url->link('ativocoach/details','&token=' . $tokenSession . $url, 'SSL'));
		}
	}
}
















