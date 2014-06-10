<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\LoginHttpAuth;

use Piwik\Settings\SystemSetting;

/**
 * Defines Settings for LoginHttpAuthPlugin.
 */
class Settings extends \Piwik\Plugin\Settings
{
    /** @var SystemSetting */
    public $authName;

    protected function init()
    {
        // System setting --> textbox
        $this->createAuthNameSetting();
    }

    private function createAuthNameSetting()
    {
        $this->authName = new SystemSetting('authName', 'Authentication realm');
        $this->authName->uiControlType = static::CONTROL_TEXT;
        $this->authName->uiControlAttributes = array('size' => 15);
        $this->authName->defaultValue = 'Piwik';
        $this->authName->description = 'Use this value for the AuthName directive in .htaccess.';

        $this->addSetting($this->authName);
    }

}
