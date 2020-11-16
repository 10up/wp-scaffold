<?php
/**
 * Template Name: Style Guide
 *
 * @package TenUpTheme
 */

namespace TenUpTheme\Utility;

use function TenUpTheme\Utility\adjust_brightness;
use function TenUpTheme\Utility\get_colors;

get_header();
?>

<div class="uikit__container">

	<h1 class="uikit__heading">
		<div class="uikit__block">
			<span><?php echo esc_html( get_the_title() ); ?></span>
		</div>
	</h1>

	<div class="uikit__content">

		<?php
			$colors = get_colors( '/assets/css/frontend/global/colors.css' );

		if ( ! empty( $colors ) ) :
			?>
		<section class="uikit__section" id="colors">
			<h2 class="heading">Primary Palette</h2>

			<div class="content">
				<ul class="uikit__colors">

				<?php foreach ( $colors as $color ) : ?>

					<li class="uikit__color" style="background: <?php echo esc_attr( $color ); ?>; border-color: <?php echo esc_attr( adjust_brightness( $color, -25 ) ); ?>">
						<p class="uikit__color--label uikit__text--small"><?php echo esc_html( $color ); ?></p>
					</li>

				<?php endforeach; ?>

				</ul>
			</div><!--/.content-->

		</section><!--/.uikit__section-->
		<?php endif; ?>

		<section class="uikit__section" id="headings">
			<h2 class="heading">Headings</h2>

			<div class="content">
				<h1>H1, Heading 1 {64px}</h1>
				<h2>H2, Heading 2 {48px}</h2>
				<h3>H3, Heading 3 {38px}</h3>
				<h4>H4, Heading 4 {30px}</h4>
				<h5>H5, Heading 5 {26px}</h5>
			</div><!--/.content-->

		</section><!--/.uikit__section-->

		<section class="uikit__section" id="body">
			<h2 class="heading">Body</h2>

			<div class="content">
				<p>
					22pt, Acta Book, line 36 ( 1.5rem ). Lorem ipsum dolor sit amet,
					consectetur adipiscing elit. Multa sunt dicta ab antiquis de contemnendis
					ac despiciendis rebus humanis; Hoc mihi cum tuo fratre convenit. Fortasse
					id optimum, sed ubi illud: Plus semper voluptatis? Haec quo modo conveniant,
					non sane intellego. Lorem ipsum dolor sit amet, consectetur adipiscing
					elit. Multa sunt dicta ab antiquis de contemnendis ac despiciendis rebus
					humanis; Hoc mihi cum tuo fratre convenit. Fortasse id optimum, sed ubi
					illud: Plus semper voluptatis? Haec quo modo conveniant, non sane intellego.
				</p>

				<p>
					This is an <a href="#!">inline link text</a> example and hover link example.
				</p>
			</div><!--/.content-->

		</section><!--/.uikit__section-->

		<section class="uikit__section" id="buttons">
			<h2 class="heading">Buttons</h2>

			<div class="content">
				<button type="button" class="button-primary">Button</button>
				<button type="button" class="button-secondary">Button</button>
				<button type="button" class="button-tertiary">Button</button>
			</div><!--/.content-->

		</section><!--/.uikit__section-->

		<section class="uikit__section" id="inputs">
			<h2 class="heading">Inputs</h2>

			<div class="content">

				<div class="uikit-mb-1">
					<label for="w1">Text Input</label>
					<input type="text" id="w1" name="" placeholder="Input placeholder text" />
				</div>

				<div class="uikit-mb-1">
					<label for="pwd">Password Input</label>
					<input type="password" id="pwd" name="" placeholder="password placeholder text" />
				</div>

				<div class="uikit-mb-1">
					<label for="email">Email Input</label>
					<input type="email" id="email" name="" placeholder="you@example.com" />
				</div>

				<div class="uikit-mb-1">
					<label for="w2">Label</label>
					<textarea id="w2" cols="10" rows="10"></textarea>
				</div>

				<div class="uikit-mb-1">
					<label for="volume">Range Input</label>
					<input type="range" id="start" name="" min="0" max="11" />
				</div>

				<div class="uikit-mb-1">
					<label for="date">Date Input</label>
					<input type="date" id="date" name="" value="" />
				</div>

				<div class="uikit-mb-1">
					<label for="num">Number Input</label>
					<input type="number" id="num" name="" value="" />
				</div>

				<div class="uikit-mb-1">
					<label for="w3">Label</label>
					<select id="w3">
						<option disabled selected>Select an Option</option>
						<option value="1">Option 1</option>
						<option value="2">Option 2</option>
						<option value="3">Option 3</option>
						<option value="4">Option 4</option>
						<option value="5">Option 5</option>
					</select>
				</div>

				<div class="uikit-mb-1">
					<fieldset>
						<legend>Checkbox Field Grouping</legend>
						<div>
							<input type="checkbox" id="w4-1">
							<label for="w4-1">Label</label>
						</div>
						<div>
							<input type="checkbox" id="w4-2">
							<label for="w4-2">Label</label>
						</div>
						<div>
							<input type="checkbox" id="w4-3">
							<label for="w4-3">Label</label>
						</div>
					</fieldset>
				</div>

				<div class="uikit-mb-1">
					<fieldset>
						<legend>Radio Button Field Grouping</legend>
						<div>
							<input type="radio" id="w5" name="group">
							<label for="w5">Label</label>
						</div>
						<div>
							<input type="radio" id="w6" name="group">
							<label for="w6">Label</label>
						</div>
						<div>
							<input type="radio" id="w7" name="group">
							<label for="w7">Label</label>
						</div>
					</fieldset>
				</div>

			</div><!--/.content-->

		</section><!--/.uikit__section-->

		<section class="uikit__section" id="lists">
			<h2 class="heading">Lists</h2>

			<div class="content">
				<ul>
					<li>Morbi natoque habitasse</li>
					<li>Magnis ullamcorper risus taciti
						<ul>
							<li>Justo metus turpis habitant nisl</li>
							<li>Platea primis semper</li>
						</ul>
					</li>
					<li>Nibh id natoque elementum</li>
				</ul>

				<ol>
					<li>Morbi natoque habitasse</li>
					<li>Magnis ullamcorper risus taciti
						<ol>
							<li>Justo metus turpis habitant nisl</li>
							<li>Platea primis semper</li>
						</ol>
					</li>
					<li>Nibh id natoque elementum</li>
				</ol>
			</div><!--/.content-->
		</section><!--/.uikit__section-->

		<section class="uikit__section" id="tables">
			<h2 class="heading">Tables</h2>

			<div class="content">
				<table>
					<caption>Egestas duis tincidunt cum</caption>
					<thead>
						<tr>
							<td scope="col">ID</td>
							<th scope="col">Item</th>
							<th scope="col">Purchase Date</th>
							<th scope="col">Price</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th scope="row" colspan="3">Sum</th>
							<td>$15.55</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<th scope="row">1</th>
							<td>Stick of gum</td>
							<td>02/13/15</td>
							<td>$0.19</td>
						</tr>
						<tr>
							<th scope="row">2</th>
							<td>Toothbrush</td>
							<td>11/03/14</td>
							<td>$2.37</td>
						</tr>
						<tr>
							<th scope="row">3</th>
							<td>Umbrella</td>
							<td>05/12/17</td>
							<td>$12.99</td>
						</tr>
					</tbody>
				</table>
			</div><!--/.content-->
		</section><!--/.uikit__section-->

	</div><!--/.uikit__content-->

</div><!--/.uikit__container-->

<?php get_footer(); ?>
