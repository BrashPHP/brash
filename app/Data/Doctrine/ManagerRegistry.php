<?php declare(strict_types=1);

namespace Core\Data\Doctrine;

use Core\Data\Doctrine\EntityManagerBuilder;
use Core\Exceptions\ConfigException;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Internal\Hydration\ArrayHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\AbstractManagerRegistry;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Doctrine\Persistence\Proxy;

use function key;
use function reset;
use function sprintf;

/**
 * ManagerRegistry
 */
class ManagerRegistry extends AbstractManagerRegistry
{

    /**
     * @var \Closure[]
     */
    private $factories = [];

    /**
     * @var object[]
     */
    private $services = [];

    public function __construct(private array $doctrineParams, private LoggerInterface $loggerInterface)
    {
        $connectionName = 'default';
        $managerName = 'default';
        $connections = [];
        $managers = [];


        $connections[$connectionName] = $connectionName;
        $managers[$managerName] = $managerName;

        $this->factories[$connectionName] = function () use ($connectionName) {
            return $this->getManager($connectionName)->getConnection();
        };

        $this->factories[$managerName] = function () {
            return $this->createManager();
        };


        reset($connections);
        reset($managers);

        parent::__construct('ORM', $connections, $managers, key($connections), key($managers), Proxy::class);

        $this->registerUuidType();
    }

    /**
     * Re-opens all closed managers
     *
     * It will be useful for long-running applications.
     * When to use it? When you expect your repositories to break with doctrine possibly closing connection.
     *
     * @return void
     */
    public function reopenManagers(): void
    {
        foreach ($this->getManagers() as $name => $manager) {
            $manager->isOpen() || $this->resetManager($name);
        }
    }

    /**
     * Clears all managers
     *
     * It will be useful for long-running applications.
     *
     * @return void
     */
    public function clearManagers(): void
    {
        foreach ($this->getManagers() as $manager) {
            $manager->clear();
        }
    }

    /**
     * Closes all connections
     *
     * It will be useful for long-running applications.
     *
     * @return void
     */
    public function closeConnections(): void
    {
        foreach ($this->getConnections() as $connection) {
            $connection->close();
        }
    }

    /**
     * Gets an array hydrator
     *
     * @param string $name
     *
     * @return ArrayHydrator
     *
     * @link https://github.com/pmill/doctrine-array-hydrator
     */
    public function getHydrator(string $name = null): ArrayHydrator
    {
        return new ArrayHydrator($this->getManager($name));
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException If the registry doesn't contain the named service.
     */
    protected function getService($name)
    {
        if (isset($this->services[$name]) && $this->services[$name]->isOpen()) {
            return $this->services[$name];
        }

        if (!isset($this->factories[$name])) {
            throw new ORMInvalidArgumentException(
                sprintf('Doctrine Manager registry does not contain named service "%s"', $name)
            );
        }

        $this->loggerInterface->info("Start new EntityManager and Connection");

        $this->services[$name] = $this->factories[$name]();

        return $this->services[$name];
    }

    /**
     * {@inheritDoc}
     */
    protected function resetService($name)
    {
        unset($this->services[$name]);
    }


    private function createManager(): EntityManagerInterface
    {
        return EntityManagerBuilder::produce($this->doctrineParams);
    }

    /**
     * @return void
     */
    private function registerUuidType(): void
    {
        Type::hasType(UuidType::NAME) ?
            Type::overrideType(UuidType::NAME, UuidType::class) :
            Type::addType(UuidType::NAME, UuidType::class);

        Type::hasType(UuidBinaryType::NAME) ?
            Type::overrideType(UuidBinaryType::NAME, UuidBinaryType::class) :
            Type::addType(UuidBinaryType::NAME, UuidBinaryType::class);

        Type::hasType(UuidBinaryOrderedTimeType::NAME) ?
            Type::overrideType(UuidBinaryOrderedTimeType::NAME, UuidBinaryOrderedTimeType::class) :
            Type::addType(UuidBinaryOrderedTimeType::NAME, UuidBinaryOrderedTimeType::class);
    }
}
