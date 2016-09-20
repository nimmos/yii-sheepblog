<?php
	namespace app\controllers;
	use yii\web\Controller;
	class ExampleController extends Controller {
		public $defaultAction = "hello-world";
		public function actions () {
			return [
				'greeting' => 'app\components\GreetingAction',
			];
		}
		public function actionIndex () {
			$message = "index action of the ExampleController";
			return $this->render("example", [
				'message' => $message
				]);
		}
		public function actionHelloWorld () {
			return "Hello world!";
		}
		public function actionOpenGoogle () {
			return $this->redirect('http://google.com');
		}
		public function actionTestParams ($first, $second) {
			return "$first is testing $second";
		}
	}
?>