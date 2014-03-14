<?php

namespace SolasMatch\API\V0;

use \SolasMatch\Common as Common;
use \SolasMatch\API\DAO as DAO;
use \SolasMatch\API as API;

/**
 * Description of Badges
 *
 * @author sean
 */

require_once __DIR__."/../DataAccessObjects/BadgeDao.class.php";

class Badges
{
    public static function init()
    {
        /**
         * Gets a single badge object based on its $badgeId
         **/
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/badges/:badgeId/',
            function ($badgeId, $format = ".json") {
                if (!is_numeric($badgeId)&& strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::getBadge($badgeId), null, $format);
            },
            'getBadge'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/badges(:format)/',
            function ($format = ".json") {
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::getBadge(), null, $format);
            },
            'getBadges'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::POST,
            '/v0/badges(:format)/',
            function ($format = ".json") {
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Badge");
                $data->setId(null);
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::insertAndUpdateBadge($data), null, $format);
            },
            'createBadge',
            '\SolasMatch\API\Lib\Middleware::authenticateUserMembership'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::PUT,
            '/v0/badges/:badgeId/',
            function ($badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                $data = API\Dispatcher::getDispatcher()->request()->getBody();
                $client = new Common\Lib\APIHelper($format);
                $data = $client->deserialize($data, "\SolasMatch\Common\Protobufs\Models\Badge");
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::insertAndUpdateBadge($data), null, $format);
            },
            'updateBadge',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge'
        );
        
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::DELETE,
            '/v0/badges/:badgeId/',
            function ($badgeId, $format = ".json") {
                if (!is_numeric($badgeId) && strstr($badgeId, '.')) {
                    $badgeId = explode('.', $badgeId);
                    $format = '.'.$badgeId[1];
                    $badgeId = $badgeId[0];
                }
                API\Dispatcher::sendResponse(null, DAO\BadgeDao::deleteBadge($badgeId), null, $format);
            },
            'deleteBadge',
            '\SolasMatch\API\Lib\Middleware::authenticateUserForOrgBadge'
        );

        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/badges/:badgeId/users(:format)/',
            function ($badgeId, $format = ".json") {
                $data = UserDao::getUsersWithBadge($badgeId);
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'getUsersWithBadge'
        );
        
        /*
         * Checks if a user has a particular badge
         */
        API\Dispatcher::registerNamed(
            Common\Enums\HttpMethodEnum::GET,
            '/v0/badges/:badgeId/:userId/',
            function ($badgeId, $userId, $format = ".json") {
                if (!is_numeric($userId)&& strstr($userId, '.')) {
                    $userId = explode('.', $userId);
                    $format = '.'.$userId[1];
                    $userId = $userId[0];
                }
                $data = DAO\UserDao::userHasBadge($badgeId, $userId);
                if (is_array($data)) {
                    $data = $data[0];
                }
                API\Dispatcher::sendResponse(null, $data, null, $format);
            },
            'userHasBadge'
        );

    }
}
Badges::init();
