<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\LoginHttpAuth;

use Piwik\Settings\Setting;
use Piwik\Settings\FieldConfig;

/**
 * Defines Settings for LoginHttpAuth.
 *
 * Usage like this:
 * $settings = new SystemSettings();
 * $settings->metric->getValue();
 * $settings->description->getValue();
 */
class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /** @var Setting */
    public $authName;

    protected function init()
    {
        $this->authName = $this->createAuthNameSetting();
    }

    private function createAuthNameSetting()
    {
        return $this->makeSetting('authName', $default = 'Piwik', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = 'Authentication realm';
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->uiControlAttributes = array('size' => 15);
            $field->description = 'Use this value for the AuthName directive in .htaccess.';
        });
    }
}
