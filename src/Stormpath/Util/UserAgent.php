<?php

namespace Stormpath\Util;

class UserAgent {

    private $agentArray = array();

    private $userAgent;

    public function add($key, $value)
    {
        $this->agentArray[$key] = $value;

        return $this;
    }

    public function getUserAgent()
    {
        $this->buildUserAgent();

        return $this->userAgent;
    }

    private function buildUserAgent()
    {
        $userAgent = array();
        foreach($this->agentArray as $k=>$v) {
            $userAgent[] .= $k . '/' . $v;
        }

        $this->userAgent = implode(' ', $userAgent);

    }


}