<?php

class ControllerAtivocoachSendtoapi extends Controller {
	public function index()	{
		$this->load->language('ativocoach/ativocoach');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('ativocoach_description'));
		$this->document->setKeywords($this->language->get('ativocoach_keywords'));

		$filter_data = array (
			'email'	=> (!empty($this->request->get['email'])) ? $this->request->get['email'] : ""
		);
		$this->load->model('ativocoach/ativocoach');
		$results = $this->model_ativocoach_ativocoach->getDataToApi($filter_data);
		if ($results) {
			foreach ($results as $result => $key) {
				$treinador = array();
				//get default data
				$treinador['idAtivo'] 			= $key['customer_id'];
				$treinador['nome'] 				= $key['firstname'];
				$treinador['email'] 			= $key['email'];
				$treinador['senha']				= $key['dirty_password'];
				$treinador['confirmarSenha']	= $key['dirty_password'];
				$treinador['status']			= $key['verified'];
				//search custom field data
				foreach (json_decode($key['custom_field']) as $keyCustom => $valueCustom) {
					$customFieldData = array(
						'language_id' 		=> (int)$this->config->get('config_language_id'),
						'custom_field_id' 	=> $keyCustom
					);
					$dataFieldName = $this->model_ativocoach_ativocoach->getCustomField($customFieldData);
					$treinador[$dataFieldName['json_name']] = $valueCustom;
				}

				$customAddress = array(
					'customer_id' 	=> $key['customer_id']
				);
				$dataAddress = $this->model_ativocoach_ativocoach->getCustomerAddress($customAddress);
				//search addresss
				$treinador['endereco'] = array();
				$treinador['endereco']['idEndereco'] 	= $dataAddress['address_id'];
				$treinador['endereco']['logradouro'] 	= $dataAddress['address_1'];
				$treinador['endereco']['complemento'] 	= $dataAddress['address_2'];
				$treinador['endereco']['cep'] 			= $dataAddress['postcode'];
				$treinador['endereco']['cidade'] 		= $dataAddress['city'];
				$treinador['endereco']['estado'] 		= $dataAddress['estado'];

				//search coach plan
				$customerPlano = array(
					'language_id' => (int)$this->config->get('config_language_id'),
					'customer_id' => $key['customer_id']
				);
				$dataPlano = $this->model_ativocoach_ativocoach->getCustomerPlano($customerPlano);
				$treinador['planos'] = array();
				for ($dP=0; $dP < count($dataPlano); $dP++) { 
					$treinador['planos'][$dP]['dataVencimentoPlano']	= $dataPlano[$dP]['dataVencimentoPlano'];
					$treinador['planos'][$dP]['qtdMaximaAlunos']		= $dataPlano[$dP]['qtdMaximaAlunos'];
					$treinador['planos'][$dP]['nomePlano']				= $dataPlano[$dP]['nomePlano'];
				}


				$json_return = "{ \"treinador\":".json_encode($treinador)."}";
				var_dump(($json_return));
				print_r("<hr />**");
			}
		} else {
			print_r('{
				"Success": false,
				"Message": "Sem lista de Clientes Modificados",
				"Code": 0
					}');
		}
	}
}

/*
s:47:"{"1":"","4":"3589","2":"","6":"6560640","5":""}";
{
"treinador": {
	"idAtivo": 298,
	"nome": "",
	"email": "",
	"senha": "", //SHA1(abcabc)                      Enviar senha sha1, criação de campo extra e manda em formato sha1
	"confirmarSenha": "", //SHA1(abcabc)  Enviar senha sha1, criação de campo extra e manda em formato sha1
	"status": 1, //1 - ok, 2 - falta confirmar e-mail                                                                                                                  
	"dataVencimentoPlano": "2017-03-01T00:00:00",
	"qtdMaximaAlunos": 200, //-1 para ilimitado
	"nomePlano": "Plano Gold",
	"cpf": "",
	"cnpj": , //ou CPF ou CNPJ não podem ser nulos
	"dataNascimento": "1988-08-01T00:00:00",
	 
	//opcional: enviar tudo Vamos utilizar apenas 1 endereço, vale para  cobrança
	"empresa": "Martinlabs",
	"nomeFantasia": "Gil LB",
	"telefone": "(11) 9706-29099",
	"ehHomem": true,
	"cref": "mwekj29802",
	"endereco": {
		"idEndereco": 0,
		"cep": "03070-000",
		"numero": "121",
		"complemento": "BL Veneza Apto 163",
		"logradouro": "Rua Melo Peixoto",
		"cidade": "São Paulo",
		"estado": "SP"
		}
},

Nome
Email
Senha
ConfirmarSenha
Cpf
Cnpj
DataNascimento
Empresa
NomeFantasia
Telefone
Sexo
Cref
Cep
Numero
Complemento
Logradouro
Cidade
Estado
Especialidades
*/