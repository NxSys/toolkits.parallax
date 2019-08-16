<?php

namespace NxSys\Toolkits\Parallax;

use parallel\Channel as pChannel;

class Channel
{
	const Infinite=pChannel::Infinite;
	public $oTarget;

	public function __constructor(int $iCapacity=null)
	{
		if (!$iCapacity)
		{
			$this->oTarget=new pChannel;
		}
		else
		{
			$this->oTarget=new pChannel($iCapacity);
		}
	}

	public function __call(string $name, array $arguments)
	{
		return call_user_func_array([$this->oTarget, $name], $arguments);
	}

	public static function __callStatic( string $name, array $arguments)
	{
		return call_user_func_array([pChannel::class, $name], $arguments);
	}
}
