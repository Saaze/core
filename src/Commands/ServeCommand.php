<?php

namespace Saaze\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends Command
{
    protected static $defaultName = 'serve';

    protected function configure()
    {
        $this->setDescription('Serve the Saaze site')
             ->addOption('host', 'H', InputOption::VALUE_REQUIRED, 'The host address to serve the application on', '127.0.0.1')
             ->addOption('port', 'p', InputOption::VALUE_REQUIRED, 'The port to serve the application on', 8000);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getOption('host');
        $port = $input->getOption('port');

        $output->writeln("<info>Web server started: http://{$host}:{$port}</info>");

        $command = sprintf(
            '%s -S %s:%s %s',
            $this->escapeArgument((new PhpExecutableFinder)->find(false)),
            $host,
            $port,
            SAAZE_BASE_DIR . '/public/index.php'
        );

        passthru($command, $status);

        return $status;
    }

    /**
     * Escapes a string to be used as a shell argument (kindly borrowed from Laravel).
     *
     * @param  string  $argument
     * @return string
     */
    public static function escapeArgument($argument)
    {
        // Fix for PHP bug #43784 escapeshellarg removes % from given string
        // Fix for PHP bug #49446 escapeshellarg doesn't work on Windows
        // @see https://bugs.php.net/bug.php?id=43784
        // @see https://bugs.php.net/bug.php?id=49446
        if ('\\' === DIRECTORY_SEPARATOR) {
            if ('' === $argument) {
                return '""';
            }

            $escapedArgument = '';
            $quote = false;

            foreach (preg_split('/(")/', $argument, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) as $part) {
                if ('"' === $part) {
                    $escapedArgument .= '\\"';
                } elseif (self::isSurroundedBy($part, '%')) {
                    // Avoid environment variable expansion
                    $escapedArgument .= '^%"'.substr($part, 1, -1).'"^%';
                } else {
                    // escape trailing backslash
                    if ('\\' === substr($part, -1)) {
                        $part .= '\\';
                    }
                    $quote = true;
                    $escapedArgument .= $part;
                }
            }

            if ($quote) {
                $escapedArgument = '"'.$escapedArgument.'"';
            }

            return $escapedArgument;
        }

        return "'".str_replace("'", "'\\''", $argument)."'";
    }
}
