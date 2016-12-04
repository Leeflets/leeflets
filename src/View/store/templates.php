<?php if ( !isset( $_GET['slim'] ) ) : ?>
<section id="admin-design" class="panel admin wide store">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
<?php endif; ?>

				<div class="section-header">
					<button type="button" class="close panel">&times;</button>
					<h3>Templates &amp; Addons <small>Change the look and the functionality of your site.</small></h3>
				</div> 
									
				<ul class="nav nav-tabs" id="templates-addons">
					<li class="active templates"><a href="#templates" data-toggle="tab">Templates</a></li>
					<li class="addons"><a href="#addons" data-toggle="tab">Addons</a></li>
				</ul>
				
				<div class="tab-content">
				
					<!-- Begin Templates Tab -->
					
					<div class="tab-pane active" id="templates">
						
						<!-- Begin Active Template -->
						
						<div class="media">
							<a class="span6 well">
								<img class="media-object" src="<?php echo $active_template['screenshot']; ?>">
							</a>
							
							<div class="span6 media-body">
								<h4 class="media-heading"><?php echo $active_template['name']; ?></h4>
								
								<p><?php echo $active_template['description']; ?></p>
							
								<ul class="unstyled details">
									<li><strong>Version:</strong> <?php echo $active_template['version']; ?></li>
									<li><strong>Creator:</strong> <?php
										if ( $active_template['author']['url'] ) {
											printf( '<a href="%s">%s</a>', $active_template['author']['url'], $active_template['author']['name'] );
										}
										else {
											echo $active_template['author']['name'];
										}
									?></li>
									<?php if ( isset( $active_template['changelog'][0]['date'] ) ) : ?>
									<li>
										<strong>Updated:</strong> <?php echo date( 'Y.m.d', strtotime( $active_template['changelog'][0]['date'] ) ); ?>
										<?php
										if ( isset( $active_template['changelog'][0]['changes'] ) ) {
											echo '&mdash;', $active_template['changelog'][0]['changes'];
										}
										?>
									</li>
									<?php endif; ?>
								</ul>
							</div>
						</div>
						
						<!-- End Active Template -->

						<?php if ( $templates ) : ?>
					
						<div class="section-header">
							<h3>Installed Templates</h3>
						</div>
						
						<?php /*
						<div id="templates-menu" class="row-fluid mb-thirty">
							<div class="span6">
								<button class="btn btn-primary" type="button">Featured</button>
								<button class="btn" type="button">New</button>
								<button class="btn" type="button">Purchased</button>
							</div>
							
							<div id="templates-search" class="span6 align-right">
								<div class="input-append">
									<input class="span6" id="appendedInputButton" type="text" placeholder="Search Templates">
									<button class="btn btn-primary" type="button">Search</button>
								</div>
							</div>
						</div>
						*/ ?>

						<div id="installed-templates">
							<div class="row-fluid">                                  
								<ul class="thumbnails">
									
								<?php 
								$i = 1;
								foreach ( $templates as $template ) : 
									?>

									<li id="template-<?php echo $i; ?>" class="span4">
										<div class="thumbnail">
											<img src="<?php echo $template['screenshot']; ?>" alt="" title="<?php echo htmlspecialchars( $template['name'], null, 'utf-8' ); ?>">
											
											<?php /*
											<ul class="unstyled">
												<li><a href="#">Preview</a></li>
												<li><a class="toggle-modal" href="#template-01-details" data-toggle="modal">Details</a></li>
											</ul>
											*/ ?>
											
											<a href="<?php echo $this->router->admin_url( '/store/activate-template/' . rawurlencode( $template['slug'] ) . '/' ); ?>" class="btn btn-primary activate-template">Activate</a>
										</div>
									</li>

									<?php
									$i++;
								endforeach; 
								?>

								</ul>
							</div>
						</div>

						<?php endif; ?>
						
						<!-- End Marketplace Templates -->

						<div class="section-header">
							<h3>Template Marketplace <small>Browse our template marketplace.</small></h3>
						</div>

						<div id="marketplace-templates">
							<div class="row-fluid">                                  
								<ul class="thumbnails" data-ajax-fill="<?php echo $this->router->admin_url( '/store/products/templates/' ); ?>">
								</ul>
							</div>
						</div>
												
					</div>
					
					<!-- End Templates Tab -->
					
					<!-- Begin Addons Tab -->
					<div class="tab-pane" id="addons">

						<?php if ( $addons ) : ?>
						
						<!-- Begin Installed Addons -->
						
						<form action="<?php echo $this->router->admin_url( '/store/addon/' ); ?>">

						<table id="installed-addons" class="table table-striped">
							<tbody>
								<?php foreach ( $addons as $id => $addon ) : ?>
								<tr>
									<td style="min-width: 60px;">
										<img src="<?php echo $addon['screenshot']; ?>" alt="" width="60">
									</td>
									<td style="min-width: 120px;">
										<strong><?php echo htmlspecialchars( $addon['name'], null, 'utf-8' ); ?></strong><br>
										<ul class="unstyled">
											<li>v<?php echo htmlspecialchars( $addon['version'], null, 'utf-8' ); ?></li>
										</ul>
									</td>
									<td>
										<small><?php echo htmlspecialchars( $addon['description'], null, 'utf-8' ); ?></small>
									</td>
									<td>
										<div class="toggle-button" style="width: 100px; height: 25px;">
                                            <input type="checkbox" value="<?php echo $id; ?>"<?php echo ( $addon['active'] ) ? ' checked="checked"' : ''; ?>>
										</div>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>

						</form>
						
						<!-- End Installed Addons -->

						<?php endif; ?>
						
						<div class="section-header">
							<h3>Addon Marketplace <small>Browse our addon marketplace.</small></h3>
						</div>
						
						<!-- Begin Addon Marketplace Menu -->
						
						<? /*
						<div id="addons-menu" class="row-fluid mb-thirty">
							<div class="span6">
								<button class="btn btn-primary" type="button">Featured</button>
								<button class="btn" type="button">New</button>
								<button class="btn" type="button">Purchased</button>
							</div>
							
							<div id="templates-search" class="span6 align-right">
								<div class="input-append">
									<input class="span6" id="appendedInputButton" type="text" placeholder="Search Addons">
									<button class="btn btn-primary" type="button">Search</button>
								</div>
							</div>
						</div>
						*/ ?>
						
						<!-- End Addon Marketplace Menu -->
						
						<!-- Begin Addons Marketplace -->
						
						<table id="marketplace-addons" class="table table-striped">
							<tbody data-ajax-fill="<?php echo $this->router->admin_url( '/store/products/addons/' ); ?>">
							</tbody>
						</table>

						<? /*
						<div id="addon-details">
							<div id="addon-01-details" class="modal fat hide fade">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h3 id="myModalLabel">Addon Name <small>by Leeflets</small></h3>
								</div>
								
								<div class="modal-body">
									<div class="media">
										<div class="row-fluid"> 
											<a class="span3 well">
												<img class="media-object" src="http://placehold.it/270x270">
											</a>
											
											<div class="span9 media-body">
												<p>Vestibulum id ligula porta felis euismod semper. Nulla vitae elit libero, a pharetra augue. Curabitur blandit tempus porttitor. Nulla vitae elit libero, a pharetra augue. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>
											</div>
										</div>
									</div>
									
									<div class="row-fluid"> 
										<div class="span6">
											<h5 class="underline"><i class="icon-star"></i> Addon Highlights</h5>
											
											<ul class="unstyled">
												<li><i class="icon-ok"></i> Responsive Layout</li>
												<li><i class="icon-ok"></i> Optimized for Retina</li>
												<li><i class="icon-ok"></i> Commerce Functionality</li>
												<li><i class="icon-ok"></i> Featured Content Slider</li>
												<li><i class="icon-ok"></i> Stylish Design</li>
											</ul>
										</div>
										
										<div class="span6">
											<h5 class="underline"><i class="icon-plus"></i> Compatible Themes</h5>
											
											<ul class="unstyled">
												<li><i class="icon-ok"></i> Responsive Layout</li>
												<li><i class="icon-ok"></i> Optimized for Retina</li>
												<li><i class="icon-ok"></i> Commerce Functionality</li>
												<li><i class="icon-ok"></i> Featured Content Slider</li>
												<li><i class="icon-ok"></i> Stylish Design</li>
											</ul>
										</div>
									</div>
								</div>
								
								<div class="modal-footer">
									<button class="btn pull-left" data-dismiss="modal" aria-hidden="true">Cancel</button>
									
									<button class="btn btn-primary">Preview</button>
									<button class="btn btn-primary">Purchase $3.00</button>
								</div>
							</div>
							
							<div id="addon-02-details" class="modal fat hide fade">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h3 id="myModalLabel">Addon Name <small>by Leeflets</small></h3>
								</div>
								
								<div class="modal-body">
									<div class="media">
										<div class="row-fluid"> 
											<a class="span3 well">
												<img class="media-object" src="http://placehold.it/270x270">
											</a>
											
											<div class="span9 media-body">
												<p>Vestibulum id ligula porta felis euismod semper. Nulla vitae elit libero, a pharetra augue. Curabitur blandit tempus porttitor. Nulla vitae elit libero, a pharetra augue. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>
											</div>
										</div>
									</div>
									
									<div class="row-fluid"> 
										<div class="span6">
											<h5 class="underline"><i class="icon-star"></i> Addon Highlights</h5>
											
											<ul class="unstyled">
												<li><i class="icon-ok"></i> Responsive Layout</li>
												<li><i class="icon-ok"></i> Optimized for Retina</li>
												<li><i class="icon-ok"></i> Commerce Functionality</li>
												<li><i class="icon-ok"></i> Featured Content Slider</li>
												<li><i class="icon-ok"></i> Stylish Design</li>
											</ul>
										</div>
										
										<div class="span6">
											<h5 class="underline"><i class="icon-plus"></i> Compatible Themes</h5>
											
											<ul class="unstyled">
												<li><i class="icon-ok"></i> Responsive Layout</li>
												<li><i class="icon-ok"></i> Optimized for Retina</li>
												<li><i class="icon-ok"></i> Commerce Functionality</li>
												<li><i class="icon-ok"></i> Featured Content Slider</li>
												<li><i class="icon-ok"></i> Stylish Design</li>
											</ul>
										</div>
									</div>
								</div>
								
								<div class="modal-footer">
									<button class="btn pull-left" data-dismiss="modal" aria-hidden="true">Cancel</button>
									
									<button class="btn btn-primary">Preview</button>
									<button class="btn btn-primary">Purchase $3.00</button>
								</div>
							</div>
							
							<div id="addon-03-details" class="modal fat hide fade">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h3 id="myModalLabel">Addon Name <small>by Leeflets</small></h3>
								</div>
								
								<div class="modal-body">
									<div class="media">
										<div class="row-fluid"> 
											<a class="span3 well">
												<img class="media-object" src="http://placehold.it/270x270">
											</a>
											
											<div class="span9 media-body">
												<p>Vestibulum id ligula porta felis euismod semper. Nulla vitae elit libero, a pharetra augue. Curabitur blandit tempus porttitor. Nulla vitae elit libero, a pharetra augue. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>
											</div>
										</div>
									</div>
									
									<div class="row-fluid"> 
										<div class="span6">
											<h5 class="underline"><i class="icon-star"></i> Addon Highlights</h5>
											
											<ul class="unstyled">
												<li><i class="icon-ok"></i> Responsive Layout</li>
												<li><i class="icon-ok"></i> Optimized for Retina</li>
												<li><i class="icon-ok"></i> Commerce Functionality</li>
												<li><i class="icon-ok"></i> Featured Content Slider</li>
												<li><i class="icon-ok"></i> Stylish Design</li>
											</ul>
										</div>
										
										<div class="span6">
											<h5 class="underline"><i class="icon-plus"></i> Compatible Themes</h5>
											
											<ul class="unstyled">
												<li><i class="icon-ok"></i> Responsive Layout</li>
												<li><i class="icon-ok"></i> Optimized for Retina</li>
												<li><i class="icon-ok"></i> Commerce Functionality</li>
												<li><i class="icon-ok"></i> Featured Content Slider</li>
												<li><i class="icon-ok"></i> Stylish Design</li>
											</ul>
										</div>
									</div>
								</div>
								
								<div class="modal-footer">
									<button class="btn pull-left" data-dismiss="modal" aria-hidden="true">Cancel</button>
									
									<button class="btn btn-primary">Preview</button>
									<button class="btn btn-primary">Purchase $3.00</button>
								</div>
							</div>
						</div>                        
						<!-- End Addons Marketplace -->

						*/ ?>
						
					</div>
					<!-- End Addons Tab -->
					
				</div>

<?php if ( !isset( $_GET['slim'] ) ) : ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>