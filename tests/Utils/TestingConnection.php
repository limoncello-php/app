<?php namespace Tests\Utils;

/**
 * Copyright 2015-2016 info@neomerx.com (www.neomerx.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;

/**
 * This class is a wrapper for DBAL Connection which is intended to be used in tests.
 * It can turn the connection into a mode when all changes to database will be lost
 * when connection is closed (technically it opens transaction and prevents committing it).
 * It also provides 'capturing' connection from application. It could be used to check
 * changes in the database before they lost on connection close.
 *
 * How to use
 * In a test call `setPreventCommits` method and when test finishes don't forget to call `reset` (it is
 * safe to call this method after every test in `tearDown` method). Between those two calls a method
 * `getCapturedConnection` could be called to get connection used by the app.
 *
 * @package Limoncello\Testing
 */
class TestingConnection extends Connection
{
    /**
     * @var bool
     */
    private static $isPreventCommits = false;

    /**
     * @var Connection|null
     */
    private static $appConnection = null;

    /**
     * @var bool
     */
    private $isOpeningTransaction = false;

    /**
     * @inheritdoc
     */
    public function __construct(array $params, Driver $driver, $config, $eventManager)
    {
        // Doctrine does not copy transaction level if existing connection is used. For testing purposes
        // it creates problems. If we have made changes in test and new connection is created from existing
        // one then transaction level info is lost and we cannot control which `commit` calls we should
        // allow and which not. Our hack would be the following: if current connection should prevent final
        // committing to database and new connection is created we remember transaction level and then restore
        // it for new connection with `beginTransaction`.
        $requiredTransLevel = null;

        // If commits should be prevented we will use persistent connection so multiple queries in 1 test will
        // use the same connection to database which simulates changes were actually saved within the test.
        if (self::$isPreventCommits === true && self::$appConnection !== null) {
            // if current transaction preventing commits and new connection is opened remember transaction level
            if (self::$appConnection->isTransactionActive() === true) {
                $requiredTransLevel = self::$appConnection->getTransactionNestingLevel();
            }

            $params['pdo'] = self::$appConnection;
        }

        parent::__construct($params, $driver, $config, $eventManager);

        // restore transaction level
        if ($requiredTransLevel !== null) {
            for ($level = 0; $level < $requiredTransLevel; $level++) {
                $this->beginTransaction();
            }
        }
    }

    /**
     * @return void
     */
    public static function setPreventCommits()
    {
        self::$isPreventCommits = true;
    }

    /**
     * @return Connection|null
     */
    public static function getCapturedConnection()
    {
        return self::$appConnection;
    }

    /**
     * @return void
     */
    public static function reset()
    {
        if (self::$isPreventCommits === true &&
            static::$appConnection !== null &&
            static::$appConnection->isTransactionActive() === true
        ) {
            static::$appConnection->rollBack();
        }
        if (static::$appConnection !== null) {
            static::$appConnection->close();
        }

        static::$appConnection  = null;
        self::$isPreventCommits = false;
    }

    /**
     * @inheritdoc
     */
    public function connect()
    {
        $isNewConnection = parent::connect();

        if ($isNewConnection === true) {
            if (self::$appConnection !== null) {
                // We already have opened connection and user has opened another one. If we continue
                // with two opened connections during transaction the current test will hang.
                // Therefore we have to close previous connection.
                // Actually it's a warning sign that user might do something wrong.
                static::reset();
            }

            self::$appConnection = $this;

            if (self::$isPreventCommits === true && $this->isOpeningTransaction === false) {
                try {
                    $this->isOpeningTransaction = true;
                    $this->beginTransaction();
                } finally {
                    $this->isOpeningTransaction = false;
                }
            }
        }

        return $isNewConnection;
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        if ($this->getTransactionNestingLevel() === 1 && self::$isPreventCommits === true) {
            // this should never happen if user does everything right
            // if we are here then on the next step it will be an exception
            $this->setRollbackOnly();
        }

        parent::commit();
    }
}
