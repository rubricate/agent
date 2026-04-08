<?php

namespace Rubricate\Agent;

interface IDetectionAgent
{
    public function isBrowser(?string $key = null): bool;
    public function isRobot(?string $key = null): bool;
    public function isMobile(?string $key = null): bool;
    public function isReferral(): bool;
}
