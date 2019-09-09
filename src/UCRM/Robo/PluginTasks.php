<?php
declare(strict_types=1);

namespace UCMR\Robo;

use UCRM\Robo\Tasks\SftpTasks;

trait PluginTasks
{
    /**
     * @param string $host  The hostname of the remote server.
     * @param int $port     The port to use when connecting to the remote server, defaults to 22.
     *
     * @return SftpTasks
     */
    protected function taskSftp(string $host, int $port)
    {
        // Always construct your tasks with the task builder.
        return $this->task(SftpTasks::class, $host, $port);
    }

    // ...
}