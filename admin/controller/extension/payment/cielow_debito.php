<?php
class ControllerExtensionPaymentCielowDebito extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/payment/cielow_debito');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('cielow_debito', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            if (isset($this->request->post['save_stay']) && ($this->request->post['save_stay'] = 1)) {
                $this->response->redirect($this->url->link('extension/payment/cielow_debito', 'token=' . $this->session->data['token'], true));
            } else {
                $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
            }
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_image_manager'] = $this->language->get('text_image_manager');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_browse'] = $this->language->get('text_browse');
        $data['text_clear'] = $this->language->get('text_clear');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_botao'] = $this->language->get('text_botao');
        $data['text_texto'] = $this->language->get('text_texto');
        $data['text_fundo'] = $this->language->get('text_fundo');
        $data['text_borda'] = $this->language->get('text_borda');

        $data['tab_geral'] = $this->language->get('tab_geral');
        $data['tab_api'] = $this->language->get('tab_api');
        $data['tab_situacoes_pedido'] = $this->language->get('tab_situacoes_pedido');
        $data['tab_finalizacao'] = $this->language->get('tab_finalizacao');

        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_credenciamento'] = $this->language->get('entry_credenciamento');
        $data['entry_chave'] = $this->language->get('entry_chave');
        $data['entry_soft_descriptor'] = $this->language->get('entry_soft_descriptor');
        $data['entry_ambiente'] = $this->language->get('entry_ambiente');
        $data['entry_visa'] = $this->language->get('entry_visa');
        $data['entry_mastercard'] = $this->language->get('entry_mastercard');
        $data['entry_situacao_pendente'] = $this->language->get('entry_situacao_pendente');
        $data['entry_situacao_autorizada'] = $this->language->get('entry_situacao_autorizada');
        $data['entry_situacao_nao_autorizada'] = $this->language->get('entry_situacao_nao_autorizada');
        $data['entry_situacao_capturada'] = $this->language->get('entry_situacao_capturada');
        $data['entry_situacao_cancelada'] = $this->language->get('entry_situacao_cancelada');
        $data['entry_titulo'] = $this->language->get('entry_titulo');
        $data['entry_imagem'] = $this->language->get('entry_imagem');
        $data['entry_botao_normal'] = $this->language->get('entry_botao_normal');
        $data['entry_botao_efeito'] = $this->language->get('entry_botao_efeito');

        $data['help_total'] = $this->language->get('help_total');
        $data['help_credenciamento'] = $this->language->get('help_credenciamento');
        $data['help_chave'] = $this->language->get('help_chave');
        $data['help_soft_descriptor'] = $this->language->get('help_soft_descriptor');
        $data['help_ambiente'] = $this->language->get('help_ambiente');
        $data['help_visa'] = $this->language->get('help_visa');
        $data['help_mastercard'] = $this->language->get('help_mastercard');
        $data['help_situacao_pendente'] = $this->language->get('help_situacao_pendente');
        $data['help_situacao_autorizada'] = $this->language->get('help_situacao_autorizada');
        $data['help_situacao_nao_autorizada'] = $this->language->get('help_situacao_nao_autorizada');
        $data['help_situacao_capturada'] = $this->language->get('help_situacao_capturada');
        $data['help_situacao_cancelada'] = $this->language->get('help_situacao_cancelada');
        $data['help_titulo'] = $this->language->get('help_titulo');
        $data['help_imagem'] = $this->language->get('help_imagem');
        $data['help_exibir_juros'] = $this->language->get('help_exibir_juros');
        $data['help_botao_normal'] = $this->language->get('help_botao_normal');
        $data['help_botao_efeito'] = $this->language->get('help_botao_efeito');

        $data['button_save_stay'] = $this->language->get('button_save_stay');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['token'] = $this->session->data['token'];

        $erros = array(
            'warning',
            'titulo',
            'credenciamento',
            'chave',
            'soft_descriptor'
        );

        foreach ($erros as $erro) {
            if (isset($this->error[$erro])) {
                $data['error_'.$erro] = $this->error[$erro];
            } else {
                $data['error_'.$erro] = '';
            }
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_extension'),
            'href'      => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/payment/cielow_debito', 'token=' . $this->session->data['token'], true),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('extension/payment/cielow_debito', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

        $data['versao'] = '3.9.2';

        $this->document->addStyle('view/javascript/jquery/colorpicker/css/bootstrap-colorpicker.min.css');
        $this->document->addScript('view/javascript/jquery/colorpicker/js/bootstrap-colorpicker.min.js');

        $campos = array(
            'total' => '',
            'lojas' => array(0),
            'geo_zone_id' => '',
            'status' => '',
            'sort_order' => '',
            'credenciamento' => '',
            'chave' => '',
            'soft_descriptor' => '',
            'ambiente' => '',
            'visa' => '',
            'mastercard' => '',
            'situacao_pendente_id' => '',
            'situacao_autorizada_id' => '',
            'situacao_nao_autorizada_id' => '',
            'situacao_capturada_id' => '',
            'situacao_cancelada_id' => '',
            'titulo' => '',
            'imagem' => '',
            'cor_normal_texto' => '#FFFFFF',
            'cor_normal_fundo' => '#33b0f0',
            'cor_normal_borda' => '#33b0f0',
            'cor_efeito_texto' => '#FFFFFF',
            'cor_efeito_fundo' => '#0487b0',
            'cor_efeito_borda' => '#0487b0'
        );

        foreach ($campos as $campo => $valor) {
            if (!empty($valor)) {
                if (isset($this->request->post['cielow_debito_'.$campo])) {
                    $data['cielow_debito_'.$campo] = $this->request->post['cielow_debito_'.$campo];
                } else if ($this->config->get('cielow_debito_'.$campo)) {
                    $data['cielow_debito_'.$campo] = $this->config->get('cielow_debito_'.$campo);
                } else {
                    $data['cielow_debito_'.$campo] = $valor;
                }
            } else {
                if (isset($this->request->post['cielow_debito_'.$campo])) {
                    $data['cielow_debito_'.$campo] = $this->request->post['cielow_debito_'.$campo];
                } else {
                    $data['cielow_debito_'.$campo] = $this->config->get('cielow_debito_'.$campo);
                }
            }
        }

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('tool/image');

        if (isset($this->request->post['cielow_debito_imagem']) && is_file(DIR_IMAGE . $this->request->post['cielow_debito_imagem'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['cielow_debito_imagem'], 100, 100);
        } elseif (is_file(DIR_IMAGE . $this->config->get('cielow_debito_imagem'))) {
            $data['thumb'] = $this->model_tool_image->resize($this->config->get('cielow_debito_imagem'), 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['no_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['custom_fields'] = array();

        $this->load->model('customer/custom_field');
        $custom_fields = $this->model_customer_custom_field->getCustomFields();

        foreach ($custom_fields as $custom_field) {
            $data['custom_fields'][] = array(
                'custom_field_id'    => $custom_field['custom_field_id'],
                'name'               => $custom_field['name'],
                'type'               => $custom_field['type'],
                'location'           => $custom_field['location']
            );
        }

        for ($i = 1; $i <= 12; $i++) {
            $data['parcelas'][] = $i;
        }

        $this->load->model('extension/payment/cielow');
        $this->model_extension_payment_cielow->updateTable();

        $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent('cielow');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/cielow_debito.tpl', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/cielow_debito')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $erros = array(
            'chave',
            'titulo'
        );

        foreach ($erros as $erro) {
            if (!(trim($this->request->post['cielow_debito_'.$erro]))) {
                $this->error[$erro] = $this->language->get('error_'.$erro);
            }
        }

        if (!preg_match('/^[0-9]+$/', $this->request->post['cielow_debito_credenciamento'])) {
            $this->error['credenciamento'] = $this->language->get('error_credenciamento');
        }

        if (strlen($this->request->post['cielow_debito_soft_descriptor']) <= 13) {
            if (!preg_match('/^[A-Z0-9]+$/', $this->request->post['cielow_debito_soft_descriptor'])) {
                $this->error['soft_descriptor'] = $this->language->get('error_soft_descriptor');
            }
        } else {
            $this->error['soft_descriptor'] = $this->language->get('error_soft_descriptor');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    public function install() {
        $this->load->model('extension/payment/cielow');
        $this->model_extension_payment_cielow->install();

        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'sale/cielow_search');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'sale/cielow_search');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'sale/cielow_error_log');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'sale/cielow_error_log');
    }

    public function uninstall() {
        $this->load->model('extension/payment/cielow');
        $this->model_extension_payment_cielow->uninstall();

        $this->load->model('user/user_group');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'sale/cielow_search');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'sale/cielow_search');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'sale/cielow_error_log');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'sale/cielow_error_log');
    }
}