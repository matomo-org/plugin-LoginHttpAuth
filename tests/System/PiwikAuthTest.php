<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\LoginHttpAuth\tests\System;

use Piwik\Access;
use Piwik\API\Request;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Date;
use Piwik\Db;
use Piwik\FrontController;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;
use Piwik\Version;

/**
 * @group LoginHttpAuth
 * @group LoginHttpAuth_System
 */
class PiwikAuthTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->skipTestIfPiwikLessThan2_15_1();

        FrontController::getInstance()->init();
        Fixture::createWebsite('2012-01-01 00:00:00');

        // sanity checks
        $this->assertTrue(empty($_SERVER['PHP_AUTH_USER']));
        $this->assertInstanceOf('Piwik\Plugins\LoginHttpAuth\Auth', StaticContainer::get('Piwik\Auth'));
    }

    public function test_TrackerAuthentication_DoesNotFail_WhenLoginHttpAuthIsUsed()
    {
        $date = Date::factory('2015-01-01 00:00:00');

        $tracker = Fixture::getTracker($idSite = 1, $date->addHour(1)->getDatetime());
        $tracker->setTokenAuth(Fixture::getTokenAuth());

        $tracker->setUrl('http://shield.org/protocol/theta');
        Fixture::checkResponse($tracker->doTrackPageView('I Am A Robot Tourist'));

        // authentication is required to track dates in the past, so to verify we
        // authenticated, we check the tracked visit times
        $expectedDateTimes = array('2015-01-01 01:00:00');
        $actualDateTimes = $this->getVisitDateTimes();

        $this->assertEquals($expectedDateTimes, $actualDateTimes);
    }

    public function test_BulkTrackingAuthentication_DoesNotFail_WhenLoginHttpAuthIsUsed()
    {
        $superUserTokenAuth = Fixture::getTokenAuth();

        $date = Date::factory('2015-01-01 00:00:00');

        $tracker = Fixture::getTracker($idSite = 1, $date->getDatetime());
        $tracker->setTokenAuth($superUserTokenAuth);
        $tracker->enableBulkTracking();

        $tracker->setForceVisitDateTime($date->getDatetime());
        $tracker->setUrl('http://shield.org/level/10/dandr/pcoulson');
        $tracker->doTrackPageView('Death & Recovery');

        $tracker->setForceVisitDateTime($date->addHour(1)->getDatetime());
        $tracker->setUrl('http://shield.org/logout');
        $tracker->doTrackPageView('Going dark');

        Fixture::checkBulkTrackingResponse($tracker->doBulkTrack());

        // authentication is required to track dates in the past, so to verify we
        // authenticated, we check the tracked visit times
        $expectedDateTimes = array('2015-01-01 00:00:00', '2015-01-01 01:00:00');
        $actualDateTimes = $this->getVisitDateTimes();

        $this->assertEquals($expectedDateTimes, $actualDateTimes);
    }

    /**
     * @dataProvider getApiRequestAuthTests
     */
    public function test_ApiRequestAuthentication_DoesNotFail_WhenLoginHttpAuthIsUsed($useHttpAuth)
    {
        Access::getInstance()->setSuperUserAccess(false);

        $_GET = array(
            'idSite' => 1,
            'date' => '2012-01-01',
            'period' => 'day',
            'module' => 'API',
            'method' => 'VisitsSummary.get',
        );

        if ($useHttpAuth) {
            $_SERVER['PHP_AUTH_USER'] = Fixture::ADMIN_USER_LOGIN;
        } else {
            $_GET['token_auth'] = Fixture::getTokenAuth();
        }

        $frontController = FrontController::getInstance();
        $frontController->init();
        $output = $frontController->dispatch();

        $this->assertNotContains('error', $output);
    }

    public function getApiRequestAuthTests()
    {
        return array(
            array(true),
            array(false),
        );
    }

    private function getVisitDateTimes()
    {
        $rows = Db::fetchAll("SELECT visit_last_action_time FROM " . Common::prefixTable('log_visit')
            . " ORDER BY visit_last_action_time ASC");

        $dates = array();
        foreach ($rows as $row) {
            $dates[] = $row['visit_last_action_time'];
        }
        return $dates;
    }

    protected static function configureFixture($fixture)
    {
        parent::configureFixture($fixture);
        $fixture->createSuperUser = true;
    }

    private function skipTestIfPiwikLessThan2_15_1()
    {
        if (version_compare(Version::VERSION, '2.15.0', '<=')) {
            $this->markTestSkipped("These tests will not pass unless ");
        }
    }
}
