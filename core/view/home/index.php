<?php if ( isset( $_GET['published'] ) ) : ?>
	<div class="message notice">
		<p><strong>Site published successfully!</strong></p>
	</div>
<?php endif; ?>

<a href="<?php echo $this->router->admin_url( '/content/edit/' ); ?>">Edit Content</a><br />
<a href="<?php echo $this->router->admin_url( '/content/view/' ); ?>">View Content</a>
<br /><br />
<a href="<?php echo $this->router->admin_url( '/content/publish/' ); ?>">Publish</a><br />
<a href="<?php echo $this->router->site_url(); ?>">View Published Site</a>
<br /><br />
<a href="<?php echo $this->router->admin_url( 'user/logout' ); ?>">Logout</a>
