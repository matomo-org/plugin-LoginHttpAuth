<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\LoginHttpAuth\Test\Unit\AuthTest;

use Piwik\AuthResult;
use Piwik\Plugins\LoginHttpAuth\Auth;

/**
 * @group LoginHttpAuth
 * @group LoginHttpAuth_Unit
 */
class AuthTest extends \PHPUnit_Framework_TestCase
{
    const TEST_USER = "terrymcginnis";
    const TEST_SUPERUSER = 'barbaragordon';

    public static $terryUserDetails = array(
        'login' => self::TEST_USER,
        'token_auth' => 'terrys token auth',
        'superuser_access' => 0
    );

    public static $gordonUserDetails = array(
        'login' => self::TEST_SUPERUSER,
        'token_auth' => 'commissioners token auth',
        'superuser_access' => 1
    );

    /**
     * @var Auth
     */
    private $auth;

    private $backupEnv;
    private $backupServer;

    public function setUp()
    {
        parent::setUp();

        $this->auth = new Auth($this->makeMockUserModel());

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

    /**
     * @dataProvider getServerEnvVarsToTest
     */
    public function test_Auth_SuccessfullyAuthenticatesUser_IfUserIsSpecifiedInAServerOrEnvVar($varKey, $isEnv)
    {
        if ($isEnv) {
            $_ENV[$varKey] = self::TEST_USER;
        } else {
            $_SERVER[$varKey] = self::TEST_USER;
        }

        $result = $this->auth->authenticate();

        $this->checkTerryAuthResult($result);
    }

    public function test_Auth_SuccessfullyAuthenticatesSuperUser()
    {
        $_SERVER['PHP_AUTH_USER'] = self::TEST_SUPERUSER;

        $result = $this->auth->authenticate();

        $this->checkGordonAuthResult($result);
    }

    public function getServerEnvVarsToTest()
    {
        return array(
            array('PHP_AUTH_USER', false),
            array('AUTH_USER', true),
            array('REMOTE_USER', true),
            array('REDIRECT_REMOTE_USER', true)
        );
    }

    private function makeMockUserModel()
    {
        $mock = $this->getMock('\\Piwik\\Plugins\\UsersManager\\Model', array('getUser'));
        $mock->expects($this->any())->method('getUser')->will($this->returnCallback(function ($login) {
            if ($login == AuthTest::TEST_USER) {
                return AuthTest::$terryUserDetails;
            } else if ($login == self::TEST_SUPERUSER) {
                return AuthTest::$gordonUserDetails;
            } else {
                return null;
            }
        }));
        return $mock;
    }

    private function checkTerryAuthResult(AuthResult $result)
    {
        $this->assertEquals(AuthResult::SUCCESS, $result->getCode());
        $this->assertEquals(self::TEST_USER, $result->getIdentity());
        $this->assertEquals(self::$terryUserDetails['token_auth'],$result->getTokenAuth());
    }

    private function checkGordonAuthResult(AuthResult $result)
    {
        $this->assertEquals(AuthResult::SUCCESS_SUPERUSER_AUTH_CODE, $result->getCode());
        $this->assertEquals(self::TEST_SUPERUSER, $result->getIdentity());
        $this->assertEquals(self::$gordonUserDetails['token_auth'], $result->getTokenAuth());
    }
}