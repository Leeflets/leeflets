(function($) {
	$(document).keyup(function(e) {
		if ( e.keyCode == 27 ) { // Escape key
			if ( window.parent.leeflets ) {
				window.parent.leeflets.hide_all();
			}
		}
	});

	$(document).ready(function() {
		// Add container so that any overflow
		// will not create horizontal scroll
		$('body').append('<div class="lf-edit-container"></div>');
	});

	$(window).load(function() {
		// Check if elements are visible
		$('[data-lf-edit]').each(function() {
			if ($(this).outerHeight() > 0 && $(this).outerWidth() > 0) {
				return;
			}

			var fieldsets = $('html').data('lf-edit');
			fieldsets = fieldsets + ' ' + $(this).data('lf-edit');
			$('html').data('lf-edit', fieldsets);
			$(this).removeAttr('data-lf-edit');
		});

		var boxes = [];
		$('[data-lf-edit]').each(function() {
			var box = {};
			box.$container = $(this);
			box.$pad = $('<div class="lf-edit-box"></div>');
			box.$pad.appendTo('.lf-edit-container');

			box.$pad.click(function() {
				window.parent.leeflets.load_content_panel(box.$container.data('lf-edit'));
				return false;
			});

			boxes.push(box);
		});

		function size_position_boxes() {
			$.each(boxes, function() {
				var box = this,
					offset = box.$container.offset(),
					width = box.$container.outerWidth(),
					height = box.$container.outerHeight();

				if (width <= 20) {
					width = 20;
				}

				if (height <= 20) {
					height = 20;
				}

				var top = offset.top - 20,
					left = offset.left - 20;
				
				width = width + 40;
				height = height + 40;

				box.$pad.css({
					'top': top + 'px',
					'left': left + 'px',
					'width': width + 'px',
					'height': height + 'px'
				});
			});
		}

		size_position_boxes();
		on_resize(size_position_boxes);
	});

})(jQuery);

// debulked onresize handler
function on_resize(c,t){onresize=function(){clearTimeout(t);t=setTimeout(c,100)};return c};