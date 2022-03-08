<?php

namespace HelloNico\Brotli;

use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Process extends \Symfony\Component\Process\Process
{
    private static $sigchild;

    /**
     * Returns whether PHP has been compiled with the '--enable-sigchild' option or not.
     *
     * @return bool
     */
    protected function isSigchildEnabled()
    {
        if (null !== self::$sigchild) {
            return self::$sigchild;
        }

        if (!\function_exists('phpinfo')) {
            return self::$sigchild = false;
        }

        $process = new PhpProcess(<<<EOF
            <?php phpinfo(\INFO_GENERAL); ?>
        EOF
    );

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            throw $exception;
        }

        return self::$sigchild = false !== strpos($process->getOutput(), '--enable-sigchild');
    }
}
