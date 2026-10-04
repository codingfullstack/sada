( function( wp, window ) {
	'use strict';

	var api = window.photographerPortfolioRankMath || {};
	var templateContent = api.content || '';

	if ( ! templateContent || ! wp || ! wp.hooks ) {
		return;
	}

	wp.hooks.addFilter(
		'rank_math_content',
		'photographer-portfolio/template-content',
		function( content ) {
			return [ content || '', templateContent ].join( '\n\n' );
		},
		11
	);

	wp.hooks.addAction(
		'rank_math_loaded',
		'photographer-portfolio/template-content',
		function() {
			if ( window.rankMathEditor && 'function' === typeof window.rankMathEditor.refresh ) {
				window.rankMathEditor.refresh( 'content' );
			}
		},
		20
	);
}( window.wp, window ) );
