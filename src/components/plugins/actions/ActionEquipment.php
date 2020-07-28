<?php
namespace deflou\components\plugins\actions;

use deflou\interfaces\stages\IStageBeforeActionRun;
use deflou\interfaces\triggers\actions\IApplicationAction;
use deflou\interfaces\triggers\events\IApplicationEvent;
use extas\components\plugins\Plugin;
use extas\interfaces\parsers\IParser;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\samples\parameters\ISampleParameter;

/**
 * Class ActionEquipment
 *
 * @method IRepository parsers()
 *
 * @package deflou\components\plugins\actions
 * @author jeyroik <jeyroik@gmail.com>
 */
class ActionEquipment extends Plugin implements IStageBeforeActionRun
{
    public const FIELD__EVENT = 'event';
    public const FIELD__ACTION = 'action';

    /**
     * @param IApplicationAction $action
     * @param IApplicationEvent $event
     * @return IApplicationAction
     */
    public function __invoke(IApplicationAction $action, IApplicationEvent $event): IApplicationAction
    {
        /**
         * @var $parsers IParser[]
         */
        $parsers = $this->parsers()->all([]);
        $artifacts = $action->getParameterValue(IApplicationAction::PARAM__ARTIFACTS, []);
        if (empty($artifacts)) {
            $artifacts = $action->getParameterValue(IApplicationAction::PARAM__SOURCE, []);
        }

        foreach ($parsers as $parser) {
            $artifacts = $this->applyParser($parser, $artifacts, $event, $action);
        }

        $action->hasParameter(IApplicationAction::PARAM__ARTIFACTS)
            ? $action->setParameterValue(IApplicationAction::PARAM__ARTIFACTS, $artifacts)
            : $action->addParameterByValue(IApplicationAction::PARAM__ARTIFACTS, $artifacts);

        return $action;
    }

    /**
     * @param IParser $parser
     * @param array $artifacts
     * @param IApplicationEvent $event
     * @param IApplicationAction $action
     * @return array
     */
    protected function applyParser(
        IParser $parser,
        array $artifacts,
        IApplicationEvent $event,
        IApplicationAction $action
    ): array
    {
        foreach ($artifacts as $name => $options) {
            $value = $options[ISampleParameter::FIELD__VALUE];
            $parser[static::FIELD__ACTION] = $action->getParametersValues();
            $parser[static::FIELD__EVENT] = $event->getParametersValues();

            if ($parser->canParse($value)) {
                $options[ISampleParameter::FIELD__VALUE] = $parser->parse($value);
                $artifacts[$name] = $options;
            }
        }

        return $artifacts;
    }
}
