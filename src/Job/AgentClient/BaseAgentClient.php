<?php
namespace NxSys\Toolkits\Parallax\Job\AgentClient;

use NxSys\Toolkits\Parallax\Job;

use NxSys\Toolkits\Parallax\Channel\Message;

use NXS_PARALLAX_STUB;

#pull channels out of the env

#attach to event manager

#expose eventloop
/**
 * Helps to facilitate Agent's directives to the job
 */
abstract class BaseAgentClient implements IAgentAware
{
	static $hAgentInstance;
	static bool $bHasBeenConfigured = false;
	private function __contruct()
	{}

	/**
	 * Returns singleton of self
	 * @return BaseAgentClient
	 */
	static function getConfiguredInstance(): BaseAgentClient
	{
		self::getInstance();
		if (!self::$bHasBeenConfigured)
		{
			self::$hAgentInstance->setup();
			self::$bHasBeenConfigured = true;
		}
		return self::$hAgentInstance;
	}

	static function getInstance(): BaseAgentClient
	{
		// NXS_PARALLAX_STUB\_L(print_r(get_defined_constants(true)['user'], true));
		$sNamedAgent=sprintf('NxSys\Toolkits\Parallax\Job\AgentClient\%sAgentClient', PARALLAX_JOB_TYPE);

		if (self::$hAgentInstance==null)
		{
			self::$hAgentInstance=new $sNamedAgent;
		}
		return self::$hAgentInstance;
	}

	abstract public function setup();

	public function registerJob(array $aSettings)
	{

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
