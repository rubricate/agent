<?php

namespace Rubricate\Agent;

class UserAgent implements IBaseAgent, IDetectionAgent, IBrowserAgent
{
    private $agent = null;
    private $isBrowser = false;
    private $isRobot = false;
    private $isMobile = false;

    private $languages = [];
    private $charsets = [];

    private $platforms = [];
    private $browsers = [];
    private $mobiles = [];
    private $robots = [];

    private $platform = 'unknown platform';
    private $browser = '';
    private $version = '';
    private $mobile = '';
    private $robot = '';

    public function __construct(array $config = [])
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->agent = trim($_SERVER['HTTP_USER_AGENT']);
        }

        if (!empty($config)) {
            $this->initializeConfig($config);
        }

        if ($this->agent) {
            $this->compileData();
        }
    }

    private function initializeConfig(array $config): void
    {
        $this->platforms = $config['platforms'] ?? [];
        $this->browsers  = $config['browsers'] ?? [];
        $this->mobiles   = $config['mobiles'] ?? [];
        $this->robots    = $config['robots'] ?? [];
    }

    private function compileData(): void
    {
        $this->setPlatform();

        $methods = ['setRobot', 'setBrowser', 'setMobile'];

        foreach ($methods as $method) {
            if ($this->$method()) {
                break;
            }
        }
    }

    private function setPlatform(): bool
    {
        if (is_array($this->platforms) && count($this->platforms) > 0) {
            foreach ($this->platforms as $key => $val) {
                if (preg_match('|' . preg_quote($key) . '|i', $this->agent)) {
                    $this->platform = $val;
                    return true;
                }
            }
        }

        return false;
    }

    private function setBrowser(): bool
    {
        if (is_array($this->browsers) && count($this->browsers) > 0) {
            foreach ($this->browsers as $key => $val) {
                if (preg_match('|' . preg_quote($key) . '.*?([0-9\.]+)|i', $this->agent, $match)) {
                    $this->isBrowser = true;
                    $this->version = $match[1];
                    $this->browser = $val;
                    $this->setMobile();
                    return true;
                }
            }
        }

        return false;
    }

    private function setRobot(): bool
    {
        if (is_array($this->robots) && count($this->robots) > 0) {
            foreach ($this->robots as $key => $val) {
                if (preg_match('|' . preg_quote($key) . '|i', $this->agent)) {
                    $this->isRobot = true;
                    $this->robot = $val;
                    return true;
                }
            }
        }

        return false;
    }

    private function setMobile(): bool
    {
        if (is_array($this->mobiles) && count($this->mobiles) > 0) {
            foreach ($this->mobiles as $key => $val) {
                if (false !== (strpos(strtolower($this->agent), (string) $key))) {
                    $this->isMobile = true;
                    $this->mobile = $val;
                    return true;
                }
            }
        }

        return false;
    }

    public function isBrowser($key = null): bool
    {
        if (!$this->isBrowser) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return array_key_exists($key, $this->browsers) && $this->browser === $this->browsers[$key];
    }

    public function isRobot($key = null): bool
    {
        if (!$this->isRobot) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return array_key_exists($key, $this->robots) && $this->robot === $this->robots[$key];
    }

    public function isMobile($key = null): bool
    {
        if (!$this->isMobile) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return array_key_exists($key, $this->mobiles) && $this->mobile === $this->mobiles[$key];
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
        return empty($_SERVER['HTTP_REFERER']) ? '' : trim($_SERVER['HTTP_REFERER']);
    }

    public function getLanguages(): array
    {
        if (count($this->languages) === 0) {
            $this->setLanguages();
        }

        return $this->languages;
    }

    private function setLanguages(): void
    {
        $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';

        if ($acceptLang !== '') {
            $languages = preg_replace('/(;q=[0-9\.]+)/i', '', strtolower(trim($acceptLang)));
            $this->languages = explode(',', $languages);
        }

        if (count($this->languages) === 0) {
            $this->languages = ['undefined'];
        }
    }

    public function acceptLang(string $lang = 'en'): bool
    {
        return in_array(strtolower($lang), $this->getLanguages(), true);
    }
}
