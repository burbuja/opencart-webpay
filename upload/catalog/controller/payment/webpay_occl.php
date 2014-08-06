<?php
class ControllerPaymentWebpayOCCL extends Controller {

	// Pagina de Pago
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		// Url CGI
		$this->data['action'] = $this->config->get('webpay_occl_kcc_url') . 'tbk_bp_pago.cgi';

		$this->data['tbk_tipo_transaccion'] = 'TR_NORMAL';
		$tbk_monto_explode = explode('.', $order_info['total']);
		$this->data['tbk_monto'] = $tbk_monto_explode[0] . '00';
		$this->data['tbk_orden_compra'] = $order_info['order_id'];
		$this->data['tbk_id_sesion'] = date("Ymdhis");
		$this->data['tbk_url_fracaso'] = $this->url->link('payment/webpay_occl/failure', '', 'SSL');
		$this->data['tbk_url_exito'] = $this->url->link('payment/webpay_occl/success', '', 'SSL');
		//$this->data['tbk_monto_cuota'] = 0;
		//$this->data['tbk_numero_cuota'] = 0;

		$tbk_file = fopen(DIR_LOGS . 'TBK' . $this->data['tbk_id_sesion'] . '.log', 'w+');
		fwrite ($tbk_file, $tbk_monto_explode[0].'00;'.$order_info['order_id']);
		fclose($tbk_file);

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/webpay_occl.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/webpay_occl.tpl';
		} else {
			$this->template = 'default/template/payment/webpay_occl.tpl';
		}

		$this->render();
	}

	public function callback() {
        $this->load->model('checkout/order');

        if (isset($this->request->post['TBK_ORDEN_COMPRA']) && isset($this->request->post['TBK_ID_SESION'])) {
            $order_info = $this->model_checkout_order->getOrder($this->request->post['TBK_ORDEN_COMPRA']);
            $this->model_checkout_order->confirm($order_info['order_id'], $this->config->get('config_order_status_id'));

            $tbk_log_file = DIR_LOGS . 'TBK' . $this->request->post['TBK_ID_SESION'] . '.log'; // Definir la ubicación del archivo de registro
            $tbk_cache_file = DIR_CACHE . 'TBK' . $this->request->post['TBK_ID_SESION'] . '.txt'; // Definir la ubicación del archivo temporal
        }

		$this->data['tbk_answer'] = 'RECHAZADO'; // Definir la respuesta por defecto

		/* Verificar la autorización de la transacción */
		if (isset($this->request->post['TBK_RESPUESTA']) && $this->request->post['TBK_RESPUESTA'] == 0) {

			/* Verificar orden de compra única y si vienen o no todos los datos */
			if (!$order_info['order_status_id']) {

				/* Verificar el MAC: Revisar si el archivo de registro existe */
				if (isset($this->request->post['TBK_ID_SESION']) && file_exists($tbk_log_file)) {
					$tbk_log = fopen($tbk_log_file, 'r');
					$tbk_log_string = fgets($tbk_log);
					fclose($tbk_log);
					$tbk_details = explode(';', $tbk_log_string);

					/* Verificar el MAC: Revisar si el archivo cumple con los requisitos */
					if (isset($tbk_details) && count($tbk_details) >= 1) {
						$tbk_monto = $tbk_details[0];
						$tbk_orden_compra = $tbk_details[1];

						$tbk_cache = fopen($tbk_cache_file, 'w+');
						foreach ($this->request->post as $tbk_key => $tbk_value) {
							fwrite($tbk_cache, "$tbk_key=$tbk_value&");
						}
						fclose($tbk_cache);

						exec($this->config->get('webpay_occl_kcc_path') . 'tbk_check_mac.cgi ' . $tbk_cache_file, $tbk_result);

						/* Verificar el MAC: Revisar el resultado de la ejecución del programa */
						if (isset($tbk_result[0]) && $tbk_result[0] == 'CORRECTO') {

							/* Verificar la orden de compra */
		/*VER*/					if ($this->request->post['TBK_ORDEN_COMPRA'] == $tbk_orden_compra && !$order_info['order_status_id']) {
								$tbk_monto_explode = explode('.', $order_info['total']);
								$tbk_monto_en_orden_compra = $tbk_monto_explode[0] . '00';

								/* Verificar el monto */
		/*VER*/						if ($this->request->post['TBK_MONTO'] == $tbk_monto && $this->request->post['TBK_MONTO'] == $tbk_monto_en_orden_compra) {
									$tbk_ok = true;
									$message = 'Transacci&oacute;n aprobada';
									$this->data['tbk_answer'] = 'ACEPTADO';
								} else {
									$message = 'Monto no coincide';
								}
							} else {
								$message = 'Orden de compra no coincide';
							}
						} else {
							$message = 'MAC rechazado';
						}
					} else {
						$message = 'Archivo de registro no cumple con los requisitos';
					}
				} else {
					$message = 'Archivo de registro no se encuentra';
				}
			}
		} elseif (isset($this->request->post['TBK_RESPUESTA'])) {
			switch ($this->request->post['TBK_RESPUESTA']) {
				case '-1':
					$message = 'Rechazo de transacci&oacute;n';
					break;
				case '-2':
					$message = 'Transacci&oacute;n debe reintentarse';
					break;
				case '-3':
					$message = 'Error en transacci&oacute;n';
					break;
				case '-4':
					$message = 'Rechazo de transacci&oacute;n';
					break;
				case '-5':
					$message = 'Rechazo por error de tasa';
					break;
				case '-6':
					$message = 'Excede cupo m&aacute;ximo mensual';
					break;
				case '-7':
					$message = 'Excede l&iacute;mite diario por transacci&oacute;n';
					break;
				case '-8':
					$message = 'Rubro no autorizado';
					break;
				default:
					$message = 'Error desconocido';
			}


			$this->data['tbk_answer'] = 'ACEPTADO';
		}

		if (isset($tbk_ok) && $tbk_ok == true) {
			/* Pago exitoso */
			$this->model_checkout_order->update($order_info['order_id'], $this->config->get('webpay_occl_order_status_id'), $message, false);
		} elseif (isset($message) && !$order_info['order_status_id']) {
			/* Pago no realizado */
			$this->model_checkout_order->update($order_info['order_id'], $this->config->get('config_order_status_id'), $message, false);
		}

		$this->template = 'default/template/payment/webpay_occl_callback.tpl';
		$this->response->setOutput($this->render());
	}

	public function failure() {
		$this->language->load('payment/webpay_occl');

		if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
			$this->data['base'] = $this->config->get('config_url');
		} else {
			$this->data['base'] = $this->config->get('config_ssl');
		}
	
		$this->data['language'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');

		$this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		$this->data['text_response'] = $this->language->get('text_response');
		$this->data['text_failure'] = $this->language->get('text_failure');
		$this->data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart', '', 'SSL'));

		$this->data['continue'] = $this->url->link('checkout/cart');

		if ((isset($this->request->post['TBK_ORDEN_COMPRA']))) {
            $this->data['tbk_orden_compra'] = $this->request->post['TBK_ORDEN_COMPRA'];
        } elseif (isset($this->session->data['order_id'])) {
			$this->data['tbk_orden_compra'] = $this->session->data['order_id'];
		} else {
			$this->data['tbk_orden_compra'] = 0;
		}

		// Solo mostrar si existe un registro de orden de compra
		// if(isset($this->request->post['TBK_ID_SESION']) && file_exists(DIR_CACHE . 'TBK' . $this->request->post['TBK_ID_SESION'] . '.txt')) {
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/webpay_occl_failure.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/webpay_occl_failure.tpl';
			} else {
				$this->template = 'default/template/payment/webpay_occl_failure.tpl';
			}

			$this->response->setOutput($this->render());
		
		// } else {
		// 	$this->redirect($this->url->link('common/home', '', 'SSL'));
		// }
	}

	public function success() {
		if (isset($this->request->post['TBK_ID_SESION']) && isset($this->request->post['TBK_ORDEN_COMPRA']) && file_exists(DIR_CACHE . 'TBK' . $this->request->post['TBK_ID_SESION'] . '.txt')) {
			$this->language->load('checkout/cart');
			$this->language->load('payment/webpay_occl');

			if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
				$this->data['base'] = $this->config->get('config_url');
			} else {
				$this->data['base'] = $this->config->get('config_ssl');
			}
	
			$this->data['language'] = $this->language->get('code');
			$this->data['direction'] = $this->language->get('direction');

			$this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

			$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

			$this->data['text_response'] = $this->language->get('text_response');
			$this->data['text_success'] = $this->language->get('text_success');
			$this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success', '', 'SSL'));

			$this->data['column_name'] = $this->language->get('column_name');
			$this->data['column_model'] = $this->language->get('column_model');
			$this->data['column_quantity'] = $this->language->get('column_quantity');
			$this->data['column_price'] = $this->language->get('column_price');
			$this->data['column_total'] = $this->language->get('column_total');

			$this->data['button_continue'] = $this->language->get('button_continue');

			$this->data['continue'] = $this->url->link('checkout/success');

			$this->data['tbk_nombre_comercio'] = 'XX';
			$this->data['tbk_url_comercio'] = 'XX';
			$this->data['tbk_nombre_comprador'] = 'XX';
			$this->data['tbk_orden_compra'] = 0;
			$this->data['tbk_tipo_transaccion'] = 0;
			//$this->data['tbk_respuesta'] = 0;
			$this->data['tbk_monto'] = 0;
			$this->data['tbk_codigo_autorizacion'] = 0;
			$this->data['tbk_final_numero_tarjeta'] = '************0000';
			//$this->data['tbk_fecha_contable'] = '00-00-0000';
			$this->data['tbk_fecha_transaccion'] = '00-00-0000';
			$this->data['tbk_hora_transaccion'] = '00:00:00';
			$this->data['tbk_id_transaccion'] = 0;
			$this->data['tbk_tipo_pago'] = 'XX';
			$this->data['tbk_numero_cuotas'] = '00';
			$this->data['tbk_tipo_cuotas'] = 'XX';
			$this->data['tbk_mac'] = 0;

			if ($this->config->get('webpay_occl_return_policy')) {
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation($this->config->get('webpay_occl_return_policy'));
				$this->data['return_policy'] = sprintf('Revise nuestra <a href=\'%s\' title=\'%s\'>%s</a>', $this->url->link('information/information', 'information_id=' . $this->config->get('webpay_occl_return_policy'), 'SSL'), $information_info['title'], $information_info['title']);
			}

			$tbk_cache = fopen(DIR_CACHE . 'TBK' . $this->request->post['TBK_ID_SESION'] . '.txt', 'r');
			$tbk_cache_string = fgets($tbk_cache);
			fclose($tbk_cache);

			$tbk_detalles = explode('&', $tbk_cache_string);

			$tbk_orden_compra = explode('=', $tbk_detalles[0]);
			$tbk_tipo_transaccion = explode('=', $tbk_detalles[1]);
			$tbk_respuesta = explode('=', $tbk_detalles[2]);
			$tbk_monto = explode('=', $tbk_detalles[3]);
			$tbk_codigo_autorizacion = explode('=', $tbk_detalles[4]);
			$tbk_final_numero_tarjeta = explode('=', $tbk_detalles[5]);
			$tbk_fecha_contable = explode('=', $tbk_detalles[6]);
			$tbk_fecha_transaccion = explode('=', $tbk_detalles[7]);

			if (substr($tbk_fecha_contable[1], 0, 2) == '12' && date('d') == '01') {
				$tbk_anno_contable = date('Y') - 1;
			} elseif (substr($tbk_fecha_contable[1], 0, 2) == '01' && date('d') == '12') {
				$tbk_anno_contable = date('Y') + 1;
			} else {
				$tbk_anno_contable = date('Y');
			}

			if (substr($tbk_fecha_transaccion[1], 0, 2) == '12' && date('d') == '01') {
				$tbk_anno_transaccion = date('Y') - 1;
			} elseif (substr($tbk_fecha_transaccion[1], 0, 2) == '01' && date('d') == '12') {
				$tbk_anno_transaccion = date('Y') + 1;
			} else {
				$tbk_anno_transaccion = date('Y');
			}

			$tbk_hora_transaccion = explode('=', $tbk_detalles[8]);
			$tbk_id_transaccion = explode('=', $tbk_detalles[10]);
			$tbk_tipo_pago = explode('=', $tbk_detalles[11]);
			$tbk_numero_cuotas = explode('=', $tbk_detalles[12]);
			$tbk_mac = explode('=', $tbk_detalles[13]);

			$this->data['tbk_nombre_comercio'] = $this->config->get('config_name');
			$this->data['tbk_url_comercio'] = $this->data['base'];
			$this->data['tbk_nombre_comprador'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
			$this->data['tbk_orden_compra'] = $tbk_orden_compra[1];
			$this->data['tbk_tipo_transaccion'] = 'Venta';
			//$this->data['tbk_tipo_transaccion'] = $tbk_tipo_transaccion[1];
			//$this->data['tbk_respuesta'] = $tbk_respuesta[1];
			$this->data['tbk_monto'] = $tbk_monto[1];
			//$this->data['tbk_monto'] = number_format($tbk_monto[1], 0, ',', '.');
			$this->data['tbk_codigo_autorizacion'] = $tbk_codigo_autorizacion[1];
			$this->data['tbk_final_numero_tarjeta'] = '************' . $tbk_final_numero_tarjeta[1];			
			//$this->data['tbk_fecha_contable'] = substr($tbk_fecha_contable[1], 2, 2) . '-' . substr($tbk_fecha_contable[1], 0, 2) . '-' . $tbk_anno_contable;
			$this->data['tbk_fecha_transaccion'] = substr($tbk_fecha_transaccion[1], 2, 2) . '-' . substr($tbk_fecha_transaccion[1], 0, 2) . '-' . $tbk_anno_transaccion;
			$this->data['tbk_hora_transaccion'] = substr($tbk_hora_transaccion[1], 0, 2) . ':' . substr($tbk_hora_transaccion[1], 2, 2) . ':' . substr($tbk_hora_transaccion[1], 4, 2);
			$this->data['tbk_id_transaccion'] = $tbk_id_transaccion[1];

			if ($tbk_tipo_pago[1] == 'VD') {
				$this->data['tbk_tipo_pago'] = 'Redcompra';
			} else {
				$this->data['tbk_tipo_pago'] = 'Cr&eacute;dito';
			}

			if ($tbk_numero_cuotas[1] == 0) {
				$this->data['tbk_numero_cuotas'] = '00';
			} else {
				$this->data['tbk_numero_cuotas'] = $tbk_numero_cuotas[1];
			}

			if ($tbk_tipo_pago[1] == 'VN') {
				$this->data['tbk_tipo_cuotas'] = 'Sin cuotas';
			} elseif ($tbk_tipo_pago[1] == 'VC') {
				$this->data['tbk_tipo_cuotas'] = 'Cuotas normales';
			} elseif ($tbk_tipo_pago[1] == 'SI') {
				$this->data['tbk_tipo_cuotas'] = 'Sin inter&eacute;s';
			} elseif ($tbk_tipo_pago[1] == 'S2') {
				$this->data['tbk_tipo_cuotas'] = 'Dos cuotas sin inter&eacute;s';
			} elseif ($tbk_tipo_pago[1] == 'CI') {
				$this->data['tbk_tipo_cuotas'] = 'Cuotas comercio';
			} elseif ($tbk_tipo_pago[1] == 'VD') {
				$this->data['tbk_tipo_cuotas'] = 'D&eacute;bito';
			}

			$this->data['tbk_mac'] = $tbk_mac[1];

			// Products
			$this->data['products'] = $this->cart->getProducts();

			// Vouchers
			$this->data['vouchers'] = array();
		
			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$this->data['vouchers'][] = array(
						'key'         => $key,
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount']),
						'remove'      => $this->url->link('checkout/cart', 'remove=' . $key)   
					);
				}
			}

			// Totals
			$this->load->model('setting/extension');

			$total_data = array();					
			$total = 0;
			$taxes = $this->cart->getTaxes();

			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$sort_order = array(); 

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('total/' . $result['code']);

						$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
					}

					$sort_order = array(); 

					foreach ($total_data as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
					}

					array_multisort($sort_order, SORT_ASC, $total_data);			
				}
			}

			$this->data['totals'] = $total_data;

			if (isset($this->session->data['order_id'])) {
				$this->cart->clear();

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
			}
			
			// Solo mostrar exito si la transacción fue realizada correctamente
			if($tbk_respuesta[1] == 0) {
				
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/webpay_occl_success.tpl')) {
					$this->template = $this->config->get('config_template') . '/template/payment/webpay_occl_success.tpl';
				} else {
					$this->template = 'default/template/payment/webpay_occl_success.tpl';
				}

				$this->response->setOutput($this->render());
			 } else {
				// Mostrar Fracaso si no existe respuesta o es distinta a cero

				$this->redirect($this->url->link('payment/webpay_occl/failure', '', 'SSL'));
			}


		} else {
			//exit();
			$this->redirect($this->url->link('common/home', '', 'SSL'));
		}
	}
}
?>
