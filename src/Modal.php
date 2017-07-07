<?php
/**
 * Created by abelair.
 * Date: 2017-07-05
 * Time: 3:06 PM
 */

namespace atk4\ui;


class Modal extends View
{
	public $defaultTemplate = 'modal.html';
	public $title = 'a title';
	public $ui = 'modal scrolling';
	public $uri = null;

	public function init()
	{
		parent::init();
		$this->template->trySet('title', $this->title);
	}

	public function setUri($uri)
	{
		$this->uri = $uri;
	}

	public function renderView() {
		if ($this->uri) {
			$this->template->trySet('uri', $this->uri);
		}
		parent::renderView();
	}

}