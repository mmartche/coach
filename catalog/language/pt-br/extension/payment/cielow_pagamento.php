<?php
// Heading
$_['heading_title']        = 'Dados para o pagamento';

// Text
$_['text_basket']          = 'Carrinho de compras';
$_['text_checkout']        = 'Finalizar pedido';
$_['text_pagamento']       = 'Cartão de débito';
$_['text_payment']         = 'Seu pedido será enviado assim que o pagamento for confirmado.';
$_['text_detalhes']        = 'Detalhes do cartão de débito:';
$_['text_carregando']      = 'Carregando...';
$_['text_validando']       = 'Validando dados do cartão...';
$_['text_redirecionando']  = 'Aguarde o redirecionamento...';
$_['text_cartao_debito']   = 'Cartão de Débito';
$_['text_visa']            = 'Visa Electron';
$_['text_mastercard']      = 'Maestro';
$_['text_mes']             = 'Mês';
$_['text_ano']             = 'Ano';
$_['text_criada']          = 'Criada (Aguardando pagamento)';
$_['text_autorizada']      = 'Autorizada (Pré-aprovada)';
$_['text_capturada']       = 'Capturada (Aprovada)';
$_['text_nao_validada']    = 'Não validada';
$_['text_nao_autenticada'] = 'Não autenticada';
$_['text_nao_autorizada']  = 'Não autorizada';
$_['text_ambiente']        = '<strong>ATENÇÃO:</strong><br />- Você está no ambiente de teste, em resumo, nenhum pagamento será validado.<br />- Não utilize dados de cartões válidos, utilize informações genéricas para teste.<br />- Serão autorizadas as transações com valores que não contenham centavos.<br />- Serão negadas as transações com valores que contenham centavos.';
$_['text_informacoes']     = '<strong>IMPORTANTE:</strong><br />- <strong>Só utilize cartões que sejam de débito e crédito</strong>, pois cartões que são somente débito, não são autorizados para pagamento através de lojas online.<br />- <strong>Utilize o código de segurança que está no verso do seu cartão</strong>, se o seu cartão não possui código de segurança no verso, ele não é autorizado para este pagamento.<br />- Após a digitação dos dados do seu cartão de débito, você será direcionado(a) para fazer uma <strong>autenticação no banco emissor do seu cartão</strong>.<br />- Tenha em mãos o seu <strong>celular habilitado no Internet Banking, token eletrônico, cartão de códigos, CPF e senha do cartão de débito</strong>, para o processo de autenticação.<br />- <strong>Caso seu pagamento não seja autenticado ou autorizado, tente só mais uma vez</strong>, pois o banco emissor do seu cartão poderá bloquear o pagamento por 24 horas.';

// Button
$_['button_pagar']         = 'Confirmar pagamento';

// Entry
$_['entry_bandeira']       = 'Bandeira do cartão: ';
$_['entry_total']          = 'Total: ';
$_['entry_cartao']         = 'Número do seu cartão ';
$_['entry_validade_mes']   = 'Cartão válido até o mês: ';
$_['entry_validade_ano']   = 'Cartão válido até o ano: ';
$_['entry_codigo']         = 'Código de segurança (CVV): ';
$_['entry_pedido']         = 'Pedido: ';
$_['entry_data']           = 'Data: ';
$_['entry_tipo']           = 'Pago com: ';
$_['entry_tid']            = 'TID: ';
$_['entry_nsu']            = 'NSU: ';
$_['entry_status']         = 'Status: ';
$_['entry_erro']           = 'Erro: ';
$_['entry_mensagem']       = 'Mensagem: ';

// Error
$_['error_cartao']         = 'O número do cartão não é válido.';
$_['error_mes']            = 'Selecione o mês.';
$_['error_ano']            = 'Selecione o ano.';
$_['error_codigo']         = 'O código de segurança não é válido.';
$_['error_preenchimento']  = '<strong>Atenção:</strong><br />Todos os campos são de preenchimento obrigatório.';
$_['error_configuracao']   = '<strong>Atenção:</strong><br />Não foi possível autorizar o seu pagamento por problemas técnicos.<br />Tente novamente ou selecione outra forma de pagamento.<br />Em caso de dúvidas, entre em contato com nosso atendimento.';
$_['error_status']         = '<strong>Atenção:</strong><br />Não foi possível obter uma resposta sobre a autorização do seu pagamento.<br />Tente novamente ou selecione outra forma de pagamento.<br />Em caso de dúvidas, entre em contato com nosso atendimento.';
$_['error_validacao']      = '<strong>Os dados do cartão não são válidos.</strong><br />Verifique se você preencheu todos os campos corretamente e se você possui limite disponível para o pagamento do pedido.<br /><strong>Observação:</strong> Se seu cartão for apenas débito, ele não possuirá o código de segurança (CVV), e não poderá ser utilizado em compras online.<br /><strong>Importante:</strong> Se o seu cartão estiver bloqueado ou com alguma restrição, seu pagamento não será autorizado.<br />Para mais informações entre em contato com o banco emissor do seu cartão.<br /><br />Você pode tentar outro cartão ou selecionar outra forma de pagamento.<br />Em caso de dúvidas, entre em contato com nosso atendimento.';
$_['error_autenticacao']   = '<strong>Seu pagamento não foi autenticado.</strong><br />Verifique se você preencheu todos os campos corretamente e se você possui limite disponível para o pagamento do pedido.<br /><strong>Observação:</strong> Se seu cartão for apenas débito, ele não possuirá o código de segurança (CVV), e não poderá ser utilizado em compras online.<br /><strong>Importante:</strong> Se o seu cartão estiver bloqueado ou com alguma restrição, seu pagamento não será autorizado.<br />Para mais informações entre em contato com o banco emissor do seu cartão.<br /><br />Você pode tentar outro cartão ou selecionar outra forma de pagamento.<br />Em caso de dúvidas, entre em contato com nosso atendimento.';
$_['error_autorizacao']    = '<strong>Seu pagamento não foi autorizado.</strong><br />Verifique se você preencheu todos os campos corretamente e se você possui limite disponível para o pagamento do pedido.<br /><strong>Observação:</strong> Se seu cartão for apenas débito, ele não possuirá o código de segurança (CVV), e não poderá ser utilizado em compras online.<br /><strong>Importante:</strong> Se o seu cartão estiver bloqueado ou com alguma restrição, seu pagamento não será autorizado.<br />Para mais informações entre em contato com o banco emissor do seu cartão.<br /><br />Você pode tentar outro cartão ou selecionar outra forma de pagamento.<br />Em caso de dúvidas, entre em contato com nosso atendimento.';
$_['error_bandeiras']      = '<strong>Atenção:</strong><br />Nenhum cartão foi habilitado nas configurações da extensão.';