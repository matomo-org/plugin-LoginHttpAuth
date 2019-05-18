<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\LoginHttpAuth\Test\Integration\AuthTest;

use Piwik\AuthResult;
use Piwik\Plugins\LoginHttpAuth\Auth;
use Piwik\Plugins\UsersManager\API as UsersManagerAPI;
use Piwik\Plugins\UsersManager\UserUpdater;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group LoginHttpAuth
 * @group LoginHttpAuth_Integration
 */
class AuthTest extends IntegrationTestCase
{
    const TEST_USER = "terrymcginnis";
    const TEST_SUPERUSER = 'barbaragordon';

    /**
     * @var Auth
     */
    private $auth;

    private $backupEnv;
    private $backupServer;

    public function setUp()
    {
        parent::setUp();

        UsersManagerAPI::getInstance()->addUser(self::TEST_USER, 'anotherparttimer', 'terry.mcginnis@hamiltonhill.edu');
        UsersManagerAPI::getInstance()->addUser(self::TEST_SUPERUSER, 'streetballet', 'barbara.gordon@gotham.gov');

        $userUpdater = new UserUpdater();
        $userUpdater->setSuperUserAccessWithoutCurrentPassword(self::TEST_SUPERUSER, true);

        $this->auth = new Auth();

        $this->backupEnv = $_ENV;
        $_ENV = array();

        $this->backupServer = $_SERVER;
        $_SERVER = array();
    }

    public function tearDown()
    {
        $_ENV = $this->backupEnv;
        $_SERVER = $this->backupServer;

        parent::tearDown();
    }

    public function test_Auth_CorrectlyAuthenticatesNormalUser_IfUserIsInDb()
    {
        $_SERVER['PHP_AUTH_USER'] = self::TEST_USER;

        $result = $this->auth->authenticate();

        $this->assertEquals(AuthResult::SUCCESS, $result->getCode());
    }

    public function test_Auth_CorrectlyAuthenticatesSuperUser_IfUserIsInDb()
    {
        $_SERVER['PHP_AUTH_USER'] = self::TEST_SUPERUSER;

        $result = $this->auth->authenticate();

        $this->assertEquals(AuthResult::SUCCESS_SUPERUSER_AUTH_CODE, $result->getCode());
    }

    public function test_Auth_DelegatesToLoginAuth_WhenHttpServerDidntAuthenticate_AuthenticatingByPassword()
    {
        unset($_SERVER['PHP_AUTH_USER']);

        $this->auth->setLogin(self::TEST_USER);
        $this->auth->setPassword('anotherparttimer');
        $result = $this->auth->authenticate();

        $this->assertEquals(AuthResult::SUCCESS, $result->getCode());
    }

    public function test_Auth_DelegatesToLoginAuth_WhenHttpServerDidntAuthenticate_AuthenticatingByTokenAuth()
    {
        unset($_SERVER['PHP_AUTH_USER']);

        $this->auth->setLogin(self::TEST_USER);
        $this->auth->setTokenAuth(UsersManagerAPI::getInstance()->getTokenAuth(self::TEST_USER, md5('anotherparttimer')));
        $result = $this->auth->authenticate();

        $this->assertEquals(AuthResult::SUCCESS, $result->getCode());
    }
}
