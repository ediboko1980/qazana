<?php
namespace Qazana;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Qazana star rating widget.
 *
 * Qazana widget that displays star rating.
 *
 * @since 2.3.0
 */
class Widget_Star_Rating extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve star rating widget name.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'star-rating';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve star rating widget title.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Star Rating', 'qazana' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve star rating widget icon.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-rating';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'star', 'rating', 'rate', 'review' ];
	}

	/**
	 * Register star rating widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.3.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_rating',
			[
				'label' => __( 'Rating', 'qazana' ),
			]
		);

		$this->add_control(
			'rating_scale',
			[
				'label' => __( 'Rating Scale', 'qazana' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'5' => '0-5',
					'10' => '0-10',
				],
				'default' => '5',
			]
		);

		$this->add_control(
			'rating',
			[
				'label' => __( 'Rating', 'qazana' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10,
				'step' => 0.1,
				'default' => 5,
			]
		);

		$this->add_control(
			'star_style',
			[
				'label' => __( 'Icon', 'qazana' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
				//	'star_fontawesome' => 'Font Awesome',
					'star_unicode' => 'Unicode',
				],
				'default' => 'star_unicode',
				'render_type' => 'template',
				'prefix_class' => 'qazana--star-style-',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'unmarked_star_style',
			[
				'label' => __( 'Unmarked Style', 'qazana' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'solid' => [
						'title' => __( 'Solid', 'qazana' ),
						'icon' => 'fa fa-star',
					],
					'outline' => [
						'title' => __( 'Outline', 'qazana' ),
						'icon' => 'fa fa-star-o',
					],
				],
				'default' => 'solid',
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'qazana' ),
				'type' => Controls_Manager::TEXT,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'qazana' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'qazana' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'qazana' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'qazana' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'qazana' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'prefix_class' => 'qazana-star-rating--align-',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'qazana' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'qazana' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .qazana-star-rating__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .qazana-star-rating__title',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_responsive_control(
			'title_gap',
			[
				'label' => __( 'Gap', 'qazana' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}:not(.qazana-star-rating--align-justify) .qazana-star-rating__title' => 'margin-right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}}:not(.qazana-star-rating--align-justify) .qazana-star-rating__title' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_stars_style',
			[
				'label' => __( 'Stars', 'qazana' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'qazana' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .qazana-star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label' => __( 'Spacing', 'qazana' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .qazana-star-rating i:not(:last-of-type)' => 'margin-right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .qazana-star-rating i:not(:last-of-type)' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'stars_color',
			[
				'label' => __( 'Color', 'qazana' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .qazana-star-rating i:before' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'stars_unmarked_color',
			[
				'label' => __( 'Unmarked Color', 'qazana' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .qazana-star-rating i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function get_rating() {
		$settings = $this->get_settings_for_display();
		$rating_scale = (int) $settings['rating_scale'];
		$rating = (float) $settings['rating'] > $rating_scale ? $rating_scale : $settings['rating'];

		return [ $rating, $rating_scale ];
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function render_stars( $icon ) {
		$rating_data = $this->get_rating();
		$rating = $rating_data[0];
		$floored_rating = (int) $rating;
		$stars_html = '';

		for ( $stars = 1; $stars <= $rating_data[1]; $stars++ ) {
			if ( $stars <= $floored_rating ) {
				$stars_html .= '<i class="qazana-star-full">' . $icon . '</i>';
			} elseif ( $floored_rating + 1 === $stars && $rating !== $floored_rating ) {
				$stars_html .= '<i class="qazana-star-' . ( $rating - $floored_rating ) * 10 . '">' . $icon . '</i>';
			} else {
				$stars_html .= '<i class="qazana-star-empty">' . $icon . '</i>';
			}
		}

		return $stars_html;
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$rating_data = $this->get_rating();
		$textual_rating = $rating_data[0] . '/' . $rating_data[1];
		$icon = '&#61445;';

		if ( 'star_fontawesome' === $settings['star_style'] ) {
			if ( 'outline' === $settings['unmarked_star_style'] ) {
				$icon = '&#61446;';
			}
		} elseif ( 'star_unicode' === $settings['star_style'] ) {
			$icon = '&#9733;';

			if ( 'outline' === $settings['unmarked_star_style'] ) {
				$icon = '&#9734;';
			}
		}

		$this->add_render_attribute( 'icon_wrapper', [
			'class' => 'qazana-star-rating',
			'title' => $textual_rating,
			'itemtype' => 'http://schema.org/Rating',
			'itemscope' => '',
			'itemprop' => 'reviewRating',
		] );

		$schema_rating = '<span itemprop="ratingValue" class="qazana-screen-only">' . $textual_rating . '</span>';
		$stars_element = '<div ' . $this->get_render_attribute_string( 'icon_wrapper' ) . '>' . $this->render_stars( $icon ) . ' ' . $schema_rating . '</div>';
		?>

		<div class="qazana-star-rating__wrapper">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<div class="qazana-star-rating__title"><?php echo $settings['title']; ?></div>
			<?php endif; ?>
			<?php echo $stars_element; ?>
		</div>
		<?php
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<#
			var getRating = function() {
				var ratingScale = parseInt( settings.rating_scale, 10 ),
					rating = settings.rating > ratingScale ? ratingScale : settings.rating;

				return [ rating, ratingScale ];
			},
			ratingData = getRating(),
			rating = ratingData[0],
			textualRating = ratingData[0] + '/' + ratingData[1],
			renderStars = function( icon ) {
				var starsHtml = '',
					flooredRating = Math.floor( rating );

				for ( var stars = 1; stars <= ratingData[1]; stars++ ) {
					if ( stars <= flooredRating  ) {
						starsHtml += '<i class="qazana-star-full">' + icon + '</i>';
					} else if ( flooredRating + 1 === stars && rating !== flooredRating ) {
						starsHtml += '<i class="qazana-star-' + ( rating - flooredRating ).toFixed( 1 ) * 10 + '">' + icon + '</i>';
					} else {
						starsHtml += '<i class="qazana-star-empty">' + icon + '</i>';
					}
				}

				return starsHtml;
			},
			icon = '&#61445;';

			if ( 'star_fontawesome' === settings.star_style ) {
				if ( 'outline' === settings.unmarked_star_style ) {
					icon = '&#61446;';
				}
			} else if ( 'star_unicode' === settings.star_style ) {
				icon = '&#9733;';

				if ( 'outline' === settings.unmarked_star_style ) {
					icon = '&#9734;';
				}
			}

			view.addRenderAttribute( 'iconWrapper', 'class', 'qazana-star-rating' );
			view.addRenderAttribute( 'iconWrapper', 'itemtype', 'http://schema.org/Rating' );
			view.addRenderAttribute( 'iconWrapper', 'title', textualRating );
			view.addRenderAttribute( 'iconWrapper', 'itemscope', '' );
			view.addRenderAttribute( 'iconWrapper', 'itemprop', 'reviewRating' );

			var stars = renderStars( icon );
		#>

		<div class="qazana-star-rating__wrapper">
			<# if ( ! _.isEmpty( settings.title ) ) { #>
				<div class="qazana-star-rating__title">{{ settings.title }}</div>
			<# } #>
			<div {{{ view.getRenderAttributeString( 'iconWrapper' ) }}} >
				{{{ stars }}}
				<span itemprop="ratingValue" class="qazana-screen-only">{{ textualRating }}</span>
			</div>
		</div>

		<?php
	}
}
