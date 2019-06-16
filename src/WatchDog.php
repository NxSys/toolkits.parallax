<?php
/**
 * $BaseName$
 * $Id$
 *
 * DESCRIPTION
 * @link http://nxsys.org/spaces/aether
 * @link https://onx.zulipchat.com
 *
 * @package Aether
 * @subpackage SDK
 * @license http://nxsys.org/spaces/aether/wiki/license
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2019 Nexus Systems, inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */
/** @namespace Native Namespace */
namespace NxSys\Toolkits\Parallax;

/** Local Project Dependencies **/
use NxSys\Toolkits\Parallax;

/** Framework Dependencies **/


/** Library Dependencies **/
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;


use Thread;
use InvalidArgumentException;

/**
 * Thread\Job health monitor
 * 
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 */
class WatchDog  
{
    /**
     * Register's a new thread with the watchdog
     *
     * This will keep (implied) references of threads and optional 
     * heartbeet function. Uses isRunning by
     *
     * @param Thread $hThread Thread to watch
     * @param string $sInspectMethod Method on thread to check, defaults to isRunning
     * @return void
     * @throws InvalidArgumentException
     **/
    public function registerThread(Thread $hThread, string $sInspectMethod = 'isRunning'): void
    {
        if(method_exists($hThread, $sInspectMethod))
        {   
            $this->aThreads[] = ['thread' => $hThread, 'inspectMethod' => $sInspectMethod];
        }
        else
        {
            throw new InvalidArgumentException(sprintf('%s is not an accessable method on %s.',
                                                       $sInspectMethod, get_class($hThread))
            );
            
        }
        return;
    }

    /**
     * Checks Thread health
     *
     * Inspects each registered thread and returns a set of failed threads.
     * Optionally will bail on first if $bNoSubsequentCheck is set
     *
     * @param bool $bNoSubsequentCheck Returns only the first failing thread
     * @return array
     * @throws conditon
     **/
    public function inspectThreads(bool $bNoSubsequentCheck = false): array
    {
        $errs=[];
        foreach ($this->aThreads as $idx => $aThreadInfo)
        {
            if(! ($aThreadInfo['thread']->{$aThreadInfo['inspectMethod']}()) ) //false from IM
            {
                $errs[]=$aThreadInfo['thread'];
                if($bNoSubsequentCheck)
                {
                    break;
                }
            }
        }
        return $errs;
    }
    

}
