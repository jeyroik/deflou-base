<?php
namespace tests\base;

use deflou\components\applications\activities\Activity;
use deflou\components\applications\Application;
use deflou\components\Deflou;
use deflou\components\Input;
use deflou\components\triggers\Trigger;
use deflou\interfaces\IDeflou;
use Dotenv\Dotenv;
use extas\components\console\TSnuffConsole;
use extas\components\packages\Installer;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepositoryDynamic;
use extas\components\THasMagicClass;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseTest
 *
 * @package tests\base
 * @author jeyroik <jeyroik@gmail.com>
 */
class BaseTest extends TestCase
{
    use TSnuffConsole;
    use TSnuffPlugins;
    use TSnuffRepositoryDynamic;
    use THasMagicClass;

    protected IDeflou $deflou;

    public function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->deflou = new Deflou();
        $this->createSnuffDynamicRepositories([
            ['applications', 'name', Application::class],
            ['activities', 'name', Activity::class],
            ['triggers', 'name', Trigger::class]
        ]);
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffDynamicRepositories();
    }

    public function testBasic()
    {
        $installer = new Installer([
            Installer::FIELD__INPUT => $this->getInput(),
            Installer::FIELD__OUTPUT => $this->getOutput()
        ]);
        $installer->installPackages([
            json_decode(file_get_contents(getcwd() . '/extas.json'), true)
        ]);

        $output = $this->deflou->dispatchEvent(new Input([
            'app' => 'test',
            'event' => 'test'
        ]));

        $this->assertFalse($output->hasErrors(), 'Output has errors: ' . print_r($output, true));
    }
}
