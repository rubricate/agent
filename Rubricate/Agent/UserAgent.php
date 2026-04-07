<?php

namespace Rubricate\Agent;

class UserAgent
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

        if ($config) {
            $this->initializeConfig($config);
        }

        if ($this->agent) {
            $this->compileData();
        }
    }

    private function initializeConfig(array $config)
    {
        $this->platforms = isset($config['platforms']) ? $config['platforms'] : [];
        $this->browsers = isset($config['browsers']) ? $config['browsers'] : [];
        $this->mobiles = isset($config['mobiles']) ? $config['mobiles'] : [];
        $this->robots = isset($config['robots']) ? $config['robots'] : [];
    }

    private function compileData()
    {
        $this->setPlatform();

        $methods = ['setRobot', 'setBrowser', 'setMobile'];

        foreach ($methods as $method) {
            if ($this->$method()) {
                break;
            }
        }
    }

    private function setPlatform()
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

    private function setBrowser()
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

    private function setRobot()
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

    private function setMobile()
    {
        if (is_array($this->mobiles) && count($this->mobiles) > 0) {
            foreach ($this->mobiles as $key => $val) {
                if (false !== (strpos(strtolower($this->agent), $key))) {
                    $this->isMobile = true;
                    $this->mobile = $val;
                    return true;
                }
            }
        }

        return false;
    }

    public function isBrowser($key = null)
    {
        if (!$this->isBrowser) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return array_key_exists($key, $this->browsers) && $this->browser === $this->browsers[$key];
    }

    public function isRobot($key = null)
    {
        if (!$this->isRobot) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return array_key_exists($key, $this->robots) && $this->robot === $this->robots[$key];
    }

    public function isMobile($key = null)
    {
        if (!$this->isMobile) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return array_key_exists($key, $this->mobiles) && $this->mobile === $this->mobiles[$key];
    }

    public function isReferral()
    {
        return !empty($_SERVER['HTTP_REFERER']);
    }

    public function getAgentString()
    {
        return $this->agent;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getRobot()
    {
        return $this->robot;
    }

    public function getMobile()
    {
        return $this->mobile;
    }

    public function getReferrer()
    {
        return empty($_SERVER['HTTP_REFERER']) ? '' : trim($_SERVER['HTTP_REFERER']);
    }

    public function getLanguages()
    {
        if (count($this->languages) === 0) {
            $this->setLanguages();
        }

        return $this->languages;
    }

    private function setLanguages()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && $_SERVER['HTTP_ACCEPT_LANGUAGE'] !== '') {
            $languages = preg_replace('/(;q=[0-9\.]+)/i', '', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])));
            $this->languages = explode(',', $languages);
        }

        if (count($this->languages) === 0) {
            $this->languages = ['undefined'];
        }
    }

    public function acceptLang($lang = 'en')
    {
        return in_array(strtolower($lang), $this->getLanguages(), true);
    }
}

