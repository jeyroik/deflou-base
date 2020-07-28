<?php
namespace tests\base;

use deflou\components\applications\activities\Activity;
use deflou\components\applications\Application;
use deflou\components\Deflou;
use deflou\components\Input;
use deflou\components\triggers\actions\ApplicationAction;
use deflou\components\triggers\events\ApplicationEvent;
use deflou\components\triggers\Trigger;
use deflou\components\triggers\TriggerLog;
use deflou\interfaces\IDeflou;
use deflou\interfaces\triggers\actions\IApplicationAction;
use deflou\interfaces\triggers\events\IApplicationEvent;
use deflou\interfaces\triggers\ITriggerLog;
use Dotenv\Dotenv;
use extas\components\conditions\Condition;
use extas\components\conditions\ConditionRepository;
use extas\components\conditions\TSnuffConditions;
use extas\components\console\TSnuffConsole;
use extas\components\packages\Installer;
use extas\components\parsers\Parser;
use extas\components\parsers\ParserSimpleReplace;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepository;
use extas\components\repositories\TSnuffRepositoryDynamic;
use extas\components\THasMagicClass;
use extas\interfaces\conditions\IConditionParameter;
use extas\interfaces\samples\parameters\ISampleParameter;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
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
    use TSnuffRepository;
    use THasMagicClass;
    use TSnuffConditions;

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
            ['parsers', 'name', Parser::class],
            ['triggersLogs', 'id', TriggerLog::class]
        ]);
        $this->registerSnuffRepos([
            'conditionRepository' => ConditionRepository::class
        ]);
        $this->createSnuffConditions(['equal', 'not_empty', 'not_equal']);
        $this->getMagicClass('parsers')->create(new Parser([
            Parser::FIELD__NAME => 'event',
            Parser::FIELD__SAMPLE_NAME => 'simple_replace',
            Parser::FIELD__CLASS => ParserSimpleReplace::class,
            Parser::FIELD__VALUE => '',
            Parser::FIELD__CONDITION => '!@',
            Parser::FIELD__PARAMETERS => [
                ParserSimpleReplace::FIELD__PARAM_NAME => [
                    ISampleParameter::FIELD__NAME => ParserSimpleReplace::FIELD__PARAM_NAME,
                    ISampleParameter::FIELD__VALUE => 'event'
                ]
            ]
        ]));
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffDynamicRepositories();
    }

    public function testMissedAppParam()
    {
        /**
         * @var BufferedOutput $output
         */
        $cOutput = $this->getOutput(true);

        $this->prepareDefault($cOutput);

        $output = $this->deflou->dispatchEvent(new Input([
            'event' => 'test_event',
            'test' => 'is ok'
        ]));

        $this->assertTrue(
            $output->hasErrors(),
            'Output has not errors: ' . print_r($output, true) . PHP_EOL .
            'Installation output: '  . $cOutput->fetch() . PHP_EOL
        );
    }

    public function testMissedEventParam()
    {
        /**
         * @var BufferedOutput $output
         */
        $cOutput = $this->getOutput(true);

        $this->prepareDefault($cOutput);

        $output = $this->deflou->dispatchEvent(new Input([
            'app' => 'test',
            'test' => 'is ok'
        ]));

        $this->assertTrue(
            $output->hasErrors(),
            'Output has not errors: ' . print_r($output, true) . PHP_EOL .
            'Installation output: '  . $cOutput->fetch() . PHP_EOL
        );
    }

    public function testBasic()
    {
        /**
         * @var BufferedOutput $output
         */
        $cOutput = $this->getOutput(true);

        $this->prepareDefault($cOutput);

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

        $this->validateApplicationEvent();
        $this->validateApplicationAction();
        $this->validateTriggerLog();
    }

    protected function validateTriggerLog(): void
    {
        /**
         * @var ITriggerLog[] $logs
         */
        $logs = $this->getMagicClass('triggersLogs')->all([
            TriggerLog::FIELD__TRIGGER_NAME => 'test'
        ]);

        $this->assertCount(1, $logs);
        $log = array_shift($logs);

        $this->assertFalse($log->isSuccess());
        $this->assertEquals('test', $log->getResponseBody());

        $event = $this->getMagicClass('applicationEvents')->one([
            IApplicationEvent::FIELD__NAME => 'test_event'
        ]);
        $action = $this->getMagicClass('applicationActions')->one([
            IApplicationAction::FIELD__NAME => 'test_action'
        ]);

        $this->assertEquals($event->getId(), $log->getEventId());
        $this->assertEquals($action->getId(), $log->getActionId());
    }

    protected function validateApplicationAction(): void
    {
        /**
         * @var IApplicationAction[] $appActions
         */
        $appActions = $this->getMagicClass('applicationActions')->all([
            IApplicationAction::FIELD__NAME => 'test_action'
        ]);

        $this->assertCount(
            1,
            $appActions,
            'Incorrect actions count;' . print_r($appActions, true)
        );
        
        $appAction = array_shift($appActions);
        unset($appAction[$appAction::FIELD__ID], $appAction[$appAction::FIELD__CREATED_AT]);

        $this->assertEquals(
            [
                ApplicationAction::FIELD__NAME => 'test_action',
                ApplicationAction::FIELD__SAMPLE_NAME => 'testAction',
                ApplicationAction::FIELD__APPLICATION_NAME => 'test',
                ApplicationAction::FIELD__APPLICATION_SAMPLE_NAME => 'testSample',
                ApplicationAction::FIELD__PARAMETERS => [
                    ApplicationAction::PARAM__SOURCE => [
                        ISampleParameter::FIELD__NAME => ApplicationAction::PARAM__SOURCE,
                        ISampleParameter::FIELD__VALUE => [
                            'test' => [
                                ISampleParameter::FIELD__NAME => 'test',
                                ISampleParameter::FIELD__VALUE => 'verified: @event.test'
                            ]
                        ]
                    ],
                    ApplicationAction::PARAM__ARTIFACTS => [
                        ISampleParameter::FIELD__NAME => ApplicationAction::PARAM__ARTIFACTS,
                        ISampleParameter::FIELD__VALUE => [
                            'test' => [
                                ISampleParameter::FIELD__NAME => 'test',
                                ISampleParameter::FIELD__VALUE => 'verified: is ok'
                            ]
                        ]
                    ]
                ]
            ],
            $appAction->__toArray(),
            'Incorrect application action:' . print_r($appAction->__toArray(), true)
        );
    }

    protected function validateApplicationEvent(): void
    {
        /**
         * @var IApplicationEvent[] $appEvents
         */
        $appEvents = $this->getMagicClass('applicationEvents')->all([
            IApplicationEvent::FIELD__NAME => 'test_event'
        ]);

        $this->assertCount(1, $appEvents);
        $appEvent = array_shift($appEvents);
        unset($appEvent[$appEvent::FIELD__ID], $appEvent[$appEvent::FIELD__CREATED_AT]);

        $this->assertEquals(
            [
                ApplicationEvent::FIELD__NAME => 'test_event',
                ApplicationEvent::FIELD__SAMPLE_NAME => 'testEvent',
                ApplicationEvent::FIELD__APPLICATION_NAME => 'test',
                ApplicationEvent::FIELD__APPLICATION_SAMPLE_NAME => 'testSample',
                ApplicationEvent::FIELD__PARAMETERS => [
                    ApplicationEvent::PARAM__SOURCE => [
                        ISampleParameter::FIELD__NAME => ApplicationEvent::PARAM__SOURCE,
                        ISampleParameter::FIELD__VALUE => [
                            'app' => 'test',
                            'event' => 'test_event',
                            'test' => 'is ok'
                        ]
                    ],
                    ApplicationEvent::PARAM__ARTIFACTS => [
                        ISampleParameter::FIELD__NAME => ApplicationEvent::PARAM__ARTIFACTS,
                        ISampleParameter::FIELD__VALUE => [
                            'app' => 'test',
                            'event' => 'test_event',
                            'test' => 'is ok'
                        ]
                    ]
                ],
                ApplicationEvent::FIELD__SOURCE => 'unknown'
            ],
            $appEvent->__toArray(),
            'Incorrect application event:' . print_r($appEvent->__toArray(), true)
        );
    }

    /**
     * @param OutputInterface $cOutput
     */
    protected function prepareDefault(OutputInterface $cOutput): void
    {
        $installer = new Installer([
            Installer::FIELD__INPUT => $this->getInput(),
            Installer::FIELD__OUTPUT => $cOutput
        ]);
        $installer->installPackages([
            'deflou/base' => json_decode(file_get_contents(getcwd() . '/extas.json'), true)
        ]);

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
            Trigger::FIELD__NAME => 'test',
            Trigger::FIELD__EVENT_NAME => 'test_event',
            Trigger::FIELD__ACTION_NAME => 'test_action',
            Trigger::FIELD__EVENT_PARAMETERS => [
                'test' => [
                    IConditionParameter::FIELD__NAME => 'test',
                    IConditionParameter::FIELD__VALUE => 'is ok',
                    IConditionParameter::FIELD__CONDITION => '='
                ]
            ],
            Trigger::FIELD__ACTION_PARAMETERS => [
                'test' => [
                    ISampleParameter::FIELD__NAME => 'test',
                    ISampleParameter::FIELD__VALUE => 'verified: @event.test'
                ]
            ]
        ]));

        $this->getMagicClass('triggers')->create(new Trigger([
            Trigger::FIELD__NAME => 'incorrect event param',
            Trigger::FIELD__EVENT_NAME => 'test_event',
            Trigger::FIELD__ACTION_NAME => 'test_action',
            Trigger::FIELD__EVENT_PARAMETERS => [
                'test_unknown' => [
                    IConditionParameter::FIELD__NAME => 'test_unknown',
                    IConditionParameter::FIELD__VALUE => 'is ok',
                    IConditionParameter::FIELD__CONDITION => '='
                ]
            ],
            Trigger::FIELD__ACTION_PARAMETERS => [
                'test' => [
                    ISampleParameter::FIELD__NAME => 'test',
                    ISampleParameter::FIELD__VALUE => 'missing verify: @event.test'
                ]
            ]
        ]));

        $this->getMagicClass('triggers')->create(new Trigger([
            Trigger::FIELD__NAME => 'not applicable by condition',
            Trigger::FIELD__EVENT_NAME => 'test_event',
            Trigger::FIELD__ACTION_NAME => 'test_action',
            Trigger::FIELD__EVENT_PARAMETERS => [
                'test' => [
                    IConditionParameter::FIELD__NAME => 'test',
                    IConditionParameter::FIELD__VALUE => 'is ok',
                    IConditionParameter::FIELD__CONDITION => '!='
                ]
            ],
            Trigger::FIELD__ACTION_PARAMETERS => [
                'test' => [
                    ISampleParameter::FIELD__NAME => 'test',
                    ISampleParameter::FIELD__VALUE => 'broken verified: @event.test'
                ]
            ]
        ]));
    }
}
