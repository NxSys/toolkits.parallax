<?php
	require_once 'vendor/autoload.php';
	use NxSys\Toolkits\Parallax,
		NxSys\Toolkits\Parallax\Agent;

function codecept_debug($x){var_dump($x);}

		$oAgent=new Agent\ProcessAgent;

		$aParams=[
			'PARALLAX_JOB_LOADERPATH' => 'vendor/autoload.php',
			'val' => 1
		];
		$oAgent->run('NxSys\Toolkits\Parallax\SampleJob', $aParams);
