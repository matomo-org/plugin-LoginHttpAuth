<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\LoginHttpAuth;

use Piwik\Session;
use Piwik\View;

class Controller extends \Piwik\Plugins\Login\Controller
{
    public function logmeout()
    {
        // Effectively log out of Http auth
        $settings = new Settings('LoginHttpAuthPlugin');
        header('WWW-Authenticate: Basic realm="'. $settings->authName->getValue() .'"');
        header('HTTP/1.0 401 Unauthorized');
        self::clearSession();
    }

    public function logout()
    {
        $view = new View('@LoginHttpAuth/logout');
        return $view->render();
    }
}
