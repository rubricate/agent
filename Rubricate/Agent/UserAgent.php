<?php

declare(strict_types=1);

namespace Rubricate\Agent;

readonly class UserAgent implements IBaseAgent, IDetectionAgent, IBrowserAgent
{
    private ?string $agent;
    private bool $isBrowser;
    private bool $isRobot;
    private bool $isMobile;

    private array $languages;
    private array $charsets;

    private array $platforms;
    private array $browsers;
    private array $mobiles;
    private array $robots;

    private string $platform;
    private string $browser;
    private string $version;
    private string $mobile;
    private string $robot;

    public function __construct(array $config = [])
    {
        $this->agent = isset($_SERVER['HTTP_USER_AGENT'])
            ? trim((string) $_SERVER['HTTP_USER_AGENT'])
            : null;

        $this->platforms = $config['platforms'] ?? [];
        $this->browsers  = $config['browsers'] ?? [];
        $this->mobiles   = $config['mobiles'] ?? [];
        $this->robots    = $config['robots'] ?? [];

        $this->languages = [];
        $this->charsets  = [];

        $platform = 'unknown platform';
        $browser  = '';
        $version  = '';
        $mobile   = '';
        $robot    = '';

        $isBrowser = false;
        $isRobot   = false;
        $isMobile  = false;

        if ($this->agent) {
            foreach ($this->platforms as $key => $val) {
                if (preg_match('|' . preg_quote((string) $key) . '|i', $this->agent)) {
                    $platform = (string) $val;
                    break;
                }
            }

            foreach ($this->robots as $key => $val) {
                if (preg_match('|' . preg_quote((string) $key) . '|i', $this->agent)) {
                    $isRobot = true;
                    $robot = (string) $val;
                    break;
                }
            }

            if (!$isRobot) {
                foreach ($this->browsers as $key => $val) {
                    if (preg_match('|' . preg_quote((string) $key) . '.*?([0-9\.]+)|i', $this->agent, $match)) {
                        $isBrowser = true;
                        $version = $match[1];
                        $browser = (string) $val;
                        break;
                    }
                }
            }

            foreach ($this->mobiles as $key => $val) {
                if (str_contains(strtolower($this->agent), strtolower((string) $key))) {
                    $isMobile = true;
                    $mobile = (string) $val;
                    break;
                }
            }
        }

        $this->platform  = $platform;
        $this->browser   = $browser;
        $this->version   = $version;
        $this->mobile    = $mobile;
        $this->robot     = $robot;
        $this->isBrowser = $isBrowser;
        $this->isRobot   = $isRobot;
        $this->isMobile  = $isMobile;
    }

    public function isBrowser(?string $key = null): bool
    {
        if (!$this->isBrowser) {
            return false;
        }
        return $key === null || (isset($this->browsers[$key]) && $this->browser === $this->browsers[$key]);
    }

    public function isRobot(?string $key = null): bool
    {
        if (!$this->isRobot) {
            return false;
        }
        return $key === null || (isset($this->robots[$key]) && $this->robot === $this->robots[$key]);
    }

    public function isMobile(?string $key = null): bool
    {
        if (!$this->isMobile) {
            return false;
        }
        return $key === null || (isset($this->mobiles[$key]) && $this->mobile === $this->mobiles[$key]);
    }

    public function isReferral(): bool
    {
        return !empty($_SERVER['HTTP_REFERER']);
    }

    public function getAgentString(): ?string
    {
        return $this->agent;
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function getBrowser(): string
    {
        return $this->browser;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getRobot(): string
    {
        return $this->robot;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function getReferrer(): string
    {
        return trim((string) ($_SERVER['HTTP_REFERER'] ?? ''));
    }

    public function getLanguages(): array
    {
        return $this->languages ?: $this->parseLanguages();
    }

    private function parseLanguages(): array
    {
        $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if ($acceptLang === '') {
            return ['undefined'];
        }
        $languages = preg_replace('/(;q=[0-9\.]+)/i', '', strtolower(trim($acceptLang)));
        return explode(',', $languages);
    }

    public function acceptLang(string $lang = 'en'): bool
    {
        return in_array(strtolower($lang), $this->getLanguages(), true);
    }
}
