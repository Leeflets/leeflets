<section class="content edit-content">

	<?php if ( isset( $_GET['saved'] ) ) : ?>
		<div class="message notice">
			<p><strong>Saved successfully!</strong></p>
		</div>
	<?php endif; ?>

	<h1>Content</h1>

	<?php $form->html() ?>

</section>