<?php
class ModelExtensionPaymentCielowDebito extends Model {
    public function getMethod($address, $total) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('cielow_debito_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('cielow_debito_total') > 0 && $this->config->get('cielow_debito_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('cielow_debito_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            if (strlen(trim($this->config->get('cielow_debito_imagem'))) > 0) {
                $title = '<img src="'.HTTPS_SERVER.'image/'.$this->config->get('cielow_debito_imagem').'" alt="'.$this->config->get('cielow_debito_titulo').'" title="'.$this->config->get('cielow_debito_titulo').'" />';
            } else {
                $title = $this->config->get('cielow_debito_titulo');
            }

              $method_data = array( 
                'code'       => 'cielow_debito',
                'title'      => $title,
                'terms'      => '',
                'sort_order' => $this->config->get('cielow_debito_sort_order')
              );
        }

        return $method_data;
    }

    public function getOrder($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_cielow` WHERE order_id = '" . (int)$order_id . "' AND produto = 'A' ORDER BY order_cielow_id DESC LIMIT 1");

        return $query->row;
    }

    public function addOrder($data) {
        $columns = implode(", ", array_keys($data));
        $values  = "'".implode("', '", array_values($data))."'";
        $this->db->query("INSERT INTO `" . DB_PREFIX . "order_cielow` ($columns) VALUES ($values)");
    }

    public function editOrder($order_cielow_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "order_cielow SET tid = '" . $data['tid'] . "', `nsu` = '" . $data['nsu'] . "', `status` = '" . $data['status'] . "', dataPedido = '" . $data['dataPedido'] . "', bandeira = '" . $data['bandeira'] . "', produto = '" . $data['produto'] . "', parcelas = '" . $data['parcelas'] . "', autorizacaoCodigo = '" . $data['autorizacaoCodigo'] . "', autorizacaoMensagem = '" . $data['autorizacaoMensagem'] . "', autorizacaoData = '" . $data['autorizacaoData'] . "', autorizacaoValor = '" . $data['autorizacaoValor'] . "', autorizacaoLR = '" . $data['autorizacaoLR'] . "', autorizacaoNSU = '" . $data['autorizacaoNSU'] . "', capturaCodigo = '" . $data['capturaCodigo'] . "', capturaMensagem = '" . $data['capturaMensagem'] . "', capturaData = '" . $data['capturaData'] . "', capturaValor = '" . $data['capturaValor'] . "', xml = '" . $data['xml'] . "' WHERE order_cielow_id = '" . (int)$order_cielow_id . "'");
    }
}