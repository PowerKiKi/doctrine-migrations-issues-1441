<?php

declare(strict_types=1);

namespace ApplicationTest;

use Application\Cases;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class AllTest extends TestCase
{
    static public function dataProvider(): iterable
    {
        yield 'automated enums based on PHP enum will incorrectly always update one of the two fields' => [
            'src/Cases/WithAutomatedEnum/Model',
            [
                'UserRole' => Cases\WithAutomatedEnum\Type\UserRole::class,
                'UserStatus' => Cases\WithAutomatedEnum\Type\UserStatus::class,
            ],
        ];

        yield 'property mapping is kept in sync, but it is not enum in DB' => [
            'src/Cases/PropertyMapping/Model',
            [],
        ];

        yield 'cookbook1 is worse syntax (more verbose, PHP type unsafe) to have same failing result as property mapping' => [
            'src/Cases/Cookbook1/Model',
            [],
        ];

        yield 'cookbook2 is not scalable and also fail like automated enums anyway' => [
            'src/Cases/Cookbook2/Model',
            [
                'UserRole' => Cases\Cookbook2\Type\UserRole::class,
                'UserStatus' => Cases\Cookbook2\Type\UserStatus::class,
            ],
        ];

        yield 'cookbook1bis does not really support PHP enum an always update anyway' => [
            'src/Cases/Cookbook1bis/Model',
            [],
            ['enum' => 'string']
        ];
    }

    #[DataProvider('dataProvider')]
    public function test(string $path, array $types, array $typeMapping = []): void
    {
        $em = $this->createEntityManager($path, $types, $typeMapping);

        $em->getConnection()->executeStatement('DROP TABLE IF EXISTS User');

        $this->assertMappingIsValid($em);
        $this->updateSchema($em);

        $this->assertMappingIsSync($em);
        $this->assertDatabase($em);
    }

    private function createEntityManager(string $path, array $types, array $typeMapping): EntityManager
    {
        $config = ORMSetup::createAttributeMetadataConfiguration([$path], true);

        $connection = DriverManager::getConnection([
            'driverClass' => Driver::class,
            'host' => '127.0.0.1',
            'dbname' => 'test',
            'user' => 'test',
            'password' => 'test',
        ], $config);

        $entityManager = new EntityManager($connection, $config);

        foreach ($types as $shortName => $type) {
            Type::addType($shortName, $type);
        }

        $platform = $entityManager->getConnection()->getDatabasePlatform();
        foreach ($typeMapping as $name => $type) {
            $platform->registerDoctrineTypeMapping($name, $type);
        }

        return $entityManager;
    }

    private function assertMappingIsValid(EntityManager $em): void
    {
        $validator = new SchemaValidator($em);

        $result = '';
        $errors = $validator->validateMapping();
        foreach ($errors as $className => $errorMessages) {
            $result .= $className . ':' . PHP_EOL;
            foreach ($errorMessages as $e) {
                $result .= $e . PHP_EOL;
            }
            $result .= PHP_EOL;
        }

        self::assertSame('', trim($result), 'should have valid mapping');
    }

    private function updateSchema(EntityManager $em): void
    {
        $validator = new SchemaValidator($em);
        foreach ($validator->getUpdateSchemaList() as $update) {
            $em->getConnection()->executeStatement($update);
        }
    }

    private function assertMappingIsSync(EntityManager $em): void
    {
        $validator = new SchemaValidator($em);
        self::assertSame([], $validator->getUpdateSchemaList(), 'database should be in sync with mapping');
    }

    private function assertDatabase(EntityManager $em): void
    {
        $fields = $em->getConnection()->fetchAllAssociative('DESCRIBE User;');

        $role = $fields[1];
        $status = $fields[2];

        self::assertSame("enum('visitor','member','admin')", $role['Type'], 'role should be enum in DB');
        self::assertSame("enum('new','active','archived')", $status['Type'], 'status should be enum in DB');
    }
}
