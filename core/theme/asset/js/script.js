$(document).ready(function() {

	var leeflets = new LEEFLETS();
	leeflets.init();

	$('textarea.redactor').redactor();
});

function LEEFLETS() {
	var self = this,
		$nav = $('nav.primary'),
		$clip = $('body > .clip'),
		$container = $('.container', $clip),
		$viewer = $('.viewer', $container);

	//console.log($viewer.get(0));

	self.init = function() {
		self.set_sizes();
		self.nav_events();
		on_resize(self.set_sizes);
	};

	self.set_sizes = function() {
		var $content = $('.content', $container),

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
	};

	self.nav_events = function() {
		$('.settings, .content', $nav).click(function() {
			var $content_old = $('.content', $container);

			var $anch = $(this),
				href = $anch.attr('href');

			$.get(href, {ajax:1}, function(data) {
				$container.prepend(data);
				var $content_new = $('.content', $container).eq(0);
				self.set_sizes();

				var $to_animate;
				var anim_callback;
				if ($content_old.get(0)) {
					$to_animate = $content_new;
					$content_new.css({
						'position': 'absolute',
						'z-index': 7
					});
					anim_callback = function() {
						$content_old.remove();
						$content_new.css({
							'position': 'relative',
							'z-index': 6
						});
					};
				}
				else {
					$to_animate = $container;
				}

				$to_animate.css({
					'left': ($content_new.outerWidth() * -1) + 'px'
				});
				$to_animate.animate({'left': '0px'}, 200, anim_callback);
			});

			return false;
		});
	};
}

// debulked onresize handler
function on_resize(c,t){onresize=function(){clearTimeout(t);t=setTimeout(c,100)};return c};
