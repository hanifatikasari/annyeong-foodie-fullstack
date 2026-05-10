<?php if (!empty($errors)) : ?>
	<div class="alert alert-danger" role="alert">
		<p><strong>Whoops!</strong> There are some problems with your input.</p>
		<ul>
			<?php foreach ($errors as $error) : ?>
			<li><?= $error ?></li>
			<?php endforeach ?>
		</ul>
	</div>
<?php endif ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>