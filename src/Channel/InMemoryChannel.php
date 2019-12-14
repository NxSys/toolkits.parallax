<?php

# https://github.com/klaussilveira/SimpleSHM
# ftok? ftok(__FILE__? pid file?, chr(128))
#sprintf('%u', ftok()) it gets signed sometimes...

namespace NxSys\Toolkits\Parallax\Channel;

use InvalidArgumentException;
use LengthException;
use NxSys\Toolkits\Parallax;
use ParallaxChannel_InvalidParameterException;

const INMEM_DEFAULT_SEGMENT_DATA_OFFSET=256;
const INMEM_DEFAULT_SEGMENT_SIZE=(INMEM_DEFAULT_SEGMENT_DATA_OFFSET-1)+65280;
const INMEM_DEFAULT_SEGMENT_FLAG='w';
const INMEM_DEFAULT_SEGMENT_MODE='0700';

/**
 * 
 * @todo start storing\using data lengths
 */
class InMemoryChannel extends BaseChannel implements IChannel
{
	const DEFAULT_SEGMENT_DATA_OFFSET=INMEM_DEFAULT_SEGMENT_DATA_OFFSET;
	const DEFAULT_SEGMENT_SIZE=INMEM_DEFAULT_SEGMENT_SIZE;
	const DEFAULT_SEGMENT_FLAG=INMEM_DEFAULT_SEGMENT_FLAG;
	const DEFAULT_SEGMENT_MODE=INMEM_DEFAULT_SEGMENT_MODE;
	
	/** @var resource $hSegment Handle to Memory Segment */
	protected $hSegment = null;

	public $aConfig = [];

	/** @var string $sDSN Re\Initialization Parameters */
	public $sDSN = null;

	public $iMsgBuff=1;

	static $aUsedKeys=[];

	public function enqueue(Message $oMsg)
	{
		if (!$this->hSegment)
		{
			$this->open();
		}
		$this->zero();
		shmop_write($this->hSegment, serialize($oMsg),self::DEFAULT_SEGMENT_DATA_OFFSET);
		return;
	}

	public function dequeue(bool $isReadOnce=false): Message
	{
		$oMsg=new Message;
		if (!$this->hSegment)
		{
			$this->open();
		}
		$sData=shmop_read($this->hSegment, self::DEFAULT_SEGMENT_DATA_OFFSET, self::DEFAULT_SEGMENT_SIZE);
		$oMsg=unserialize(trim($sData));
		if($isReadOnce)
		{
			$this->zero();
		}
		return $oMsg;
	}

	protected function zero()
	{
		shmop_write($this->hSegment,
			 str_pad('', self::DEFAULT_SEGMENT_SIZE-DEFAULT_SEGMENT_DATA_OFFSET+1, "\0"),
			 self::DEFAULT_SEGMENT_DATA_OFFSET);	
		return;	
	}

	/**
	 * @inheritdoc
	 */
	public function open($iSegmentSize=INMEM_DEFAULT_SEGMENT_SIZE, 
		$sSegmentFlag=INMEM_DEFAULT_SEGMENT_FLAG, $iSegmentMode=INMEM_DEFAULT_SEGMENT_MODE)
	{
		$iKey=$this->convertId($this->getId());
		if(isset(self::$aUsedKeys[$iKey]))
		{
			throw new ParallaxChannel_InvalidParameterException("Reusing keys is not supported by this transport.");
		}
		$this->aConfig=[$iKey, $sSegmentFlag, $iSegmentMode, $iSegmentSize];
	}

	protected function startup()
	{
		if(!$this->aConfig || 4!=count($this->aConfig))
		{
			throw new LengthException('Config array must have a lengh of 4.');
			
		}
		list($iKey, $sSegmentFlag, $iSegmentMode, $iSegmentSize) = $this->aConfig;
		$this->hSegment=shmop_open($iKey, $sSegmentFlag, $iSegmentMode, $iSegmentSize);
		debug_print_backtrace();
	}

	protected function shutdown()
	{
		if (!$this->hSegment)
		{
			return;
		}
		shmop_close($this->hSegment);
		unset($this->hSegment);	
		//fyi shutdown will not delete the segment
	}
	/**
	 *  @inheritdoc
	 */
	publicM function close()
	{
		//never opened it!
		if (!$this->hSegment)
		{
			return;
		}
		self::$aUsedKeys[]=$this->convertId($this->getId()); //this key is retired!
		shmop_close($this->hSegment);
		shmop_delete($this->hSegment);
		return;
	}
	public function count()
	{
		return 1;
	}

	/**
	 * Converts an compatible shmop key
	 *
	 * Hashes a value passed and takes the first 8 hex digits of it
	 *
	 * @param string $sId Id string
	 * @return int
	 * @throws conditon
	 **/
	public function convertId(string $sId)
	{
		return substr(hash('md5', $sId), 0, 16);
	}

	public function setMessageBufferCount(int $iMessageCount=1)
	{
		if ($iMessageCount>1)
		{
			throw new ParallaxChannel_InvalidParameterException("Message count must be 1");
		}
		parent::setMessageBufferCount($iMessageCount);
	}	

	public function __descruct()
	{
		$this->close();
	}
}
