<?php
namespace Gos\Component\Fixture\Tests;

use Gos\Component\Fixture\Fixture;

class FixtureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\Common\DataFixtures\AbstractFixture
     */
    protected $abstractFixture;

    /**
     * @var \Gos\Component\Fixture\Fixture
     */
    protected $fixture;

    protected function setUp()
    {
        $this->abstractFixture = $this->getMockBuilder('Doctrine\Common\DataFixtures\AbstractFixture')
            ->setMethods(array('getReference', 'load'))
            ->getMock()
        ;

        $this->fixture = new Fixture(__DIR__.'/Fixtures/A');

        parent::setUp();
    }

    protected function tearDown()
    {
        $this->abstractFixture = null;
        $this->fixture = null;
        parent::tearDown();
    }

    public function testAddDirectory()
    {
        $this->fixture->addDirectory(__DIR__.'/Fixtures/B');
        $this->fixture->load('TestFileLoadFromB.yml');

        $result = $this->fixture->fetch();

        $this->assertCount(3, $result);
    }

    public function testFileLoading()
    {
        $this->fixture->load('TestFileLoad.yml');
        $result = $this->fixture->fetch();

        $this->assertCount(3, $result);

        $data = array();
        foreach ($result as $node) {
            foreach ($node as $key => $value) {
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

        $this->fixture->load('TestReference.yml', $this->abstractFixture);
        $this->fixture->fetch();
    }

    /**
     * @expectedException \Exception
     */
    public function testParseReferenceException()
    {
        $this->fixture->load('TestReference.yml');
        $this->fixture->fetch();
    }

    public function testCollection()
    {
        $this->fixture->load('TestCollection.yml');
        $result = $this->fixture->fetch();

        $this->assertCount(3, $result);

        foreach ($result as $node) {
            $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $node['roles']);
            $this->assertCount(1, $node['roles']);
        }
    }
}
