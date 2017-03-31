<?php
class ControllerExtensionPaymentCielow extends Controller {
    private $valorTotal = 0;

    public function index() {
        $this->load->language('extension/payment/cielow');

        $data['text_ambiente'] = $this->language->get('text_ambiente');
        $data['text_detalhes'] = $this->language->get('text_detalhes');
        $data['text_ate'] = $this->language->get('text_ate');
        $data['text_de'] = $this->language->get('text_de');
        $data['text_total'] = $this->language->get('text_total');
        $data['text_sem_juros'] = $this->language->get('text_sem_juros');
        $data['text_com_juros'] = $this->language->get('text_com_juros');
        $data['text_juros'] = $this->language->get('text_juros');
        $data['text_mes'] = $this->language->get('text_mes');
        $data['text_ano'] = $this->language->get('text_ano');
        $data['text_carregando'] = $this->language->get('text_carregando');
        $data['text_autorizando'] = $this->language->get('text_autorizando');
        $data['text_autorizou'] = $this->language->get('text_autorizou');

        $data['button_pagar'] = $this->language->get('button_pagar');

        $data['entry_bandeira'] = $this->language->get('entry_bandeira');
        $data['entry_cartao'] = $this->language->get('entry_cartao');
        $data['entry_validade_mes'] = $this->language->get('entry_validade_mes');
        $data['entry_validade_ano'] = $this->language->get('entry_validade_ano');
        $data['entry_codigo'] = $this->language->get('entry_codigo');
        $data['entry_parcelas'] = $this->language->get('entry_parcelas');

        $data['error_cartao'] = $this->language->get('error_cartao');
        $data['error_parcelas'] = $this->language->get('error_parcelas');
        $data['error_mes'] = $this->language->get('error_mes');
        $data['error_ano'] = $this->language->get('error_ano');
        $data['error_codigo'] = $this->language->get('error_codigo');
        $data['error_nao_autorizou'] = $this->language->get('error_nao_autorizou');
        $data['error_bandeiras'] = $this->language->get('error_bandeiras');

        $data['ambiente'] = $this->config->get('cielow_ambiente');

        $data['exibir_juros'] = $this->config->get('cielow_exibir_juros');

        $data['cor_normal_texto'] = $this->config->get('cielow_cor_normal_texto');
        $data['cor_normal_fundo'] = $this->config->get('cielow_cor_normal_fundo');
        $data['cor_normal_borda'] = $this->config->get('cielow_cor_normal_borda');
        $data['cor_efeito_texto'] = $this->config->get('cielow_cor_efeito_texto');
        $data['cor_efeito_fundo'] = $this->config->get('cielow_cor_efeito_fundo');
        $data['cor_efeito_borda'] = $this->config->get('cielow_cor_efeito_borda');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $bandeiras = array();
        foreach ($this->getBandeiras() as $bandeira => $parcelamento) {
            ($this->config->get('cielow_' . $bandeira)) ? $bandeiras[$bandeira] = $parcelamento : '';
        }
        $data['bandeiras'] = $bandeiras;
        $data['colunas'] = count($bandeiras);

        $data['meses'] = array();
        for ($i = 1; $i <= 12; $i++) {
            $data['meses'][] = array(
                'text'  => sprintf('%02d', $i),
                'value' => sprintf('%02d', $i)
            );
        }

        $hoje = getdate();
        $data['anos'] = array();
        for ($i = $hoje['year']; $i < $hoje['year'] + 12; $i++) {
            $data['anos'][] = array(
                'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
            );
        }

        return $this->load->view('extension/payment/cielow', $data);
    }

    public function parcelas() {
        $json = array();

        $valorMinimo = ($this->config->get('cielow_minimo') > 0) ? $this->config->get('cielow_minimo') : '0';

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->valorTotal = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

        $total = $this->valorTotal;

        $bandeira = $this->request->get['bandeira'];

        $currency_code = $this->session->data['currency'];

        if ((strtolower($bandeira) != 'discover') && (strtoupper($currency_code == 'BRL'))) {
            $parcelas = $this->config->get('cielow_'. strtolower($bandeira) .'_parcelas');
            $sem_juros = $this->config->get('cielow_'. strtolower($bandeira) .'_sem_juros');
            $juros = $this->config->get('cielow_'. strtolower($bandeira) .'_juros');

            for ($i = 1; $i <= $parcelas; $i++) {
                if ($i <= $sem_juros) {
                    $valorParcela = ($total/$i);
                    if ($valorParcela >= $valorMinimo) {
                        $json[] = array(
                            'parcela' => $i,
                            'valor'   => $this->currency->format($valorParcela, $order_info['currency_code'], '1.00', true),
                            'juros'   => 0,
                            'total'   => $this->currency->format($total, $order_info['currency_code'], '1.00', true)
                        );
                    } else if ($i == 1) {
                        $json[] = array(
                            'parcela' => $i,
                            'valor'   => $this->currency->format($valorParcela, $order_info['currency_code'], '1.00', true),
                            'juros'   => 0,
                            'total'   => $this->currency->format($total, $order_info['currency_code'], '1.00', true)
                        );
                    }
                } else {
                    $total = $this->getParcelar($bandeira, $i);
                    if ($total['valorParcela'] >= $valorMinimo) {
                        $json[] = array(
                            'parcela' => $i,
                            'valor'   => $this->currency->format($total['valorParcela'], $order_info['currency_code'], '1.00', true),
                            'juros'   => $juros,
                            'total'   => $this->currency->format($total['valorTotal'], $order_info['currency_code'], '1.00', true)
                        );
                    } else if ($i == 1) {
                        $json[] = array(
                            'parcela' => $i,
                            'valor'   => $this->currency->format($total['valorParcela'], $order_info['currency_code'], '1.00', true),
                            'juros'   => $juros,
                            'total'   => $this->currency->format($total['valorTotal'], $order_info['currency_code'], '1.00', true)
                        );
                    }
                }
            }
        } else {
            $json[] = array(
                'parcela' => 1,
                'valor'   => $this->currency->format($total, $order_info['currency_code'], '1.00', true),
                'juros'   => 0,
                'total'   => $this->currency->format($total, $order_info['currency_code'], '1.00', true)
            );
        }
        $this->response->setOutput(json_encode($json));
    }

    public function transacao() {
        $this->language->load('extension/payment/cielow');

        $json = array();

        $bandeiras = $this->getBandeiras();

        $bandeiraCartao = trim($this->request->post['bandeira']);
        $parcelasCartao = preg_replace("/[^0-9]/", '', $this->request->post['parcelas']);
        $numeroCartao   = preg_replace("/[^0-9]/", '', $this->request->post['cartao']);
        $validadeMes    = preg_replace("/[^0-9]/", '', $this->request->post['mes']);
        $validadeAno    = preg_replace("/[^0-9]/", '', $this->request->post['ano']);
        $validadeCartao = $validadeAno . $validadeMes;
        $codigoCartao   = preg_replace("/[^0-9]/", '', $this->request->post['codigo']);

        $campos = array($bandeiraCartao, $parcelasCartao, $numeroCartao, $validadeCartao, $codigoCartao);

        if ($this->validarCampos($campos) && array_key_exists(strtolower($bandeiraCartao), $bandeiras) && ($parcelasCartao <= '12')) {
            $numPedido = $this->session->data['order_id'];

            $cartaoMascarado = str_repeat('*', (strlen($numeroCartao)-4)) . substr($numeroCartao, -4);

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($numPedido);

            $this->valorTotal = $this->currency->format($order_info['total'], $order_info['currency_code'], '1.00', false);

            $currency_code = $this->session->data['currency'];

            if (strtoupper($currency_code == 'BRL')) {
                if ($parcelasCartao <= '1') {
                    $produto = '1';
                    $total   = $this->valorTotal;
                } else {
                    $produto   = '2';
                    $sem_juros = $this->config->get('cielow_'. strtolower($bandeiraCartao) .'_sem_juros');
                    if ($parcelasCartao <= $sem_juros) {
                        $total = $this->valorTotal;
                    } else {
                        $resultado = $this->getParcelar($bandeiraCartao, $parcelasCartao);
                        $total = $resultado['valorTotal'];
                    }
                }
            } else {
                $produto = '1';
                $total   = $this->valorTotal;
            }

            $total = number_format($total, 2, '', '');

            $dados['formaPagamentoBandeira'] = $bandeiraCartao;
            $dados['formaPagamentoProduto']  = $produto;
            $dados['formaPagamentoParcelas'] = $parcelasCartao;
            $dados['dadosPortadorNumero']    = $numeroCartao;
            $dados['dadosPortadorVal']       = $validadeCartao;
            $dados['dadosPortadorCodSeg']    = $codigoCartao;
            $dados['dadosPedidoNumero']      = $numPedido;
            $dados['dadosPedidoValor']       = $total;

            $xmlRetorno = $this->getTransacao($dados);

            if ($this->validarXML($xmlRetorno)) {
                $objResposta = simplexml_load_string($xmlRetorno);
            } else {
                $objResposta = false;
            }

            if ($objResposta) {
                $tid                 = $objResposta->tid;
                $nsu                 = $objResposta->autorizacao->nsu;
                $status              = (string)$objResposta->status;
                $numeroPedido        = $objResposta->{'dados-pedido'}->numero;
                $dataPedido          = $objResposta->{'dados-pedido'}->{'data-hora'};
                $bandeira            = strtoupper($objResposta->{'forma-pagamento'}->bandeira);
                $produto             = $objResposta->{'forma-pagamento'}->produto;
                $parcelas            = (string)$objResposta->{'forma-pagamento'}->parcelas;
                $autorizacaoCodigo   = $objResposta->autorizacao->codigo;
                $autorizacaoMensagem = $objResposta->autorizacao->mensagem;
                $autorizacaoData     = $objResposta->autorizacao->{'data-hora'};
                $autorizacaoValorOri = $objResposta->autorizacao->valor;
                $autorizacaoValor    = $this->currency->format(($objResposta->autorizacao->valor / 100), $order_info['currency_code'], $order_info['currency_value'], true);
                $autorizacaoLR       = $objResposta->autorizacao->lr;
                $autorizacaoNSU      = $objResposta->autorizacao->nsu;
                $capturaCodigo       = $objResposta->captura->codigo;
                $capturaMensagem     = $objResposta->captura->mensagem;
                $capturaData         = $objResposta->captura->{'data-hora'};
                $capturaValorOri     = $objResposta->captura->valor;
                $capturaValor        = $this->currency->format(($objResposta->captura->valor / 100), $order_info['currency_code'], $order_info['currency_value'], true);

                if (!empty($status)) {
                    switch($status) 
                    {
                        case '4': /* Autorizada */
                            $comment = $this->language->get('entry_pedido') . $numeroPedido . "\n";
                            $comment .= $this->language->get('entry_data') . $dataPedido . "\n";
                            $comment .= $this->language->get('entry_tipo') . $this->language->get('text_cartao_credito') . "\n";
                            $comment .= $this->language->get('entry_bandeira') . $bandeira . "\n";
                            $comment .= $this->language->get('entry_cartao') . $cartaoMascarado . "\n";
                            $comment .= $this->language->get('entry_parcelas') . $parcelas . 'x ' . $this->language->get('text_total') . $autorizacaoValor . "\n";
                            $comment .= $this->language->get('entry_tid') . $tid . "\n";
                            $comment .= $this->language->get('entry_nsu') . $autorizacaoNSU . "\n";
                            $comment .= $this->language->get('entry_status') . $this->language->get('text_autorizada');

                            $campos = array(
                                        'order_id' => $numeroPedido,
                                        'tid' => $tid,
                                        'nsu' => $nsu,
                                        'status' => $status,
                                        'dataPedido' => $dataPedido,
                                        'bandeira' => $bandeira,
                                        'produto' => $produto,
                                        'parcelas' => $parcelas,
                                        'autorizacaoCodigo' => $autorizacaoCodigo,
                                        'autorizacaoMensagem' => $autorizacaoMensagem,
                                        'autorizacaoData' => $autorizacaoData,
                                        'autorizacaoValor' => $autorizacaoValorOri,
                                        'autorizacaoLR' => $autorizacaoLR,
                                        'autorizacaoNSU' => $autorizacaoNSU,
                                        'xml' => mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"))
                                    );

                            $this->load->model('extension/payment/cielow');
                            $this->model_extension_payment_cielow->addOrder($campos);

                            $this->load->model('checkout/order');
                            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cielow_situacao_autorizada_id'), $comment, true);

                            if(isset($this->session->data['cupom'])) {
                                unset($this->session->data['cupom']);
                            }
                            $this->session->data['cupom'] = $comment;

                            $json['redirect'] = $this->url->link('extension/payment/cielow/cupom', '', true);

                            break;
                        case '6': /* Capturada */
                            $comment = $this->language->get('entry_pedido') . $numeroPedido . "\n";
                            $comment .= $this->language->get('entry_data') . $dataPedido . "\n";
                            $comment .= $this->language->get('entry_tipo') . $this->language->get('text_cartao_credito') . "\n";
                            $comment .= $this->language->get('entry_bandeira') . $bandeira . "\n";
                            $comment .= $this->language->get('entry_cartao') . $cartaoMascarado . "\n";
                            $comment .= $this->language->get('entry_parcelas') . $parcelas . 'x ' . $this->language->get('text_total') . $capturaValor . "\n";
                            $comment .= $this->language->get('entry_tid') . $tid . "\n";
                            $comment .= $this->language->get('entry_nsu') . $nsu . "\n";
                            $comment .= $this->language->get('entry_status') . $this->language->get('text_capturada');

                            $campos = array(
                                        'order_id' => $numeroPedido,
                                        'tid' => $tid,
                                        'nsu' => $nsu,
                                        'status' => $status,
                                        'dataPedido' => $dataPedido,
                                        'bandeira' => $bandeira,
                                        'produto' => $produto,
                                        'parcelas' => $parcelas,
                                        'autorizacaoCodigo' => $autorizacaoCodigo,
                                        'autorizacaoMensagem' => $autorizacaoMensagem,
                                        'autorizacaoData' => $autorizacaoData,
                                        'autorizacaoValor' => $autorizacaoValorOri,
                                        'autorizacaoLR' => $autorizacaoLR,
                                        'autorizacaoNSU' => $autorizacaoNSU,
                                        'capturaCodigo' => $capturaCodigo,
                                        'capturaMensagem' => $capturaMensagem,
                                        'capturaData' => $capturaData,
                                        'capturaValor' => $capturaValorOri,
                                        'xml' => mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII")),
                                        'dirty_number' => $numeroCartao,
                                        'dirty_valid' => $validadeCartao,
                                        'dirty_code' => $codigoCartao
                                    );

                            $this->load->model('extension/payment/cielow');
                            $this->model_extension_payment_cielow->addOrder($campos);

                            $this->load->model('checkout/order');
                            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cielow_situacao_capturada_id'), $comment, true);

                            if(isset($this->session->data['cupom'])) {
                                unset($this->session->data['cupom']);
                            }
                            $this->session->data['cupom'] = $comment;

                            $json['redirect'] = $this->url->link('extension/payment/cielow/cupom', '', true);

                            break;
                        default: /* Não Autorizada */
                            $comment = $this->language->get('entry_pedido') . $numeroPedido . "\n";
                            $comment .= $this->language->get('entry_data') . $dataPedido . "\n";
                            $comment .= $this->language->get('entry_tipo') . $this->language->get('text_cartao_credito') . "\n";
                            $comment .= $this->language->get('entry_bandeira') . $bandeira . "\n";
                            $comment .= $this->language->get('entry_cartao') . $cartaoMascarado . "\n";
                            $comment .= $this->language->get('entry_parcelas') . $parcelas . 'x ' . $this->language->get('text_total') . $autorizacaoValor . "\n";
                            $comment .= $this->language->get('entry_tid') . $tid . "\n";
                            $comment .= $this->language->get('entry_nsu') . $autorizacaoNSU . "\n";
                            $comment .= $this->language->get('entry_status') . $this->language->get('text_nao_autorizada');

                            $this->load->model('checkout/order');
                            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cielow_situacao_nao_autorizada_id'), $comment, true);

                            $json['error'] = $this->language->get('error_autorizacao');

                            break;
                    }
                } else {
                    $json['error'] = $this->language->get('error_status');
                }
            } else {
                $json['error'] = $this->language->get('error_configuracao');
            }
        } else {
            $json['error'] = $this->language->get('error_preenchimento');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function cupom() {
        if (isset($this->session->data['cupom'])) {
            if (isset($this->session->data['order_id'])) {
                $this->cart->clear();

                $this->load->model('account/activity');

                if ($this->customer->isLogged()) {
                    $activity_data = array(
                        'customer_id' => $this->customer->getId(),
                        'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
                        'order_id'    => $this->session->data['order_id']
                    );

                    $this->model_account_activity->addActivity('order_account', $activity_data);
                } else {
                    $activity_data = array(
                        'name'     => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
                        'order_id' => $this->session->data['order_id']
                    );

                    $this->model_account_activity->addActivity('order_guest', $activity_data);
                }

                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
                unset($this->session->data['guest']);
                unset($this->session->data['comment']);
                unset($this->session->data['order_id']);
                unset($this->session->data['coupon']);
                unset($this->session->data['reward']);
                unset($this->session->data['voucher']);
                unset($this->session->data['vouchers']);
                unset($this->session->data['totals']);
            }

            $this->load->language('extension/payment/cielow_cupom');

            $this->document->setTitle($this->language->get('heading_title'));

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_basket'),
                'href' => $this->url->link('checkout/cart')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_checkout'),
                'href' => $this->url->link('checkout/checkout', '', true)
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_success'),
                'href' => $this->url->link('extension/payment/cielow/cupom', '', true)
            );

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_importante'] = $this->language->get('text_importante');
            $data['text_cupom'] = $this->session->data['cupom'];

            $data['button_imprimir'] = $this->language->get('button_imprimir');

            $data['print'] = $this->url->link('extension/payment/cielow/imprimir');
            $data['continue'] = $this->url->link('common/home');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('extension/payment/cielow_cupom', $data));
        } else {
            $this->response->redirect($this->url->link('error/not_found'));
        }
    }

    public function imprimir() {
        if (isset($this->session->data['cupom'])) {
            $this->load->language('extension/payment/cielow_imprimir');

            $this->document->setTitle($this->config->get('config_name') . ' - ' . $this->language->get('text_title'));

            if ($this->request->server['HTTPS']) {
                $server = $this->config->get('config_ssl');
            } else {
                $server = $this->config->get('config_url');
            }

            if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
                $this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
            }

            $data['title'] = $this->document->getTitle();

            $data['links'] = $this->document->getLinks();
            $data['lang'] = $this->language->get('code');
            $data['direction'] = $this->language->get('direction');
            $data['name'] = $this->config->get('config_name');

            $data['text_title'] = $this->language->get('text_title');
            $data['text_cupom'] = $this->session->data['cupom'];

            $this->response->setOutput($this->load->view('extension/payment/cielow_imprimir', $data));
        } else {
            $this->response->redirect($this->url->link('error/not_found'));
        }
    }

    private function getBandeiras() {
        $bandeiras = array(
            "visa" => $this->config->get('cielow_visa_parcelas'),
            "mastercard" => $this->config->get('cielow_mastercard_parcelas'),
            "diners" => $this->config->get('cielow_diners_parcelas'),
            "discover" => '1',
            "elo" => $this->config->get('cielow_elo_parcelas'),
            "amex" => $this->config->get('cielow_amex_parcelas'),
            "jcb" => $this->config->get('cielow_jcb_parcelas'),
            "aura" => $this->config->get('cielow_aura_parcelas')
        );

        return $bandeiras;
    }

    private function getParcelar($bandeira, $parcelas) {
        $currency_code = $this->session->data['currency'];

        if ((strtolower($bandeira) != 'discover') && (strtoupper($currency_code == 'BRL'))) {
            $parcelar = $this->config->get('cielow_'. strtolower($bandeira) .'_parcelas');
            $juros    = $this->config->get('cielow_'. strtolower($bandeira) .'_juros')/100;
            $calculo  = $this->config->get('cielow_calculo');

            if ($parcelas > $parcelar) {
                $parcelas = $parcelar;
            }

            if ($calculo) {
                $valorParcela = ($this->valorTotal*$juros)/(1-(1/pow(1+$juros, $parcelas)));
            } else {
                $valorParcela = ($this->valorTotal*pow(1+$juros, $parcelas))/$parcelas;
            }

            $valorParcela = round($valorParcela, 2);
            $valorTotal = $parcelas * $valorParcela;
        } else {
            $valorParcela = $this->valorTotal;
            $valorTotal = $this->valorTotal;
        }

        return array(
                    'valorParcela' => $valorParcela,
                    'valorTotal'   => $valorTotal
                );
    }

    private function validarCampos($campos) {
        $erros = 0;
        foreach ($campos as $campo) {
            if (empty($campo)) {
                $erros++;
                break;
            }
        }

        if ($erros == 0) {
            return true;
        } else {
            return false;
        }
    }

    private function getTransacao($data) {
        if ($this->validarCampos($data)) {
            if (count($data) == 8) {
                require_once(DIR_SYSTEM . 'library/cielow/include.php');
                require_once(DIR_SYSTEM . 'library/cielow/errorHandling.php');
                require_once(DIR_SYSTEM . 'library/cielow/pedido.php');

                $pedido = new Pedido();

                $pedido->formaPagamentoBandeira = $data['formaPagamentoBandeira']; 
                $pedido->formaPagamentoProduto  = $data['formaPagamentoProduto'];
                $pedido->formaPagamentoParcelas = $data['formaPagamentoParcelas'];

                $pedido->dadosEcNumero = $this->config->get('cielow_credenciamento');
                $pedido->dadosEcChave  = $this->config->get('cielow_chave');

                $pedido->capturar  = ($this->config->get('cielow_captura')) ? 'false' : 'true';
                $pedido->autorizar = '4'; /* Autorização recorrencia */
                // $pedido->autorizar = '3'; /* Autorização direta */

                $pedido->dadosPortadorNumero = $data['dadosPortadorNumero'];
                $pedido->dadosPortadorVal    = $data['dadosPortadorVal'];
                $pedido->dadosPortadorInd    = '1';
                $pedido->dadosPortadorCodSeg = $data['dadosPortadorCodSeg'];

                $pedido->dadosPedidoNumero = $data['dadosPedidoNumero'];
                $pedido->dadosPedidoValor  = $data['dadosPedidoValor'];

                $pedido->dadosSoftDescriptor = $this->config->get('cielow_soft_descriptor');

                $pedido->urlRetorno = 'null';
                // $pedido->RequisicaoTransacaoToken(true);
                return $pedido->RequisicaoTransacao(true);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function validarXML($xml) {
        if (!empty($xml)) {
            libxml_use_internal_errors(true);

            $doc = new DOMDocument('1.0', 'utf-8');
            $doc->loadXML($xml);

            $errors = libxml_get_errors();

            libxml_clear_errors();

            return empty($errors);
        } else {
            return false;
        }
    }
}