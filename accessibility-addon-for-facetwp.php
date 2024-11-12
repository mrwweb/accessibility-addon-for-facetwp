<?php
/**
 * Plugin Name: Accessibility Addon for FacetWP
 * Description: Filters the output of FacetWP facets to use real input elements instead of divs with fake inputs. Does not fully support all facets.
 * Version: 1.0
 * Author: Mark Root-Wiley, MRW Web Design
 * Author URI: https://MRWweb.com
 * Textdomain: afwp
 * Requires Plugins: facetwp
 * Update URI: https://github.com/mrwweb/accessibility-addon-for-facetwp
*/

namespace MRW\A11yFWP;

/**
 * This plugin is an explicit alternative to FacetWP's accessibility script and is not guaranteed to work when it's enabled
 * 
 * @todo I don't actually know if this will override the user-facing setting
 */
add_filter( 'facetwp_load_a11y', '\__return_false', 999 );

add_filter( 'facetwp_facet_html', __NAMESPACE__ . '\transform_facet_markup', 10, 2 );
/**
 * Adjusts markup for specific facets so they use real input elements
 *
 * @param string $output HTML
 * @param array $params FacetWP field parameters
 * 
 * @return string Updated HTML output for a facet
 * 
 * @todo consider whether a combination of totally custom output and str_replace make sense or whether doing something with the WP HTML API might make more sense in the long term
 */
function transform_facet_markup( $output, $params ) {
	$facet_type = $params['facet']['type'];
	switch ($facet_type) {
		case 'checkboxes':
			// Note: The trick to this working was moving the facetwp-checkbox class and data-value attribute to the `input`. Clicking the label works because the input element still emits a click event when the label is clicked. Leaving that class and attribute on the wrapping list item resulted in two events being fired when the label was clicked.
			$output = '';
			$output .= '<fieldset><legend>' . esc_html( $params['facet']['label'] ) . '</legend><ul>';
			foreach( $params['values'] as $value ) {
				if( $value['counter'] > 0 || ! $params['facet']['preserve_ghosts'] === 'no' ) {
					$output .= sprintf(
						'<li>
							<input type="checkbox" id="%3$s"%1$s value="%2$s" class="facetwp-checkbox%1$s" data-value="%2$s">
							<label for="%3$s">
								<span class="facetwp-display-value">%4$s</span>
								<span class="facetwp-counter">(%5$d)</span>
							</label>
						</li>',
						in_array( $value['facet_value'], $params['selected_values'] ) ? ' checked' : '',
						esc_attr( $value['facet_value'] ),
						'checkbox-' . esc_attr( $value['term_id'] ),
						esc_html( $value['facet_display_value'] ),
						$value['counter']
					);
				}
			}
			$output .= '</ul></fieldset>';
			break;

		case 'search':
			// use search landmark and insert a real button
			$output = '<search>' . $output . '</search>';
			// remove the fake button
			$output = str_replace( '<i class="facetwp-icon"></i>', '', $output );
			// add label to search input
			$id = $params['facet']['name'];
			$output = str_replace( '<input', '<label for="' . esc_attr( $id ) . '">' . esc_html__( 'Search', 'afwp' ) . '</label><div class="trec-facetwp-search-wrapper"><input id="' . esc_attr( $id ) . '"', $output );
			// facetwp-icon class is important to retain event handling
			$output = str_replace( '</span>', '<button class="facetwp-icon"><span class="afwp-search-submit-text">Submit</span></button></div></span>', $output );
			// placeholders are bad for UX
			$output = str_replace( 'placeholder="Enter keywords"', '', $output );
			break;
		
		case 'pager':            
			// put links in a list with nav element
			$output = str_replace( '<div', '<nav aria-labelledby="resource-paging-heading"><h3 class="screen-reader-text" id="resource-paging-heading">' . esc_html__( 'Results Pages', 'afwp' ) . '</h3><ul', $output );
			$output = str_replace( '</div>', '</ul></nav>', $output );
			$output = str_replace( '<a', '<li><a', $output );
			$output = str_replace( '</a>', '</a></li>', $output );
			// add tabindex to valid links only for keyboard accessibility
			$output = str_replace( 'data-page', 'tabindex="0" data-page', $output );
			break;

		case 'sort':
			// add label to sort select
			$id = $params['facet']['name'];
			$output = str_replace( '<select', '<label for="sort">' . esc_html__( 'Sort by', 'afwp' ) . '</label><select id="sort"', $output );
			break;
	}
	
	return $output;
}
