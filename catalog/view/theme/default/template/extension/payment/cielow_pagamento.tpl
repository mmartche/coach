<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row">
    <div id="content" class="col-sm-12">
    <style>
    #responsivo .button.button-primary,
    #responsivo button.button-primary,
    #responsivo input[type="submit"].button-primary,
    #responsivo input[type="reset"].button-primary,
    #responsivo input[type="button"].button-primary {
      color: <?php echo $cor_normal_texto; ?>;
      background-color: <?php echo $cor_normal_fundo; ?>;
      border-color: <?php echo $cor_normal_borda; ?>;
    }
    #responsivo .button.button-primary:hover,
    #responsivo button.button-primary:hover,
    #responsivo input[type="submit"].button-primary:hover,
    #responsivo input[type="reset"].button-primary:hover,
    #responsivo input[type="button"].button-primary:hover,
    #responsivo .button.button-primary:focus,
    #responsivo button.button-primary:focus,
    #responsivo input[type="submit"].button-primary:focus,
    #responsivo input[type="reset"].button-primary:focus,
    #responsivo input[type="button"].button-primary:focus {
      color: <?php echo $cor_efeito_texto; ?>;
      background-color: <?php echo $cor_efeito_fundo; ?>;
      border-color: <?php echo $cor_efeito_borda; ?>;
    }
    </style>
      <?php if ($bandeiras) { ?>
      <div id="responsivo">
        <?php if ($ambiente) { ?>
        <div class="alert-warning" id="attention"><?php echo $text_ambiente; ?></div>
        <?php } ?>
        <div class="alert-warning" id="attention"><?php echo $text_informacoes; ?></div>
        <?php if ($falhou) { ?>
        <div class="alert-warning" id="attention"><?php echo $error_validacao; ?></div>
        <?php } ?>
        <h4><strong><?php echo $text_detalhes; ?></strong></h4>
        <form onkeypress="return event.keyCode != 13" id="payment">
          <div class="row-form">
            <?php $i=1; foreach($bandeiras as $bandeira) { ?>
            <div class="three columns value">
              <?php if($bandeira == 'visa'): $nomeBandeira = 'VISA ELECTRON'; elseif($bandeira == 'mastercard'): $nomeBandeira = 'MAESTRO'; endif; ?>
              <input type="radio" name="bandeira" id="input-bandeira" value="<?php echo $bandeira; ?>" <?php echo ($i == 1) ? 'checked' : ''; ?> />
              <img alt="<?php echo $nomeBandeira; ?>" title="<?php echo $nomeBandeira; ?>" src="<?php echo HTTPS_SERVER . 'image/cielow/' . strtolower($bandeira) . '_debito.png'; ?>" border="0" />
              <strong><?php echo $entry_total . $total; ?></strong>
            </div>
            <?php $i++; } ?>
          </div>
          <h4>&nbsp;</h4>
          <div class="row-form">
            <div class="three columns value">
              <label for="input-cartao"><?php echo $entry_cartao; ?><span id='bandeiraEscolhida'></span>:</label>
              <input type="text" name="cartao" value="" placeholder="" id="input-cartao" maxlength="19" autocomplete="cc-number" class="u-full-width" />
            </div>
            <div class="three columns value">
              <label class="" for="input-mes"><strong><?php echo $entry_validade_mes; ?></strong></label>
              <select name="mes" id="input-mes" class="u-full-width">
                <option value=""><?php echo $text_mes; ?></option>
                <?php foreach ($meses as $mes) { ?>
                <option value="<?php echo $mes['value']; ?>"><?php echo $mes['text']; ?></option>
                <?php } ?>  
              </select>
            </div>
            <div class="three columns value">
              <label class="" for="input-ano"><strong><?php echo $entry_validade_ano; ?></strong></label>
              <select name="ano" id="input-ano" class="u-full-width">
                <option value=""><?php echo $text_ano; ?></option>
                <?php foreach ($anos as $ano) { ?>
                <option value="<?php echo $ano['value']; ?>"><?php echo $ano['text']; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="three columns value">
              <label class="u-full-width" for="input-codigo"><strong><?php echo $entry_codigo; ?></strong></label>
              <input type="text" name="codigo" value="" placeholder="" id="input-codigo" maxlength="4" autocomplete="off" class="u-full-width" />
            </div>
          </div>
          <input class="button-primary" value="<?php echo $button_pagar; ?>" id="button-confirm" data-loading-text="<?php echo $text_carregando; ?>" type="button">
        </form>
        <div class="alert alert-warning" id="warning"></div>
        <div class="alert alert-success" id="success"></div>
      </div>
    </div>
    <?php } else { ?>
    <div id="responsivo">
      <div class="alert-warning" id="attention"><?php echo $error_bandeiras; ?></div>
    </div>
    <?php } ?>
  </div>
</div>
<script type="text/javascript"><!--
$('#warning').hide();
$('#success').hide();

$('#input-cartao').on('keyup change', function() {
    $(this).val($(this).val().replace(/[^\d]/,''));
});

$('#input-codigo').on('keyup change', function() {
    $(this).val($(this).val().replace(/[^\d]/,''));
});

$('input[name=\'bandeira\']').on('change', function() {
    $('#bandeiraEscolhida').html('');
    if(this.value == 'visa'){
        $('#bandeiraEscolhida').html('VISA ELECTRON');
    }else if(this.value == 'mastercard'){
        $('#bandeiraEscolhida').html('MAESTRO');
    }
});

$('input:radio[name=\'bandeira\']').each(function() {
  if ($(this).is(':checked'))
    $(this).trigger('change');
});

function transacao() {
  $.ajax({
    url: 'index.php?route=extension/payment/cielow_debito/transacao',
    type: 'post',
    data: $('#payment input[type=\'text\'], #payment input[type=\'radio\']:checked, #payment select'),
    dataType: 'json',
    beforeSend: function() {
      $('body').loading({message: '<?php echo $text_validando; ?>'});
      $('#button-confirm').button('loading');
      $('#button-confirm').attr('disabled', 'true');
      $('input', this).attr('readonly', true);
      $('select', this).attr('readonly', true);
    },
    complete: function() {
      $('input', this).removeAttr('readonly');
      $('select', this).removeAttr('readonly');
      $('#button-confirm').removeAttr('disabled');
      $('#button-confirm').button('reset');
      $('input[name="cartao"]').val('');
      $('select[name="mes"]').val('');
      $('select[name="ano"]').val('');
      $('input[name="codigo"]').val('');
      $('body').loading('stop');
    },
    success: function(json) {
      $('body').loading('stop');
      if(json['error']){
        $('#warning').html(json['error']).show();
      }else{
        $('#button-confirm').attr('disabled', 'true');
        $('#button-confirm').hide();
        $('#success').html('<?php echo $text_redirecionando; ?>').show();
        location.href = json['redirect'];
      }
    }
  })
}

function validar() {
  var erros = 0;
  var campos = {
        cartao: '<?php echo $error_cartao; ?>',
        mes: '<?php echo $error_mes; ?>',
        ano: '<?php echo $error_ano; ?>',
        codigo: '<?php echo $error_codigo; ?>'
      };

  $('#warning').hide();
  $('.text-danger').remove();
  $('#payment input[type=\'text\'], #payment select').removeClass('alert-danger');

  $("#payment input[type=\'text\'], #payment input[type=\'radio\']:checked, #payment select").each(function(){
    for(var chave in campos){
      if($(this).attr("name") == chave){
        if($(this).attr("name") == 'cartao'){
          if($.trim($(this).val()).length < 13){
            $(this).toggleClass('alert-danger');
            $(this).after('<div class="text-danger"><i class="fa fa-times"></i> '+campos[chave]+'</div>');
            erros++;
          }else{
            $(this).removeClass('alert-danger');
          }
        }else if($(this).attr("name") == 'codigo'){
          if($.trim($(this).val()).length !== 3){
            $(this).toggleClass('alert-danger');
            $(this).after('<div class="text-danger"><i class="fa fa-times"></i> '+campos[chave]+'</div>');
            erros++;    
          }else{
            $(this).removeClass('alert-danger');
          }
        }else{
          if($.trim($(this).val()).length == 0){
            $(this).toggleClass('alert-danger');
            $(this).after('<div class="text-danger"><i class="fa fa-times"></i> '+campos[chave]+'</div>');
            erros++;
          }else{
            $(this).removeClass('alert-danger');
          }
        }
      }
    }
  });

  if(erros == 0){
    transacao();
  }else{
    return false;
  };
};

$('#payment input[type=\'text\'], #payment select').blur(function() {
  $('.text-danger').remove();
  $('#payment input[type=\'text\'], #payment select').removeClass('alert-danger');
  validar();
});

$('#button-confirm').on('click', function() {
  validar();
});
//--></script>
<?php echo $footer; ?>