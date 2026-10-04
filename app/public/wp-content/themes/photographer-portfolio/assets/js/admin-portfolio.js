/**
 * Portfolio admin media controls.
 */

(function ($) {
	'use strict';

	function updateGalleryFields() {
		var galleryIds = [];

		$('.portfolio-admin-gallery li').each(function () {
			var id = parseInt($(this).attr('data-attachment-id'), 10);

			if (!id) {
				return;
			}

			galleryIds.push(id);
		});

		$('.portfolio-admin-gallery-ids').val(galleryIds.join(','));
	}

	function updateFeaturedFields() {
		var featuredIds = [];

		$('.portfolio-admin-featured li').each(function () {
			var id = parseInt($(this).attr('data-attachment-id'), 10);

			if (id) {
				featuredIds.push(id);
			}
		});

		$('.portfolio-admin-featured-ids').val(featuredIds.slice(0, 3).join(','));
	}

	function mediaItem(attachment, removeClass) {
		var imageUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

		return [
			'<li data-attachment-id="' + attachment.id + '">',
			'<img src="' + imageUrl + '" alt="">',
			'<button class="button-link-delete ' + removeClass + '" type="button">Pašalinti</button>',
			'</li>'
		].join('');
	}

	function initSortable() {
		$('.portfolio-admin-gallery').sortable({
			items: 'li',
			placeholder: 'portfolio-admin-gallery-placeholder',
			update: updateGalleryFields
		});

		$('.portfolio-admin-featured').sortable({
			items: 'li',
			update: updateFeaturedFields
		});
	}

	$(document).on('click', '.portfolio-admin-select-hero', function () {
		var frame = wp.media({
			title: 'Pasirinkti viršelio nuotrauką',
			button: {
				text: 'Naudoti šią nuotrauką'
			},
			multiple: false
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			var imageUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

			$('.portfolio-admin-hero-id').val(attachment.id);
			$('.portfolio-admin-hero-preview').html('<img src="' + imageUrl + '" alt="">');
		});

		frame.open();
	});

	$(document).on('click', '.portfolio-admin-remove-hero', function () {
		$('.portfolio-admin-hero-id').val('');
		$('.portfolio-admin-hero-preview').empty();
	});

	$(document).on('click', '.portfolio-admin-select-gallery', function () {
		var frame = wp.media({
			title: 'Pridėti nuotraukas į Portfolio galeriją',
			button: {
				text: 'Pridėti nuotraukas'
			},
			multiple: true
		});

		frame.on('select', function () {
			var existingIds = $('.portfolio-admin-gallery-ids').val().split(',');
			var $gallery = $('.portfolio-admin-gallery');

			frame.state().get('selection').each(function (model) {
				var attachment = model.toJSON();

				if (existingIds.indexOf(String(attachment.id)) !== -1) {
					return;
				}

				$gallery.append(mediaItem(attachment, 'portfolio-admin-remove-gallery-image'));
				existingIds.push(String(attachment.id));
			});

			updateGalleryFields();
		});

		frame.open();
	});

	$(document).on('click', '.portfolio-admin-remove-gallery-image', function () {
		$(this).closest('li').remove();
		updateGalleryFields();
	});

	$(document).on('click', '.portfolio-admin-select-featured', function () {
		var frame = wp.media({
			title: 'Pasirinkti 3 portfelio puslapio nuotraukas',
			button: {
				text: 'Naudoti pasirinktas nuotraukas'
			},
			multiple: true
		});

		frame.on('select', function () {
			var existingIds = $('.portfolio-admin-featured-ids').val().split(',');
			var $featured = $('.portfolio-admin-featured');

			frame.state().get('selection').each(function (model) {
				var attachment = model.toJSON();

				if (existingIds.indexOf(String(attachment.id)) !== -1 || $featured.children().length >= 3) {
					return;
				}

				$featured.append(mediaItem(attachment, 'portfolio-admin-remove-featured-image'));
			});

			updateFeaturedFields();
		});

		frame.open();
	});

	$(document).on('click', '.portfolio-admin-remove-featured-image', function () {
		$(this).closest('li').remove();
		updateFeaturedFields();
	});

	$(initSortable);
})(jQuery);
