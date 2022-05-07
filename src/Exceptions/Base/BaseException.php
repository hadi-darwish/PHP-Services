<?php
	namespace RawadyMario\Exceptions\Base;

	use Exception;
	use RawadyMario\Languages\Classes\Translate;

	class BaseException extends Exception {

		public function __construct() {
			$this->message = Translate::TranslateString($this->message);
			parent::__construct();
		}
	}