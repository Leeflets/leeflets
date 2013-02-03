(function($) {

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

		$('[data-lf-edit]').each(function() {
			var $container = $(this),
				fieldsets = $container.data('lf-edit'),
				offset = $container.offset(),
				width = $container.outerWidth(),
				height = $container.outerHeight();

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

			var $box = $('<div class="lf-edit-box"></div>');
			$box.css({
				'top': top + 'px',
				'left': left + 'px',
				'width': width + 'px',
				'height': height + 'px' 
			});
			$box.appendTo('.lf-edit-container');

			$box.click(function() {
				window.parent.leeflets.load_content_panel(fieldsets);
				return false;
			});

			var edit_title = $container.data('lf-edit-title');
			if (edit_title) {
				$box.append('<h1>' + edit_title + '</h1>');
			}
		});
	});

})(jQuery);