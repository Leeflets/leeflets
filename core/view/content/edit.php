<?php if ( !$form->is_submitted() ) : ?>
<section id="admin-content" class="panel admin">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
<?php endif; ?>

                <header class="section-header">
                    <button type="button" class="close panel">&times;</button>
                    <h3>Content <small>Update your content.</small></h3>
                </header>

				<?php if ( $form->is_submitted() ) : ?>
                    <?php if ( $form->is_errors() ) : ?>

                        <div class="alert alert-error">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>Sorry!</strong> Something went wrong. Please see the messages below.
                        </div>

                    <?php else : ?>

                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>Awesome!</strong> Your changes have been saved. That was almost too simple.
                    </div>

                    <?php endif; ?>    
				<?php endif; ?>

				<?php $form->html() ?>

<?php if ( !$form->is_submitted() ) : ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>