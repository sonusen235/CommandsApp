<?php
/**
 * li₃: the most RAD framework for PHP (http://li3.me)
 *
 * Copyright 2016, Union of RAD. All rights reserved. This source
 * code is distributed under the terms of the BSD 3-Clause License.
 * The full license text can be found in the LICENSE.txt file.
 */
namespace app\controllers;
use flash\extensions\storage\Flash;
use lithium\action\Controller;
use app\models\Session;
use app\models\User;
class PagesController extends Controller {

	public function view() {
		// $user = new User();
		$user = User::all();
		echo "<pre>"		;print_r('user');exit;
		// Session::start();
		Flash::write('This is a test flash message', array('class' => 'required_field'));
		$flashRead = Flash::read();
		print_r($flashRead);//exit;

		$sessionId = \lithium\storage\Session::read('session_id');
		// print_r($sessionId);exit;
		$options = [];
		$path = func_get_args();

		if (!$path || $path === ['home']) {
			$path = ['home'];
			$options['compiler'] = ['fallback' => true];
		}

		$options['template'] = join('/', $path);
		return $this->render($options);
	}
}

?>