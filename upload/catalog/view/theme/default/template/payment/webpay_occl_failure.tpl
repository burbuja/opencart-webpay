<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
<!-- <meta http-equiv="refresh" content="5;url=<?php echo $continue; ?>"> -->
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
</head>
<body>
<div style="text-align: center;">
  <h1><?php echo $heading_title; ?></h1>
  <p><?php echo $text_response; ?></p>
  <hr />
  <table style="margin-left: auto; margin-right: auto; text-align: left;">
    <tr>
      <th colspan="2" style="text-align: center;">Transacción fracasada</th>
    </tr>
    <tr>
      <td>N&uacute;mero del pedido:</td>
      <td><?php echo $tbk_orden_compra; ?></td>
    </tr>
    <tr>
    	<td colspan="2">
    		Las posibles causas de este rechazo son:
    		<ul>
    			<li>Error en el ingreso de los datos de su tarjeta de crédito o débito (fecha y/o código de seguridad).</li>
				<li>Su tarjeta de crédito o débito no cuenta con el cupo necesario para pagar la compra.</li>
				<li>Tarjeta aún no habilitada en el sistema financiero.</li>
			</ul>
    	</td>
    </tr>
  </table>
  <hr />
  <p><?php echo $text_failure; ?></p>
  <p><?php echo $text_failure_wait; ?></p>
</div>
</body>
</html>