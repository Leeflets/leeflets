<div class="modal skinny">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Welcome!</h3>
    </div>

	<div class="modal-body">
	    <div id="logo" class="centered">
	        <img src="<?php echo $this->router->admin_url( '/core/theme/asset/img/logo.png' ); ?>" alt="Leeflets" />
	    </div>

		<?php $form->html() ?>
    </div>
</div>
