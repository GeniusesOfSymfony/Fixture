#Gos Fixtures Component#

[![Build Status](https://travis-ci.org/GeniusesOfSymfony/Fixture.svg?branch=master)](https://travis-ci.org/GeniusesOfSymfony/Fixture) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GeniusesOfSymfony/Fixture/badges/quality-score.png?s=cd81b8904ea4e7fed18dca9f818a70c95cd5609f)](https://scrutinizer-ci.com/g/GeniusesOfSymfony/Fixture/) [![Code Coverage](https://scrutinizer-ci.com/g/GeniusesOfSymfony/Fixture/badges/coverage.png?s=12196f0bbfc1df793c50a3ece8b7f8487df774b0)](https://scrutinizer-ci.com/g/GeniusesOfSymfony/Fixture/) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/be933d87-787f-4a41-949d-65e01eb5b5f7/mini.png)](https://insight.sensiolabs.com/projects/be933d87-787f-4a41-949d-65e01eb5b5f7)

**This project is currently in developpement, so please take care.**

Create fixture can be painful mainly when you write them for little entity.You would create them faster as possible and with specific values.

Here an example from doctrine-data-fixture.

```php
namespace MyDataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserRoleData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $adminRole = new Role();
        $adminRole->setName('admin');

        $anonymousRole = new Role;
        $anonymousRole->setName('anonymous');

        $manager->persist($adminRole);
        $manager->persist($anonymousRole);
        $manager->flush();

        // store reference to admin role for User relation to Role
        $this->addReference('admin-role', $adminRole);
    }
}
```

If you hate store data in php array and dont want take time to create a dedicated component, it's for you. Store their in YAML file and fetch easly your data !

How to use
----------

```php
namespace MyDataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserRoleData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
    	//Instanciate the fixture component with the path of folder who contains our YAML data fixture
        //File are retrieve via Symfony Finder Component, so your do path/*/*/folder and all think who is
        //interpreted by the finder
        $fixture = new Fixture('path/to/my/fixture/folder', $this);

        //If you want split / order your fixture you can also add dynamically some folder
        $this->fixture->addDirectory('path/to/my/fixture/another_folder');

        //Now load your specific file
        $fixture->load('myDataFixture.yml');

        //And now you can fetch your data
        foreach($this->fixture->fetch() as $data){
        	//attach to your entity with PropertyAccess or by the hand.
        }

    }
}
```
Here an example of YAML data :
```yaml
database:
    name:
        - 'fr'
        - 'en'
        - 'it'
        - 'es'
    locale:
        - 'fr_FR'
        - 'en_US'
        - 'it_IT'
        - 'es_ES'
    status:
        - 'active'
        - 'active'
        - 'active'
        - 'inactive'
    default:
        - true

```

####Handle one to many with ArrayCollection####

```yaml
collection:
    scope: [ "roles" ]
database:
	username:
    	- 'alice'
        - 'bob'
        - 'peter'
    roles: #this is a one to Many
        - 'client'
        - 'editor'
        - 'admin'

```

####Retrieve reference####

```yaml
database:
	user:
    	- &alice
```

Roadmap
-------

Actually this component not cover all features you can meet. You can't create reference from YAML, and collection are not fully supported, currently they just convert array into ArrayCollection because we dont have meet this use case at this time, but it's we will, so we plan.

[ ] Generate reference directly from YAML
[ ] Fully support for Collection

Concret example
---------------
```php
public function load(ObjectManager $manager)
{
    $fixture = new Fixture(src/*/*/DataFixtures/YML/');
    $this->localeManager = $this->container->get('gos.i18n_bundle.locale_entity.manager');

    $this->fixture->load('LocaleData.yml', $this);

    foreach ($this->fixture->fetch() as $data) {
        $locale = $this->localeManager->create($data);
        $this->setReference($locale->getName(), $locale);
    }

    $this->localeManager->save();
}
```
You can also see :
------------------
* [Fixture Bundle version for Symfony2](https://github.com/GeniusesOfSymfony/FixtureBundle)
* [Doctrine Data Fixture](https://github.com/doctrine/data-fixtures)
* [Doctrine Fixtures Bundles](https://github.com/doctrine/DoctrineFixturesBundle)

Running the tests
------------------

PHPUnit 3.5 or newer together with Mock_Object package is required. To setup and run tests follow these steps:

* go to the root directory of the project
* run: composer install --dev
* run: phpunit

License
---------

The project is under MIT lisence, for more information see the LICENSE file inside the project







