<?php
/**
 * Section Header Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 07/03/2016
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
 * Section Header pattern class.
 *
 * This class serves to build section headers.
 *
 * @since 0.1.0
 **/
class SectionHeader extends BasePattern {

	/**
	 * Overwrite initial output value for Section Headers.
	 *
	 * @since 0.1.0
	 * @access private
	 **/
	 protected function create_output() {
		$this->output_tag_open();
		$this->output .= '<div class="sectionheader-inner">';
		$text_colour = 'default';
		if ( $this->modifiers['type'] === 'taped_header' ) {
			$this->output .= '<svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" height="100%" viewBox="0 0 589.99999 177.99999"><path d="M1.116 161.497c.047-8.527.49-15.753.984-16.06.495-.305.91-1.316.922-2.246.012-.93.868-2.79 1.9-4.135 1.423-1.85 1.66-3.026.972-4.834-.5-1.313-.656-2.796-.348-3.294.308-.5 0-4.558-.682-9.02-1.2-7.844-1.17-8.167.896-9.76 2.573-1.982 4.24-6.114 4.24-10.51 0-1.78.484-3.72 1.076-4.312.944-.944 1.378-4.42 1.81-14.507.075-1.762.816-2.905 2.16-3.332 1.93-.612 2.022-1.252 1.613-11.317-.378-9.28-.18-11.086 1.51-13.878 1.495-2.47 1.702-3.66.895-5.168-.576-1.077-.78-3.3-.452-4.938.328-1.64.096-5.31-.514-8.16-.678-3.156-.76-5.808-.213-6.787.495-.883.53-2.31.078-3.172-2.19-4.175-4.05-12.38-3.035-13.395.7-.7 19.294-1.313 54.814-1.808C318.94 7.388 439.527 5.21 505.756 2.982c47.738-1.603 71.34-1.98 64.744-1.034-3.332 1.697 8.127-1.94 11.216.168.613.614 2.135 1.626 3.38 2.25 3.252 1.63 3.898 5.308 3.798 21.634-.138 22.52-1.184 30.617-4.866 37.64-2.99 5.702-3.07 6.17-1.872 10.652.934 3.495.976 5.516.162 7.948-.6 1.793-1.12 7.43-1.157 12.53-.036 5.097-.34 9.713-.676 10.256-1.116 1.805-1.517 16.044-.505 17.934.703 1.317.56 2.897-.493 5.417-.818 1.958-1.487 4.743-1.487 6.19 0 1.606-1.116 3.673-2.866 5.307l-2.865 2.677.775 9.376c.428 5.156.55 9.97.27 10.697-.28.727-1.927 1.536-3.662 1.796-1.734.26-76.053 2.09-165.153 4.068-167.007 3.705-254.058 5.747-320 7.504-20.9.557-48.23 1.01-60.734 1.01L1.032 177l.084-15.503z" fill="'. $this->modifiers['colour'] . '"/></svg>';
			$contrast = exchange_get_contrast_YIQ( $this->modifiers['colour'] );
		}

		if ( ! empty( $contrast ) ) {
			$text_colour = $contrast;
		}
		$this->output .= '<h4 class="sectionheader__text--' . $text_colour . '"><span>' . $this->input . '</span>';

		if ( current_theme_supports( 'decorated_section_headers' )
			&& $this->modifiers['type'] === 'decorated_header' 
			&& isset ( $this->modifiers['colour'] ) ) {
			$this->add_decoration();
		}

		$this->output .= '</h4></div>';
		$this->output_tag_close();
	}

	/**
	 * Overwrite initial output value for Section Headers.
	 *
	 * @since 0.2.0
	 * @access private
	 **/
	protected function add_decoration() {
		if ( empty( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['PATTERNS']['decoration_taxonomy'] ) ) {
			return;
		}
		$decoration_tax = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['PATTERNS']['decoration_taxonomy'];

		if ( empty( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['TAXONOMIES'][ $decoration_tax ] ) ) {
			return;
		}
		$tax_arr = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['TAXONOMIES'][ $decoration_tax ];
		$decoration_colour = exchange_hex_to_slug( $this->modifiers['colour'] );
		if ( empty( $decoration_colour ) ) {
			return;
		}
		foreach( $tax_arr as $tax ) {
			if ( empty( $tax['colour'] ) ) {
				continue;
			}
			if ( $tax['colour'] === $decoration_colour ) {
				$this->output .= '<div class="sectionheader__decoration sectionheader__decoration--' . $decoration_colour . '"></div>';
			}
		}
	}
}
