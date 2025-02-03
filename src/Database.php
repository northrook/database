<?php

namespace Core;

use Doctrine\DBAL\{Connection, DriverManager, Exception\TableNotFoundException, Result};
use Psr\Log\LoggerInterface;
use RuntimeException;
use Exception;
use SensitiveParameter;

final class Database
{
    protected readonly Connection $connection;

    /**
     * @param LoggerInterface $logger
     * @param array{'driver': string ...}|string  $dsn
     */
    public function __construct(
        #[SensitiveParameter]
        string|array                       $dsn,
        protected readonly LoggerInterface $logger,
    ) {
        $this->connection = DriverManager::getConnection( $this->connectionParameters( $dsn ) );
        dump( \get_defined_vars() );
    }

    private function connectionParameters( string|array $parameters ) : array
    {
        if ( \is_array( $parameters ) ) {
            return $parameters;
        }

        $driver = \strstr( $parameters, ':', true );

        if ( \str_contains( $driver, 'sqlite' ) ) {
            [$prefix, $path] = \explode( ':', $parameters, 2 );

            $parameters = ['driver' => 'pdo_sqlite'];

            if ( 'memory:' === $path ) {
                return $parameters + ['memory' => true];
            }

            $location = \dirname( $path );

            if ( ! \file_exists( $location ) ) {
                \mkdir( $location, 0777, true );
                $this->logger->notice(
                    '{status} Created directory {location} for DSN {dsn}',
                    [
                        'location' => $location,
                        'dsn'      => $parameters,
                    ],
                );
            }

            $parameters['path'] = $path;

            return $parameters;
        }

        return $parameters ?? [];
    }

    /**
     * @param string      $table
     * @param null|string $connection
     *
     * @return void
     */
    public function get( string $table, ?string $connection = null ) : ?Result
    {
        return $this->getTable( $table );
    }

    public function getTable( string $table ) : ?Result
    {
        $query = $this->connection->createQueryBuilder();

        try {
            return $query->select( '*' )->from( $table )->executeQuery();
        }
        catch ( TableNotFoundException ) {
        }
        catch ( Exception $e ) {
            dump( $e );
        }

        return null;
    }

    /**
     * @return Connection
     */
    public function getConnection() : Connection
    {
        // $this->connection->
        return $this->connection;
    }

    /**
     * @param Exception $exception
     *
     * @return void
     */
    private function handleExceptions( Exception $exception ) : void
    {
        throw new RuntimeException(
            __CLASS__." encountered a critical error:\n".$exception->getMessage(),
            $exception->getCode(),
            $exception,
        );
    }
}
