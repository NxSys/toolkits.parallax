<?php 

namespace NxSys\Toolkits\Parallax;

use ReflectionExtension;

class EnvironmentTest extends \Codeception\Test\Unit
{

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testPHPVersion()
    {
		$this->assertGreaterThanOrEqual(PHP_VERSION_ID, 702000);
    }

	public function testRequiredExtensionsPresent()
	{
		$aReqdExts=[
			'parallel',
			'SPL'
		];
		foreach ($aReqdExts as $key => $reqext)
		{
			if (in_array($reqext, get_loaded_extensions()))
			{
				unset($aReqdExts[$key]);
			}
		}
		$this->assertEmpty($aReqdExts, 'extentions are missing: '.implode(', ', $aReqdExts));
	}

	public function testMinParallelVersionPresent()
	{
		$ver=(new ReflectionExtension('parallel'))->getVersion();
		$this->assertLessThanOrEqual(version_compare($ver, '1.1.1'), 0);
	}
}