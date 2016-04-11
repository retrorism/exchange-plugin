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
	 * Undocumented function long description
	 *
	 * @param array $input Array containing names and statements.
	 * @return HTML string with names and statements.
	 **/
	public function build_interview( $input ) {
		return print_r( $input, true );
	}

}
