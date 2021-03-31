<?php
$methods = [
	'submitAmbassador' => [
		'params' => [
			[
				'name' => 'firstname',
				'source' => 'p',
				'pattern' => 'name',
				'required' => true
			],
			[
				'name' => 'secondname',
				'source' => 'p',
				'pattern' => 'name',
				'required' => true
			],
			[
				'name' => 'position',
				'source' => 'p',
				'default' => '',
				'required' => false
			],
			[
				'name' => 'phone',
				'source' => 'p',
				'pattern' => 'phone_ukr',
				'required' => true
			],
			//[
			//	'name' => 'email',
			//	'source' => 'p',
			//	'required' => false,
			//	'default' => ''
			//],
		]
	]
];