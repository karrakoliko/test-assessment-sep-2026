<?php

namespace Tests\Integration\Core\Persistence\Database\Resetter;

use App\Core\Persistence\Database\Resetter\DatabaseResetterInterface;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\TestCaseIntegration;

class DatabaseResetterTest extends TestCaseIntegration
{
    #[Test]
    public function resetClearsDataAndRecreatesSchema(): void
    {
        /** @var Connection $connection */
        $connection = self::$container->get(Connection::class);

        // Create a temporary table that is not part of the standard schema
        $connection->executeStatement('CREATE TABLE temp_reset_test (id INT PRIMARY KEY, name VARCHAR(255))');
        $connection->insert('temp_reset_test', [
            'id' => 1,
            'name' => 'Test Reset',
        ]);

        self::assertCount(1, $connection->fetchAllAssociative('SELECT * FROM temp_reset_test'));

        /** @var DatabaseResetterInterface $resetter */
        $resetter = self::$container->get(DatabaseResetterInterface::class);
        $resetter->reset();

        $schemaManager = $connection->createSchemaManager();

        // Verify the temporary table was dropped and the standard schema was recreated
        self::assertFalse($schemaManager->tablesExist(['temp_reset_test']));
        self::assertTrue($schemaManager->tablesExist(['categories', 'articles']));
        self::assertCount(0, $connection->fetchAllAssociative('SELECT * FROM categories'));
    }
}
