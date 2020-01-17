<?php
namespace NxSys\Toolkits\Parallax\Job\AgentClient;

interface IAgentAware
{
	function setAgentInitializationRoutine(callable $hAgentCallback): void;

	function registerJob(array $aSettings);
 
	function signalStartup(): bool;
	function signalShutdown(): bool;
	function signalHalt(): bool;
	function signalContinue(): bool;

	// protected function signalAgent(string $sSignalName, $sSignalData): bool;

	function getStartUpChannels(): array; //Channel[]
	function getSystemChannels(): array; //Channel[]
}
