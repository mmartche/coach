<?php echo $header; ?>
<div class="container">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>
	<div class="row">
	<?php
		echo $column_left;
		if ($column_left && $column_right) {
			$class = 'col-sm-6';
		} elseif ($column_left || $column_right) {
			$class = 'col-sm-9';
		} else {
			$class = 'col-sm-12';
		}
	?>
	<div id="content" class="<?php echo $class; ?>">
		<?php echo $content_top; ?>
		<h1><?php echo $heading_title; ?></h1>
		<h2>Olá <?php echo $client_name; ?></h2>
		<?php foreach ($ativocoachs as $ativocoach) { ?>
			<div class="col-xs-12">
				<div class="row">
					<hr />
					<h4>Nome do Aluno: <?php echo $ativocoach['student_name']; ?></h4>
					<h6><?php echo $ativocoach['student_email']; ?></h6>
				</div>
			</div>
		<?php } ?>
		<?php if (!$ativocoachs) { ?>
			<p><?php echo $text_empty; ?></p>
			<div class="buttons">
				<div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
			</div>
		<?php } ?>
		<?php if (!empty($invitePendingCoachName)) { ?>
			<p>
				<label>Você tem pedido pendente do treinador <?php echo $invitePendingCoachName; ?></label>
				<br />
				<a href="#">Clique aqui para confirmar</a>
			</p>
		<?php } ?>
		<?php echo $content_bottom; ?>
	</div>
	<?php echo $column_right; ?>
</div>
<?php echo $footer; ?>

