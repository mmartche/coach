<?php
class ControllerJournal2Search extends Controller {

    private static $SHOW_PRICE = true;
    private static $SHOW_IMAGES = true;

    protected $data = array();

    protected function render() {
        if (version_compare(VERSION, '2.2', '<')) {
            $this->template = $this->config->get('config_template') . '/template/' . $this->template;
        }

        $this->template = str_replace($this->config->get('config_template') . '/template/' . $this->config->get('config_template') . '/template/', $this->config->get('config_template') . '/template/', $this->template);

        return Front::$IS_OC2 ? $this->load->view($this->template, $this->data) : parent::render();
    }

    public function __construct($reg) {
        parent::__construct($reg);
    }

    public function index() {
        $this->load->model('journal2/search');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $json = array('results' => array());

        if(isset($this->request->get['search'])) {

            $results = $this->model_journal2_search->search($this->request->get['search'], $this->journal2->settings->get('autosuggest_limit', 0), $this->journal2->settings->get('search_autocomplete_include_description', '1') === '1');

            $image_width    = $this->journal2->settings->get('autosuggest_product_image_width', 50);
            $image_height   = $this->journal2->settings->get('autosuggest_product_image_height', 50);
            $image_type     = $this->journal2->settings->get('autosuggest_product_image_type', 'fit');

            foreach($results as $result) {
                $result = $this->model_catalog_product->getProduct($result['product_id']);
                if (self::$SHOW_IMAGES) {
                    $image = Journal2Utils::resizeImage($this->model_tool_image, $result['image'], $image_width, $image_height, $image_type);
                } else {
                    $image = null;
                }

                if (self::$SHOW_PRICE && (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price'))) {
                    $price = Journal2Utils::currencyFormat($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = null;
                }

                if (self::$SHOW_PRICE && (float)$result['special']) {
                    $special = Journal2Utils::currencyFormat($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $special = null;
                }

                $json['results'][] = array(
                    'name'  => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
                    'url'   => htmlspecialchars_decode($this->url->link('product/product', '&product_id=' . $result['product_id'])),
                    'image' => $image,
                    'price' => $price,
                    'special' => $special
                );
            }

            if ($json['results']) {
                $json['view_more_text'] = $this->journal2->settings->get('autosuggest_view_more_text', 'View More');
                if (VERSION === '1.5.4' || VERSION === '1.5.4.1') {
                    $json['view_more_url'] = $this->url->link('product/search', '&filter_name=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8')));
                } else {
                    $json['view_more_url'] = $this->url->link('product/search', '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8')) . ($this->journal2->settings->get('search_autocomplete_include_description', '1') === '1' ? '&description=true' : ''));
                }
            } else {
                $this->language->load('product/search');
                $json['view_more_text'] = $this->language->get('text_empty');
                $json['view_more_url'] = '';
            }

        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
