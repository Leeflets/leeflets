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

				<?php $form->html() ?>

<?php if ( !$form->is_submitted() ) : ?>
            </div>
        </div>
    </div>
    <?php $this->partial( 'button-bar' ); ?>
</section>
<?php endif; ?>