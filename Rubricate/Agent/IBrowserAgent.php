<?php

namespace Rubricate\Agent;

interface IBrowserAgent
{
    public function getBrowser(): string;
    public function getVersion(): string;
    public function getLanguages(): array;
    public function acceptLang(string $lang = 'en'): bool;
}
