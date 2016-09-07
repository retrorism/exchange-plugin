<?php
/**
 * PullQuote Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 08/03/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

/**
 * Pullquote pattern class.
 *
 * This class serves to build paragraph elements.
 *
 * @since 0.1.0
 **/
class PullQuote extends BaseQuote {
	/**
	 * Quotes array.
	 *
	 * @var array $quotes Two quote-strings, one for opening, one for closing.
	 */
	private $quotes;

	/**
	 * Overwrite initial output value for Pullquotes
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {
		$this->prepare_quotes();

		if ( ! empty( $this->input['pquote_text'] ) ) {
			$this->output_tag_open( 'aside' );
			$this->output .= $this->quotes['open'];
			$this->output .= $this->input['pquote_text'];
		}
		// Prepare caption.
		$this->set_quote_caption();
		if ( $this->has_caption ) {
			$this->output .= $this->caption->embed();
		}

		// Close element.
		$this->output .= $this->quotes['close'];
		$this->output_tag_close( 'aside' );
	}

	protected function prepare_quotes() {
		$open_path = get_stylesheet_directory() . '/assets/images/svg/T_quotes_Opening_WEB.svg';
		$open_icon = exchange_build_svg( $open_path );
		$close_path = get_stylesheet_directory() . '/assets/images/svg/T_quotes_Closing_WEB.svg';
		$close_icon = exchange_build_svg( $close_path );
		$this->quotes['close'] = $close_icon ? '<div class="pullquote__quote--close">' . $close_icon . '</div>' : '';
		$this->quotes['open'] = $open_icon ? '<div class="pullquote__quote--open">' . $open_icon .'</div>' : '';
	}
}
