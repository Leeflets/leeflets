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
		self.drag_events();
	};

	self.drag_events = function() {
		// disable the default browser action for file drops on the document
		$(document).bind('drop dragover', function (e) {
			e.preventDefault();
		});

		$(document).bind('dragover', function (e) {
			var $uploads = $('div.file-upload');

			var timeout = window.dropZoneTimeout;

			if (!timeout) {
				$('.drop-pad', $uploads).show();
			}
			else {
				clearTimeout(timeout);
			}

			window.dropZoneTimeout = setTimeout(function () {
				window.dropZoneTimeout = null;
				$('.drop-pad', $uploads).hide();
			}, 100);
		});
	};

	self.install_events = function() {
		if ( !$('body.logged-out').length ) return;
	};

	self.connection_field_events = function() {
		$('fieldset.connection').each(function() {
			var $fieldset = $(this);
			self.hide_show_connection_fields($fieldset);
			$('select', $fieldset).change(function() {
				self.hide_show_connection_fields($fieldset);
			});
		});
	};

	self.panel_events = function($panel) {
		$('textarea.redactor', $panel).redactor();
		$('input.datepicker', $panel).datepicker({attachTo: $panel});

		$('div.file-upload').each(function() {
			var $div = $(this),
				$filename = $('.filename', $div),
				$remove = $('.btn-remove', $div),
				$input_append = $('.input-append', $div);

			$('input', this).fileupload({
				url: $(this.form).data('upload-url'),
				paramName: 'files',
				formData: [{
					name: 'input-name',
					value: $('input[type=file]', $div).attr('name')
				}],
				dataType: 'json',
				dropZone: $div,
				done: function (e, data) {
					$('.progress', $div).hide();

					$.each(data.result.files, function (index, file) {
						$filename.text(file.name);
					});

					$input_append.append($remove);
				},
				progressall: function (e, data) {
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$('.progress .bar', $div).css('width', progress + '%');
				},
				start: function(e) {
					$('.alert-error', $div).remove();
					$('.progress', $div).show();
					$('.progress .bar', $div).css('width', '1%');
				}
			});

			$remove.click(function() {
				if (!$filename.text()) return;
				var url = $(this.form).data('upload-url');
				url += '?file=' + encodeURIComponent( $filename.text() );
				url += '&input-name=' + encodeURIComponent( $('input[type=file]', $div).attr('name') );
				$.ajax(url, {
					type: 'DELETE',
					dataType: 'json',
					success: function(data, status, xhr) {
						if (data.success) {
							$filename.text('');
							$remove = $remove.detach();
						}
						else {
							$('.alert-error', $div).remove();
							var $error = $(self.get_error_html('Failed to remove file.'));
							$div.append($error);
							$error.hide().fadeIn();
						}
					}
				});
			});

			if (!$filename.text()) {
				$remove = $remove.detach();
			}
		});
			
		self.repeatable($panel);

		$('.close.panel', $panel).click(function() {
			self.toggle_panel($panel);
		});

		self.connection_field_events();

		$('.alert', $panel).hide().fadeIn();

		$('form', $panel).submit(function() {
			var request_data = $(this).serialize();
			request_data += '&ajax=1';
			$.post($(this).attr('action'), request_data, function(data) {
				$('.span12', $panel).html(data);
				$viewer[0].src = $viewer[0].src;
				self.panel_events($panel);
			});
			return false;
		});
	};

	self.get_error_html = function(msg) {
		return '\
			<div class="alert alert-error">\
				<button type="button" class="close" data-dismiss="alert">×</button>\
				' + msg + '\
			</div>\
		';
	};

	self.hide_show_connection_fields = function($fieldset) {
		if('direct' == $('select', $fieldset).val()) {
			$('.control-group', $fieldset).not('.type').hide();
		}
		else {
			$('.control-group', $fieldset).not('.type').show();
		}
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
			$('.alert', $el).fadeOut();
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

/* Backstretch (Just for the Demo)
*********************************************************************************************/

(function(e,t,n){"use strict";e.fn.backstretch=function(r,s){return(r===n||r.length===0)&&e.error("No images were supplied for Backstretch"),e(t).scrollTop()===0&&t.scrollTo(0,0),this.each(function(){var t=e(this),n=t.data("backstretch");n&&(s=e.extend(n.options,s),n.destroy(!0)),n=new i(this,r,s),t.data("backstretch",n)})},e.backstretch=function(t,n){return e("body").backstretch(t,n).data("backstretch")},e.expr[":"].backstretch=function(t){return e(t).data("backstretch")!==n},e.fn.backstretch.defaults={centeredX:!0,centeredY:!0,duration:5e3,fade:0};var r={wrap:{left:0,top:0,overflow:"hidden",margin:0,padding:0,height:"100%",width:"100%",zIndex:-999999},img:{position:"absolute",display:"none",margin:0,padding:0,border:"none",width:"auto",height:"auto",maxWidth:"none",zIndex:-999999}},i=function(n,i,o){this.options=e.extend({},e.fn.backstretch.defaults,o||{}),this.images=e.isArray(i)?i:[i],e.each(this.images,function(){e("<img />")[0].src=this}),this.isBody=n===document.body,this.$container=e(n),this.$wrap=e('<div class="backstretch"></div>').css(r.wrap).appendTo(this.$container),this.$root=this.isBody?s?e(t):e(document):this.$container;if(!this.isBody){var u=this.$container.css("position"),a=this.$container.css("zIndex");this.$container.css({position:u==="static"?"relative":u,zIndex:a==="auto"?0:a,background:"none"}),this.$wrap.css({zIndex:-999998})}this.$wrap.css({position:this.isBody&&s?"fixed":"absolute"}),this.index=0,this.show(this.index),e(t).on("resize.backstretch",e.proxy(this.resize,this)).on("orientationchange.backstretch",e.proxy(function(){this.isBody&&t.pageYOffset===0&&(t.scrollTo(0,1),this.resize())},this))};i.prototype={resize:function(){try{var e={left:0,top:0},n=this.isBody?this.$root.width():this.$root.innerWidth(),r=n,i=this.isBody?t.innerHeight?t.innerHeight:this.$root.height():this.$root.innerHeight(),s=r/this.$img.data("ratio"),o;s>=i?(o=(s-i)/2,this.options.centeredY&&(e.top="-"+o+"px")):(s=i,r=s*this.$img.data("ratio"),o=(r-n)/2,this.options.centeredX&&(e.left="-"+o+"px")),this.$wrap.css({width:n,height:i}).find("img:not(.deleteable)").css({width:r,height:s}).css(e)}catch(u){}return this},show:function(t){if(Math.abs(t)>this.images.length-1)return;this.index=t;var n=this,i=n.$wrap.find("img").addClass("deleteable"),s=e.Event("backstretch.show",{relatedTarget:n.$container[0]});return clearInterval(n.interval),n.$img=e("<img />").css(r.img).bind("load",function(t){var r=this.width||e(t.target).width(),o=this.height||e(t.target).height();e(this).data("ratio",r/o),e(this).fadeIn(n.options.speed||n.options.fade,function(){i.remove(),n.paused||n.cycle(),n.$container.trigger(s,n)}),n.resize()}).appendTo(n.$wrap),n.$img.attr("src",n.images[t]),n},next:function(){return this.show(this.index<this.images.length-1?this.index+1:0)},prev:function(){return this.show(this.index===0?this.images.length-1:this.index-1)},pause:function(){return this.paused=!0,this},resume:function(){return this.paused=!1,this.next(),this},cycle:function(){return this.images.length>1&&(clearInterval(this.interval),this.interval=setInterval(e.proxy(function(){this.paused||this.next()},this),this.options.duration)),this},destroy:function(n){e(t).off("resize.backstretch orientationchange.backstretch"),clearInterval(this.interval),n||this.$wrap.remove(),this.$container.removeData("backstretch")}};var s=function(){var e=navigator.userAgent,n=navigator.platform,r=e.match(/AppleWebKit\/([0-9]+)/),i=!!r&&r[1],s=e.match(/Fennec\/([0-9]+)/),o=!!s&&s[1],u=e.match(/Opera Mobi\/([0-9]+)/),a=!!u&&u[1],f=e.match(/MSIE ([0-9]+)/),l=!!f&&f[1];return!((n.indexOf("iPhone")>-1||n.indexOf("iPad")>-1||n.indexOf("iPod")>-1)&&i&&i<534||t.operamini&&{}.toString.call(t.operamini)==="[object OperaMini]"||u&&a<7458||e.indexOf("Android")>-1&&i&&i<533||o&&o<6||"palmGetResource"in t&&i&&i<534||e.indexOf("MeeGo")>-1&&e.indexOf("NokiaBrowser/8.5.0")>-1||l&&l<=6)}()})(jQuery,window)
