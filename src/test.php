<?php
require_once 'C:\dev\projects\onx\aether\applications.aether-acn\src\Common.php';



//----------------------------
class foo extends Threaded
{
	public $data=0;
}

class t extends Thread
{
	public $foo=null;
	public $name = "Unset";
	public function run()
	{
		for (;  ;)
		{ 
			$this->foo->data++;
			echo $this->name . ": " . (string) $this->foo->data . "\n";
			$this->wait();
		}
	}
}

class fiberTest extends Thread
{
	public $isHalted = false;
	public $name;
	public function run()
	{
		while (!$this->isHalted)
		{
			echo "$this->name\n";
			sleep(1);
		}
	}

	public function halt()
	{
		$this->isHalted = true;
	}
}
/**
$t=new t;
$t2 = new t;
$shared = new foo();
$t->foo = $shared;
$t->name = "Foo";
$t2->foo = $shared;
$t2->name = "Bar";
$t->start();
$c = 0;
while ($c<5)
{
	$t->notify();
	sleep(1);
	$c++;
}
$t2->start();
while (true)
{
	$t->notify();
	sleep(1);
	$t2->notify();
	sleep(1);
}
*/

$f = new fiberTest();
$f->name = "Foo";
$f2 = new fiberTest();
$f2->name = "Bar";
$f->start();
$f2->start();
sleep(5);
$f->halt();
sleep(5);
$f2->halt();
sleep(5);
echo ">>>END OF PROC<<<";