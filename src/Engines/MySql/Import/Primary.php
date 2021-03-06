<?php
/**
 * SwiftOtter_Base is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SwiftOtter_Base is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with SwiftOtter_Base. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Joseph Maxwell
 * @copyright SwiftOtter Studios, 12/3/16
 * @package default
 **/

namespace Driver\Engines\MySql\Import;

use Driver\Commands\CleanupInterface;
use Driver\Commands\CommandInterface;
use Driver\Pipeline\Environment\EnvironmentInterface;
use Driver\Pipeline\Transport\Status;
use Driver\Pipeline\Transport\TransportInterface;
use Driver\System\Configuration;
use Driver\System\LocalConnectionLoader;
use Driver\System\Logs\LoggerInterface;
use Driver\System\Random;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Primary extends Command implements CommandInterface
{
    /** @var LocalConnectionLoader */
    private $localConnection;

    /** @var array */
    private $properties;

    /** @var LoggerInterface */
    private $logger;

    /** @var Random */
    private $random;

    /** @var ?string */
    private $path;

    /** @var Configuration */
    private $configuration;

    /** @var ConsoleOutput */
    private $output;

    const DEFAULT_DUMP_PATH = '/tmp';

    public function __construct(
        LocalConnectionLoader $localConnection,
        Configuration $configuration,
        LoggerInterface $logger,
        Random $random,
        ConsoleOutput $output,
        array $properties = []
    ) {
        $this->localConnection = $localConnection;
        $this->properties = $properties;
        $this->logger = $logger;
        $this->random = $random;
        $this->configuration = $configuration;
        $this->output = $output;
        return parent::__construct('import-data-from-system-primary');
    }

    public function go(TransportInterface $transport, EnvironmentInterface $environment)
    {
        $transport->getLogger()->notice("Import database from var/downloaded into local MySql started");
        $this->output->writeln("<comment>Import database from var/downloaded into local MySql started</comment>");

        $transport->getLogger()->debug(
            "Local connection string: " . str_replace(
                $this->localConnection->getPassword(),
                '',
                $this->assembleCommand($environment)
            )
        );
        $this->output->writeln("<comment>Local connection string: </comment>" . str_replace(
                $this->localConnection->getPassword(),
                '',
                $this->assembleCommand($environment)
            )
        );

        $results = null;
        $command = $this->assembleCommand($environment);

        $results = system($command);

        if ($results) {
            $this->output->writeln('<error>Import to local MYSQL failed: ' . $results . '</error>');
            throw new \Exception('Import to local MYSQL failed: ' . $results);
        }
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function assembleCommand(EnvironmentInterface $environment)
    {
        return implode(' ', $this->getImportCommand($environment));
    }

    private function getImportCommand(EnvironmentInterface $environment)
    {
        $date = date('Y-m-d');
        return [
            "mysql -u \"{$this->localConnection->getUser()}\"",
            "-h {$this->localConnection->getHost()}",
            "-p",
            "{$this->localConnection->getDatabase()}-".$date,
            "<",
            "var/downloaded/{$this->localConnection->getDatabase()}-".$date.".sql"
        ];
    }
}
