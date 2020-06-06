<?php
namespace NxSys\Toolkits\Parallax\Job\AgentClient;

interface IAgentAware
{
	// function setAgentInitializationRoutine(callable $hAgentCallback): void; replaced by setup()?
	function setup();

	function registerJob(array $aSettings);

	function sendStartupSignal(): bool;
	function sendShutdownSignal(): bool;
	function sendHaltSignal(): bool;
	function sendContinueSignal(): bool;

	// protected function signalAgent(string $sSignalName, $sSignalData): bool;

	function getStartUpChannels(): array; //Channel[]
	function getSystemChannels(): array; //Channel[]
}
