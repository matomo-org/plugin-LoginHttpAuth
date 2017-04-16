<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\LoginHttpAuth;

use Piwik\View;
use Piwik\Config;
use Piwik\Url;

class Controller extends \Piwik\Plugins\Login\Controller
{
    public function logmeout()
    {
        // Effectively log out of Http auth
        $settings = new SystemSettings('LoginHttpAuth');
        header('WWW-Authenticate: Basic realm="'. $settings->authName->getValue() .'"');
        header('HTTP/1.0 401 Unauthorized');
        self::clearSession();
    }

    public function logout()
    {
        self::clearSession();

        $logoutUrl = @Config::getInstance()->General['login_logout_url'];
        if(empty($logoutUrl)) {
            $view = new View('@LoginHttpAuth/logout');
            return $view->render();
        } else {
            Url::redirectToUrl($logoutUrl);
        }
    }
}
