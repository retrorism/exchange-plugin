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
		return print_r( $this->input, true );
	}

}
