/**
 * Minimal theme JavaScript foundation.
 */

(function () {
	'use strict';

	document.documentElement.classList.remove('no-js');
	document.documentElement.classList.add('js');

	const scatterTargets = [
		'.editorial-text',
		'.site-main p',
		'.site-main li',
		'.site-main blockquote',
		'.site-main figcaption',
		'.wp-block-paragraph',
		'.wp-block-post-excerpt',
		'.wp-block-quote',
	].join(',');

	const excludedParents = [
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'nav',
		'menu',
		'button',
		'input',
		'textarea',
		'select',
		'label',
		'form',
		'.site-header',
		'.site-footer',
		'.primary-navigation',
		'.footer-navigation',
		'.language-switcher',
		'.social-links',
		'.contact-detail',
		'.portfolio-featured-section__header',
		'.portfolio-gallery__caption',
		'.about-editorial__eyebrow',
		'.about-closing__brand',
	].join(',');
	const textBlocks = 'p, li, blockquote, figcaption';
	const spacingPattern = [0, 6, 1, 8, 3, 9, 2, 5, 7, 4, 1, 9, 0, 5, 8, 2, 6, 3, 7, 0, 4, 9, 1, 6, 8, 5, 2, 7, 3];

	const getSpaceIndex = function (word, index) {
		let hash = index * 37;

		for (let i = 0; i < word.length; i += 1) {
			hash = (hash + word.charCodeAt(i) * (i + 11)) % 1543;
		}

		hash = (hash + (index % 7) * 19 + (index % 11) * 23) % spacingPattern.length;

		return spacingPattern[hash];
	};

	const wrapTextNode = function (node, wordOffset) {
		const text = node.nodeValue;
		const words = text.match(/\S+/g);

		if (!words) {
			return wordOffset;
		}

		const fragment = document.createDocumentFragment();
		let index = wordOffset;

		words.forEach(function (word, wordIndex) {
			const span = document.createElement('span');
			span.className = 'editorial-word editorial-space-' + getSpaceIndex(word, index);
			span.textContent = word + (wordIndex === words.length - 1 ? '' : ' ');
			fragment.appendChild(span);
			index += 1;
		});

		node.parentNode.replaceChild(fragment, node);

		return index;
	};

	const scatterElement = function (element) {
		if (element.dataset.editorialScatter === 'true' || element.closest(excludedParents)) {
			return;
		}

		const blocks = element.matches(textBlocks) ? [element] : Array.from(element.querySelectorAll(textBlocks));
		const targets = blocks.length ? blocks : [element];

		targets.forEach(function (target) {
			if (target.dataset.editorialScatter === 'true' || target.closest(excludedParents)) {
				return;
			}

			const walker = document.createTreeWalker(
				target,
				NodeFilter.SHOW_TEXT,
				{
					acceptNode: function (node) {
						if (!node.nodeValue.trim() || node.parentElement.closest('.editorial-word, script, style, code, pre')) {
							return NodeFilter.FILTER_REJECT;
						}

						return NodeFilter.FILTER_ACCEPT;
					}
				}
			);

			const textNodes = [];
			let currentNode = walker.nextNode();

			while (currentNode) {
				textNodes.push(currentNode);
				currentNode = walker.nextNode();
			}

			if (!textNodes.length) {
				return;
			}

			let wordOffset = 0;
			textNodes.forEach(function (node) {
				wordOffset = wrapTextNode(node, wordOffset);
			});

			target.classList.add('editorial-scatter');
			target.dataset.editorialScatter = 'true';
		});
	};

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll(scatterTargets).forEach(scatterElement);
	});
})();
