<?php
	namespace app\components;
	use yii\base\Widget;
	class FirstWidget extends Widget {
		public function init () {
			parent::init();
			ob_start();
		}
		public function run () {
			$content = ob_get_clean();
			return "<h1>$content</h1>";
		}
	}
?>