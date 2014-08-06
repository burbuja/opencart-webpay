<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
<!-- <meta http-equiv="refresh" content="5;url=<?php echo $continue; ?>"> -->
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<meta charset="utf-8">
</head>
<body>
<div style="text-align: center;">
  <h1><?php echo $heading_title; ?></h1>
  <p><?php echo $text_response; ?></p>
  <hr />
  <table style="margin-left: auto; margin-right: auto; text-align: left;">
    <tr>
      <th colspan="2" style="text-align: center;">Datos de la compra</th>
    </tr>
    <tr>
      <td>Nombre del comercio:</td>
      <td><?php echo $tbk_nombre_comercio; ?></td>
    </tr>
    <tr>
      <td>URL del comercio:</td>
      <td><?php echo $tbk_url_comercio; ?></td>
    </tr>
    <tr>
      <td>Nombre del comprador:</td>
      <td><?php echo $tbk_nombre_comprador; ?></td>
    </tr>
    <tr>
      <td>N&uacute;mero del pedido:</td>
      <td><?php echo $tbk_orden_compra; ?></td>
    </tr>
    <tr>
      <td>Monto (pesos chilenos):</td>
      <td>$<?php echo ($tbk_monto / 100); ?></td>
    </tr>
    <tr>
      <th colspan="2" style="text-align: center;">Datos de la transacci&oacute;n</th>
    </tr>
    <tr>
      <td>C&oacute;digo de autorizaci&oacute;n:</td>
      <td><?php echo $tbk_codigo_autorizacion; ?></td>
    </tr>
    <tr>
      <td>Fecha de la transacci&oacute;n:</td>
      <td><?php echo $tbk_fecha_transaccion; ?></td>
    </tr>
    <tr>
      <td>Hora de la transacci&oacute;n:</td>
      <td><?php echo $tbk_hora_transaccion; ?></td>
    </tr>
    <tr>
      <td>Número de Tarjeta:</td>
      <td><?php echo $tbk_final_numero_tarjeta; ?></td>
    </tr>
    <tr>
      <td>Tipo de transacci&oacute;n:</td>
      <td><?php echo $tbk_tipo_transaccion; ?></td>
    </tr>
    <tr>
      <td>Tipo de pago:</td>
      <td><?php echo $tbk_tipo_pago; ?></td>
    </tr>
    <tr>
      <td>N&uacute;mero de cuotas:</td>
      <td><?php echo $tbk_numero_cuotas; ?></td>
    </tr>
    <tr>
      <td>Tipo de cuotas:</td>
      <td><?php echo $tbk_tipo_cuotas; ?></td>
    </tr>
  </table>
  <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    <thead>
      <tr>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?php echo $column_name; ?></td>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?php echo $column_model; ?></td>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?php echo $column_quantity; ?></td>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?php echo $column_price; ?></td>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;"><?php echo $column_total; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product) { ?>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $product['name']; ?>
          <?php foreach ($product['option'] as $option) { ?>
          <br />
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $product['model']; ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['quantity']; ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['price']; ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $product['total']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $voucher['description']; ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">1</td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $voucher['amount']; ?></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $voucher['amount']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;" colspan="4"><b><?php echo $total['title']; ?>:</b></td>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
  <p><?php echo $return_policy; ?></p>
  <hr />
  <p><?php echo $text_success; ?></p>
  <p><?php echo $text_success_wait; ?></p>
  <!--
Faltan:
- Ver archivo './catalog/model/checkout/order.php' para obtener las variables $vouchers y $totals.
  -->
</div>
</body>
</html>