/**
 * Minimal portfolio lightbox.
 */

(function () {
	'use strict';

	var links = [];
	var currentIndex = -1;
	var lightbox;
	var image;
	var caption;
	var startX = 0;

	function buildLightbox() {
		lightbox = document.createElement('div');
		lightbox.className = 'portfolio-lightbox';
		lightbox.setAttribute('role', 'dialog');
		lightbox.setAttribute('aria-modal', 'true');
		lightbox.setAttribute('aria-label', 'Fotografijos peržiūra');
		lightbox.hidden = true;
		lightbox.innerHTML = [
			'<button class="portfolio-lightbox__close" type="button" aria-label="Uždaryti">&times;</button>',
			'<button class="portfolio-lightbox__nav portfolio-lightbox__nav--prev" type="button" aria-label="Ankstesnė fotografija">&lsaquo;</button>',
			'<figure class="portfolio-lightbox__figure">',
			'<img class="portfolio-lightbox__image" alt="">',
			'<figcaption class="portfolio-lightbox__caption"></figcaption>',
			'</figure>',
			'<button class="portfolio-lightbox__nav portfolio-lightbox__nav--next" type="button" aria-label="Kita fotografija">&rsaquo;</button>'
		].join('');

		document.body.appendChild(lightbox);
		image = lightbox.querySelector('.portfolio-lightbox__image');
		caption = lightbox.querySelector('.portfolio-lightbox__caption');

		lightbox.querySelector('.portfolio-lightbox__close').addEventListener('click', close);
		lightbox.querySelector('.portfolio-lightbox__nav--prev').addEventListener('click', previous);
		lightbox.querySelector('.portfolio-lightbox__nav--next').addEventListener('click', next);
		lightbox.addEventListener('click', function (event) {
			if (event.target === lightbox) {
				close();
			}
		});
		lightbox.addEventListener('touchstart', function (event) {
			startX = event.changedTouches[0].clientX;
		}, { passive: true });
		lightbox.addEventListener('touchend', function (event) {
			var delta = event.changedTouches[0].clientX - startX;

			if (Math.abs(delta) > 50) {
				delta > 0 ? previous() : next();
			}
		}, { passive: true });
	}

	function render(index) {
		var item = links[index];
		var title = item.getAttribute('data-portfolio-lightbox-title') || item.querySelector('img').alt || '';
		var alt = item.querySelector('img').alt || '';

		currentIndex = index;
		image.src = item.href;
		image.alt = alt;
		caption.textContent = title;
	}

	function open(event) {
		var link = event.currentTarget;
		event.preventDefault();

		if (!lightbox) {
			buildLightbox();
		}

		render(links.indexOf(link));
		lightbox.hidden = false;
		document.body.classList.add('has-portfolio-lightbox');
		lightbox.querySelector('.portfolio-lightbox__close').focus();
	}

	function close() {
		if (!lightbox) {
			return;
		}

		lightbox.hidden = true;
		document.body.classList.remove('has-portfolio-lightbox');
	}

	function next() {
		render((currentIndex + 1) % links.length);
	}

	function previous() {
		render((currentIndex - 1 + links.length) % links.length);
	}

	function onKeydown(event) {
		if (!lightbox || lightbox.hidden) {
			return;
		}

		if ('Escape' === event.key) {
			close();
		}

		if ('ArrowRight' === event.key) {
			next();
		}

		if ('ArrowLeft' === event.key) {
			previous();
		}
	}

	document.addEventListener('DOMContentLoaded', function () {
		links = Array.prototype.slice.call(document.querySelectorAll('[data-portfolio-lightbox]'));
		links.forEach(function (link) {
			link.addEventListener('click', open);
		});
		document.addEventListener('keydown', onKeydown);
	});
})();
