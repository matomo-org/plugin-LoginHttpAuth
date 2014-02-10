<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\LoginHttpAuth;

use Piwik\AuthResult;
use Piwik\DB;
use Piwik\Piwik;
use Piwik\Plugins\Login;
use Piwik\Plugins\UsersManager\Model;

class Auth extends \Piwik\Plugins\Login\Auth
{
    /**
     * Authentication module's name
     *
     * @return string
     */
    public function getName()
    {
        return 'LoginHttpAuth';
    }

    /**
     * Authenticates user
     *
     * @return \Piwik\AuthResult
     */
    public function authenticate()
    {
        $httpLogin = $this->getHttpAuthLogin();
        if (!empty($httpLogin)) {
            $hasSuperUser = Piwik::hasTheUserSuperUserAccess($httpLogin);
            if ($hasSuperUser) {
                return new AuthResult(AuthResult::SUCCESS_SUPERUSER_AUTH_CODE, $httpLogin, null);
            }

            $model = new Model();
            if ($model->userExists($httpLogin)) {
                return new AuthResult(AuthResult::SUCCESS, $httpLogin, null);
            }

            return new AuthResult(AuthResult::FAILURE, $httpLogin, null);
        }
        return parent::authenticate();
    }

    protected function getHttpAuthLogin()
    {
        $httpLogin = false;
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $httpLogin = $_SERVER['PHP_AUTH_USER'];
        } elseif (isset($_ENV['AUTH_USER'])) {
            $httpLogin = $_ENV['AUTH_USER'];
        } elseif (isset($_ENV['REMOTE_USER'])) {
            $httpLogin = $_ENV['REMOTE_USER'];
        } elseif (isset($_ENV['REDIRECT_REMOTE_USER'])) {
            $httpLogin = $_ENV['REDIRECT_REMOTE_USER'];
        }
        return $httpLogin;
    }
}

