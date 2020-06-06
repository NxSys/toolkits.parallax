<?php

# https://github.com/klaussilveira/SimpleSHM
# ftok? ftok(__FILE__? pid file?, chr(128))
#sprintf('%u', ftok()) it gets signed sometimes...

namespace NxSys\Toolkits\Parallax\Channel;

use InvalidArgumentException;
use LengthException;
use NxSys\Toolkits\Parallax;
use NxSys\Toolkits\Parallax\Agent\ParallaxRuntimeException_ProcessAgent_LaunchFailure;
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
		if (strlen(serialize($oMsg))
			> $this->aConfig['SegmentSize']+self::DEFAULT_SEGMENT_DATA_OFFSET)
		{
			throw new ParallaxChannel_MessageSizeTooLargeException;
		}
		$ret=shmop_write($this->hSegment, serialize($oMsg), self::DEFAULT_SEGMENT_DATA_OFFSET);
		return $ret;
	}

	public function dequeue(bool $isReadOnce=false): Message
	{
		$oMsg=new Message;
		if (!$this->hSegment)
		{
			$this->open();
		}
		// if (self::DEFAULT_SEGMENT_DATA_OFFSET+$this->aConfig['SegmentSize']>) {
		// 	# code...
		// }

		// codecept_debug(sprintf('dequeue:%s///%s', self::DEFAULT_SEGMENT_DATA_OFFSET, $this->aConfig['SegmentSize']-1));
		$sData=shmop_read($this->hSegment, self::DEFAULT_SEGMENT_DATA_OFFSET, $this->aConfig['SegmentSize']);
		$oMsg=unserialize(trim($sData));
		if($isReadOnce)
		{
			$this->zero();
		}
		return $oMsg;
	}

	protected function zero()
	{
		shmop_write(
			$this->hSegment,
			str_pad('', $this->aConfig['SegmentSize'], "\0"),
			self::DEFAULT_SEGMENT_DATA_OFFSET);
		return;
	}

	/**
	 * Undocumented function
	 *
	 * Note: Subclasses that expect to communicate with third party applications should overide
	 * this function and device a method to set an appropreate shared memory key.
	 *
	 * @param integer $iSegmentSize Defaults to INMEM_DEFAULT_SEGMENT_SIZE
	 * @param string $sSegmentFlag Defaults to INMEM_DEFAULT_SEGMENT_FLAG
	 * @param integer $iSegmentMode Defaults to INMEM_DEFAULT_SEGMENT_MODE
	 * @return void
	 */
	public function open(... $aArgs)
	{
		# plx+shm:000148680000000054f849ab00000000648c43d8_I?k=0xABCD&s=65280&f=w&m=700
		$iKey=$this->convertId($this->getId());
		if(isset(self::$aUsedKeys[$iKey]))
		{
			throw new ParallaxChannel_InvalidParameterException("Reusing keys is not supported by this transport.");
		}
		// list($iKey, $sSegmentFlag, $iSegmentMode, $iSegmentSize) = $this->aConfig;

		$this->aConfig = array_combine(["SegmentSize", "SegmentFlag", "SegmentMode"], $aArgs);
		$this->aConfig["Key"] = $iKey;
		$this->prepareConfigOptions();
		// codecept_debug($this->aConfig);
		// codecept_debug(var_export($this->aConfig["SegmentSize"], true));
		$this->hSegment=shmop_open($this->aConfig["Key"],
								   $this->aConfig["SegmentFlag"],
								   $this->aConfig["SegmentMode"],
								   INMEM_DEFAULT_SEGMENT_DATA_OFFSET+$this->aConfig["SegmentSize"]
								);
	}


	protected function prepareConfigOptions(): bool
	{
		if ($this->aConfig["SegmentSize"] == null)
		{
			$this->aConfig["SegmentSize"] = INMEM_DEFAULT_SEGMENT_SIZE;
		}
		if (is_string($this->aConfig["SegmentSize"]))
		{
			$this->aConfig["SegmentSize"] = (int) $this->aConfig["SegmentSize"];
		}


		if ($this->aConfig["SegmentFlag"] == null)
		{
			$this->aConfig["SegmentFlag"] = INMEM_DEFAULT_SEGMENT_FLAG;
		}
		if ($this->aConfig["SegmentMode"] == null)
		{
			$this->aConfig["SegmentMode"] = INMEM_DEFAULT_SEGMENT_MODE;
		}
		if (is_string($this->aConfig["SegmentMode"]))
		{
			$this->aConfig["SegmentMode"] = (int) $this->aConfig["SegmentMode"];
		}


		if(!$this->aConfig || 4!=count($this->aConfig))
		{
			throw new LengthException('Config array must have a lengh of 4.');

		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	protected function startup(array $aCRN)
	{
		codecept_debug($aCRN);
		if ($this->sId && $this->getId()!=$aCRN['path'])
		{
			throw new ParallaxChannel_InvalidParameterException($aCRN['path'].' is invalid');
		}
		$this->setId($aCRN['path']);

		// parse_str($aCRN['query'], $this->aConfig);
		parse_str($aCRN['query'], $this->aConfig);
		codecept_debug($this->aConfig);

		//check query order?
		unset($this->aConfig['k']); //see
		$this->open(...array_values($this->aConfig));

		#new func?
		#@todo make sure order isn't bugged....
		//list($iKey, $sSegmentFlag, $iSegmentMode, $iSegmentSize) = $this->aConfig;

		// ::open...?
		//$this->hSegment=shmop_open($iKey, $sSegmentFlag, $iSegmentMode, $iSegmentSize);
		//debug_print_backtrace();
		return;
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
	public function close()
	{
		//never opened it!
		if (!$this->hSegment)
		{
			return;
		}
		self::$aUsedKeys[]=$this->convertId($this->getId()); //this key is retired!

		shmop_delete($this->hSegment);
		shmop_close($this->hSegment);
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
	 * @return string
	 * @throws conditon
	 **/
	public function convertId(string $sId)
	{
		codecept_debug(hexdec("0" . substr(hash('md5', $sId), 0, 15)));
		return hexdec("0" . substr(hash('md5', $sId), 0, 15));
	}

	public function setMessageBufferCount(int $iMessageCount=1)
	{
		if ($iMessageCount>1)
		{
			throw new ParallaxChannel_InvalidParameterException("Message count must be 1");
		}
		parent::setMessageBufferCount($iMessageCount);
	}

	public function __destruct()
	{
		$this->close();
	}
}
