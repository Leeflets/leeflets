$(document).ready(function() {
	var leeflets = new LEEFLETS();
	leeflets.init();
});

function LEEFLETS() {
	var self = this,
		$nav = $('nav.primary'),
		$clip = $('body > .clip'),
		$container = $('.container', $clip),
		$viewer = $('.viewer', $container);

	self.init = function() {
		self.set_sizes();
		self.nav_events();
		self.content_events($('.content', $container));
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

	self.content_events = function($content) {
		$('textarea.redactor', $content).redactor();
		self.repeatable();
	};

	self.repeatable = function() {
		var $content = $('.content.edit-content', $container);
		if (!$content.get(0)) return;

		var $repeatable = $('fieldset.repeatable', $content);

		$repeatable.each(function() {
			var $control = $(this);
			var $add_new = $('<p class="add-first"><a href="">Add new</a></p>');
			$control.append($add_new);
			$('a', $add_new).click(function() {
				$('fieldset', $control).show();
				$add_new.hide();
				return false;
			});

			if ($('fieldset', $control).length > 0) {
				$add_new.hide();
			}
		});

		$('fieldset', $repeatable).each(function() {
			$(this).append('<div class="controls"><a href="" class="remove">x</a><a href="" class="add">+</a></div>');
			self.field_group_events($(this));
		});
	};

	self.field_group_events = function( $group ) {

		$('.add', $group).click(function() {
			var $new = $group.clone();
			$('input, textarea, select', $new).val('');
			$group.after($new);
			self.field_group_events($new);
			self.sequence_fields($group.parents('fieldset.repeatable'));
			return false;
		});

		$('.remove', $group).click(function() {
			var $repeatable = $group.parents('fieldset.repeatable');
			if ( $('fieldset', $repeatable).length == 1 ) {
				$('input, textarea, select', $group).val('');
				$group.hide();
				$('.add-first', $repeatable).show();
			}
			else {
				$group.remove();
			}
			self.sequence_fields($repeatable);
			return false;
		});
	};

	self.sequence_fields = function( $repeatable ) {
		$('fieldset', $repeatable).each(function(i) {
			var $fieldset = $(this);
			$('input, textarea, select', $fieldset).each(function() {
				var new_name = $(this).attr('name').replace(/\[[0-9]+\]/, '[' + i + ']');
				$(this).attr('name', new_name);
			});
		});
	};

	self.show_home = function() {
		var $content = $('.content', $container);
		$container.animate({
			'left': ($content.outerWidth() * -1) + 'px'
		}, function() {
			$content.remove();
			$container.css('left', '0px');
		});
	};

	self.nav_events = function() {
		$('.home', $nav).click(function() {
			self.show_home();
			return false;
		});

		$('.settings, .content', $nav).click(function() {
			var $content_old = $('.content', $container);

			var $anch = $(this),
				href = $anch.attr('href');

			if ($content_old.hasClass($anch.attr('container-name'))) {
				self.show_home();
				return false;
			}

			$.get(href, {ajax:1}, function(data) {
				$container.prepend(data);
				var $content_new = $('.content', $container).eq(0);
				self.content_events($content_new);
				self.set_sizes();

				$content_new.css('overflow', 'hidden');
				var set_scroll = function() {
					$content_new.css('overflow', 'scroll');
				};

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
						set_scroll();
					};
				}
				else {
					$to_animate = $container;
					anim_callback = set_scroll;
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
