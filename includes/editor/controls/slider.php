<?php
namespace Qazana;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Qazana slider control.
 *
 * A base control for creating slider control. Displays a draggable range slider.
 * The slider control can optionally have a number of unit types (`size_units`)
 * for the user to choose from. The control also accepts a range argument that
 * allows you to set the `min`, `max` and `step` values per unit type.
 *
 * @since 1.0.0
 */
class Control_Slider extends Control_Base_Units {

	/**
	 * Get slider control type.
	 *
	 * Retrieve the control type, in this case `slider`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'slider';
	}

	/**
	 * Get slider control default values.
	 *
	 * Retrieve the default value of the slider control. Used to return the default
	 * values while initializing the slider control.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Control default value.
	 */
	public function get_default_value() {
		return array_merge(
			parent::get_default_value(), [
				'size' => '',
			]
		);
	}

	/**
	 * Get slider control default settings.
	 *
	 * Retrieve the default settings of the slider control. Used to return the
	 * default settings while initializing the slider control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return array_merge(
			parent::get_default_settings(), [
				'label_block' => true,
			]
		);
	}

	/**
	 * Render slider control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="qazana-control-field">
			<label for="<?php echo $control_uid; ?>" class="qazana-control-title">{{{ data.label }}}</label>
			<?php $this->print_units_template(); ?>
			<div class="qazana-control-input-wrapper qazana-clearfix">
				<div class="qazana-slider"></div>
				<div class="qazana-slider-input">
					<input id="<?php echo $control_uid; ?>" type="number" min="{{ data.min }}" max="{{ data.max }}" step="{{ data.step }}" data-setting="size" />
				</div>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="qazana-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
