<?php

# https://github.com/klaussilveira/SimpleSHM
# ftok? ftok(__FILE__? pid file?, chr(128))
#sprintf('%u', ftok()) it gets signed sometimes...

namespace NxSys\Toolkits\Parallax\Channel;

use InvalidArgumentException;
use NxSys\Toolkits\Parallax;

class InMemoryChannel extends BaseChannel implements IChannel
{
	public $iMsgBuff=1;

	// abstract public function _sendMessage(Message $oMsg);
	// abstract public function _recvMessage(): Message;

	public function setMessageBufferCount(int $iMessageCount=1)
	{
		if ($iMessageCount>1)
		{
			throw new ParallaxChannel_InvalidParameterException("Message count must be 1");
		}
		parent::setMessageBufferCount($iMessageCount);
	}	
}
