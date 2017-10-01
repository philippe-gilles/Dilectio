function type_photo_filename_invalid(value) {
	var filename = $("#type-photo-file-filename");
    if ((!value) || (value === undefined) || (value.length == 0)) {
        filename.parent().addClass('is-invalid');
    }
    else {
        filename.parent().removeClass('is-invalid');
    }
}

$(document).ready(function() {
	$(document).on("change", "#type-photo-file", function() {
		type_photo_filename_invalid($(this).val());
	});

	$(document).on("focusin", "#type-photo-file-filename", function() {
		type_photo_filename_invalid($(this).val());
	});

	$(document).on("focusout", "#type-photo-file-filename", function() {
		type_photo_filename_invalid($(this).val());
	});

	$(document).on("blur", "#type-photo-filename", function() {
		type_photo_filename_invalid($(this).val());
	});
});
