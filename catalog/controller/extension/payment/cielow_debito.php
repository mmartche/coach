<?php
class ControllerExtensionPaymentCielowDebito extends Controller {
    public function index() {
        $this->load->language('extension/payment/cielow_debito');

        $data['text_ambiente'] = $this->language->get('text_ambiente');
        $data['text_informacoes'] = $this->language->get('text_informacoes');
        $data['text_detalhes'] = $this->language->get('text_detalhes');
        $data['text_carregando'] = $this->language->get('text_carregando');
        $data['text_validando'] = $this->language->get('text_validando');
        $data['text_redirecionando'] = $this->language->get('text_redirecionando');
        $data['text_mes'] = $this->language->get('text_mes');
        $data['text_ano'] = $this->language->get('text_ano');

        $data['button_pagar'] = $this->language->get('button_pagar');

        $data['entry_bandeira'] = $this->language->get('entry_bandeira');
        $data['entry_cartao'] = $this->language->get('entry_cartao');
        $data['entry_validade_mes'] = $this->language->get('entry_validade_mes');
        $data['entry_validade_ano'] = $this->language->get('entry_validade_ano');
        $data['entry_codigo'] = $this->language->get('entry_codigo');
        $data['entry_total'] = $this->language->get('entry_total');

        $data['error_cartao'] = $this->language->get('error_cartao');
        $data['error_mes'] = $this->language->get('error_mes');
        $data['error_ano'] = $this->language->get('error_ano');
        $data['error_codigo'] = $this->language->get('error_codigo');
        $data['error_validacao'] = $this->language->get('error_validacao');
        $data['error_bandeiras'] = $this->language->get('error_bandeiras');
        $data['error_nao_validou'] = $this->language->get('error_nao_validou');

        $data['ambiente'] = $this->config->get('cielow_debito_ambiente');

        $data['cor_normal_texto'] = $this->config->get('cielow_debito_cor_normal_texto');
        $data['cor_normal_fundo'] = $this->config->get('cielow_debito_cor_normal_fundo');
        $data['cor_normal_borda'] = $this->config->get('cielow_debito_cor_normal_borda');
        $data['cor_efeito_texto'] = $this->config->get('cielow_debito_cor_efeito_texto');
        $data['cor_efeito_fundo'] = $this->config->get('cielow_debito_cor_efeito_fundo');
        $data['cor_efeito_borda'] = $this->config->get('cielow_debito_cor_efeito_borda');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], true);

        $bandeiras = array();
        foreach ($this->getBandeiras() as $bandeira) {
            ($this->config->get('cielow_debito_' . $bandeira)) ? array_push($bandeiras, $bandeira) : '';
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

        return $this->load->view('extension/payment/cielow_debito', $data);
    }

    public function transacao() {
        $this->language->load('extension/payment/cielow_debito');

        $json = array();

        $bandeiras = $this->getBandeiras();

        $bandeiraCartao = trim($this->request->post['bandeira']);
        $numeroCartao   = preg_replace("/[^0-9]/", '', $this->request->post['cartao']);
        $validadeMes    = preg_replace("/[^0-9]/", '', $this->request->post['mes']);
        $validadeAno    = preg_replace("/[^0-9]/", '', $this->request->post['ano']);
        $validadeCartao = $validadeAno . $validadeMes;
        $codigoCartao   = preg_replace("/[^0-9]/", '', $this->request->post['codigo']);

        $campos = array($bandeiraCartao, $numeroCartao, $validadeCartao, $codigoCartao);

        if ($this->validarCampos($campos) && in_array(strtolower($bandeiraCartao), $bandeiras)) {
            $numPedido = $this->session->data['order_id'];

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($numPedido);

            $valorTotal = $this->currency->format($order_info['total'], $order_info['currency_code'], '1.00', false);
            $total = number_format($valorTotal, 2, '', '');

            require_once(DIR_SYSTEM . 'library/cielow/include.php');
            require_once(DIR_SYSTEM . 'library/cielow/errorHandling.php');
            require_once(DIR_SYSTEM . 'library/cielow/pedido.php');

            $pedido = new Pedido();

            $pedido->formaPagamentoBandeira = $bandeiraCartao;
            $pedido->formaPagamentoProduto  = 'A';
            $pedido->formaPagamentoParcelas = '1';
            $pedido->dadosEcNumero          = $this->config->get('cielow_debito_credenciamento');
            $pedido->dadosEcChave           = $this->config->get('cielow_debito_chave');
            $pedido->capturar               = 'true';
            $pedido->autorizar              = '2'; /* Autorizar autenticada e não autenticada */
            $pedido->dadosPortadorNumero    = $numeroCartao;
            $pedido->dadosPortadorVal       = $validadeCartao;
            $pedido->dadosPortadorInd       = '1';
            $pedido->dadosPortadorCodSeg    = $codigoCartao;
            $pedido->dadosPedidoNumero      = $numPedido;
            $pedido->dadosPedidoValor       = $total;
            $pedido->dadosSoftDescriptor    = $this->config->get('cielow_debito_soft_descriptor');
            $pedido->urlRetorno             = HTTPS_SERVER . 'index.php?route=extension/payment/cielow_debito/retorno';

            $xmlRetorno = $pedido->RequisicaoTransacao(true);

            if ($this->validarXML($xmlRetorno)) {
                $objResposta = simplexml_load_string($xmlRetorno);
            } else {
                $objResposta = false;
            }

            if ($objResposta) {
                $tid          = $objResposta->tid;
                $status       = $objResposta->status;
                $numeroPedido = $objResposta->{'dados-pedido'}->numero;
                $dataPedido   = $objResposta->{'dados-pedido'}->{'data-hora'};
                $bandeira     = strtoupper($objResposta->{'forma-pagamento'}->bandeira);
                $produto      = $objResposta->{'forma-pagamento'}->produto;
                $parcelas     = (string)$objResposta->{'forma-pagamento'}->parcelas;

                if (!empty($status)) {
                    switch($status)
                    {
                        case '0': /* Criada */
                            $campos = array(
                                        'order_id' => $numeroPedido,
                                        'tid' => $tid,
                                        'status' => $status,
                                        'dataPedido' => $dataPedido,
                                        'bandeira' => $bandeira,
                                        'produto' => $produto,
                                        'parcelas' => $parcelas,
                                        'xml' => mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"))
                                    );
                            $this->load->model('extension/payment/cielow_debito');
                            $this->model_extension_payment_cielow_debito->addOrder($campos);
                            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cielow_debito_situacao_pendente_id'), $this->language->get('text_criada'), true);
                            $json['redirect'] = (string)$objResposta->{'url-autenticacao'};
                            break;
                        case '1': /* Em andamento */
                            $campos = array(
                                        'order_id' => $numeroPedido,
                                        'tid' => $tid,
                                        'status' => $status,
                                        'dataPedido' => $dataPedido,
                                        'bandeira' => $bandeira,
                                        'produto' => $produto,
                                        'parcelas' => $parcelas,
                                        'xml' => mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"))
                                    );
                            $this->load->model('extension/payment/cielow_debito');
                            $this->model_extension_payment_cielow_debito->addOrder($campos);
                            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cielow_debito_situacao_pendente_id'), $this->language->get('text_criada'), false);
                            $json['redirect'] = (string)$objResposta->{'url-autenticacao'};
                            break;
                        default:
                            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cielow_debito_situacao_pendente_id'), $this->language->get('text_nao_validado'), false);
                            if ($status = '3') { /* Não autenticada */
                                $json['error'] = $this->language->get('error_autenticacao');
                            } else if ($status = '5') { /* Não autorizada */
                                $json['error'] = $this->language->get('error_autorizacao');
                            } else {
                                $json['error'] = $this->language->get('error_validacao');
                            }
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

    public function retorno() {
        $this->language->load('extension/payment/cielow_debito');

        if (isset($this->session->data['order_id'])) {
            $this->session->data['falhou'] = true;

            $order_id = $this->session->data['order_id'];

            $this->load->model('extension/payment/cielow_debito');
            $transaction_info = $this->model_extension_payment_cielow_debito->getOrder($order_id);

            $transaction_id = $transaction_info['order_cielow_id'];
            $tid = $transaction_info['tid'];

            require_once(DIR_SYSTEM . 'library/cielow/include.php');
            require_once(DIR_SYSTEM . 'library/cielow/errorHandling.php');
            require_once(DIR_SYSTEM . 'library/cielow/pedido.php');

            $pedido = new Pedido();

            $pedido->dadosEcNumero = $this->config->get('cielow_debito_credenciamento');
            $pedido->dadosEcChave  = $this->config->get('cielow_debito_chave');
            $pedido->tid           = $tid;

            $xmlRetorno = $pedido->RequisicaoConsulta();

            if ($this->validarXML($xmlRetorno)) {
                $objResposta = simplexml_load_string($xmlRetorno);
            } else {
                $objResposta = false;
            }

            if ($objResposta) {
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);

                $tid          = $objResposta->tid;
                $status       = $objResposta->status;
                $numeroPedido = $objResposta->{'dados-pedido'}->numero;
                $dataPedido   = $objResposta->{'dados-pedido'}->{'data-hora'};
                $bandeira     = strtoupper($objResposta->{'forma-pagamento'}->bandeira);
                $produto      = $objResposta->{'forma-pagamento'}->produto;
                $parcelas     = (string)$objResposta->{'forma-pagamento'}->parcelas;

                if (!empty($status)) {
                    switch($status)
                    {
                        case '4': /* Autorizada */
                            $nsu                 = $objResposta->autorizacao->nsu;
                            $autorizacaoCodigo   = $objResposta->autorizacao->codigo;
                            $autorizacaoMensagem = $objResposta->autorizacao->mensagem;
                            $autorizacaoData     = $objResposta->autorizacao->{'data-hora'};
                            $autorizacaoValorOri = $objResposta->autorizacao->valor;
                            $autorizacaoValor    = $this->currency->format(($objResposta->autorizacao->valor / 100), $order_info['currency_code'], $order_info['currency_value'], true);
                            $autorizacaoLR       = $objResposta->autorizacao->lr;
                            $autorizacaoNSU      = $objResposta->autorizacao->nsu;

                            $comment  = $this->language->get('entry_pedido') . $numeroPedido . "\n";
                            $comment .= $this->language->get('entry_data') . $dataPedido . "\n";
                            $comment .= $this->language->get('entry_tipo') . $this->language->get('text_cartao_debito') . "\n";
                            $comment .= $this->language->get('entry_bandeira') . $bandeira . "\n";
                            $comment .= $this->language->get('entry_total') . $autorizacaoValor . "\n";
                            $comment .= $this->language->get('entry_tid') . $tid . "\n";
                            $comment .= $this->language->get('entry_nsu') . $autorizacaoNSU . "\n";
                            $comment .= $this->language->get('entry_status') . $this->language->get('text_autorizada');

                            $dados['order_id'] = $numeroPedido;
                            $dados['tid'] = $tid;
                            $dados['nsu'] = $nsu;
                            $dados['status'] = $status;
                            $dados['dataPedido'] = $dataPedido;
                            $dados['bandeira'] = $bandeira;
                            $dados['produto'] = $produto;
                            $dados['parcelas'] = $parcelas;
                            $dados['autorizacaoCodigo'] = $autorizacaoCodigo;
                            $dados['autorizacaoMensagem'] = $autorizacaoMensagem;
                            $dados['autorizacaoData'] = $autorizacaoData;
                            $dados['autorizacaoValor'] = $autorizacaoValorOri;
                            $dados['autorizacaoLR'] = $autorizacaoLR;
                            $dados['autorizacaoNSU'] = $autorizacaoNSU;
                            $dados['capturaCodigo'] = '';
                            $dados['capturaMensagem'] = '';
                            $dados['capturaData'] = '';
                            $dados['capturaValor'] = '';
                            $dados['xml'] = mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"));

                            $this->load->model('extension/payment/cielow_debito');
                            $this->model_extension_payment_cielow_debito->editOrder($transaction_id, $dados);
                            $this->model_checkout_order->addOrderHistory($numeroPedido, $this->config->get('cielow_debito_situacao_autorizada_id'), $comment, true);

                            if(isset($this->session->data['cupom'])) {
                                unset($this->session->data['cupom']);
                            }
                            $this->session->data['cupom'] = $comment;

                            break;
                        case '6': /* Capturada */
                            $nsu                 = $objResposta->autorizacao->nsu;
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

                            $comment  = $this->language->get('entry_pedido') . $numeroPedido . "\n";
                            $comment .= $this->language->get('entry_data') . $dataPedido . "\n";
                            $comment .= $this->language->get('entry_tipo') . $this->language->get('text_cartao_debito') . "\n";
                            $comment .= $this->language->get('entry_bandeira') . $bandeira . "\n";
                            $comment .= $this->language->get('entry_total') . $autorizacaoValor . "\n";
                            $comment .= $this->language->get('entry_tid') . $tid . "\n";
                            $comment .= $this->language->get('entry_nsu') . $nsu . "\n";
                            $comment .= $this->language->get('entry_status') . $this->language->get('text_capturada');

                            $dados['order_id'] = $numeroPedido;
                            $dados['tid'] = $tid;
                            $dados['nsu'] = $nsu;
                            $dados['status'] = $status;
                            $dados['dataPedido'] = $dataPedido;
                            $dados['bandeira'] = $bandeira;
                            $dados['produto'] = $produto;
                            $dados['parcelas'] = $parcelas;
                            $dados['autorizacaoCodigo'] = $autorizacaoCodigo;
                            $dados['autorizacaoMensagem'] = $autorizacaoMensagem;
                            $dados['autorizacaoData'] = $autorizacaoData;
                            $dados['autorizacaoValor'] = $autorizacaoValorOri;
                            $dados['autorizacaoLR'] = $autorizacaoLR;
                            $dados['autorizacaoNSU'] = $autorizacaoNSU;
                            $dados['capturaCodigo'] = $capturaCodigo;
                            $dados['capturaMensagem'] = $capturaMensagem;
                            $dados['capturaData'] = $capturaData;
                            $dados['capturaValor'] = $capturaValorOri;
                            $dados['xml'] = mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"));

                            $this->load->model('extension/payment/cielow_debito');
                            $this->model_extension_payment_cielow_debito->editOrder($transaction_id, $dados);
                            $this->model_checkout_order->addOrderHistory($numeroPedido, $this->config->get('cielow_debito_situacao_capturada_id'), $comment, true);

                            if(isset($this->session->data['cupom'])) {
                                unset($this->session->data['cupom']);
                            }
                            $this->session->data['cupom'] = $comment;

                            break;
                        default: 
                            if ($status = '3') { /* Não autenticada */
                                $status_text = $this->language->get('text_nao_autenticada');
                            } else if ($status = '5') { /* Não autorizada */
                                $status_text = $this->language->get('text_nao_autorizada');
                            } else {
                                $status_text = $this->language->get('text_nao_validada');
                            }

                            $comment  = $this->language->get('entry_pedido') . $numeroPedido . "\n";
                            $comment .= $this->language->get('entry_data') . $dataPedido . "\n";
                            $comment .= $this->language->get('entry_tipo') . $this->language->get('text_cartao_debito') . "\n";
                            $comment .= $this->language->get('entry_bandeira') . $bandeira . "\n";
                            $comment .= $this->language->get('entry_tid') . $tid . "\n";
                            $comment .= $this->language->get('entry_status') . $status_text;

                            $dados['order_id'] = $numeroPedido;
                            $dados['tid'] = $tid;
                            $dados['nsu'] = '';
                            $dados['status'] = $status;
                            $dados['dataPedido'] = $dataPedido;
                            $dados['bandeira'] = $bandeira;
                            $dados['produto'] = $produto;
                            $dados['parcelas'] = $parcelas;
                            $dados['autorizacaoCodigo'] = '';
                            $dados['autorizacaoMensagem'] = '';
                            $dados['autorizacaoData'] = '';
                            $dados['autorizacaoValor'] = '';
                            $dados['autorizacaoLR'] = '';
                            $dados['autorizacaoNSU'] = '';
                            $dados['capturaCodigo'] = '';
                            $dados['capturaMensagem'] = '';
                            $dados['capturaData'] = '';
                            $dados['capturaValor'] = '';
                            $dados['xml'] = mb_convert_encoding($xmlRetorno,'UTF-8',mb_detect_encoding($xmlRetorno,"ISO-8859-1, UTF-8, ASCII"));

                            $this->load->model('extension/payment/cielow_debito');
                            $this->model_extension_payment_cielow_debito->editOrder($transaction_id, $dados);
                            $this->model_checkout_order->addOrderHistory($numeroPedido, $this->config->get('cielow_debito_situacao_nao_autorizada_id'), $comment, false);
                            break;
                    }

                    if (($status == '4') || ($status == '6')) {
                        $this->response->redirect($this->url->link('extension/payment/cielow_debito/cupom', '', true));
                    } else {
                        $this->response->redirect($this->url->link('extension/payment/cielow_debito/pagamento', '', true));
                    }
                } else {
                    $this->response->redirect($this->url->link('extension/payment/cielow_debito/pagamento', '', true));
                }
            } else {
               $this->response->redirect($this->url->link('extension/payment/cielow_debito/pagamento', '', true));
            }
        } else {
            $this->response->redirect($this->url->link('error/not_found'));
        }
    }

    public function pagamento() {
        if (isset($this->session->data['order_id'])) {
            $this->load->language('extension/payment/cielow_pagamento');

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
                'text' => $this->language->get('text_pagamento'),
                'href' => $this->url->link('extension/payment/cielow_debito/pagamento', '', true)
            );

            $this->document->addScript('catalog/view/javascript/jquery/cielow/loading/js/jquery.loading.v1.min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/cielow/loading/css/jquery.loading.v1.min.css');
            $this->document->addStyle('catalog/view/javascript/jquery/cielow/skeleton/normalize.css');
            $this->document->addStyle('catalog/view/javascript/jquery/cielow/skeleton/skeleton.v2.css');

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_ambiente'] = $this->language->get('text_ambiente');
            $data['text_informacoes'] = $this->language->get('text_informacoes');
            $data['text_detalhes'] = $this->language->get('text_detalhes');
            $data['text_carregando'] = $this->language->get('text_carregando');
            $data['text_validando'] = $this->language->get('text_validando');
            $data['text_redirecionando'] = $this->language->get('text_redirecionando');
            $data['text_mes'] = $this->language->get('text_mes');
            $data['text_ano'] = $this->language->get('text_ano');

            $data['button_pagar'] = $this->language->get('button_pagar');

            $data['entry_bandeira'] = $this->language->get('entry_bandeira');
            $data['entry_cartao'] = $this->language->get('entry_cartao');
            $data['entry_validade_mes'] = $this->language->get('entry_validade_mes');
            $data['entry_validade_ano'] = $this->language->get('entry_validade_ano');
            $data['entry_codigo'] = $this->language->get('entry_codigo');
            $data['entry_total'] = $this->language->get('entry_total');

            $data['error_cartao'] = $this->language->get('error_cartao');
            $data['error_mes'] = $this->language->get('error_mes');
            $data['error_ano'] = $this->language->get('error_ano');
            $data['error_codigo'] = $this->language->get('error_codigo');
            $data['error_validacao'] = $this->language->get('error_validacao');
            $data['error_bandeiras'] = $this->language->get('error_bandeiras');
            $data['error_nao_validou'] = $this->language->get('error_nao_validou');

            $data['ambiente'] = $this->config->get('cielow_debito_ambiente');

            $data['cor_normal_texto'] = $this->config->get('cielow_debito_cor_normal_texto');
            $data['cor_normal_fundo'] = $this->config->get('cielow_debito_cor_normal_fundo');
            $data['cor_normal_borda'] = $this->config->get('cielow_debito_cor_normal_borda');
            $data['cor_efeito_texto'] = $this->config->get('cielow_debito_cor_efeito_texto');
            $data['cor_efeito_fundo'] = $this->config->get('cielow_debito_cor_efeito_fundo');
            $data['cor_efeito_borda'] = $this->config->get('cielow_debito_cor_efeito_borda');

            $data['falhou'] = false;

            if (isset($this->session->data['falhou'])) {
                $data['falhou'] = $this->session->data['falhou'];
                unset($this->session->data['falhou']);
            }

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

            $data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], true);

            $bandeiras = array();
            foreach ($this->getBandeiras() as $bandeira) {
                ($this->config->get('cielow_debito_' . $bandeira)) ? array_push($bandeiras, $bandeira) : '';
            }
            $data['bandeiras'] = $bandeiras;
            $data['colunas']   = count($bandeiras);

            $data['meses'] = array();
            for ($i = 1; $i <= 12; $i++) {
                $data['meses'][] = array(
                    'text'  => sprintf('%02d', $i),
                    'value' => sprintf('%02d', $i)
                );
            }

            $hoje = getdate();
            $data['anos'] = array();
            for ($i = $hoje['year']; $i < $hoje['year'] + 20; $i++) {
                $data['anos'][] = array(
                    'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                    'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
                );
            }

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('extension/payment/cielow_pagamento', $data));
        } else {
            $this->response->redirect($this->url->link('error/not_found'));
        }
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

            $data['print'] = $this->url->link('extension/payment/cielow_debito/imprimir');
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
            "visa",
            "mastercard",
        );

        return $bandeiras;
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