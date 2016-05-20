<?php
/**
 * Interview - conversation style
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 08/04/16
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

/**
 * Interview Conversation Class
 *
 *  Class description
 *
 * @since 0.1.0
 **/
class InterviewConversation extends BaseInterview {

	/**
	 * Build interview, Conversation-style.
	 *
	 * @return HTML string with names and statements.
	 **/
	public function build_interview() {
		if ( is_array( $this->input ) && count( $this->input ) ) {
			$output = '';
			foreach ( $this->input as $vs ) {
				if ( ! empty( $vs['voice'] ) && ! empty( $vs['statement'] ) ) {
					$voice = '<td class="' . $this->element . '__voice">' . $vs['voice'] . '</td>';
					$statement = '<td class="' . $this->element . '__statement">' . $vs['statement'] . '</td>';
					$row = '<tr>' . $voice . $statement . '</tr>';
					$output .= $row;
				}
			}
			$paragraph = new Paragraph( '<table>' . $output . '</table>', $this->element );
			return $paragraph->embed();
		} else {
			return __( 'There are no questions or answers to show here!',EXCHANGE_PLUGIN );
		}
	}

}
