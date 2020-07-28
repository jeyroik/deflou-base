<?php
namespace deflou\components\plugins\events;

use deflou\interfaces\applications\IApplication;
use deflou\interfaces\servers\requests\IApplicationRequest;
use extas\interfaces\repositories\IRepository;

/**
 * Class ApplicationDetermineByParamApp
 *
 * @method IRepository applications()
 *
 * @package deflou\components\plugins\events
 * @author jeyroik <jeyroik@gmail.com>
 */
class ApplicationDetermineByParamApp extends ApplicationDetermine
{
    /**
     * @param IApplicationRequest $request
     * @return IApplication|null
     */
    protected function dispatch(IApplicationRequest $request): ?IApplication
    {
        $data = $request->getParameterValue(IApplicationRequest::PARAM__DATA, []);

        $appName = $data['app'] ?? '';

        if (!$appName) {
            return null;
        }

        return $this->applications()->one([IApplication::FIELD__NAME => $appName]);
    }
}
