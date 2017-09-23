<?php
namespace Qazana\Core\Settings\Base;

use Qazana\Controls_Stack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Model extends Controls_Stack {

	abstract public function get_css_wrapper_selector();

	abstract public function get_panel_page_settings();
}
