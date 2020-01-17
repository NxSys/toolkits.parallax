<?php
namespace NxSys\Toolkits\Parallax\Job\AgentClient;

use NxSys\Toolkits\Parallax\Channel\Message;

class BaseAgentClient implements IAgentAware
{
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
