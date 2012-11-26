<section class="content edit-settings">

	<?php if ( isset( $_GET['saved'] ) ) : ?>
		<div class="message notice">
			<p><strong>Saved successfully!</strong></p>
		</div>
	<?php endif; ?>

	<?php if ( $error ) : ?>
		<div class="message error">
			<p><strong><?php echo $error; ?></strong></p>
		</div>
	<?php endif; ?>

	<h1>Settings</h1>

	<?php $form->html() ?>

</section>