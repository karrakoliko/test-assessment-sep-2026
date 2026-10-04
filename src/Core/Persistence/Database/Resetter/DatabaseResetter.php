<?php

namespace App\Core\Persistence\Database\Resetter;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use Doctrine\DBAL\Tools\DsnParser;

readonly class DatabaseResetter implements DatabaseResetterInterface
{
    /**
     * @phpstan-type array{dbname: string}
     */
    private array $connectionParams;

    public function __construct(
        #[\SensitiveParameter]
        private string $dbUrl,
        private string $initSqlFilePath,
        private int $maxRetries = 60,
        private int $retryDelayMs = 500
    ) {

        if (!file_exists($this->initSqlFilePath)) {
            throw new \RuntimeException(
                sprintf('Unable to read database initialization SQL: %s', $this->initSqlFilePath)
            );
        }

        $this->connectionParams = (new DsnParser())->parse($this->dbUrl);

    }

    public function reset(): void
    {
        $this->recreateDatabase();
        $this->initializeSchema();
    }

    protected function recreateDatabase(): void
    {
        $connectionParamsWithoutDbName = $this->connectionParams;
        unset($connectionParamsWithoutDbName['dbname']);

        for ($attempt = 0; $attempt <= $this->maxRetries; ++$attempt) {

            try {
                $connection = DriverManager::getConnection($connectionParamsWithoutDbName);
                $schema = $connection->createSchemaManager();

                try {
                    $schema->dropDatabase($this->connectionParams['dbname']);
                } catch (DatabaseDoesNotExist) {
                    // database may not exist, and it's ok
                }

                $schema->createDatabase($this->connectionParams['dbname']);
                $connection->close();

                return;
            } catch (\Throwable $e) {
                if ($attempt >= $this->maxRetries) {
                    throw $e;
                }

                usleep($this->retryDelayMs * 1_000);
            }
        }
    }

    protected function initializeSchema(): void
    {
        $connection = DriverManager::getConnection($this->connectionParams);
        try {
            $connection->executeStatement(file_get_contents($this->initSqlFilePath));
        } finally {
            $connection->close();
        }
    }
}
