<?php
namespace Qazana\Extensions\Tags;

use Qazana\Core\DynamicTags\Tag;
use Qazana\Extensions\Global_Dynamic_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Author_Name extends Tag {

	public function get_name() {
		return 'author-name';
	}

	public function get_title() {
		return __( 'Author Name', 'qazana' );
	}

	public function get_group() {
		return Global_Dynamic_Tags::AUTHOR_GROUP;
	}

	public function get_categories() {
		return [ Global_Dynamic_Tags::TEXT_CATEGORY ];
	}

	public function render() {
		echo wp_kses_post( get_the_author() );
	}
}