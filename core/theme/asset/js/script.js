$(document).ready(function() {
	var $nav = $('nav.primary'),
		$clip = $('body > .clip'),
		$container = $('.container', $clip),
		$content = $('.content', $container),
		$viewer = $('.viewer', $container),

		win_w = $(document).width(),
		win_h = $(window).height(),
		nav_w = $nav.outerWidth(),
		content_w = $content.outerWidth(),
		viewer_w = win_w - nav_w,
		container_w = content_w + viewer_w;

	$viewer.outerWidth(viewer_w);
	$clip.outerWidth(win_w - nav_w);
	$container.outerWidth(container_w);

	$nav.outerHeight(win_h);
	$content.outerHeight(win_h);
	$viewer.outerHeight(win_h);

	$('textarea.redactor').redactor();
});
