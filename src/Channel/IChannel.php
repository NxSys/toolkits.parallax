<?php
/**
 * $BaseName$
 * $Id$
 *
 * DESCRIPTION
 *  A Core file for Parallax
 *
 * @link http://nxsys.org/spaces/onx
 * @link https://onx.zulipchat.com
 *
 * @package ONX
 * @subpackage Parallax
 * @license http://nxsys.org/spaces/onx/wiki/license
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2019 Nexus Systems, inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

/** @namespace Native Namespace */
namespace NxSys\Toolkits\Parallax\Channel;

use Serializable,
	Iterator,
	Countable;
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;

const MODE_HOST='host';

/**
 * Emulates IT_MODE_FIFO | IT_MODE_DELETE
 */
interface IChannel extends
	Serializable,
	// Iterator,
	Countable
	//, CoreEsc\SPL\ISplQueue
{
	const MODE_HOST=MODE_HOST;

	// function configure(string $sId, array $aConfig);

	/**
	 * Initializes the channel 
	 *
	 * Setup and notes parameters for the the memory segment
	 * Note: use a mode of 0760 to allow 'group' access to the segment
	 *
	 * @return bool if this operation succeded
	 * @throws conditon
	 **/
	function open();
	function close();

	function setId(string $sId=null);
	function getId(): string;

	function setMessageBufferCount(int $iCount = -1);
	function getMessageBufferCount(): int;

	function enqueue(Message $mValue);
	function dequeue();
	
	#figure out if this has to create the chan
	// function detectMode();
	// function getMode(): int;

	// function getEnv(): array;
	// function mergeEnv(array $aData);

	// function sendJob(string $sJobClassname);
	// function ctrlRunJob(string $sJob, array $aArguments);

}