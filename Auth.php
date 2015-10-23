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

class Auth extends \Piwik\Plugins\Login\Auth
{
    /**
     * @var Model
     */
    private $userModel;

    /**
     * Constructor.
     *
     * @param Model|null $userModel
     */
    public function __construct(Model $userModel = null)
    {
        parent::__construct();
        if ($userModel === null) {
            $userModel = new Model();
        }

        $this->userModel = $userModel;
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

