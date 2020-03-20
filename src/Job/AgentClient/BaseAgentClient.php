<?php
namespace NxSys\Toolkits\Parallax\Job\AgentClient;

use NxSys\Toolkits\Parallax\Channel\Message;


#pull channels out of the env

#attach to event manager

#expose eventloop

class BaseAgentClient implements IAgentAware
{
	static $hInstance;
	private function __contruct()
	{}

	/**
	 * Returns singleton of self
	 * @return BaseAgentClient
	 */
	static function getConfiguredInstance(): BaseAgentClient
	{
		if (self::$hInstance==null)
		{
			self::$hInstance=new self;
		}
		return self::$hInstance;
	}

	public function getEvent(): Message
	{
		# code...
		return new Message;
	}

	public function waitEvent(int $iTimeout=0, int $iBatchSize=1): array
	{
		# code...
		return [];
	}
}
