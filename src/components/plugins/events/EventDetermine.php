<?php
namespace deflou\components\plugins\events;

use deflou\interfaces\applications\activities\IActivity;
use deflou\interfaces\servers\requests\IApplicationRequest;
use deflou\interfaces\stages\IStageEventDetermine;
use deflou\interfaces\stages\IStageEventDetermined;
use extas\components\plugins\Plugin;

/**
 * Class EventDetermine
 *
 * @package deflou\components\plugins\events
 * @author jeyroik <jeyroik@gmail.com>
 */
abstract class EventDetermine extends Plugin implements IStageEventDetermine
{
    /**
     * @param IApplicationRequest $request
     * @return IApplicationRequest
     */
    public function __invoke(IApplicationRequest $request): IApplicationRequest
    {
        if ($request->hasParameter($request::PARAM__EVENT)) {
            return $request;
        }

        $event = $this->dispatch($request);

        if ($event) {
            $this->runAfter(IStageEventDetermined::NAME, $event, $request);

            $app = $event->getApplication();
            $stage = 'deflou.application.' . $app->getSampleName() . '.' . $event->getSampleName() . '.determined';
            $this->runAfter($stage, $event, $request);

            $stage = 'deflou.application.' . $app->getName() . '.' . $event->getName() . '.determined';
            $this->runAfter($stage, $event, $request);

            $request->addParameterByValue($request::PARAM__EVENT, $event);
        }

        return $request;
    }

    /**
     * @param string $stage
     * @param IActivity $event
     * @param IApplicationRequest $request
     */
    protected function runAfter(
        string $stage,
        IActivity $event,
        IApplicationRequest $request
    ): void
    {
        foreach ($this->getPluginsByStage($stage) as $plugin) {
            /**
             * @var IStageEventDetermined $plugin
             */
            $plugin($event, $request);
        }
    }

    /**
     * @param IApplicationRequest $request
     * @return IActivity|null
     */
    abstract protected function dispatch(IApplicationRequest $request): ?IActivity;
}
