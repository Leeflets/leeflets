<?php 
$i = 1;
foreach ( $products as $addon ) :
	?>

	<tr>
		<td style="min-width: 60px;">
			<img src="<?php echo htmlspecialchars( $addon['image_url'], null, 'utf-8' ) ?>" width="60">
		</td>
		<td style="min-width: 120px;">
			<strong><?php echo htmlspecialchars( $addon['name'], null, 'utf-8' ); ?></strong><br>
			<ul class="unstyled">
				<li>v<?php echo htmlspecialchars( $addon['version'], null, 'utf-8' ); ?></li>
			</ul>
		</td>
		<td>
			<small><?php echo htmlspecialchars( $addon['desc'], null, 'utf-8' ); ?></small>
		</td>
		<td style="min-width: 80px;">
			<a class="btn btn-primary pull-right" href="<?php echo htmlspecialchars( $addon['url'], null, 'utf-8' ); ?>">Details</a>
		</td>
	</tr>

	<?php
	$i++;
endforeach; 
