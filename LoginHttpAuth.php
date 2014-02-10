<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\LoginHttpAuth;

use Exception;
use Piwik\Plugin\Manager;
use Piwik\Plugins\Login\Login;

class LoginHttpAuth extends \Piwik\Plugin
{
    public function getListHooksRegistered()
    {
        return array(
            'Request.initAuthenticationObject' => 'initAuthenticationObject',
            'User.isNotAuthorized'             => 'noAccess',
            'API.Request.authenticate'         => 'ApiRequestAuthenticate',
        );
    }

    /**
     * Deactivate default Login module, as both cannot be activated together
     */
    public function activate()
    {
        if (Manager::getInstance()->isPluginActivated("Login") == true) {
            Manager::getInstance()->deactivatePlugin("Login");
        }
    }

    /**
     * Activate default Login module, as one of them is needed to access Piwik
     */
    public function deactivate()
    {
        if (Manager::getInstance()->isPluginActivated("Login") == false) {
            Manager::getInstance()->activatePlugin("Login");
        }
    }

    public function initAuthenticationObject($activateCookieAuth = false)
    {
        $auth = new Auth();
        \Piwik\Registry::set('auth', $auth);

        $login = new Login();
        return $login->initAuthenticationFromCookie($auth, $activateCookieAuth);
    }

    public function ApiRequestAuthenticate($tokenAuth)
    {
        $login = new Login();
        return $login->ApiRequestAuthenticate($tokenAuth);
    }

    public function noAccess(Exception $exception)
    {
        $login = new Login();
        return $login->noAccess($exception);
    }

}
