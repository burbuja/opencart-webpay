<form action="<?php echo $action; ?>" method="post" id="payment">
  <input type="hidden" name="TBK_TIPO_TRANSACCION" value="<?php echo $tbk_tipo_transaccion; ?>" />
  <input type="hidden" name="TBK_MONTO" value="<?php echo $tbk_monto; ?>" />
  <input type="hidden" name="TBK_ORDEN_COMPRA" value="<?php echo $tbk_orden_compra; ?>" />
  <input type="hidden" name="TBK_ID_SESION" value="<?php echo $tbk_id_sesion; ?>" /> 
  <input type="hidden" name="TBK_URL_FRACASO" value="<?php echo $tbk_url_fracaso; ?>" />
  <input type="hidden" name="TBK_URL_EXITO" value="<?php echo $tbk_url_exito; ?>" />
  
  <?php if(isset($tbk_monto_cuota) && isset($tbk_numero_cuota)): ?>
  <input type="hidden" name="TBK_MONTO_CUOTA" value="<?php echo $tbk_monto_cuota; ?>" /> 
  <input type="hidden" name="TBK_NUMERO_CUOTAS" value="<?php echo $tbk_numero_cuota; ?>" />
  <?php endif ?>
  
  <div class="buttons">
    <div class="right"><a onclick="$('#payment').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></div>
  </div>
</form>