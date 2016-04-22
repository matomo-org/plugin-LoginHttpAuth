<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\LoginHttpAuth;

use Piwik\AuthResult;
use Piwik\DB;
use Piwik\Plugins\Login;
use Piwik\Plugins\UsersManager\Model;

class Auth implements \Piwik\Auth
{
    /**
     * @var Model
     */
    private $userModel;

    /**
     * @var Auth
     */
    private $fallbackAuth;

    /**
     * Constructor.
     *
     * @param Model|null $userModel
     */
    public function __construct(Model $userModel = null)
    {
        if ($userModel === null) {
            $userModel = new Model();
        }

        $this->userModel = $userModel;
        $this->fallbackAuth = new \Piwik\Plugins\Login\Auth();
    }

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
            $user = $this->userModel->getUser($httpLogin);

            if(empty($user)) {
                return new AuthResult(AuthResult::FAILURE, $httpLogin, null);
            }

            $code = !empty($user['superuser_access']) ? AuthResult::SUCCESS_SUPERUSER_AUTH_CODE : AuthResult::SUCCESS;
            return new AuthResult($code, $httpLogin, $user['token_auth']);

        }
        return $this->fallbackAuth->authenticate();
    }

    protected function getHttpAuthLogin()
    {
        $httpLogin = false;
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $httpLogin = $_SERVER['PHP_AUTH_USER'];
        } elseif (isset($_SERVER['HTTP_AUTH_USER'])) {
           $httpLogin = $_SERVER['HTTP_AUTH_USER'];
        } elseif (isset($_ENV['AUTH_USER'])) {
            $httpLogin = $_ENV['AUTH_USER'];
        } elseif (isset($_ENV['REMOTE_USER'])) {
            $httpLogin = $_ENV['REMOTE_USER'];
        } elseif (isset($_ENV['REDIRECT_REMOTE_USER'])) {
            $httpLogin = $_ENV['REDIRECT_REMOTE_USER'];
        }
        return $httpLogin;
    }

    public function setTokenAuth($token_auth)
    {
        $this->fallbackAuth->setTokenAuth($token_auth);
    }

    public function getLogin()
    {
        $this->fallbackAuth->getLogin();
    }

    public function getTokenAuthSecret()
    {
        return $this->fallbackAuth->getTokenAuthSecret();
    }

    public function setLogin($login)
    {
        $this->fallbackAuth->setLogin($login);
    }

    public function setPassword($password)
    {
        $this->fallbackAuth->setPassword($password);
    }

    public function setPasswordHash($passwordHash)
    {
        $this->fallbackAuth->setPasswordHash($passwordHash);
    }
}

