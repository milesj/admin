<?php

class AdminController extends AdminAppController {

	public function index() {

	}

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow();
	}

}