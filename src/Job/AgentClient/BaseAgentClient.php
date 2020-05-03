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

	public function registerJob(array $aSettings)
	{

	}


	public function setAgentInitializationRoutine(callable $hAgentCallback): void
	{

	}

	protected function getAgentInitializationRoutine(): callable
	{
		# code...
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

	public function getStartUpChannels(): array //Channel[]
	{

	}
	public function getSystemChannels(): array //Channel[]
	{
		
	}


	//signals

	protected function sendSignal(Type $var = null)
	{
		# code...
	}

	public function sendStartupSignal(): bool
	{
		# code...
	}

	public function sendShutdownSignal(): bool
	{
			
	}

	public function sendHaltSignal(): bool
	{
		# code...
	}

	public function sendContinueSignal(): bool
	{
		# code...
	}
}
