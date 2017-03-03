<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-cielow-debito" input type="hidden" id="save_stay" name="save_stay" value="1" data-toggle="tooltip" title="<?php echo $button_save_stay; ?>" class="btn btn-success"><i class="fa fa-save"></i></button>
        <button type="submit" form="form-cielow-debito" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1> <span class="badge"><?php echo $versao; ?></span>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-cielow-debito" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-geral" data-toggle="tab"><?php echo $tab_geral; ?></a></li>
            <li><a href="#tab-api" data-toggle="tab"><?php echo $tab_api; ?></a></li>
            <li><a href="#tab-situacoes-pedido" data-toggle="tab"><?php echo $tab_situacoes_pedido; ?></a></li>
            <li><a href="#tab-finalizacao" data-toggle="tab"><?php echo $tab_finalizacao; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-geral">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="cielow_debito_total" value="<?php echo $cielow_debito_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                <div class="col-sm-10">
                  <select name="cielow_debito_geo_zone_id" id="input-geo-zone" class="form-control">
                    <option value="0"><?php echo $text_all_zones; ?></option>
                    <?php foreach ($geo_zones as $geo_zone) { ?>
                    <?php if ($geo_zone['geo_zone_id'] == $cielow_debito_geo_zone_id) { ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="cielow_debito_status" id="input-status" class="form-control">
                    <?php if ($cielow_debito_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="cielow_debito_sort_order" value="<?php echo $cielow_debito_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-api">
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-credenciamento"><span data-toggle="tooltip" title="<?php echo $help_credenciamento; ?>"><?php echo $entry_credenciamento; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="cielow_debito_credenciamento" value="<?php echo $cielow_debito_credenciamento; ?>" placeholder="<?php echo $entry_credenciamento; ?>" id="input-credenciamento" class="form-control" />
                  <?php if ($error_credenciamento) { ?>
                  <div class="text-danger"><?php echo $error_credenciamento; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-chave"><span data-toggle="tooltip" title="<?php echo $help_chave; ?>"><?php echo $entry_chave; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="cielow_debito_chave" value="<?php echo $cielow_debito_chave; ?>" placeholder="<?php echo $entry_chave; ?>" id="input-cheve" class="form-control" />
                  <?php if ($error_chave) { ?>
                  <div class="text-danger"><?php echo $error_chave; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-soft-descriptor"><span data-toggle="tooltip" title="<?php echo $help_soft_descriptor; ?>"><?php echo $entry_soft_descriptor; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="cielow_debito_soft_descriptor" value="<?php echo $cielow_debito_soft_descriptor; ?>" placeholder="<?php echo $entry_soft_descriptor; ?>" id="input-soft-descriptor" class="form-control" />
                  <?php if ($error_soft_descriptor) { ?>
                  <div class="text-danger"><?php echo $error_soft_descriptor; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-ambiente"><span data-toggle="tooltip" title="<?php echo $help_ambiente; ?>"><?php echo $entry_ambiente; ?></span></label>
                <div class="col-sm-10">
                  <select name="cielow_debito_ambiente" id="input-ambiente" class="form-control">
                    <?php if ($cielow_debito_ambiente) { ?>
                    <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                    <option value="0"><?php echo $text_no; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes; ?></option>
                    <option value="0" selected="selected"><?php echo $text_no; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-visa"><span data-toggle="tooltip" title="<?php echo $help_visa; ?>"><?php echo $entry_visa; ?></span></label>
                <div class="col-sm-10">
                  <select name="cielow_debito_visa" id="input-visa" class="form-control">
                    <?php if ($cielow_debito_visa) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-mastercard"><span data-toggle="tooltip" title="<?php echo $help_mastercard; ?>"><?php echo $entry_mastercard; ?></span></label>
                <div class="col-sm-10">
                  <select name="cielow_debito_mastercard" id="input-visa" class="form-control">
                    <?php if ($cielow_debito_mastercard) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-situacoes-pedido">
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_situacao_pendente; ?>"><?php echo $entry_situacao_pendente; ?></span></label>
                <div class="col-sm-10">
                  <select name="cielow_debito_situacao_pendente_id" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $cielow_debito_situacao_pendente_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_situacao_autorizada; ?>"><?php echo $entry_situacao_autorizada; ?></span></label>
                <div class="col-sm-10">
                  <select name="cielow_debito_situacao_autorizada_id" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $cielow_debito_situacao_autorizada_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_situacao_nao_autorizada; ?>"><?php echo $entry_situacao_nao_autorizada; ?></span></label>
                <div class="col-sm-10">
                  <select name="cielow_debito_situacao_nao_autorizada_id" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $cielow_debito_situacao_nao_autorizada_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_situacao_capturada; ?>"><?php echo $entry_situacao_capturada; ?></span></label>
                <div class="col-sm-10">
                  <select name="cielow_debito_situacao_capturada_id" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $cielow_debito_situacao_capturada_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_situacao_cancelada; ?>"><?php echo $entry_situacao_cancelada; ?></span></label>
                <div class="col-sm-10">
                  <select name="cielow_debito_situacao_cancelada_id" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $cielow_debito_situacao_cancelada_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-finalizacao">
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-titulo"><span data-toggle="tooltip" title="<?php echo $help_titulo; ?>"><?php echo $entry_titulo; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="cielow_debito_titulo" value="<?php echo $cielow_debito_titulo; ?>" placeholder="<?php echo $entry_titulo; ?>" id="input-titulo" class="form-control" />
                  <?php if ($error_titulo) { ?>
                  <div class="text-danger"><?php echo $error_titulo; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-image"><span data-toggle="tooltip" title="<?php echo $help_imagem; ?>"><?php echo $entry_imagem; ?></span></label>
                <div class="col-sm-10"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $no_image; ?>" /></a>
                  <input type="hidden" name="cielow_debito_imagem" value="<?php echo $cielow_debito_imagem; ?>" id="input-image" />
                </div>
              </div>
              <fieldset>
                <legend><?php echo $text_botao; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-botao-normal"><span data-toggle="tooltip" title="<?php echo $help_botao_normal; ?>"><?php echo $entry_botao_normal; ?></span></label>
                  <div class="col-sm-2">
                    <label><?php echo $text_texto; ?></label>
                    <div id="cor_normal_texto" class="input-group colorpicker-component"><input type="text" name="cielow_debito_cor_normal_texto" value="<?php echo $cielow_debito_cor_normal_texto; ?>" class="form-control" /><span class="input-group-addon"><i></i></span></div>
                  </div>
                  <div class="col-sm-2">
                    <label><?php echo $text_fundo; ?></label>
                    <div id="cor_normal_fundo" class="input-group colorpicker-component"><input type="text" name="cielow_debito_cor_normal_fundo" value="<?php echo $cielow_debito_cor_normal_fundo; ?>" class="form-control" /><span class="input-group-addon"><i></i></span></div>
                  </div>
                  <div class="col-sm-2">
                    <label><?php echo $text_borda; ?></label>
                    <div id="cor_normal_borda" class="input-group colorpicker-component"><input type="text" name="cielow_debito_cor_normal_borda" value="<?php echo $cielow_debito_cor_normal_borda; ?>" class="form-control" /><span class="input-group-addon"><i></i></span></div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-botao-efeito"><span data-toggle="tooltip" title="<?php echo $help_botao_efeito; ?>"><?php echo $entry_botao_efeito; ?></span></label>
                  <div class="col-sm-2">
                    <label><?php echo $text_texto; ?></label>
                    <div id="cor_efeito_texto" class="input-group colorpicker-component"><input type="text" name="cielow_debito_cor_efeito_texto" value="<?php echo $cielow_debito_cor_efeito_texto; ?>" class="form-control" /><span class="input-group-addon"><i></i></span></div>
                  </div>
                  <div class="col-sm-2">
                    <label><?php echo $text_fundo; ?></label>
                    <div id="cor_efeito_fundo" class="input-group colorpicker-component"><input type="text" name="cielow_debito_cor_efeito_fundo" value="<?php echo $cielow_debito_cor_efeito_fundo; ?>" class="form-control" /><span class="input-group-addon"><i></i></span></div>
                  </div>
                  <div class="col-sm-2">
                    <label><?php echo $text_borda; ?></label>
                    <div id="cor_efeito_borda" class="input-group colorpicker-component"><input type="text" name="cielow_debito_cor_efeito_borda" value="<?php echo $cielow_debito_cor_efeito_borda; ?>" class="form-control" /><span class="input-group-addon"><i></i></span></div>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('#cor_normal_texto').colorpicker();
$('#cor_normal_fundo').colorpicker();
$('#cor_normal_borda').colorpicker();
$('#cor_efeito_texto').colorpicker();
$('#cor_efeito_fundo').colorpicker();
$('#cor_efeito_borda').colorpicker();
//--></script>
<?php echo $footer; ?>