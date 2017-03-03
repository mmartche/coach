<?php
class ControllerSaleCielowSearch extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('sale/cielow_search');

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = null;
        }

        if (isset($this->request->get['filter_dataPedido'])) {
            $filter_dataPedido = $this->request->get['filter_dataPedido'];
        } else {
            $filter_dataPedido = null;
        }

        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = null;
        }

        if (isset($this->request->get['filter_tid'])) {
            $filter_tid = $this->request->get['filter_tid'];
        } else {
            $filter_tid = null;
        }

        if (isset($this->request->get['filter_nsu'])) {
            $filter_nsu = $this->request->get['filter_nsu'];
        } else {
            $filter_nsu = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'oc.order_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_dataPedido'])) {
            $url .= '&filter_dataPedido=' . $this->request->get['filter_dataPedido'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_tid'])) {
            $url .= '&filter_tid=' . $this->request->get['filter_tid'];
        }

        if (isset($this->request->get['filter_nsu'])) {
            $url .= '&filter_nsu=' . $this->request->get['filter_nsu'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('sale/cielow_search', 'token=' . $this->session->data['token'] . $url, true)
        );

        $filter_data = array(
            'filter_order_id'   => $filter_order_id,
            'filter_dataPedido' => $filter_dataPedido,
            'filter_customer'    => $filter_customer,
            'filter_tid'        => $filter_tid,
            'filter_nsu'        => $filter_nsu,
            'filter_status'     => $filter_status,
            'sort'              => $sort,
            'order'             => $order,
            'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'             => $this->config->get('config_limit_admin')
        );

        $this->load->model('extension/payment/cielow');

        $transactions_total = $this->model_extension_payment_cielow->getTotalTransactions($filter_data);

        $results = $this->model_extension_payment_cielow->getTransactions($filter_data);

        $data['transactions'] = array();

        foreach ($results as $result) {
            switch ($result['status']) {
                case '0':
                    $status = $this->language->get('text_criada');
                break;
                case '1':
                    $status = $this->language->get('text_andamento');
                break;
                case '3':
                    $status = $this->language->get('text_nao_autenticada');
                break;
                case '4':
                    $status = $this->language->get('text_autorizada');
                break;
                case '5':
                    $status = $this->language->get('text_nao_autorizada');
                break;
                case '6':
                    $status = $this->language->get('text_capturada');
                break;
                case '8':
                    $status = $this->language->get('text_nao_capturada');
                break;
                case '9':
                    $status = $this->language->get('text_cancelada');
                break;
            }

            $data['transactions'][] = array(
                'order_cielow_id'  => $result['order_cielow_id'],
                'order_id'         => $result['order_id'],
                'dataPedido'       => date($this->language->get('date_format_short'), strtotime($result['dataPedido'])),
                'customer'         => $result['customer'],
                'bandeira'         => $result['bandeira'],
                'parcelas'         => $result['parcelas'],
                'operacao'         => ($result['produto'] == 'A') ? $this->language->get('text_debito') : $this->language->get('text_credito'),
                'tid'              => $result['tid'],
                'nsu'              => $result['nsu'],
                'dataAutorizado'   => (!empty($result['autorizacaoData'])) ? date($this->language->get('date_format_short'), strtotime($result['autorizacaoData'])) : '',
                'valorAutorizado'  => (!empty($result['autorizacaoValor'])) ? $this->currency->format(($result['autorizacaoValor'] / 100), $this->config->get('config_currency'), '1.00', true) : '',
                'dataCapturado'    => (!empty($result['capturaData'])) ? date($this->language->get('date_format_short'), strtotime($result['capturaData'])) : '',
                'valorCapturado'   => (!empty($result['capturaValor'])) ? $this->currency->format(($result['capturaValor'] / 100), $this->config->get('config_currency'), '1.00', true) : '',
                'dataCancelado'    => (!empty($result['cancelaData'])) ? date($this->language->get('date_format_short'), strtotime($result['cancelaData'])) : '',
                'valorCancelado'   => (!empty($result['cancelaValor'])) ? $this->currency->format(($result['cancelaValor'] / 100), $this->config->get('config_currency'), '1.00', true) : '',
                'status'           => $status,
                'view_order'       => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'], true),
                'view_transaction' => $this->url->link('sale/cielow_search/info', 'token=' . $this->session->data['token'] . '&cielow_id=' . $result['order_cielow_id'], true)
            );
        }

        $data['situacoes'] = array (
                                '0' => $this->language->get('text_criada'),
                                '1' => $this->language->get('text_andamento'),
                                '3' => $this->language->get('text_nao_autenticada'),
                                '4' => $this->language->get('text_autorizada'),
                                '5' => $this->language->get('text_nao_autorizada'),
                                '6' => $this->language->get('text_capturada'),
                                '8' => $this->language->get('text_nao_capturada'),
                                '9' => $this->language->get('text_cancelada')
                            );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_todas'] = $this->language->get('text_todas');

        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_dataPedido'] = $this->language->get('column_dataPedido');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_bandeira'] = $this->language->get('column_bandeira');
        $data['column_parcelas'] = $this->language->get('column_parcelas');
        $data['column_tid'] = $this->language->get('column_tid');
        $data['column_nsu'] = $this->language->get('column_nsu');
        $data['column_autorizada'] = $this->language->get('column_autorizada');
        $data['column_capturada'] = $this->language->get('column_capturada');
        $data['column_cancelada'] = $this->language->get('column_cancelada');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_order_id'] = $this->language->get('entry_order_id');
        $data['entry_dataPedido'] = $this->language->get('entry_dataPedido');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_tid'] = $this->language->get('entry_tid');
        $data['entry_nsu'] = $this->language->get('entry_nsu');
        $data['entry_status'] = $this->language->get('entry_status');

        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_info'] = $this->language->get('button_info');

        $data['token'] = $this->session->data['token'];

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_dataPedido'])) {
            $url .= '&filter_dataPedido=' . $this->request->get['filter_dataPedido'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_tid'])) {
            $url .= '&filter_tid=' . $this->request->get['filter_tid'];
        }

        if (isset($this->request->get['filter_nsu'])) {
            $url .= '&filter_nsu=' . $this->request->get['filter_nsu'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order'] = $this->url->link('sale/cielow_search', 'token=' . $this->session->data['token'] . '&sort=oc.order_id' . $url, true);
        $data['sort_dataPedido'] = $this->url->link('sale/cielow_search', 'token=' . $this->session->data['token'] . '&sort=oc.dataPedido' . $url, true);
        $data['sort_customer'] = $this->url->link('sale/cielow_search', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, true);
        $data['sort_status'] = $this->url->link('sale/cielow_search', 'token=' . $this->session->data['token'] . '&sort=oc.status' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_dataPedido'])) {
            $url .= '&filter_dataPedido=' . $this->request->get['filter_dataPedido'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_tid'])) {
            $url .= '&filter_tid=' . $this->request->get['filter_tid'];
        }

        if (isset($this->request->get['filter_nsu'])) {
            $url .= '&filter_nsu=' . $this->request->get['filter_nsu'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $transactions_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('sale/cielow_search', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($transactions_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($transactions_total - $this->config->get('config_limit_admin'))) ? $transactions_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $transactions_total, ceil($transactions_total / $this->config->get('config_limit_admin')));

        $data['filter_order_id'] = $filter_order_id;
        $data['filter_dataPedido'] = $filter_dataPedido;
        $data['filter_customer'] = $filter_customer;
        $data['filter_tid'] = $filter_tid;
        $data['filter_nsu'] = $filter_nsu;
        $data['filter_status'] = $filter_status;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/cielow_search.tpl', $data));
    }

    public function info() {
        if (isset($this->request->get['cielow_id'])) {
            $order_cielow_id = $this->request->get['cielow_id'];
        } else {
            $order_cielow_id = 0;
        }

        $this->load->model('extension/payment/cielow');
        $transaction_info = $this->model_extension_payment_cielow->getTransaction($order_cielow_id);

        if ($transaction_info) {
            $order_id = $transaction_info['order_id'];

            $this->load->model('sale/order');
            $order_info = $this->model_sale_order->getOrder($order_id);

            $this->load->language('sale/cielow_info');

            $this->document->setTitle($this->language->get('heading_title'));

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_edit'] = $this->language->get('text_edit');
            $data['text_consultando'] = $this->language->get('text_consultando');
            $data['text_cancelando'] = $this->language->get('text_cancelando');
            $data['text_capturando'] = $this->language->get('text_capturando');
            $data['text_atualizando'] = $this->language->get('text_atualizando');

            $data['tab_details'] = $this->language->get('tab_details');
            $data['tab_xml'] = $this->language->get('tab_xml');

            $data['button_search'] = $this->language->get('button_search');
            $data['button_capturar'] = $this->language->get('button_capturar');
            $data['button_cancelar'] = $this->language->get('button_cancelar');
            $data['button_consultar'] = $this->language->get('button_consultar');

            $data['entry_order_id'] = $this->language->get('entry_order_id');
            $data['entry_added'] = $this->language->get('entry_added');
            $data['entry_total'] = $this->language->get('entry_total');
            $data['entry_customer'] = $this->language->get('entry_customer');
            $data['entry_cielow_id'] = $this->language->get('entry_cielow_id');
            $data['entry_tid'] = $this->language->get('entry_tid');
            $data['entry_nsu'] = $this->language->get('entry_nsu');
            $data['entry_bandeira'] = $this->language->get('entry_bandeira');
            $data['entry_parcelamento'] = $this->language->get('entry_parcelamento');
            $data['entry_autorizacao'] = $this->language->get('entry_autorizacao');
            $data['entry_valor_autorizado'] = $this->language->get('entry_valor_autorizado');
            $data['entry_captura'] = $this->language->get('entry_captura');
            $data['entry_valor_capturado'] = $this->language->get('entry_valor_capturado');
            $data['entry_cancelamento'] = $this->language->get('entry_cancelamento');
            $data['entry_valor_cancelado'] = $this->language->get('entry_valor_cancelado');
            $data['entry_status'] = $this->language->get('entry_status');
            $data['entry_clearsale'] = $this->language->get('entry_clearsale');
            $data['entry_fcontrol'] = $this->language->get('entry_fcontrol');

            $data['error_iframe'] = $this->language->get('error_iframe');

            $data['token'] = $this->session->data['token'];

            $url = '';

            if (isset($this->request->get['filter_order_id'])) {
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            }

            if (isset($this->request->get['filter_dataPedido'])) {
                $url .= '&filter_dataPedido=' . $this->request->get['filter_dataPedido'];
            }

            if (isset($this->request->get['filter_customer'])) {
                $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_tid'])) {
                $url .= '&filter_tid=' . $this->request->get['filter_tid'];
            }

            if (isset($this->request->get['filter_nsu'])) {
                $url .= '&filter_nsu=' . $this->request->get['filter_nsu'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('sale/cielow_search', 'token=' . $this->session->data['token'] . $url, true)
            );

            /* Informações da transação */
            switch ($transaction_info['status']) {
                case '0':
                    $status = $this->language->get('text_criada');
                break;
                case '1':
                    $status = $this->language->get('text_andamento');
                break;
                case '3':
                    $status = $this->language->get('text_nao_autenticada');
                break;
                case '4':
                    $status = $this->language->get('text_autorizada');
                break;
                case '5':
                    $status = $this->language->get('text_nao_autorizada');
                break;
                case '6':
                    $status = $this->language->get('text_capturada');
                break;
                case '8':
                    $status = $this->language->get('text_nao_capturada');
                break;
                case '9':
                    $status = $this->language->get('text_cancelada');
                break;
            }

            $data['view_order'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $order_id, true);
            $data['view_customer'] = $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $order_info['customer_id'], true);

            $data['cielow_id']         = $transaction_info['order_cielow_id'];
            $data['order_id']          = $order_id;
            $data['added']             = date($this->language->get('date_format_short'), strtotime($transaction_info['dataPedido']));
            $data['customer']          = $order_info['firstname'] . ' ' . $order_info['lastname'];
            $data['total']             = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], true);
            $data['bandeira']          = $transaction_info['bandeira'];
            $data['parcelas']          = $transaction_info['parcelas'];
            $data['operacao']          = ($transaction_info['produto'] == 'A') ? $this->language->get('text_debito') : $this->language->get('text_credito');
            $data['tid']               = $transaction_info['tid'];
            $data['nsu']               = $transaction_info['nsu'];
            $data['data_autorizacao']  = (!empty($transaction_info['autorizacaoData'])) ? date($this->language->get('date_format_short'), strtotime($transaction_info['autorizacaoData'])) : '';
            $data['valor_autorizado']  = (!empty($transaction_info['autorizacaoValor'])) ? $this->currency->format(($transaction_info['autorizacaoValor'] / 100), $this->config->get('config_currency'), '1.00', true) : '';
            $data['data_captura']      = (!empty($transaction_info['capturaData'])) ? date($this->language->get('date_format_short'), strtotime($transaction_info['capturaData'])) : '';
            $data['valor_capturado']   = (!empty($transaction_info['capturaValor'])) ? $this->currency->format(($transaction_info['capturaValor'] / 100), $this->config->get('config_currency'), '1.00', true) : '';
            $data['data_cancelamento'] = (!empty($transaction_info['cancelaData'])) ? date($this->language->get('date_format_short'), strtotime($transaction_info['cancelaData'])) : '';
            $data['valor_cancelado']   = (!empty($transaction_info['cancelaValor'])) ? $this->currency->format(($transaction_info['cancelaValor'] / 100), $this->config->get('config_currency'), '1.00', true) : '';
            $data['status']            = $status;
            $data['clearsale']         = $this->config->get('cielow_clearsale_status');
            $data['fcontrol']          = $this->config->get('cielow_fcontrol_status');
            $data['xml']               = $transaction_info['xml'];

            $data['dias_capturar'] = '';
            $data['dias_cancelar'] = '';

            $atual = strtotime(date('Y-m-d'));

            if (($transaction_info['status'] == '4') && ($transaction_info['status'] != '9')) {
                if (!empty($transaction_info['autorizacaoData'])) {
                    $inicial = strtotime(date('Y-m-d', strtotime($transaction_info['autorizacaoData'])));
                    $final = strtotime(date('Y-m-d', strtotime('+5 days', $inicial)));
                    if ($atual <= $final) {
                        $dataFinal = date($this->language->get('date_format_short'), $final);
                        $dias = (int)floor(($final - strtotime(date('Y-m-d'))) / (60 * 60 * 24));
                        $desc = ($dias > 1) ? $this->language->get('text_dias') : $this->language->get('text_dia');
                        $data['dias_capturar'] = sprintf($this->language->get('text_dias_capturar'), $dataFinal, $dias, $desc);
                    }
                }
            }

            if (($transaction_info['status'] == '4') || ($transaction_info['status'] == '5') || ($transaction_info['status'] == '6')) {
                if (!empty($transaction_info['capturaData'])) {
                    $inicial = strtotime(date('Y-m-d', strtotime($transaction_info['capturaData'])));
                    $final = strtotime(date('Y-m-d', strtotime('+89 days', $inicial)));
                } else {
                    $inicial = strtotime(date('Y-m-d', strtotime($transaction_info['autorizacaoData'])));
                    $final = strtotime(date('Y-m-d', strtotime('+5 days', $inicial)));
                }
                if ($atual <= $final) {
                    $dataFinal = date($this->language->get('date_format_short'), $final);
                    $dias = (int)floor(($final - strtotime(date('Y-m-d'))) / (60 * 60 * 24));
                    $desc = ($dias > 1) ? $this->language->get('text_dias') : $this->language->get('text_dia');
                    $data['dias_cancelar'] = sprintf($this->language->get('text_dias_cancelar'), $dataFinal, $dias, $desc);
                }
            }

            /* Informações comuns para os sistemas antifraudes */

            $products  = $this->model_sale_order->getOrderProducts($order_id);
            $shippings = $this->model_extension_payment_cielow->getOrderShipping($order_id);

            $parcelas  = $transaction_info['parcelas'];
            $telefone  = preg_replace("/[^0-9]/", '', $order_info['telephone']);
            $email     = strtolower($order_info['email']);
            $documento = '';

            $cobranca_nome        = '';
            $cobranca_logradouro  = $order_info['payment_address_1'];
            $cobranca_numero      = '';
            $cobranca_complemento = '';
            $cobranca_bairro      = $order_info['payment_address_2'];
            $cobranca_cidade      = $order_info['payment_city'];
            $cobranca_estado      = $order_info['payment_zone_code'];
            $cobranca_cep         = preg_replace("/[^0-9]/", '', $order_info['payment_postcode']);

            $entrega_nome        = $order_info['shipping_firstname'].' '.$order_info['shipping_lastname'];
            $entrega_logradouro  = $order_info['shipping_address_1'];
            $entrega_numero      = '';
            $entrega_complemento = '';
            $entrega_bairro      = $order_info['shipping_address_2'];
            $entrega_cidade      = $order_info['shipping_city'];
            $entrega_estado      = $order_info['shipping_zone_code'];
            $entrega_cep         = preg_replace("/[^0-9]/", '', $order_info['shipping_postcode']);

            $colunas = array();

            if ($this->config->get('cielow_custom_razao_id') == 'N') {
                array_push($colunas, $this->config->get('cielow_razao_coluna'));
            }

            if ($this->config->get('cielow_custom_cnpj_id') == 'N') {
                array_push($colunas, $this->config->get('cielow_cnpj_coluna'));
            }

            if ($this->config->get('cielow_custom_cpf_id') == 'N') {
                array_push($colunas, $this->config->get('cielow_cpf_coluna'));
            }

            if ($this->config->get('cielow_custom_numero_id') == 'N') {
                array_push($colunas, $this->config->get('cielow_numero_fatura_coluna'));
                array_push($colunas, $this->config->get('cielow_numero_entrega_coluna'));
            }

            if ($this->config->get('cielow_custom_complemento_id') == 'N') {
                array_push($colunas, $this->config->get('cielow_complemento_fatura_coluna'));
                array_push($colunas, $this->config->get('cielow_complemento_entrega_coluna'));
            }

            if (count($colunas)) {
                $colunas_info = $this->model_extension_payment_cielow->getOrder($colunas, $order_id);
            }

            if ($this->config->get('cielow_custom_razao_id') == 'N') {
                if (!empty($colunas_info[$this->config->get('cielow_razao_coluna')])) {
                    $cobranca_nome = $colunas_info[$this->config->get('cielow_razao_coluna')];
                }
            } else {
                if ($order_info['custom_field']) {
                    foreach ($order_info['custom_field'] as $key => $value) {
                        if ($this->config->get('cielow_custom_razao_id') == $key) {
                            $cobranca_nome = $value;
                        }
                    }
                }
            }

            if ($this->config->get('cielow_custom_cnpj_id') == 'N') {
                if (!empty($colunas_info[$this->config->get('cielow_cnpj_coluna')])) {
                    $documento = preg_replace("/[^0-9]/", '', $colunas_info[$this->config->get('cielow_cnpj_coluna')]);
                }
            } else {
                if (is_array($order_info['custom_field'])) {
                    foreach ($order_info['custom_field'] as $key => $value) {
                        if ($this->config->get('cielow_custom_cnpj_id') == $key) {
                            $documento = preg_replace("/[^0-9]/", '', $value);
                        }
                    }
                }
            }

            if (empty($cobranca_nome)) {
                $cobranca_nome = $order_info['payment_firstname'].' '.$order_info['payment_lastname'];

                if ($this->config->get('cielow_custom_cpf_id') == 'N') {
                    if (!empty($colunas_info[$this->config->get('cielow_cpf_coluna')])) {
                        $documento = preg_replace("/[^0-9]/", '', $colunas_info[$this->config->get('cielow_cpf_coluna')]);
                    }
                } else {
                    if (is_array($order_info['custom_field'])) {
                        foreach ($order_info['custom_field'] as $key => $value) {
                            if ($this->config->get('cielow_custom_cpf_id') == $key) {
                                $documento = preg_replace("/[^0-9]/", '', $value);
                            }
                        }
                    }
                }
            }

            if ($this->config->get('cielow_custom_numero_id') == 'N') {
                $cobranca_numero = preg_replace("/[^0-9]/", '', $colunas_info[$this->config->get('cielow_numero_fatura_coluna')]);
                $entrega_numero  = preg_replace("/[^0-9]/", '', $colunas_info[$this->config->get('cielow_numero_entrega_coluna')]);
            } else {
                if (is_array($order_info['payment_custom_field'])) {
                    foreach ($order_info['payment_custom_field'] as $key => $value) {
                        if ($this->config->get('cielow_custom_numero_id') == $key) {
                            $cobranca_numero = preg_replace("/[^0-9]/", '', $value);
                        }
                    }
                }
                if (is_array($order_info['shipping_custom_field'])) {
                    foreach ($order_info['shipping_custom_field'] as $key => $value) {
                        if ($this->config->get('cielow_custom_numero_id') == $key) {
                            $entrega_numero = preg_replace("/[^0-9]/", '', $value);
                        }
                    }
                }
            }

            if ($this->config->get('cielow_custom_complemento_id') == 'N') {
                $cobranca_complemento = substr($colunas_info[$this->config->get('cielow_complemento_fatura_coluna')], 0, 250);
                $entrega_complemento  = substr($colunas_info[$this->config->get('cielow_complemento_entrega_coluna')], 0, 250);
            } else {
                if (is_array($order_info['payment_custom_field'])) {
                    foreach ($order_info['payment_custom_field'] as $key => $value) {
                        if ($this->config->get('cielow_custom_complemento_id') == $key) {
                            $cobranca_complemento = substr($value, 0, 250);
                        }
                    }
                }
                if (is_array($order_info['shipping_custom_field'])) {
                    foreach ($order_info['shipping_custom_field'] as $key => $value) {
                        if ($this->config->get('cielow_custom_complemento_id') == $key) {
                            $entrega_complemento = substr($value, 0, 250);
                        }
                    }
                }
            }

            /* Informações para ClearSale */
            if ($data['clearsale']) {
                if ($this->config->get('cielow_clearsale_ambiente')) {
                    $clearsale_url = "https://www.clearsale.com.br/start/Entrada/EnviarPedido.aspx";
                } else {
                    $clearsale_url = "https://homolog.clearsale.com.br/start/Entrada/EnviarPedido.aspx";
                }

                $clearsale_itens['CodigoIntegracao'] = $this->config->get('cielow_clearsale_codigo');
                $clearsale_itens['PedidoID'] = $order_id;
                $clearsale_itens['Data'] = date('d/m/Y h:i:s', strtotime($order_info['date_added']));
                $clearsale_itens['IP'] = $order_info['ip'];
                $clearsale_itens['TipoPagamento'] = '1';
                $clearsale_itens['Parcelas'] = $parcelas;
                $clearsale_itens['Cobranca_Nome'] = substr($cobranca_nome, 0, 500);
                $clearsale_itens['Cobranca_Email'] = substr($email, 0, 150);
                $clearsale_itens['Cobranca_Documento'] = substr($documento, 0, 100);
                $clearsale_itens['Cobranca_Logradouro'] = substr($cobranca_logradouro, 0, 200);
                $clearsale_itens['Cobranca_Logradouro_Numero'] = substr($cobranca_numero, 0, 15);
                $clearsale_itens['Cobranca_Logradouro_Complemento'] = substr($cobranca_complemento, 0, 250);
                $clearsale_itens['Cobranca_Bairro'] = substr($cobranca_bairro, 0, 150);
                $clearsale_itens['Cobranca_Cidade'] = substr($cobranca_cidade, 0, 150);
                $clearsale_itens['Cobranca_Estado' ] = substr($cobranca_estado, 0, 2);
                $clearsale_itens['Cobranca_CEP'] = $cobranca_cep;
                $clearsale_itens['Cobranca_Pais'] = 'Bra';
                $clearsale_itens['Cobranca_DDD_Telefone_1'] = substr($telefone, 0, 2);
                $clearsale_itens['Cobranca_Telefone_1'] = substr($telefone, 2);

                if (utf8_strlen($order_info['shipping_method']) > 0) {
                    $clearsale_itens['Entrega_Nome'] = substr($entrega_nome, 0, 500);
                    $clearsale_itens['Entrega_Logradouro'] = substr($entrega_logradouro, 0, 200);
                    $clearsale_itens['Entrega_Logradouro_Numero'] = substr($entrega_numero, 0, 15);
                    $clearsale_itens['Entrega_Logradouro_Complemento'] = substr($entrega_complemento, 0, 250);
                    $clearsale_itens['Entrega_Bairro'] = substr($entrega_bairro, 0, 150);
                    $clearsale_itens['Entrega_Cidade'] = substr($entrega_cidade, 0, 150);
                    $clearsale_itens['Entrega_Estado'] = substr($entrega_estado, 0, 2);
                    $clearsale_itens['Entrega_CEP'] = $entrega_cep;
                    $clearsale_itens['Entrega_Pais'] = 'Bra';
                }

                $order_total = 0;

                $i = 1; 
                foreach ($products as $product) {
                    $item_valor = $this->currency->format($product['price'], $order_info['currency_code'], '1.00', false);

                    $clearsale_itens['Item_ID_'.$i]    = substr($product['product_id'], 0, 50);
                    $clearsale_itens['Item_Nome_'.$i]  = substr($product['name'], 0, 150);
                    $clearsale_itens['Item_Qtd_'.$i]   = $product['quantity'];
                    $clearsale_itens['Item_Valor_'.$i] = $item_valor;
                    $order_total += ($product['quantity'] * $item_valor);
                    $i++;
                }

                foreach ($shippings as $shipping) {
                    if ($shipping['value'] > 0) {
                        $item_valor = $this->currency->format($shipping['value'], $order_info['currency_code'], '1.00', false);

                        $clearsale_itens['Item_ID_'.$i] = substr($shipping['code'], 0, 50);
                        $clearsale_itens['Item_Nome_'.$i] = substr($shipping['title'], 0, 150);
                        $clearsale_itens['Item_Qtd_'.$i] = '1';
                        $clearsale_itens['Item_Valor_'.$i] = $item_valor;
                        $order_total += $item_valor;
                        $i++;
                    }
                }

                $clearsale_itens['Total'] = $this->currency->format($order_total, $order_info['currency_code'], '1.00', false);

                $data['clearsale_url']   = $clearsale_url;
                $data['clearsale_itens'] = $clearsale_itens;
                $data['clearsale_src']   = $clearsale_url . '?codigointegracao=' . $this->config->get('cielow_clearsale_codigo') . '&PedidoID=' . $order_id;
            }

            /* Informações para FControl */
            if ($data['fcontrol']) {
                $data['fcontrol_url']  = "https://secure.fcontrol.com.br/validatorframe/validatorframe.aspx?";

                $data['fcontrol_url'] .= 'login='.$this->config->get('cielow_fcontrol_login').
                                         '&Senha='.$this->config->get('cielow_fcontrol_senha').
                                         '&nomeComprador='.substr($cobranca_nome, 0, 255).
                                         '&ruaComprador='.substr($cobranca_logradouro, 0, 255).
                                         '&numeroComprador='.substr($cobranca_numero, 0, 8).
                                         '&complementoComprador='.substr($cobranca_complemento, 0, 50).
                                         '&bairroComprador='.substr($cobranca_bairro, 0, 150).
                                         '&cidadeComprador='.substr($cobranca_cidade, 0, 255).
                                         '&ufComprador='.substr($cobranca_estado, 0, 2).
                                         '&paisComprador=Bra'.
                                         '&cepComprador='.substr($cobranca_cep, 0, 5) . '-' . substr($cobranca_cep, 5, 3).
                                         '&cpfComprador='.$documento.
                                         '&dddComprador='.substr($telefone, 0, 2).
                                         '&telefoneComprador='.substr($telefone, 2).
                                         '&emailComprador='.$email.
                                         '&ip='.$order_info['ip'];

                if (utf8_strlen($order_info['shipping_method']) > 0) {
                    $data['fcontrol_url'] .= '&nomeEntrega='.substr($entrega_nome, 0, 255).
                                             '&ruaEntrega='.substr($entrega_logradouro, 0, 255).
                                             '&numeroEntrega='.substr($entrega_numero, 0, 8).
                                             '&complementoEntrega='.substr($entrega_complemento, 0, 50).
                                             '&bairroEntrega='.substr($entrega_bairro, 0, 150).
                                             '&cidadeEntrega='.substr($entrega_cidade, 0, 255).
                                             '&ufEntrega='.substr($entrega_estado, 0, 2).
                                             '&paisEntrega=Bra'.
                                             '&cepEntrega='.substr($entrega_cep, 0, 5) . '-' . substr($entrega_cep, 5, 3).
                                             '&dddEntrega='.substr($telefone, 0, 2).
                                             '&telefoneEntrega='.substr($telefone, 2);
                }

                $order_total = 0;
                $itens_total = 0;
                $itens_qtd   = 0;

                foreach ($products as $product) {
                    $item_valor = $product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0);
                    $order_total += $item_valor;
                    $itens_total += $product['quantity'];
                    $itens_qtd++;
                }

                foreach ($shippings as $shipping) {
                    $order_total += $shipping['value'];
                    $itens_total += 1;
                    $itens_qtd++;
                }

                $valor_compra    = $this->currency->format($order_total, $order_info['currency_code'], '1.00', false);
                $valor_pagamento = $this->currency->format(($transaction_info['autorizacaoValor'] / 100), $order_info['currency_code'], '1.00', false);

                $data['fcontrol_url'] .= '&codigoPedido='.$order_id.
                                         '&quantidadeItensDistintos='.$itens_qtd.
                                         '&quantidadeTotalItens='.$itens_total.
                                         '&valorTotalCompra='.($valor_compra*100).
                                         '&dataCompra='.date('Y-m-d', strtotime($order_info['date_added'])).'T'.date('h:i:s', strtotime($order_info['date_added'])).
                                         '&canalVenda=lojavirtual'.
                                         '&codigoIntegrador=0';

                $data['fcontrol_url'] .= '&metodoPagamentos=55'.
                                         '&numeroParcelasPagamentos='.$parcelas.
                                         '&valorPagamentos='.($valor_pagamento*100);
            }

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('sale/cielow_info.tpl', $data));
        } else {
            $this->load->language('error/not_found');

            $this->document->setTitle($this->language->get('heading_title'));

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_not_found'] = $this->language->get('text_not_found');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], true)
            );

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('error/not_found.tpl', $data));
        }
    }

    public function search() {
        $json = array();

        $this->load->language('sale/cielow_search');

        if ($this->user->hasPermission('modify', 'sale/cielow_search')) {
            if (isset($this->request->get['cielow_id'])) {
                $order_cielow_id = (int)$this->request->get['cielow_id'];

                $this->load->model('extension/payment/cielow');
                $transaction = $this->model_extension_payment_cielow->getTransaction($order_cielow_id);

                require_once(DIR_SYSTEM . 'library/cielow/include.php');
                require_once(DIR_SYSTEM . 'library/cielow/errorHandling.php');
                require_once(DIR_SYSTEM . 'library/cielow/pedido.php');

                $pedido = new Pedido();

                $pedido->dadosEcNumero = $this->config->get('cielow_credenciamento');
                $pedido->dadosEcChave  = $this->config->get('cielow_chave');
                $pedido->tid           = $transaction['tid'];

                $xmlRetorno = $pedido->RequisicaoConsulta();

                $objResposta = simplexml_load_string($xmlRetorno);

                $status = $objResposta->status;

                if (!empty($status)) {
                    switch($status){
                        case '4': /* Autorizada */
                            $dados['order_cielow_id']     = $order_cielow_id;
                            $dados['status']              = $status;
                            $dados['autorizacaoCodigo']   = $objResposta->autorizacao->codigo;
                            $dados['autorizacaoMensagem'] = $objResposta->autorizacao->mensagem;
                            $dados['autorizacaoData']     = $objResposta->autorizacao->{'data-hora'};
                            $dados['autorizacaoValor']    = $objResposta->autorizacao->valor;
                            $dados['autorizacaoLR']       = $objResposta->autorizacao->lr;
                            $dados['autorizacaoNSU']      = $objResposta->autorizacao->nsu;
                            $dados['xml']                 = mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"));

                            $this->model_extension_payment_cielow->autorizeTransaction($dados);

                            $json['mensagem'] =  $this->language->get('text_autorizada');
                            break;
                        case '6': /* Capturada */
                            $dados['order_cielow_id'] = $order_cielow_id;
                            $dados['status']          = $status;
                            $dados['capturaCodigo']   = $objResposta->captura->codigo;
                            $dados['capturaMensagem'] = $objResposta->captura->mensagem;
                            $dados['capturaData']     = $objResposta->captura->{'data-hora'};
                            $dados['capturaValor']    = $objResposta->captura->valor;
                            $dados['xml']             = mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"));
                            
                            $this->model_extension_payment_cielow->captureTransaction($dados);

                            $json['mensagem'] = $this->language->get('text_capturada');
                            break;
                        case '9': /* Cancelada */
                            $dados['order_cielow_id'] = $order_cielow_id;
                            $dados['status']          = $status;
                            $dados['cancelaCodigo']   = $objResposta->cancelamentos->cancelamento->codigo;
                            $dados['cancelaMensagem'] = $objResposta->cancelamentos->cancelamento->mensagem;
                            $dados['cancelaData']     = $objResposta->cancelamentos->cancelamento->{'data-hora'};
                            $dados['cancelaValor']    = $objResposta->cancelamentos->cancelamento->valor;
                            $dados['xml']             = mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"));

                            $this->model_extension_payment_cielow->cancelTransaction($dados);

                            $json['mensagem'] =  $this->language->get('text_cancelada');
                            break;
                        default:
                            $dados['order_cielow_id'] = $order_cielow_id;
                            $dados['status']          = $status;

                            $this->model_extension_payment_cielow->updateTransaction($dados);

                            switch($status){
                                case '0':
                                    $mensagem = $this->language->get('text_criada');
                                    break;
                                case '1':
                                    $mensagem = $this->language->get('text_andamento');
                                    break;
                                case '3':
                                    $mensagem = $this->language->get('text_nao_autenticada');
                                    break;
                                case '5':
                                    $mensagem = $this->language->get('text_nao_autorizada');
                                    break;
                                case '8':
                                    $mensagem = $this->language->get('text_nao_capturada');
                                    break;
                            }

                            $json['mensagem'] = $mensagem;
                            break;
                    }
                }
            } else {
                $json['error'] = $this->language->get('error_warning');
            }
        } else {
            $json['error'] = $this->language->get('error_permission');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function capture() {
        $json = array();

        $this->load->language('sale/cielow_search');

        if ($this->user->hasPermission('modify', 'sale/cielow_search')) {
            if (isset($this->request->get['cielow_id'])) {
                $order_cielow_id = (int)$this->request->get['cielow_id'];

                $this->load->model('extension/payment/cielow');
                $transactionInfo = $this->model_extension_payment_cielow->getTransaction($order_cielow_id);

                $this->load->model('sale/order');
                $order_info = $this->model_sale_order->getOrder($transactionInfo['order_id']);

                require_once(DIR_SYSTEM . 'library/cielow/include.php');
                require_once(DIR_SYSTEM . 'library/cielow/errorHandling.php');
                require_once(DIR_SYSTEM . 'library/cielow/pedido.php');

                $Pedido = new Pedido();

                $Pedido->tid           = $transactionInfo['tid'];
                $Pedido->dadosEcNumero = trim($this->config->get('cielow_credenciamento'));
                $Pedido->dadosEcChave  = trim($this->config->get('cielow_chave'));

                $objResposta = null;
                $xmlRetorno  = $Pedido->RequisicaoCaptura($transactionInfo['autorizacaoValor'], null);
                $objResposta = simplexml_load_string($xmlRetorno);

                $status = $objResposta->status;

                if (!empty($status)) {
                    switch($status){
                        case '6': /* Capturada */

                        $dados['order_cielow_id'] = $order_cielow_id;
                        $dados['status']          = $status;
                        $dados['capturaCodigo']   = $objResposta->captura->codigo;
                        $dados['capturaMensagem'] = $objResposta->captura->mensagem;
                        $dados['capturaData']     = $objResposta->captura->{'data-hora'};
                        $dados['capturaValor']    = $objResposta->captura->valor;
                        $dados['xml']             = mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"));

                        $this->model_extension_payment_cielow->captureTransaction($dados);

                        $capturaValor = $this->currency->format(($objResposta->captura->valor / 100), $order_info['currency_code'], '1.00', true);

                        $comment  = $this->language->get('entry_data') . $objResposta->captura->{'data-hora'} . "\n";
                        $comment .= $this->language->get('entry_valor') . $capturaValor;

                        $json['mensagem'] = $this->language->get('text_capturada');

                        break;
                    }
                }
            } else {
                $json['error'] = $this->language->get('error_warning');
            }
        } else {
            $json['error'] = $this->language->get('error_permission');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function cancel() {
        $json = array();

        $this->load->language('sale/cielow_search');

        if ($this->user->hasPermission('modify', 'sale/cielow_search')) {
            if (isset($this->request->get['cielow_id'])) {
                $order_cielow_id = (int)$this->request->get['cielow_id'];

                $this->load->model('extension/payment/cielow');
                $transactionInfo = $this->model_extension_payment_cielow->getTransaction($order_cielow_id);

                require_once(DIR_SYSTEM . 'library/cielow/include.php');
                require_once(DIR_SYSTEM . 'library/cielow/errorHandling.php');
                require_once(DIR_SYSTEM . 'library/cielow/pedido.php');

                $Pedido = new Pedido();

                $Pedido->tid           = $transactionInfo['tid'];
                $Pedido->dadosEcNumero = trim($this->config->get('cielow_credenciamento'));
                $Pedido->dadosEcChave  = trim($this->config->get('cielow_chave'));

                $objResposta = null;
                $xmlRetorno  = $Pedido->RequisicaoCancelamento();
                $objResposta = simplexml_load_string($xmlRetorno);

                $status = $objResposta->status;

                if (!empty($status)) {
                    switch($status){
                        case '9': /* Cancelada */

                        $dados['order_cielow_id'] = $order_cielow_id;
                        $dados['status']          = $status;
                        $dados['cancelaCodigo']   = $objResposta->cancelamentos->cancelamento->codigo;
                        $dados['cancelaMensagem'] = $objResposta->cancelamentos->cancelamento->mensagem;
                        $dados['cancelaData']     = $objResposta->cancelamentos->cancelamento->{'data-hora'};
                        $dados['cancelaValor']    = $objResposta->cancelamentos->cancelamento->valor;
                        $dados['xml']             = mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"));

                        $this->model_extension_payment_cielow->cancelTransaction($dados);

                        $json['mensagem'] =  $this->language->get('text_cancelada');

                        break;
                    }
                }
            } else {
                $json['error'] = $this->language->get('error_warning');
            }
        } else {
            $json['error'] = $this->language->get('error_permission');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}