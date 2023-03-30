<?php

declare(strict_types=1);

namespace Whirlwind\App\Console;

abstract class Command implements CommandInterface
{
    // foreground color control codes
    public const FG_BLACK = 30;
    public const FG_RED = 31;
    public const FG_GREEN = 32;
    public const FG_YELLOW = 33;
    public const FG_BLUE = 34;
    public const FG_PURPLE = 35;
    public const FG_CYAN = 36;
    public const FG_GREY = 37;
    // background color control codes
    public const BG_BLACK = 40;
    public const BG_RED = 41;
    public const BG_GREEN = 42;
    public const BG_YELLOW = 43;
    public const BG_BLUE = 44;
    public const BG_PURPLE = 45;
    public const BG_CYAN = 46;
    public const BG_GREY = 47;
    // fonts style control codes
    public const RESET = 0;
    public const NORMAL = 0;
    public const BOLD = 1;
    public const ITALIC = 3;
    public const UNDERLINE = 4;
    public const BLINK = 5;
    public const NEGATIVE = 7;
    public const CONCEALED = 8;
    public const CROSSED_OUT = 9;
    public const FRAMED = 51;
    public const ENCIRCLED = 52;
    public const OVERLINED = 53;

    public function __construct()
    {
        \defined('STDIN') or \define('STDIN', \fopen('php://stdin', 'r'));
        \defined('STDOUT') or \define('STDOUT', \fopen('php://stdout', 'w'));
        \defined('STDERR') or \define('STDERR', \fopen('php://stderr', 'w'));
    }

    protected function output(string $string, array $format = []): void
    {
        \fwrite(\STDOUT, $this->formatString($string, $format) . PHP_EOL);
    }

    private function formatString(string $string, array $format = []): string
    {
        $code = \implode(';', $format);

        return "\033[0m" . ($code !== '' ? "\033[" . $code . 'm' : '') . $string . "\033[0m";
    }

    protected function error(string $string): void
    {
        $this->stderr($this->formatString($string, [self::FG_RED]) . PHP_EOL);
    }

    protected function stderr(string $string): void
    {
        \fwrite(\STDERR, $string);
    }

    protected function info(string $string): void
    {
        $this->output($string, [self::FG_BLUE]);
    }

    protected function warning(string $string): void
    {
        $this->output($string, [self::FG_YELLOW]);
    }

    protected function comment(string $string): void
    {
        $this->output($string, [self::FG_GREY]);
    }

    protected function success(string $string): void
    {
        $this->output($string, [self::FG_GREEN]);
    }

    protected function confirm(string $message, $default = false)
    {
        while (true) {
            $this->stdout($message . ' (yes|no) [' . ($default ? 'yes' : 'no') . ']:');
            $input = \trim($this->stdin());

            if (empty($input)) {
                return $default;
            }

            if (!\strcasecmp($input, 'y') || !\strcasecmp($input, 'yes')) {
                return true;
            }

            if (!\strcasecmp($input, 'n') || !\strcasecmp($input, 'no')) {
                return false;
            }
        }
    }

    protected function stdout(string $message): void
    {
        \fwrite(\STDOUT, $message);
    }

    protected function stdin(bool $raw = false)
    {
        return $raw ? \fgets(\STDIN) : \rtrim(\fgets(\STDIN), PHP_EOL);
    }

    protected function input(?string $prompt = null)
    {
        if (isset($prompt)) {
            $this->stdout($prompt);
        }

        return $this->stdin();
    }
}
