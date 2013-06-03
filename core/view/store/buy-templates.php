<?php 
$i = 1;
foreach ( $templates as $template ) :
	?>

	<li id="template-<?php echo $i; ?>" class="span4">
		<div class="thumbnail">
			<img src="<?php echo htmlspecialchars( $template['image_url'], null, 'utf-8' ) ?>" alt="" title="<?php echo htmlspecialchars( $template['name'], null, 'utf-8' ); ?>">
			<a href="<?php echo htmlspecialchars( $template['url'], null, 'utf-8' ); ?>" target="_blank" class="btn btn-primary">Details</a>
		</div>
	</li>

	<?php
	$i++;
endforeach; 
