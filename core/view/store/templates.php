<section id="admin-design" class="panel admin wide">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">

				<div class="section-header">
					<button type="button" class="close panel">&times;</button>
					<h3>Templates &amp; Addons <small>Change the look and the functionality of your site.</small></h3>
				</div> 
									
				<ul class="nav nav-tabs" id="templates-addons">
					<li class="active"><a href="#templates">Templates</a></li>
					<li class=""><a href="#addons">Addons</a></li>
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
					
						<div class="section-header">
							<h3>Templates <small>Browse our template marketplace &amp; your purchased templates.</small></h3>
						</div>
						
						<!-- Begin Template Marketplace Menu -->
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
						
						<!-- End Template Marketplace Menu -->
						
						<!-- Begin Marketplace Templates -->

						<?php if ( $templates ) : ?>

						<div id="marketplace-templates">
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
											
											<a href="<?php echo $this->router->admin_url( '/store/activate-template/' . rawurlencode( $template['slug'] ) . '/' ); ?>" class="btn btn-primary">Activate</a>
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
						
					</div>
					
					<!-- End Templates Tab -->
					
					<!-- Begin Addons Tab -->
					<?php /*
					<div class="tab-pane" id="addons">
						<div class="alert alert-block alert-info">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<h4>About the Addons Control Panel</h4>
							Browse &amp; manage your currently installed addons below. Keep in mind that some addons will be disabled automatically when they are not compatible with your currently active theme.
						</div>
						
						<!-- Begin Installed Addons -->
						
						<table id="installed-addons" class="table table-striped">
							<tbody>
								<tr>
									<td style="min-width: 60px;">
										<img src="http://placehold.it/60x60">
									</td>
									<td style="min-width: 120px;">
										<strong>Super Slideshow</strong><br>
										<ul class="unstyled">
											<li>v1.3.2</li>
											<li>by <a href="#">Leeflets</a></li>
										</ul>
									</td>
									<td>
										<small>Nulla vitae elit libero, a pharetra augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec id elit non mi porta gravida at eget metus nascetur ridiculus mus.</small>
									</td>
									<td>
										<div class="toggle-button" style="width: 100px; height: 25px;">
											<div style="left: -50%; width: 150px;"><input id="checkbox1" type="checkbox" value="value1" checked="checked"><span class="labelLeft" style="width: 50px; height: 25px; line-height: 25px;">ON</span><label for="checkbox1" style="width: 50px; height: 25px;"></label><span class="labelRight" style="width: 50px; height: 25px; line-height: 25px;">OFF</span></div>
										</div>
									</td>
								</tr>
								
								<tr>
									<td style="min-width: 60px;">
										<img src="http://placehold.it/60x60">
									</td>
									<td style="min-width: 120px;">
										<strong>Better Forms</strong><br>
										<ul class="unstyled">
											<li>v1.3.2</li>
											<li>by <a href="#">Leeflets</a></li>
										</ul>
									</td>
									<td>
										<small>Nulla vitae elit libero, a pharetra augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec id elit non mi porta gravida at eget metus nascetur ridiculus mus.</small>
									</td>
									<td>
										<div class="toggle-button" style="width: 100px; height: 25px;">
											<div style="left: -50%; width: 150px;"><input id="checkbox1" type="checkbox" value="value1" checked="checked"><span class="labelLeft" style="width: 50px; height: 25px; line-height: 25px;">ON</span><label for="checkbox1" style="width: 50px; height: 25px;"></label><span class="labelRight" style="width: 50px; height: 25px; line-height: 25px;">OFF</span></div>
										</div>
									</td>
								</tr>
								
								<tr>
									<td style="min-width: 60px;">
										<img src="http://placehold.it/60x60">
									</td>
									<td style="min-width: 120px;">
										<strong>Events Calendar</strong><br>
										<ul class="unstyled">
											<li>v1.3.2</li>
											<li>by <a href="#">Leeflets</a></li>
										</ul>
									</td>
									<td>
										<small>Nulla vitae elit libero, a pharetra augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec id elit non mi porta gravida at eget metus nascetur ridiculus mus.</small>
									</td>
									<td>
										<div class="toggle-button" style="width: 100px; height: 25px;">
											<div style="left: -50%; width: 150px;"><input id="checkbox1" type="checkbox" value="value1" checked="checked"><span class="labelLeft" style="width: 50px; height: 25px; line-height: 25px;">ON</span><label for="checkbox1" style="width: 50px; height: 25px;"></label><span class="labelRight" style="width: 50px; height: 25px; line-height: 25px;">OFF</span></div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						
						<!-- End Installed Addons -->
						
						<div class="section-header">
							<h3>Addon Marketplace <small>Browse our addon marketplace &amp; your purchased addons.</small></h3>
						</div>
						
						<!-- Begin Addon Marketplace Menu -->
						
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
						
						<!-- End Addon Marketplace Menu -->
						
						<!-- Begin Addons Marketplace -->
						
						<table id="marketplace-addons" class="table table-striped">
							<tbody>
								<tr>
									<td style="min-width: 60px;">
										<img src="http://placehold.it/60x60">
									</td>
									<td style="min-width: 120px;">
										<strong>Super Slideshow</strong><br>
										<ul class="unstyled">
											<li>v1.3.2</li>
											<li>by <a href="#">Leeflets</a></li>
										</ul>
									</td>
									<td>
										<small>Nulla vitae elit libero, a pharetra augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec id elit non mi porta gravida at eget metus nascetur ridiculus mus.</small>
									</td>
									<td style="min-width: 80px;">
										<a class="btn btn-primary pull-right toggle-modal" href="#addon-01-details" data-toggle="modal">Buy $5</a>
									</td>
								</tr>
								
								<tr>
									<td style="min-width: 60px;">
										<img src="http://placehold.it/60x60">
									</td>
									<td style="min-width: 120px;">
										<strong>Better Forms</strong><br>
										<ul class="unstyled">
											<li>v1.3.2</li>
											<li>by <a href="#">Leeflets</a></li>
										</ul>
									</td>
									<td>
										<small>Nulla vitae elit libero, a pharetra augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec id elit non mi porta gravida at eget metus nascetur ridiculus mus.</small>
									</td>
									<td style="min-width: 80px;">
										<a class="btn btn-primary pull-right toggle-modal" href="#addon-02-details" data-toggle="modal">Buy $5</a>
									</td>
								</tr>
								
								<tr>
									<td style="min-width: 60px;">
										<img src="http://placehold.it/60x60">
									</td>
									<td style="min-width: 120px;">
										<strong>Events Calendar</strong><br>
										<ul class="unstyled">
											<li>v1.3.2</li>
											<li>by <a href="#">Leeflets</a></li>
										</ul>
									</td>
									<td>
										<small>Nulla vitae elit libero, a pharetra augue. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec id elit non mi porta gravida at eget metus nascetur ridiculus mus.</small>
									</td>
									<td style="min-width: 80px;">
										<a class="btn btn-primary pull-right toggle-modal" href="#addon-03-details" data-toggle="modal">Buy $5</a>
									</td>
								</tr>
							</tbody>
						</table>

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
						
					</div>
					*/ ?>
					<!-- End Addons Tab -->
					
				</div>

			</div>
		</div>
	</div>
</section>