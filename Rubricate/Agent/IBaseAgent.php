<?php

namespace Rubricate\Agent;

interface IBaseAgent
{
    public function getAgentString(): ?string;
    public function getPlatform(): string;
}
