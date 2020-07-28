<?php
namespace deflou\components\plugins\events;

use deflou\interfaces\applications\activities\IActivity;
use deflou\interfaces\applications\IApplication;
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
        $app = $request->getParameterValue($request::PARAM__EVENT_APPLICATION);

        if ($event) {
            $this->runAfter(IStageEventDetermined::NAME, $event, $app, $request);

            $stage = 'deflou.application.' . $app->getSampleName() . '.' . $event->getSampleName() . '.determined';
            $this->runAfter($stage, $event, $app, $request);

            $stage = 'deflou.application.' . $app->getName() . '.' . $event->getName() . '.determined';
            $this->runAfter($stage, $event, $app, $request);

            $request->addParameterByValue($request::PARAM__EVENT, $event);
        }

        return $request;
    }

    /**
     * @param string $stage
     * @param IActivity $event
     * @param IApplication $eventApp
     * @param IApplicationRequest $request
     */
    protected function runAfter(
        string $stage,
        IActivity $event,
        IApplication $eventApp,
        IApplicationRequest $request
    ): void
    {
        foreach ($this->getPluginsByStage($stage) as $plugin) {
            /**
             * @var IStageEventDetermined $plugin
             */
            $plugin($event, $eventApp, $request);
        }
    }

    /**
     * @param IApplicationRequest $request
     * @return IActivity|null
     */
    abstract protected function dispatch(IApplicationRequest $request): ?IActivity;
}
