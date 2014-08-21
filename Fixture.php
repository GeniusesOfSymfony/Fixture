<?php
namespace Gos\Component\Fixture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Gos\Component\Parser\Parser;
use Symfony\Component\Finder\Finder;

class Fixture
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $fixturesKey;

    /**
     * @var \Doctrine\Common\DataFixtures\AbstractFixture
     */
    protected $fixture;

    /**
     * @var string[]
     */
    protected $directories;

    /**
     * @param string[] $directories
     */
    public function __construct($directories)
    {
        $this->directories = (array) $directories;
    }

    /**
     * @param string               $fileName
     * @param AbstractFixture|null $fixture
     * @param string               $fixturesKey
     */
    public function load($fileName, AbstractFixture $fixture = null, $fixturesKey = 'database')
    {
        $this->fileName = $fileName;
        $this->fixturesKey = $fixturesKey;
        $this->fixture = $fixture;
    }

    /**
     * @param string $directory
     */
    public function addDirectory($directory)
    {
        $this->directories[] = $directory;
    }

    /**
     * @return array
     */
    public function fetch()
    {
        $buffer = [];

        $finder = new Finder();

        $files = $finder->files()
            ->in($this->directories)
            ->name($this->fileName)
        ;

        foreach ($files as $file) {
            $dataFixtures = Parser::yaml($file->getPathName());

            foreach ($dataFixtures[$this->fixturesKey] as $field => $values) {
                $i = 0;
                foreach ($values as $value) {
                    $this->parseReference($value);
                    $this->handleCollection($dataFixtures, $field, $value);
                    $buffer[$i][$field] = $value;
                    $i++;
                }
            }

        }

        return $buffer;
    }

    /**
     * @param $dataFixtures
     * @param $field
     * @param $value
     */
    protected function handleCollection($dataFixtures, $field, &$value)
    {
        if (isset($dataFixtures['collection'])) {
            if (in_array($field, $dataFixtures['collection']['scope'])) {
                $valuesCollection = new ArrayCollection();
                $valuesCollection->add($value);
                $value = $valuesCollection;
            }
        }
    }

    /**
     * @param $value
     */
    protected function parseReference(&$value)
    {
        if (is_array($value)) {
            return;
        }

        if (is_string($value)) {

            $split = str_split($value);

            if ($split[0] === '&') {
                if (null === $this->fixture) {
                    throw new \Exception('Fixture reference is triggered but no AbstractFixture loaded');
                }

                $value = $this->fixture->getReference(substr($value,1));
            }
        }
    }
}
