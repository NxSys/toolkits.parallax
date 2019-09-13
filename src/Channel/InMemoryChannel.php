<?php

# https://github.com/klaussilveira/SimpleSHM
# ftok? ftok(__FILE__? pid file?, chr(128))
#sprintf('%u', ftok()) it gets signed sometimes...

namespace NxSys\Toolkits\Parallax\Channel;

use NxSys\Toolkits\Parallax;

class InMemoryChannel extends BaseChannel 
{

	// abstract public function _sendMessage(Message $oMsg);
	// abstract public function _recvMessage(): Message;

	
}
