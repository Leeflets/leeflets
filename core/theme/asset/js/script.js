$(document).ready(function() {
	var leeflets = new LEEFLETS();
	leeflets.init();
});

function LEEFLETS() {
	var self = this,
		$nav = $('.primary-menu'),
		$viewer = $('.viewer');

	self.init = function() {
		self.nav_events();
		self.panel_events($('.panel'));
		on_resize(self.set_sizes);
	};

	self.panel_events = function($panel) {
		$('textarea.redactor', $panel).redactor();
		self.repeatable($panel);

		$('.close.panel', $panel).click(function() {
			self.toggle_panel($panel);
		});
	};

	self.repeatable = function($panel) {
		if (!$panel.length) return;

		var $repeatable = $('fieldset.repeatable', $panel);

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

	self.slide_visible = function($el, offset) {
		var current = $el.css('left');
		if (typeof current == 'undefined' || 'auto' == current) {
			current = (-1 * ($el.outerWidth() + offset)) + 'px';
			$el.css({left: current}).show();
		}

		return (offset == current.replace('px', ''));
	};

	self.toggle_slide = function($el, offset, duration) {
		var left;

		if (self.slide_visible($el, offset)) {
			left = -1 * ($el.outerWidth() + offset);
		}
		else {
			left = offset;
		}

		var css = {left: left + 'px'};

		$el.animate(css, duration);
	};

	self.toggle_nav = function() {
		self.toggle_slide($nav, 0, 200);
	};

	self.toggle_panel = function($panel) {
		var offset = $nav.outerWidth();
		self.toggle_slide($panel, offset, 400);
	};

	self.hide_panels = function() {
		var offset = $nav.outerWidth();
		$('.panel').each(function() {
			if (self.slide_visible($(this), offset)) {
				self.toggle_panel($(this));
			}
		});
	};

	self.nav_events = function() {
		if ($('.panel').length) {
			self.toggle_nav();
			self.toggle_panel($('.panel'));
		}

        $('.show-primary-nav').click(function(){
            self.toggle_nav();
            return false;
        });

		$('.home', $nav).click(function() {
			self.toggle_nav();
			self.hide_panels();
			return false;
		});

		$('.settings, .content', $nav).click(function() {
			var id = $(this).attr('id').replace('nav-', ''),
				$panel = $('#admin-' + id);

			if ($panel.length) {
				self.hide_panels();
				self.toggle_panel($panel);
				return false;
			}

			var $anch = $(this),
				href = $anch.attr('href');

			$.get(href, {ajax:1}, function(data) {
				$nav.after(data);

				var $panel = $('.panel').eq(0);
				self.panel_events($panel);
				self.hide_panels();
				self.toggle_panel($panel);
			});

			return false;
		});

		$('.publish', $nav).click(function() {
			var pos = $(this).offset(),
				y = pos.top,
				x = $nav.outerWidth();

			$.get($(this).attr('href'), {ajax:1}, function(data) {
				var $box = $('<div class="alert-box">Published! :)</div>');
				$('body').append($box);
				$box.hide().css({
					top: y + 'px',
					left: x + 'px'
				}).fadeIn(function() {
					window.setTimeout(function() {
						$box.fadeOut();
					}, 1000);
				});
			});

			return false;
		});
	};
}

// debulked onresize handler
function on_resize(c,t){onresize=function(){clearTimeout(t);t=setTimeout(c,100)};return c};
