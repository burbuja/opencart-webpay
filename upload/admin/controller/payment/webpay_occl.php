<?php 
class ControllerPaymentWebpayOCCL extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/webpay_occl');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('webpay_occl', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_successful'] = $this->language->get('text_successful');
		$this->data['text_declined'] = $this->language->get('text_declined');
		$this->data['text_off'] = $this->language->get('text_off');

		$this->data['text_payment'] = $this->language->get('text_payment');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_success'] = $this->language->get('text_success');

		$this->data['entry_kcc_url'] = $this->language->get('entry_kcc_url');
		$this->data['entry_kcc_path'] = $this->language->get('entry_kcc_path');
		$this->data['entry_return_policy'] = $this->language->get('entry_return_policy');
		$this->data['entry_callback'] = $this->language->get('entry_callback');
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');		
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['kcc_url'])) {
			$this->data['error_kcc_url'] = $this->error['kcc_url'];
		} else {
			$this->data['error_kcc_url'] = '';
		}

 		if (isset($this->error['kcc_path'])) {
			$this->data['error_kcc_path'] = $this->error['kcc_path'];
		} else {
			$this->data['error_kcc_path'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/webpay_occl', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/webpay_occl', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['webpay_occl_kcc_url'])) {
			$this->data['webpay_occl_kcc_url'] = $this->request->post['webpay_occl_kcc_url'];
		} elseif($this->config->get('webpay_occl_kcc_url') != '') {
			$this->data['webpay_occl_kcc_url'] = $this->config->get('webpay_occl_kcc_url');
		} else {
			$this->data['webpay_occl_kcc_url'] = HTTP_CATALOG . 'cgi-bin/';
		}

		if (isset($this->request->post['webpay_occl_kcc_path'])) {
			$this->data['webpay_occl_kcc_path'] = $this->request->post['webpay_occl_kcc_path'];
		} elseif($this->config->get('webpay_occl_kcc_path') != '') {
			$this->data['webpay_occl_kcc_path'] = $this->config->get('webpay_occl_kcc_path');
		} else {
			$this->data['webpay_occl_kcc_path'] = preg_replace("/\/catalog\//i", '/cgi-bin/', DIR_CATALOG, 1);
		}

		if (isset($this->request->post['webpay_occl_return_policy'])) {
			$this->data['webpay_occl_return_policy'] = $this->request->post['webpay_occl_return_policy'];
		} else {
			$this->data['webpay_occl_return_policy'] = $this->config->get('webpay_occl_return_policy'); 
		}

		$this->load->model('catalog/information');

		$this->data['informations'] = $this->model_catalog_information->getInformations();

		$this->data['callback'] = HTTP_CATALOG . 'index.php?route=payment/webpay_occl/callback';

		if (isset($this->request->post['webpay_occl_total'])) {
			$this->data['webpay_occl_total'] = $this->request->post['webpay_occl_total'];
		} else {
			$this->data['webpay_occl_total'] = $this->config->get('webpay_occl_total'); 
		} 
				
		if (isset($this->request->post['webpay_occl_order_status_id'])) {
			$this->data['webpay_occl_order_status_id'] = $this->request->post['webpay_occl_order_status_id'];
		} else {
			$this->data['webpay_occl_order_status_id'] = $this->config->get('webpay_occl_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['webpay_occl_geo_zone_id'])) {
			$this->data['webpay_occl_geo_zone_id'] = $this->request->post['webpay_occl_geo_zone_id'];
		} else {
			$this->data['webpay_occl_geo_zone_id'] = $this->config->get('webpay_occl_geo_zone_id'); 
		} 

		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['webpay_occl_status'])) {
			$this->data['webpay_occl_status'] = $this->request->post['webpay_occl_status'];
		} else {
			$this->data['webpay_occl_status'] = $this->config->get('webpay_occl_status');
		}

		if (isset($this->request->post['webpay_occl_sort_order'])) {
			$this->data['webpay_occl_sort_order'] = $this->request->post['webpay_occl_sort_order'];
		} else {
			$this->data['webpay_occl_sort_order'] = $this->config->get('webpay_occl_sort_order');
		}

		$this->template = 'payment/webpay_occl.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/webpay_occl')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['webpay_occl_kcc_url']) {
			$this->error['kcc_url'] = $this->language->get('error_kcc_url');
		}

		if (!$this->request->post['webpay_occl_kcc_path']) {
			$this->error['kcc_path'] = $this->language->get('error_kcc_path');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>