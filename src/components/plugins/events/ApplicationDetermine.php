<?php
namespace deflou\components\plugins\events;

use deflou\interfaces\applications\IApplication;
use deflou\interfaces\servers\requests\IApplicationRequest;
use deflou\interfaces\stages\IStageApplicationDetermine;
use deflou\interfaces\stages\IStageApplicationDetermined;
use extas\components\plugins\Plugin;

/**
 * Class ApplicationDetermine
 *
 * @package deflou\components\plugins\events
 * @author jeyroik <jeyroik@gmail.com>
 */
abstract class ApplicationDetermine extends Plugin implements IStageApplicationDetermine
{
    /**
     * @param IApplicationRequest $request
     * @return IApplicationRequest
     */
    public function __invoke(IApplicationRequest $request): IApplicationRequest
    {
        if ($request->hasParameter($request::PARAM__EVENT_APPLICATION)) {
            return $request;
        }

        $app = $this->dispatch($request);

        if ($app) {
            $this->runStageAfter(IStageApplicationDetermined::NAME, $app, $request);

            $stage = 'deflou.application.' . $app->getSampleName() . '.determined';
            $this->runStageAfter($stage, $app, $request);

            $stage = 'deflou.application.' . $app->getName() . '.determined';
            $this->runStageAfter($stage, $app, $request);

            $request->addParameterByValue(IApplicationRequest::PARAM__EVENT_APPLICATION, $app);
        }

        return $request;
    }

    /**
     * @param string $stage
     * @param IApplication $application
     * @param IApplicationRequest $request
     */
    protected function runStageAfter(string $stage, IApplication $application, IApplicationRequest $request): void
    {
        foreach ($this->getPluginsByStage($stage) as $plugin) {
            /**
             * @var IStageApplicationDetermined $plugin
             */
            $plugin($application, $request);
        }
    }

    /**
     * @param IApplicationRequest $request
     * @return IApplication|null
     */
    abstract protected function dispatch(IApplicationRequest $request): ?IApplication;
}
