<?php
/**
 * Interview - QA style
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
class InterviewQA extends BaseInterview {

	/**
	 * Build interview, QA-style.
	 *
	 * Undocumented function long description
	 *
	 * @param array $input Array containing questions and answers
	 * @return HTML string with questions and answers
	 **/
	public function build_interview( $input ) {
		if ( is_array( $input ) && count( $input ) > 0 ) {
			$output = '';
			foreach ( $input as $qa ) {
				if ( ! empty( $qa['question'] ) && ! empty( $qa['answer'] ) ) {
					$modifiers_q = array( 'style' => 'question' );
					$modifiers_a = array( 'style' => 'answer' );
					$question = new Paragraph( $qa['question'], $this->element, $modifiers_q  );
					$answer = new Paragraph( $qa['answer'], $this->element, $modifiers_a );
					$output .= $question->embed();
					$output .= $answer->embed();
				}
			}
			return $output;
		}
		return __( 'There are no questions or answers to show here!',EXCHANGE_PLUGIN );
	}

}
