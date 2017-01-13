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
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i><?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ($success) { ?>
		<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i><?php echo $success; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<h1>Painel <?php echo $groupName; ?></h1>
		<h2>Bem Vindo <?php echo $client_name; ?></h2>
		<?php if ($groupId == 2 || $groupId == 3) { ?>
		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ativocoach" class="form-horizontal">
			<label>Convide seus Alunos</label>
			<input type="text" name="student_name" placeholder="Add Student Name" />
			<input type="text" name="student_email" placeholder="Add Student E-mail" />
			<button type="submit">Add</button>
		</form>
		<?php } ?>
		<?php foreach ($ativocoachs as $ativocoach) { ?>
			<div class="col-xs-12">
				<div class="row">
					<hr />
					<h4><a href="<?php echo $ativocoach['url_details']; ?>"><?php echo $ativocoach['student_name']; ?></a></h4>
					<h6><?php echo $ativocoach['student_email']; ?></h6>
					<p><?php 
					if ($ativocoach['student_accepted'] == 1) {
						echo $text_accepted_success;
					} else {
						echo $text_accepted_error;
					}
					?>
					</p>
					<a class="alert-danger" href="<?php echo $ativocoach['url_disable']; ?>" >Remover Aluno</a>
				</div>
			</div>
		<?php }  ?>
		<div class="col-xs-12"><div class="row"><hr /></div></div>
		<div class="row">
			<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
			<div class="col-sm-6 text_right"><?php echo $results; ?></div>
		</div>
		<?php if (!$ativocoachs) { ?>
			<p><?php echo $text_empty; ?></p>
			<div class="buttons">
				<div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
			</div>
		<?php } ?>
		<?php echo $content_bottom; ?>
	</div>
	<?php echo $column_right; ?>
</div>
<?php echo $footer; ?>

