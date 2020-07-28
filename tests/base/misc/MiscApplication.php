<?php
namespace tests\base\misc;

use deflou\components\applications\ApplicationDispatcher;
use deflou\components\triggers\actions\ApplicationActionResponse;

/**
 * Class MiscApplication
 *
 * @package tests\base\misc
 * @author jeyroik <jeyroik@gmail.com>
 */
class MiscApplication extends ApplicationDispatcher
{

    public function testEvent()
    {
        $event = $this->getApplicationEvent();
        $event->addParameterByValue('equipment', true);

        return $event;
    }

    public function testAction()
    {
        $action = $this->getApplicationAction();

        return new ApplicationActionResponse([
            ApplicationActionResponse::FIELD__APPLICATION_ACTION_ID => $action->getId(),
            ApplicationActionResponse::FIELD__BODY => 'test',
            ApplicationActionResponse::FIELD__STATUS => 0
        ]);
    }
}
