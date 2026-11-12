<?php
/**
 * Plugin Name:       Practical Web Tools — Free Calculator Widgets
 * Plugin URI:        https://practicalwebtools.com/embed
 * Description:       Embed free calculator widgets — mortgage, loan, compound interest, BMI, calorie, due date, percentage, countdown, and word count — with the [pwt_calculator] shortcode, or embed any calculator from practicalwebtools.com as an auto-sizing iframe. The widget scripts are bundled and run entirely in the browser: no API key, no data collection.
 * Version:           1.0.0
 * Author:            Practical Web Tools
 * Author URI:        https://practicalwebtools.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       practical-web-tools-calculators
 *
 * This plugin is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 2 of the License, or (at your option)
 * any later version.
 *
 * The JavaScript widgets bundled in assets/embed/ are also available from
 * https://practicalwebtools.com/embed and are released under the MIT License.
 * MIT-licensed code may be redistributed within GPL-2.0-or-later software;
 * see the license note in readme.txt for details.
 */

defined( 'ABSPATH' ) || exit;

define( 'PWT_CALCULATORS_VERSION', '1.0.0' );

/**
 * The nine bundled widgets: asset file, calculator page path, and label.
 *
 * @return array<string, array{file:string, path:string, title:string}>
 */
function pwt_calculators_widgets() {
	static $widgets = array(
		'bmi'        => array(
			'file'  => 'bmi-widget.js',
			'path'  => '/health/bmi-calculator',
			'title' => 'BMI Calculator',
		),
		'mortgage'   => array(
			'file'  => 'mortgage-widget.js',
			'path'  => '/finance/mortgage-calculator',
			'title' => 'Mortgage Calculator',
		),
		'calorie'    => array(
			'file'  => 'calorie-widget.js',
			'path'  => '/health/calorie-calculator',
			'title' => 'Calorie Calculator',
		),
		'duedate'    => array(
			'file'  => 'duedate-widget.js',
			'path'  => '/health/due-date-calculator',
			'title' => 'Due Date Calculator',
		),
		'percentage' => array(
			'file'  => 'percentage-widget.js',
			'path'  => '/math/percentage-calculator',
			'title' => 'Percentage Calculator',
		),
		'compound'   => array(
			'file'  => 'compound-widget.js',
			'path'  => '/finance/compound-interest-calculator',
			'title' => 'Compound Interest Calculator',
		),
		'countdown'  => array(
			'file'  => 'countdown-widget.js',
			'path'  => '/other/time/countdown-timer',
			'title' => 'Countdown Timer',
		),
		'loan'       => array(
			'file'  => 'loan-widget.js',
			'path'  => '/finance/loan-calculator',
			'title' => 'Loan Calculator',
		),
		'wordcount'  => array(
			'file'  => 'wordcount-widget.js',
			'path'  => '/text/word-counter',
			'title' => 'Word Counter',
		),
	);

	return $widgets;
}

/**
 * Register every bundled script once. Enqueued on demand by the shortcode.
 *
 * @return void
 */
function pwt_calculators_register_scripts() {
	$base = plugins_url( 'assets/embed/', __FILE__ );

	// Shared engine (required by every widget except the self-contained BMI one).
	wp_register_script(
		'pwt-calculators-engine',
		$base . 'pwt-widget-engine.js',
		array(),
		PWT_CALCULATORS_VERSION,
		true
	);

	// Auto-resizer for iframe embeds ([pwt_calculator tool="..."]).
	wp_register_script(
		'pwt-calculators-frame',
		$base . 'frame.js',
		array(),
		PWT_CALCULATORS_VERSION,
		true
	);

	foreach ( pwt_calculators_widgets() as $id => $widget ) {
		$deps = ( 'bmi' === $id ) ? array() : array( 'pwt-calculators-engine' );
		wp_register_script(
			'pwt-calculators-' . $id,
			$base . $widget['file'],
			$deps,
			PWT_CALCULATORS_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'pwt_calculators_register_scripts' );

/**
 * Plugin set-up: text domain and shortcode registration.
 *
 * @return void
 */
function pwt_calculators_setup() {
	load_plugin_textdomain(
		'practical-web-tools-calculators',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);

	add_shortcode( 'pwt_calculator', 'pwt_calculators_shortcode' );
}
add_action( 'init', 'pwt_calculators_setup' );

/**
 * Sanitize the width attribute to a sane pixel value.
 *
 * @param string $raw Raw width attribute value.
 * @return int Pixel width between 200 and 1920.
 */
function pwt_calculators_width( $raw ) {
	$width = (int) $raw;
	if ( $width < 200 ) {
		$width = 880;
	}
	if ( $width > 1920 ) {
		$width = 1920;
	}

	return $width;
}

/**
 * Build a human label from a calculator path (/finance/mortgage-calculator
 * becomes "Mortgage Calculator") for the iframe title and attribution link.
 *
 * @param string $path Validated calculator path.
 * @return string
 */
function pwt_calculators_path_title( $path ) {
	$segment = substr( $path, strrpos( $path, '/' ) + 1 );
	if ( '' === $segment ) {
		$segment = trim( $path, '/' );
	}

	return ucwords( str_replace( '-', ' ', $segment ) );
}

/**
 * Editor-only inline error. Visitors never see shortcode mistakes.
 *
 * @param string $message Plain-text message (escaped here).
 * @return string
 */
function pwt_calculators_editor_error( $message ) {
	if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
		return '';
	}

	return '<div class="pwt-calculator-error" style="margin:1em 0;padding:12px 16px;border:1px solid #c3c4c7;border-left:4px solid #d63638;background:#fff;box-shadow:0 1px 1px rgba(0,0,0,.04);font-size:13px;line-height:1.6;">'
		. '<strong>' . esc_html__( 'Practical Web Tools:', 'practical-web-tools-calculators' ) . '</strong> '
		. esc_html( $message )
		. '</div>';
}

/**
 * Render the [pwt_calculator] shortcode.
 *
 * Modes (widget wins when both are given):
 *  - widget="mortgage|bmi|calorie|duedate|percentage|compound|countdown|loan|wordcount"
 *    Renders the bundled, fully client-side widget. Optional: width, theme.
 *  - tool="/finance/mortgage-calculator" — any calculator path from
 *    https://practicalwebtools.com. Renders a lazy auto-sizing iframe plus an
 *    attribution link. Optional: width, theme ("dark" appends &theme=dark).
 *
 * @param array|string $atts Shortcode attributes.
 * @return string
 */
function pwt_calculators_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'widget' => '',
			'tool'   => '',
			'width'  => '880',
			'theme'  => 'light',
		),
		$atts,
		'pwt_calculator'
	);

	$widget = strtolower( trim( (string) $atts['widget'] ) );
	$tool   = strtolower( trim( (string) $atts['tool'] ) );
	$theme  = strtolower( trim( (string) $atts['theme'] ) );
	$width  = pwt_calculators_width( $atts['width'] );

	$widgets = pwt_calculators_widgets();

	// Mode 1: bundled widget script.
	if ( '' !== $widget ) {
		if ( ! isset( $widgets[ $widget ] ) ) {
			return pwt_calculators_editor_error(
				sprintf(
					/* translators: 1: invalid widget id, 2: list of valid ids */
					__( 'unknown widget "%1$s". Valid widgets: %2$s.', 'practical-web-tools-calculators' ),
					$widget,
					implode( ', ', array_keys( $widgets ) )
				)
			);
		}

		wp_enqueue_script( 'pwt-calculators-' . $widget );

		return sprintf(
			'<div id="pwt-%1$s-widget" class="pwt-calculator-widget" style="max-width:%2$dpx;"></div>',
			esc_attr( $widget ),
			$width
		);
	}

	// Mode 2: any practicalwebtools.com calculator as an iframe.
	if ( '' !== $tool ) {
		$path = '/' . ltrim( $tool, '/' );

		if ( ! preg_match( '#^/[a-z0-9/-]+$#', $path ) || false !== strpos( $path, '..' ) ) {
			return pwt_calculators_editor_error(
				sprintf(
					/* translators: %s: invalid tool path */
					__( 'invalid tool path "%s". It must be an absolute path such as /finance/mortgage-calculator.', 'practical-web-tools-calculators' ),
					$tool
				)
			);
		}

		$src = 'https://practicalwebtools.com' . $path . '?embed=1';
		if ( 'dark' === $theme ) {
			$src .= '&theme=dark';
		}

		$title = pwt_calculators_path_title( $path );

		wp_enqueue_script( 'pwt-calculators-frame' );

		return sprintf(
			'<div class="pwt-calculator-embed"><iframe src="%1$s" title="%2$s" loading="lazy" style="display:block;width:100%%;max-width:%3$dpx;height:720px;border:0;" data-pwt-embed></iframe><p class="pwt-calculator-attribution"><a href="%4$s" rel="noopener">%5$s by Practical Web Tools</a></p></div>',
			esc_url( $src ),
			esc_attr( $title ),
			$width,
			esc_url( 'https://practicalwebtools.com' . $path ),
			esc_html( $title )
		);
	}

	// No mode given.
	return pwt_calculators_editor_error(
		__( 'add a widget attribute (for example widget="mortgage") or a tool attribute (for example tool="/finance/mortgage-calculator").', 'practical-web-tools-calculators' )
	);
}

/**
 * Add a Settings (documentation) link on the Plugins page.
 *
 * @param array $links Existing action links.
 * @return array
 */
function pwt_calculators_action_links( $links ) {
	$custom = sprintf(
		'<a href="%1$s" target="_blank" rel="noopener">%2$s</a>',
		esc_url( 'https://practicalwebtools.com/embed' ),
		esc_html__( 'Settings', 'practical-web-tools-calculators' )
	);

	array_unshift( $links, $custom );

	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pwt_calculators_action_links' );
