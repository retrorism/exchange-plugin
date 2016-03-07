<?php

/*
 * Story Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 11/2/2016
 */

class Story {
	private $taglist = array();
	public $language;
	public $category;
	public $title;
	public $editorial_intro;


	function addTag($name, $link) {
		$this->taglist[] = array('name' => $name, 'link' => $link);
	}

	function getTagList() {
		return $this->taglist;
	}


	function getTagShortList() {
		$shortlist = array();

		foreach( $this->taglist as $tag ) {
			if (count($shortlist) > 2) {
				continue;
			}

			$shortlist[] = $tag;
		}
		return $shortlist;
	}

}
