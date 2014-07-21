<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/tools.php';

use Posibrain\TchatBotConfig;

class TchatBotConfigTest extends PHPUnit_Framework_TestCase
{

    private $defaultId;

    private $defaultLang;

    private $defaultCharset;

    public function __construct()
    {
        $this->defaultId = 'sammy';
        $this->defaultLang = 'fr';
        $this->defaultCharset = 'UTF-8';
    }
    
    public function testDefault()
    {
        $config = new TchatBotConfig();
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
    }
    
    public function testDefaultEmpty()
    {
        $config = new TchatBotConfig('', '');
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
    }
    
    public function testDefaultNull()
    {
        $config = new TchatBotConfig(null, null);
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
    }

    public function testSammyFr()
    {
        $config = new TchatBotConfig($this->defaultId, $this->defaultLang);
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
    }

    public function testUnknownNameFr()
    {
        $config = new TchatBotConfig('edouard', $this->defaultLang);
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
    }

    public function testUnknownNameAndLang()
    {
        $config = new TchatBotConfig('edouard', 'pl');
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
    }

    public function testSammyUnknownLang()
    {
        $config = new TchatBotConfig($this->defaultId, 'pl');
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
    }

    public function testCharset()
    {
        $config = new TchatBotConfig('', '', array('charset' => 'ASCII'));
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals('ASCII', $config->getCharset());
    }

    public function testBrainPath()
    {
        $config = new TchatBotConfig('', '', array('brainPath' => 'app/brains'));
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
        $this->assertEquals('app/brains/', $config->getBrainsFolder());
        $config->setBrainsFolder('app/brain/');
        $this->assertEquals('app/brain/', $config->getBrainsFolder());
    }
}