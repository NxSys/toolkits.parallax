<?php

namespace NxSys\Toolkits\Parallax\Channel;

use NxSys\Toolkits\Parallax\Channel\IException;
use OverflowException;

class ParallaxChannel_MessageSizeTooLargeException extends OverflowException implements IException
{}