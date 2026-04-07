<?php

namespace Rubricate\Agent;

interface IBrowserAgent
{
    public function getBrowser();
    public function getVersion();
    public function getLanguages();
    public function acceptLang($lang = 'en');
}
