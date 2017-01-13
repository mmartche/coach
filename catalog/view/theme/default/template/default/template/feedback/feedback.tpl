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
		<h2>Bem Vindo <?php echo $client_name; ?> _ <?php echo $client_id; ?></h2>
		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-feedback" class="form-horizontal">
			<input type="text" name="uid_description" placeholder="Add Student" />
			<button type="submit">Add</button>
		</form>
		<?php foreach ($feedbacks as $feedback) { ?>
			<div class="col-xs-12">
				<div class="row">
					<hr />
					<h4><a href="<?php echo $feedback['url']; ?>"><?php echo $feedback['author']; ?></a></h4>
					<p><?php echo $feedback['description']; ?></p>
				</div>
			</div>
		<?php } ?>
		<div class="col-xs-12"><div class="row"><hr /></div></div>
		<div class="row">
			<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
			<div class="col-sm-6 text_right"><?php echo $results; ?></div>
		</div>
		<?php if (!$feedbacks) { ?>
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

