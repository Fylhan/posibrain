<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/tools.php';

use Posibrain\TchatBotIdentity;
use Posibrain\OutputDecorationEnum;

class TchatBotIdentityTest extends PHPUnit_Framework_TestCase
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

    public function testEmptyParam()
    {
        $config = new TchatBotIdentity();
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
        
        $config = new TchatBotIdentity('', '');
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
        
        $config = new TchatBotIdentity(null, null);
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
    }

    public function testKnownBot()
    {
        $config = new TchatBotIdentity($this->defaultId, $this->defaultLang);
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
        $this->assertEquals('R. Sammy', $config->getName());
        $this->assertEquals('Sammy', $config->getPseudo());
        $this->assertEquals('Fylhan', $config->getConceptorName());
        $this->assertEquals('', $config->getAvatar());
        $this->assertEquals('2013-07-11', $config->getBirthday()
            ->format('Y-m-d'));
        $this->assertEquals('Europe/Paris', $config->getTimezone()->getName());
        $this->assertEquals(OutputDecorationEnum::Html, $config->getOutputDecoration());
        $this->assertEmpty($config->getTriggers());
        $this->assertEquals(array('Instinct'), $config->getPositrons());
    }

    public function testKnownBotWithParam()
    {
        $config = new TchatBotIdentity($this->defaultId, $this->defaultLang, array(
            'charset' => 'ASCII',
            'name' => 'R. Samsammy',
            'pseudo' => 'Big Sam',
            'conceptorName' => 'Mother Earth',
            'birthday' => '2011-10-11',
            'timezone' => 'Europe/Berlin',
            'outputDecoration' => OutputDecorationEnum::Text,
            'triggers' => array('BN', 'Fylhan'),
            'positrons' => array('Haddock', 'Captain'),
        ))
        ;
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals('ASCII', $config->getCharset());
        $this->assertEquals('R. Samsammy', $config->getName());
        $this->assertEquals('Big Sam', $config->getPseudo());
        $this->assertEquals('Mother Earth', $config->getConceptorName());
        $this->assertEquals('', $config->getAvatar());
        $this->assertEquals('2011-10-11', $config->getBirthday()
            ->format('Y-m-d'));
        $this->assertEquals('Europe/Berlin', $config->getTimezone()->getName());
        $this->assertEquals(OutputDecorationEnum::Text, $config->getOutputDecoration());
        $this->assertEquals(array('BN', 'Fylhan'), $config->getTriggers());
        $this->assertEquals(array('Haddock', 'Captain'), $config->getPositrons());
    }

    public function testUnknownBot()
    {
        $config = new TchatBotIdentity('edouard', 'pl');
        $this->assertEquals('edouard', $config->getId());
        $this->assertEquals('pl', $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
        $this->assertEquals('', $config->getPseudo());
        $this->assertEquals('', $config->getConceptorName());
    }

    public function testUnknownBotWithParam()
    {
        $config = new TchatBotIdentity('edouard', 'pl', array(
            'charset' => 'ASCII',
            'pseudo' => 'Big Ed',
            'conceptorName' => 'Mother Earth'
        ));
        $this->assertEquals('edouard', $config->getId());
        $this->assertEquals('pl', $config->getLang());
        $this->assertEquals('ASCII', $config->getCharset());
        $this->assertEquals('Big Ed', $config->getPseudo());
        $this->assertEquals('Mother Earth', $config->getConceptorName());
    }

    public function testBrainPath()
    {
        $config = new TchatBotIdentity('', '', array(
            'instinctPath' => 'app/brains'
        ));
        $this->assertEquals($this->defaultId, $config->getId());
        $this->assertEquals($this->defaultLang, $config->getLang());
        $this->assertEquals($this->defaultCharset, $config->getCharset());
        $this->assertEquals('app/brains/', $config->getInstinctPath());
        $config->setInstinctPath('app/brain/');
        $this->assertEquals('app/brain/', $config->getInstinctPath());
    }
}