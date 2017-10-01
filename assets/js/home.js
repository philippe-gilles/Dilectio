/*
 * Dilectio : JS page accueil
 */

 /* JS CALENDRIERS */
 
/* INTERNATIONALISATION */
function getDayName_fr(day) {
    return ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'][day];
}

function getDayName_en(day) {
    return ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][day];
}

/* COMPOSANT */
var DILECTIO = DILECTIO || {};

DILECTIO.calendar = {

	events: [],

	runFunction: function(name, arguments) {
		var fn = window[name];
		if (typeof fn !== 'function') {return undefined;}
		var ret = fn.apply(window, arguments);
		return ret;
	},

	daysInMonth: function(year, month) {
		month++;
		switch (month) {
			case 2:
				var isLeap = ( (!(year % 4)) && ( (year % 100) || (!(year % 400)) ) );
				return (isLeap) ? 29 : 28;
			case 4, 6, 9, 11:
				return 30;
			default:
				return 31;
		}
	},
	
	add: function(arg) {
		if ($.isArray(arg)) {
			var nb_events = arg.length;
			for (var i = 0; i < nb_events; i++) {
				DILECTIO.calendar.events.push(arg[i]);
			}
		}
		else {
			DILECTIO.calendar.events.push(arg);
		}
	},

	render: function(lang, id, month, year) {
		month--;
		var date = new Date(year, month, 1);
		var day = date.getDay();
		var daysmonth = DILECTIO.calendar.daysInMonth(year, month);
		
		var calendar = $("#"+id);
		if (calendar.length > 0) {
			// Titre
			var header = $("#"+id+" header");
			var monthName = tab_month[month];
			header.html("<h1>"+monthName+" "+year+"</h1>");
			
			// Jours de la semaine
			var names = $("#"+id+" .day-names");
			for (i = 0; i < 7; i++) {
				var dayName = DILECTIO.calendar.runFunction("getDayName_"+lang, [i]);
				names.append('<p>'+dayName+'</p>');
			}

			// Espaces début
			var days = $("#"+id+" .days");
			var startSpacers = (day == 0)?6:day-1;
			for (i = 0; i < startSpacers; i++) {
				days.append('<div class="spacer pre"></div>');
			}

			// Détection du jour d'aujourd'hui
			var today = new Date();
			var todayI = 0;
			var todayM = today.getMonth();
			var todayY = today.getFullYear();
			if ((todayM == month) && (todayY == year)) {
				var todayI = today.getDate();
			}

			// Détection des événements du mois */
			var eventsmonth = {};
			var nb_events = DILECTIO.calendar.events.length;
			for (var i = 0; i < nb_events; i++) {
				var eventI = DILECTIO.calendar.events[i];
				var eventMonthI = parseInt(eventI["month"]) - 1;
				var eventYearI = parseInt(eventI["year"]);
				if ((eventMonthI == month) && (eventYearI == year)) {
					eventDayI = parseInt(eventI["day"]);
					if (!eventsmonth.hasOwnProperty(eventDayI)) {
						eventsmonth[eventDayI] = [];
					}
					eventsmonth[eventDayI].push(eventI);
				}
			}

			// Série des jours
			var contentI = "", classI = "";
			for (var i = 1; i <= daysmonth; i++) {
				contentI = "";
				classI = "day"+((todayI == i)?" day-today":"");
				if (eventsmonth.hasOwnProperty(i)) {
					var contentsI = eventsmonth[i];
					var nb_contentsI = contentsI.length;
					for (var j = 0; j < nb_contentsI; j++) {
						var eventJ = contentsI[j];
						var label = eventJ["label"];
						var descr = eventJ["descr"];
						var url = eventJ["url"];
						
						/* Evts grand écran */
						var html_large = '<a class="large-event"';
						if (descr !== undefined) {
							html_large += ' title="'+descr+'"';
						}
						if (url !== undefined) {
							html_large += ' href="'+url+'"';
						}
						html_large += '>'+label+'</a>';
						contentI += html_large;
						
						/* Evts petit écran */
						var html_small = '<a class="small-event"';
						html_small += ' title="'+label+'"';
						if (url !== undefined) {
							html_small += ' href="'+url+'"';
						}
						html_small += '></a>';
						contentI += html_small;
					}
				}
				days.append('<div class="'+classI+'"><span class="day-number">' + i + '</span>'+contentI+'</div>');
			}

			// Espaces fin
			var endSpacers = (7 - (day - 1 + daysmonth) % 7) % 7;
			for (i = 0; i < endSpacers; i++) {
				days.append('<div class="spacer post"></div>');
			}
		}
	}
}

/* JS PAGE */
DILECTIO.home = {
	
	more: false,
	infinite_scroll: true,
	
	load_cards: function(id_periode, id_next) {
		$.ajax({
			type: "POST",
			url: "ajax/home/load",
			data: {id_periode: id_periode, id_next: id_next},
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			if (valide == true) {
				var id = parseInt(data["id"]);
				/* MAJ du slider date */
				var newer = parseInt(data["newer"]);
				var older = parseInt(data["older"]);
				var slider_min = parseInt($("#slider-date").attr("min"));
				if (older < slider_min) {
					DILECTIO.home.update_slider_min(older);
				}
				/* MAJ du titre */
				var titre = data["titre"];
				var titre_periode = $("#titre-periode-"+id);
				if (titre_periode.length > 0) {
					titre_periode.html(titre);
				}
				/* MAJ des cartes */
				var html = data["html"];
				var grille_periode = $("#grille-periode-"+id);
				if (grille_periode.length > 0) {
					grille_periode.html(html);
					DILECTIO.home.apply_filters_to("#periode-"+id);
				}
				/* MAJ du bouton "infinite_scroll" */
				if (id_periode > 0) {
					DILECTIO.home.more = data["more"];
					if (DILECTIO.home.more === false) {
						DILECTIO.home.infinite_scroll = false;
						$("#periode-end").css("display", "block");
					}
				}
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
		return false;
	},
	
	load_more: function() {
		var attr_next = $(".dilectio-periode-grille").last().attr("id");
		if (attr_next !== undefined) {
			var id_periode = parseInt(attr_next.replace("periode-", ""));
			if (id_periode > 1) {
				periode = $("#periode-"+id_periode);
				if (periode.length == 1) {
					/* Analyse de la période précédente */
					var id_next = 0;
					var id_periode_precedente = id_periode - 1;
					if ($("#periode-"+id_periode_precedente).length == 1) {
						var last_card = $("#periode-"+id_periode_precedente+" .dilectio-extrait:last-child");
						if (last_card.length == 1) {
							var attr_id_next = last_card.attr("id");
							id_next = parseInt(attr_id_next.replace("article-", ""));
						}
					}
					
					if (id_next > 0) {
						/* Préparation de la prochaine periode */
						var id_periode_suivante = id_periode + 1;
						if ($("#periode-"+id_periode_suivante).length == 0) {
							var periode_suivante = periode.clone().attr("id", "periode-"+id_periode_suivante);
							periode_suivante.find("#titre-periode-"+id_periode).attr("id", "titre-periode-"+id_periode_suivante);
							periode_suivante.find("#grille-periode-"+id_periode).attr("id", "grille-periode-"+id_periode_suivante);
							periode.after(periode_suivante);
						}
						else {
							$("#periode-"+id_periode_suivante).find("#titre-periode-"+id_periode_suivante).empty();
							$("#periode-"+id_periode_suivante).find("#grille-periode-"+id_periode_suivante).empty();
						}
						
						/* Chargement de la période */
						DILECTIO.home.load_cards(id_periode, id_next);
					}
				}
			}
		}
		return false;
	},
	
	apply_slider: function() {
		var valeur = $(this).val();
		/* Scrolling */
		var container = $('.mdl-layout__content');
		var delta = container.scrollTop();
		var decalage = delta;
		$(".dilectio-extrait").each(function() {
			var time = $(this).attr("data-time");
			var card = $(this);
			var offset = card.position().top;
			decalage = offset + delta;
			if (time <= valeur) {
				return false;
			}
		});
		DILECTIO.home.infinite_scroll = false;
		container.finish().animate({scrollTop: decalage}, 200, function() {
			setTimeout(function() {DILECTIO.home.infinite_scroll = DILECTIO.home.more;}, 200);
		});
		DILECTIO.home.update_slider_caption(valeur);
		return false;
	},
	
	update_slider: function() {
		return false;
	},
	
	update_slider_min: function(min) {
		/* Petit hack pour la mise à jour du slider MDL */
		var clone = $("#slider-date").removeClass("is-upgraded").removeAttr("data-upgraded").removeData("upgraded").clone().attr("min", min);
		$("#slider-date").parent().remove();
		$("p.dilectio-slider").prepend(clone);
		componentHandler.upgradeDom();
		return false;
	},
	
	update_slider_caption: function(valeur) {
		var today = new Date();
		var today_year = today.getFullYear();
		var timestamp = new Date(valeur * 1000);
		var year = timestamp.getFullYear();
		var month = tab_month[timestamp.getMonth()];
		var day = timestamp.getDate();
		var format = day+" "+month;
		if (today_year > year) {format = format+" "+year;}
		$("#slider-caption").html(format);
		return false;
	},
	
	refresh_clear_filters: function() {
		var val_categorie = parseInt($("#filter-categorie").val());
		var val_type = parseInt($("#filter-type").val());
		var val_unread = $("#filter-read").is(":checked");
		var val_mine = $("#filter-mine").is(":checked");
		var val_favorite = $("#filter-favorite").is(":checked");
		if ((val_categorie == 0) && (val_type == 0) && (val_unread === false) && (val_mine === false) && (val_favorite === false)) {
			$("#clear-filters").attr("disabled", "disabled");
		}
		else {
			$("#clear-filters").removeAttr("disabled");
		}
		return false;
	},

	apply_clear_filters: function() {
		$("#filter-categorie").val(0).dilectioNiceSelect("update");
		$("#filter-type").val(0).dilectioNiceSelect("update");
		$("#filter-read").prop("checked", false);
		$("label[for='filter-read']").removeClass("is-checked");
		$("#filter-mine").prop("checked", false);
		$("label[for='filter-mine']").removeClass("is-checked");
		$("#filter-favorite").prop("checked", false);
		$("label[for='filter-favorite']").removeClass("is-checked");
		DILECTIO.home.apply_filters();
		return false;
	},
	
	apply_filters: function() {
		DILECTIO.home.apply_filters_to(".dilectio-grille-extraits");
		DILECTIO.home.refresh_clear_filters();
		return false;
	},

	apply_filters_to: function(selector) {
		var val_categorie = parseInt($("#filter-categorie").val());
		var val_type = parseInt($("#filter-type").val());
		var val_unread = $("#filter-read").is(":checked");
		var val_mine = $("#filter-mine").is(":checked");
		var val_favorite = $("#filter-favorite").is(":checked");
		$(selector+" .dilectio-extrait").each(function() {
			var hide = false;
			if (val_categorie > 0) {
				var extrait_categorie = parseInt($(this).data("category"));
				if ((extrait_categorie > 0) && (extrait_categorie != val_categorie)) {hide = true;}
			}
			if (val_type > 0) {
				var extrait_type = parseInt($(this).data("type"));
				if ((extrait_type > 0) && (extrait_type != val_type)) {hide = true;}
			}
			var extrait_read = parseInt($(this).data("read"));
			var extrait_mine = parseInt($(this).data("mine"));
			var extrait_favorite = parseInt($(this).data("favorite"));
			if ((val_mine === false) && (extrait_mine == 1)) {hide = true;}
			if ((val_unread === true) && (extrait_read == 1)) {hide = true;}
			if ((val_favorite === true) && (extrait_favorite == 0)) {hide = true;}
			if (hide) {$(this).addClass("dilectio-extrait-hide");} else {$(this).removeClass("dilectio-extrait-hide");}
		});
		return false;
	},
	
	toggle_view_gallery: function() {
		var thumbs = $(".dilectio-extrait").not(".dilectio-extrait-hide").find(".dilectio-type-photo-extrait");
		if (thumbs.length > 0) {
			var tab_src = [];
			thumbs.each(function() {
				var src = $(this).data("original");
				var elem = {href: src};
				tab_src.push(elem);
			});
			$.swipebox(tab_src);
		}
		return false;
	},
	
	toggle_view_calendar: function() {
		$.swipebox([{href:'#calendar-0'}, {href:'#calendar-1'}, {href:'#calendar-2'}, {href:'#calendar-3'}], {initialIndexOnArray:1});
		return false;
	}
}

$(document).ready(function() {
	/* Application du slider */
	$(document).on("input change", "#slider-date", DILECTIO.home.apply_slider);

	/* Application des filtres */
	$("#clear-filters").click(DILECTIO.home.apply_clear_filters);
	$("#filter-categorie").change(DILECTIO.home.apply_filters);
	$("#filter-type").change(DILECTIO.home.apply_filters);
	$("#filter-categorie").dilectioNiceSelect();
	$("#filter-type").dilectioNiceSelect();
	$("#filter-read, #filter-mine, #filter-favorite").change(DILECTIO.home.apply_filters);

	/* Changements de vues */
	$("#toggle-view-gallery").click(DILECTIO.home.toggle_view_gallery);
	$("#toggle-view-calendar").click(DILECTIO.home.toggle_view_calendar);

	/* Chargement des posts d'aujourd'hui */
	DILECTIO.home.load_cards(0, 0);

	/* Chargement des posts récents */
	DILECTIO.home.load_cards(1, 0);

	/* Infinite scroll */
	$(".mdl-layout__content").on("scroll", function() {
		if (DILECTIO.home.infinite_scroll === true) {
			var scroll_height = $(this)[0].scrollHeight;
			var height = $(this).height();
			var scroll_top = $(this).scrollTop();
			if (scroll_height - height - scroll_top < 50) {
				DILECTIO.home.load_more();
			}
		}
		DILECTIO.home.update_slider();
		return false;
	});
});
