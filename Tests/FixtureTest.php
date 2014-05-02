<?php
namespace Gos\Component\Fixture\Tests;

use Gos\Component\Fixture\Fixture;

class FixtureTest extends \PHPUnit_Framework_TestCase
{
    protected $abstractFixture;

    protected function setUp()
    {
        $this->abstractFixture = $this->getMockBuilder('Doctrine\Common\DataFixtures\AbstractFixture')
            ->setMethods(array('getReference', 'load'))
            ->getMock()
        ;

        parent::setUp();
    }

    protected function tearDown()
    {
        $this->abstractFixture = null;
        parent::tearDown();
    }

    protected function loadFixture($filename)
    {
        $fixture = new Fixture($filename, $this->abstractFixture);
        $fixture->loadFromDirector(__DIR__.'/Fixtures');

        return $fixture;
    }

    public function testFileLoading()
    {
        $fixture = $this->loadFixture('TestFileLoad.yml');
        $result = $fixture->fetch();

        $this->assertCount(3, $result);

        $data = array();
        foreach($result as $node){
            foreach($node as $key => $value){
                $data[$key][] = $value;
            }
        }

        $this->assertEquals($data, array(
            'dummy' => array(
                'foo',
                'bar',
                'baz'
            )
        ));
    }

    public function testParseReference()
    {
        $this->abstractFixture->expects($this->once())
            ->method('getReference')
            ->with($this->equalTo('client'))
        ;

        $this->loadFixture('TestReference.yml')->fetch();
    }

    public function testCollection()
    {
        $fixture = $this->loadFixture('TestCollection.yml');
        $result = $fixture->fetch();

        $this->assertCount(3, $result);

        foreach($result as $node){
            $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $node['roles']);
            $this->assertCount(1, $node['roles']);
        }
    }
}