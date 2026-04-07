<?php

namespace Rubricate\Agent;

interface IDetectionAgent
{
    public function isBrowser($key = null);
    public function isRobot($key = null);
    public function isMobile($key = null);
    public function isReferral();
}
