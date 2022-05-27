<?php
/**
 * Server-side rendering of the Chart Tabs Gutenberg Block
 *
 * @package tenup-theme
 */

$tabs_item_count = count( $block->inner_blocks );

// this script gets registered by WordPress because of the definition in the block.json file
wp_enqueue_script( 'tenup-tabs-view-script' );

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'tabs' ] )

?>

<div <?php echo $wrapper_attributes; //phpcs:disable ?>>
	<div class="tab-control">
		<ul class="tab-list" role="tablist" aria-orientation="horizontal">
			<?php
			// iterate over the available inner blocks of the chart-tabs block
			for ( $index = 1; $index <= $tabs_item_count; $index++ ) :
				// access the tabs block at the current index
				$tab    = $block->inner_blocks->current();
				$label  = $tab->attributes['label'];
				$tab_id = sanitize_title( $label ) . $tab->parsed_block['attrs']['identifier'];
				?>
				<li class="tab-item" role="presentation">
					<a href="#<?php echo esc_attr( $tab_id ); ?>" role="tab" aria-controls="<?php echo esc_attr( $tab_id ); ?>" id="tab-<?php echo esc_attr( $tab_id ); ?>"><?php echo wp_kses_post( $label ); ?></a>
				</li>
				<?php
				// increase the index in the WP_Block_List class used to retrieve the current block
				$block->inner_blocks->next();
			endfor;
			// reset the index in the WP_Block_List class to the initial state
			$block->inner_blocks->rewind();
			?>
		</ul>
	</div>
	<div class="tab-group">
		<?php
			/*
			* the content is the html generated from innerBlocks
			* it is being created from the save method in JS or the render_callback
			* in php and is sanitized.
			*
			* Re sanitizing it through `wp_kses_post` causes
			* embed blocks to break and other core filters don't apply.
			* therefore no additional sanitization is done and it is being output as is
			*/
			echo $content; //phpcs:disable
		?>
	</div>
</div>
