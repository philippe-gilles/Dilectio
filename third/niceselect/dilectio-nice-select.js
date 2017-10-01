/*
 * DILECTUS NICE SELECT
 *
 * Inspired from jQuery Nice Select - v1.1.0
 * https://github.com/hernansartorio/jquery-nice-select
 * Made by Hernán Sartorio
 */
 
(function($) {
	$.fn.dilectioNiceSelect = function(method) {
		// Methods
		if (typeof method == 'string') {    
			if (method == 'update') {
				this.each(function() {
					var $select = $(this);
					var $dropdown = $(this).next('.dilectio-nice-select');
					var open = $dropdown.hasClass('open');

					if ($dropdown.length) {
						$dropdown.remove();
						create_nice_select($select);
						if (open) {
							$select.next().trigger('click');
						}
					}
				});
			}
			else if (method == 'destroy') {
				this.each(function() {
					var $select = $(this);
					var $dropdown = $(this).next('.dilectio-nice-select');

					if ($dropdown.length) {
						$dropdown.remove();
						$select.css('display', '');
					}
				});
				if ($('.dilectio-nice-select').length == 0) {
					$(document).off('.nice_select');
				}
			} 
			else {
				console.log('Method "' + method + '" does not exist.')
			}
			return this;
		}

		// Hide native select
		this.hide();

		// Create custom markup
		this.each(function() {
			var $select = $(this);

			if (!$select.next().hasClass('dilectio-nice-select')) {
				create_nice_select($select);
			}
		});

		function create_nice_select($select) {
			var attr_id = $select.attr("id");
			if (attr_id !== undefined) {
				var after_div = "<div id='nice-"+attr_id+"'></div>";
			}
			else {
				var after_div = "<div></div>";
			}
			$select.after($(after_div)
				.addClass('dilectio-nice-select')
				.addClass($select.attr('class') || '')
				.addClass($select.attr('disabled') ? 'disabled' : '')
				.attr('tabindex', $select.attr('disabled') ? null : '0')
				.html('<span class="current"></span><ul class="list"></ul>')
			);

			var $dropdown = $select.next();
			var $options = $select.find('option');
			var $selected = $select.find('option:selected');

			var valeur = $select.val();
			if (valeur == "0") {
				$dropdown.removeClass("dilectio-nice-select-not-null-val").addClass("dilectio-nice-select-null-val");
			}
			else {
				$dropdown.removeClass("dilectio-nice-select-null-val").addClass("dilectio-nice-select-not-null-val");
			}

			$dropdown.find('.current').html($selected.data('display') || $selected.text());
			
			/* Récupération des niveaux */
			var tab_level = [];
			$options.each(function(i) {
				var $option = $(this);
				var datalevel = $option.data('level');
				var level = (datalevel === undefined)?0:datalevel;
				var int_level = parseInt(level);
				tab_level[i] = parseInt(level);
			});

			/* Constitution de l'arbre */
			var i_max = tab_level.length - 1;
			var prec_tab = [];
			var tree_level = [];
			for (var i = i_max;i >= 0;i--) {
				var tab = [];
				var level = tab_level[i];
				var j_max = Math.min((level - 1), prec_tab.length);
				if (level > prec_tab.length) {
					for (var j = 0;j < prec_tab.length;j++) {
						tab[j] = (prec_tab[j] != "blank")?"full":"blank";
					}
					for (var j = prec_tab.length;j < (level - 1);j++) {
						tab[j] = "blank";
					}
					tab[(level-1)] = "half";
				}
				else if (level <= prec_tab.length) {
					for (var j = 0;j < (level - 1);j++) {
						tab[j] = (prec_tab[j] != "blank")?"full":"blank";
					}
					tab[(level-1)] = (prec_tab[(level-1)] != "blank")?"full":"half";
				}
				tree_level[i] = tab;
				prec_tab = tab;
			}

			/* Création des options */
			$options.each(function(i) {
				var $option = $(this);

				/* Texte dans le champ */
				var display = $option.data('display');

				/* Niveau */
				var option_texte = "";
				var tab = tree_level[i];
				for (var j = 0;j < tab.length;j++) {
					option_texte += "<span class='dilectio-nice-option-"+tab[j]+"-level'></span>";
				}
				if (tab.length > 0) {option_texte += "<span class='dilectio-nice-option-tiret'></span>";}
				option_texte += $option.text();

				/* Icone */
				var dataicomoon = $option.data('icomoon');
				if (dataicomoon !== undefined) {
					var dataicoclass = $option.data('icoclass');
					var icoclass = "icon-"+dataicomoon+((dataicoclass !== undefined)?(" "+dataicoclass):"");
					option_texte = "<i class='dilectio-nice-option-icomoon "+icoclass +"'></i>"+option_texte;
				}
				
				$dropdown.find('ul').append($('<li></li>')
					.attr('data-value', $option.val())
					.attr('data-display', (display || null))
					.addClass('option '+
					($option.is(':selected') ? ' selected' : '') +
					($option.is(':disabled') ? ' disabled' : ''))
					.html(option_texte)
				);
			});
		}

		/* Event listeners */

		// Unbind existing events in case that the plugin has been initialized before
		$(document).off('.nice_select');

		// Open/close
		$(document).on('click.nice_select', '.dilectio-nice-select', function(event) {
			var $dropdown = $(this);

			$('.dilectio-nice-select').not($dropdown).removeClass('open');
			$dropdown.toggleClass('open');

			if ($dropdown.hasClass('open')) {
				$dropdown.find('.option');  
				$dropdown.find('.focus').removeClass('focus');
				$dropdown.find('.selected').addClass('focus');
			}
			else {
				$dropdown.focus();
			}
		});

		// Close when clicking outside
		$(document).on('click.nice_select', function(event) {
			if ($(event.target).closest('.dilectio-nice-select').length === 0) {
				$('.dilectio-nice-select').removeClass('open').find('.option');  
			}
		});

		// Option click
		$(document).on('click.nice_select', '.dilectio-nice-select .option:not(.disabled)', function(event) {
			var $option = $(this);
			var $dropdown = $option.closest('.dilectio-nice-select');

			$dropdown.find('.selected').removeClass('selected');
			$option.addClass('selected');

			var text = $option.data('display') || $option.text();
			$dropdown.find('.current').text(text);

			var valeur = $option.data('value');
			if (valeur == "0") {
				$dropdown.removeClass("dilectio-nice-select-not-null-val").addClass("dilectio-nice-select-null-val");
			}
			else {
				$dropdown.removeClass("dilectio-nice-select-null-val").addClass("dilectio-nice-select-not-null-val");
			}

			$dropdown.prev('select').val(valeur).trigger('change');
		});

		// Keyboard events
		$(document).on('keydown.nice_select', '.dilectio-nice-select', function(event) {
			var $dropdown = $(this);
			var $focused_option = $($dropdown.find('.focus') || $dropdown.find('.list .option.selected'));

			// Space or Enter
			if (event.keyCode == 32 || event.keyCode == 13) {
				if ($dropdown.hasClass('open')) {
					$focused_option.trigger('click');
				}
				else {
					$dropdown.trigger('click');
				}
				return false;
			}
			// Down
			else if (event.keyCode == 40) {
				if (!$dropdown.hasClass('open')) {
					$dropdown.trigger('click');
				}
				else {
					var $next = $focused_option.nextAll('.option:not(.disabled)').first();
					if ($next.length > 0) {
						$dropdown.find('.focus').removeClass('focus');
						$next.addClass('focus');
					}
				}
				return false;
			}
			// Up
			else if (event.keyCode == 38) {
				if (!$dropdown.hasClass('open')) {
					$dropdown.trigger('click');
				}
				else {
					var $prev = $focused_option.prevAll('.option:not(.disabled)').first();
					if ($prev.length > 0) {
						$dropdown.find('.focus').removeClass('focus');
						$prev.addClass('focus');
					}
				}
				return false;
			}
			// Esc
			else if (event.keyCode == 27) {
				if ($dropdown.hasClass('open')) {
					$dropdown.trigger('click');
				}
			}
			// Tab
			else if (event.keyCode == 9) {
				if ($dropdown.hasClass('open')) {
					return false;
				}
			}
		});

		// Detect CSS pointer-events support, for IE <= 10. From Modernizr.
		var style = document.createElement('a').style;
		style.cssText = 'pointer-events:auto';
		if (style.pointerEvents !== 'auto') {
			$('html').addClass('no-csspointerevents');
		}

		return this;
	};
}(jQuery));
