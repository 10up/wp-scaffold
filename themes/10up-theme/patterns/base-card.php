<?php
/**
 * Title: Base Card
 * Slug: tenup-block-theme/base-card
 * Description: A card pattern with a featured image, title, date, and category.
 * Inserter: false
 *
 * @package TenUpTheme
 */

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"0"},"border":{"radius":"8px","width":"1px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch","flexWrap":"nowrap"}} -->
<div class="wp-block-group alignwide" style="border-width:1px;border-radius:8px">

	<!-- wp:post-featured-image {"aspectRatio":"16/9","width":"100%","height":"","style":{"border":{"radius":{"topRight":"8px","bottomRight":"0px","topLeft":"8px","bottomLeft":"0px"}}},"displayFallback":true} /-->

	<!-- wp:group {"align":"wide","className":"is-style-default","style":{"spacing":{"padding":{"top":"var(--wp--preset--spacing--24)","right":"var(--wp--preset--spacing--24)","bottom":"var(--wp--preset--spacing--24)","left":"var(--wp--preset--spacing--24)"},"blockGap":"var:preset|spacing|8"},"layout":{"selfStretch":"fit"},"border":{"width":"0px","style":"none","radius":{"topLeft":"0px","topRight":"0px","bottomLeft":"8px","bottomRight":"8px"}}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"space-between"}} -->
	<div class="wp-block-group alignwide is-style-default" style="border-style:none;border-width:0px;border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-left-radius:8px;border-bottom-right-radius:8px;padding-top:var(--wp--preset--spacing--24);padding-right:var(--wp--preset--spacing--24);padding-bottom:var(--wp--preset--spacing--24);padding-left:var(--wp--preset--spacing--24)">

		<!-- wp:post-title {"isLink":true,"align":"wide","style":{"spacing":{"margin":{"top":"0","right":"0","bottom":"0","left":"0"}}},"fontSize":"heading-4"} /-->

		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|8"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group">

			<!-- wp:post-date {"fontSize":"minus-1"} /-->
			<!-- wp:post-terms {"term":"category","fontSize":"minus-1"} /-->

		</div>
		<!-- /wp:group -->

	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->

