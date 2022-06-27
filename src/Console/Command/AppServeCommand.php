<?php

/**
 * Ping Boot
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * Json RPC://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phpsuit.net so we can send you a copy immediately.
 *
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

declare(strict_types=1);

namespace Pingframework\Boot\Console\Command;

use Pingframework\Boot\Annotations\Command;
use Pingframework\Boot\Application\HttpServerApplication;
use Pingframework\Ping\Annotations\Inject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Command('app:serve', 'Run http server')]
class AppServeCommand extends AbstractCommand
{
    #[Inject]
    private readonly HttpServerApplication $serverApplication;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->addOption('host', '-H', InputOption::VALUE_OPTIONAL, 'Bind host');
        $this->addOption('port', '-p', InputOption::VALUE_OPTIONAL, 'Bind port');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logo = <<<EOL
<info>
                ╭━━━╮
                ┃╭━╮┃
                ┃╰━╯┣┳━╮╭━━╮
                ┃╭━━╋┫╭╮┫╭╮┃
                ┃┃╱╱┃┃┃┃┃╰╯┃
                ╰╯╱╱╰┻╯╰┻━╮┃
                ╱╱╱╱╱╱╱╱╭━╯┃
                ╱╱╱╱╱╱╱╱╰━━╯
                ╭━━╮╱╱╱╱╱╱╱╱╱╱╱╱╭╮
                ┃╭╮┃╱╱╱╱╱╱╱╱╱╱╱╭╯╰╮
                ┃╰╯╰┳━━┳━━┳━━┳━┻╮╭╯
                ┃╭━╮┃╭╮┃╭╮┃╭╮┃╭╮┃┃
                ┃╰━╯┃╰╯┃╰╯┃╰╯┃╰╯┃╰╮
                ╰━━━┻━━┻━━┻━━┻━━┻━╯
</info>
EOL;

        $host = $input->getOption('host');
        $port = $input->getOption('port');
        if ($port !== null) {
            $port = (int)$port;
        }

        $config = $this->serverApplication->getConfig($host, $port);

        $output->writeln($logo);

        $output->writeln("========================================================");
        $output->write(["<options=bold>Number of reactors: </>", $config['reactor_num'] ?? 'auto', "\n"]);
        $output->writeln("--------------------------------------------------------");
        $output->writeln("<comment>The number of the reactor threads to start. A reactor thread is a process that handles event processing in the main program, allows you to make use of multi-core performance. This option is enabled by default and each reactor can maintain its own event loop and there is no blocking between each event loop, they run in parallel.</comment>");
        $output->writeln("========================================================");
        $output->write(["<options=bold>Number of workers: </>", $config['worker_num'] ?? 'auto', "\n"]);
        $output->writeln("--------------------------------------------------------");
        $output->writeln("<comment>The number of worker processes to start. By default this is set to the number of CPU cores you have.</comment>");
        $output->writeln("========================================================");
        $output->write(["<options=bold>Max requests: </>", $config['max_request'] ?? 'auto', "\n"]);
        $output->writeln("--------------------------------------------------------");
        $output->writeln("<comment>The default value of max_request is 0 which means there is no limit of the max request. If the max_request is set to some number, the worker process will exit and release all the memory and resource occupied by this process after receiving the max_request request. And then, the manager will respawn a new worker process to replace it.</comment>");
        $output->writeln("========================================================");
        $output->write(["<options=bold>Max requests grace: </>", $config['max_request_grace'] ?? 'auto', "\n"]);
        $output->writeln("--------------------------------------------------------");
        $output->writeln("<comment>A worker process is restarted to avoid memory leak when receiving and the condition of max_request + rand(0, max_request_grace) is met. You can disable the rand function by setting max_request_grace = 0. Refer to max_request to see how this option works together.</comment>");
        $output->writeln("========================================================");
        $output->write(["<options=bold>File upload tmp dir: </>", $config['upload_tmp_dir'] ?? 'auto', "\n"]);
        $output->writeln("--------------------------------------------------------");
        $output->writeln("<comment>Temp directory to save uploaded files to. Default is /tmp.</comment>");
        $output->writeln("========================================================");
        $output->write(["<options=bold>Listening on host: </>", $host, "\n"]);
        $output->write(["<options=bold>Listening on port: </>", $port, "\n"]);
        $output->writeln("========================================================");

        $this->serverApplication->listen($host, $port);

        return self::SUCCESS;
    }
}