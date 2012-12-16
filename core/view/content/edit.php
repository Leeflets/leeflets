<section id="admin-content" class="panel admin">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">

                <header class="section-header">
                    <button type="button" class="close panel">&times;</button>
                    <h3>Content <small>Update your content.</small></h3>
                </header>

				<?php if ( isset( $_GET['saved'] ) ) : ?>
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>Awesome!</strong> Your changes have been saved. That was almost too simple.
                    </div>
				<?php endif; ?>

				<?php $form->html() ?>

            </div>
        </div>
    </div>
</section>