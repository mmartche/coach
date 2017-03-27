<?php

class ControllerAtivocoachValidarecorrencia extends Controller {
	public function index()	{
		$this->load->language('ativocoach/ativocoach');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('ativocoach_description'));
		$this->document->setKeywords($this->language->get('ativocoach_keywords'));

	}
}