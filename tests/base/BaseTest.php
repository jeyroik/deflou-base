<?php
namespace tests\base;

use deflou\components\applications\activities\Activity;
use deflou\components\applications\Application;
use deflou\components\Deflou;
use deflou\components\Input;
use deflou\components\triggers\actions\ApplicationAction;
use deflou\components\triggers\events\ApplicationEvent;
use deflou\components\triggers\Trigger;
use deflou\interfaces\IDeflou;
use Dotenv\Dotenv;
use extas\components\console\TSnuffConsole;
use extas\components\packages\Installer;
use extas\components\parsers\Parser;
use extas\components\plugins\Plugin;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepositoryDynamic;
use extas\components\THasMagicClass;
use extas\interfaces\conditions\IConditionParameter;
use extas\interfaces\samples\parameters\ISampleParameter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use tests\base\misc\MiscApplication;

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
            ['triggers', 'name', Trigger::class],
            ['applicationEvents', 'id', ApplicationEvent::class],
            ['applicationActions', 'id', ApplicationAction::class],
            ['parsers', 'name', Parser::class]
        ]);
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffDynamicRepositories();
    }

    public function testBasic()
    {
        /**
         * @var BufferedOutput $output
         */
        $cOutput = $this->getOutput(true);

        $installer = new Installer([
            Installer::FIELD__INPUT => $this->getInput(),
            Installer::FIELD__OUTPUT => $cOutput
        ]);
        $installer->installPackages([
            'deflou/base' => json_decode(file_get_contents(getcwd() . '/extas.json'), true)
        ]);

        $this->prepareDefault();

        $output = $this->deflou->dispatchEvent(new Input([
            'app' => 'test',
            'event' => 'test_event',
            'test' => 'is ok'
        ]));

        $this->assertFalse(
            $output->hasErrors(),
            'Output has errors: ' . print_r($output, true) . PHP_EOL .
            'Installation output: '  . $cOutput->fetch() . PHP_EOL
        );
    }

    protected function prepareDefault()
    {
        $this->getMagicClass('applications')->create(new Application([
            Application::FIELD__NAME => 'test',
            Application::FIELD__SAMPLE_NAME => 'testSample',
            Application::FIELD__CLASS => MiscApplication::class
        ]));

        $this->getMagicClass('activities')->create(new Activity([
            Activity::FIELD__NAME => 'test_event',
            Activity::FIELD__SAMPLE_NAME => 'testEvent',
            Activity::FIELD__APPLICATION_NAME => 'test',
            Activity::FIELD__TYPE => Activity::TYPE__EVENT
        ]));

        $this->getMagicClass('activities')->create(new Activity([
            Activity::FIELD__NAME => 'test_action',
            Activity::FIELD__SAMPLE_NAME => 'testAction',
            Activity::FIELD__APPLICATION_NAME => 'test',
            Activity::FIELD__TYPE => Activity::TYPE__ACTION
        ]));

        $this->getMagicClass('triggers')->create(new Trigger([
            Trigger::FIELD__EVENT_NAME => 'test_event',
            Trigger::FIELD__ACTION_NAME => 'test_action',
            Trigger::FIELD__EVENT_PARAMETERS => [
                'test' => [
                    IConditionParameter::FIELD__NAME => 'test',
                    IConditionParameter::FIELD__VALUE => 'is ok',
                    IConditionParameter::FIELD__CONDITION => 'equal'
                ]
            ],
            Trigger::FIELD__ACTION_PARAMETERS => [
                'test' => [
                    ISampleParameter::FIELD__NAME => 'test',
                    ISampleParameter::FIELD__VALUE => 'verified: is ok'
                ]
            ]
        ]));
    }
}
