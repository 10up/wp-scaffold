<?php
/**
 * This file contains hooks and functions that add Web Vital
 * tracking to the Scaffold.
 *
 * @package TenUpTheme
 */

namespace TenUpTheme\WebVitals;

function setup() {
	add_action( 'wp_footer', __NAMESPACE__ . '\init_web_vitals', 0 );

	// Choose one of the below strategies to add Web Vitals to your site.
	add_action( 'wp_footer', __NAMESPACE__ . '\strategy_send_to_analytics' );

	// add_action( 'wp_footer', __NAMESPACE__ . '\strategy_send_to_ua' );

	// add_action( 'wp_footer', __NAMESPACE__ . '\strategy_send_to_ga4' );
}

/**
 * Initialize the Web Vitals tracking.
 *
 * This function is hooked into the wp_footer.
 * The reason for this is so that the JS is seperate
 * from any bundle, ensuring the browser always parses
 * this logic.
 *
 * @return void
 */
function init_web_vitals() { ?>
	<!-- Web Vitals -->
	<script id="tenup-web-vital-tracking">
		(function() {
		var script = document.createElement('script');
		script.src = 'https://unpkg.com/web-vitals/dist/web-vitals.iife.js';
		script.onload = function() {
			webVitals.getCLS(console.log);
			webVitals.getFID(console.log);
			webVitals.getLCP(console.log);
		}
		document.head.appendChild(script);
		})();
	</script>
<?php }

/**
 * Send Web Vitals data to custom analytics endpoint.
 *
 * Uses sendBeacon falling back to fetch if necessary.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Navigator/sendBeacon
 * @return void
 */
function strategy_send_to_analytics() { ?>
	<!-- Send Data to Analytics Endpoint -->
	<script id="send-data-to-endpoint">
		function sendToAnalytics(metric) {
			const body = JSON.stringify(metric);
			(navigator.sendBeacon && navigator.sendBeacon('/analytics', body)) ||
			fetch('/analytics', {body, method: 'POST', keepalive: true});
		}
	</script>
<?php }

/**
 * Send the Web Vitals data to analytics.
 * There are two ways you can send data:
 *
 * 1. Straight to an analytics endpoint
 * 2. Through Google Analytics or Google Tag Manager
 *
 * This function is hooked into the wp_footer.
 *
 * @return void
 */
function strategy_send_to_ua() { ?>
	<!-- Send data to Universal Analytics -->
	<script id="send-data-to-ua">
		function sendToGoogleAnalytics({name, delta, id}) {
		// Assumes the global `gtag()` function exists, see:
		// https://developers.google.com/analytics/devguides/collection/gtagjs
			gtag('event', name, {
				event_category: 'Web Vitals',
				event_label: id,
				value: Math.round(name === 'CLS' ? delta * 1000 : delta),
				non_interaction: true,
			});
		}
	</script>
<?php }

/**
 * Send the Web Vitals data to analytics.
 * There are two ways you can send data:
 *
 * 1. Straight to an analytics endpoint
 * 2. Through Google Analytics or Google Tag Manager
 *
 * This function is hooked into the wp_footer.
 *
 * @return void
 */
function strategy_send_to_ga4() { ?>
	<!-- Send Web Vitals data to GA4 -->
	<script id="send-data-to-ga4">
		function sendToGoogleAnalytics({name, delta, value, id}) {
			// Assumes the global `gtag()` function exists, see:
			// https://developers.google.com/analytics/devguides/collection/ga4
			gtag('event', name, {
				value: delta,
				metric_id: id,
				metric_value: value,
				metric_delta: delta,
			});
		}
	</script>
<?php }
