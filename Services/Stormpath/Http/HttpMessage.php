<?php

interface Services_Stormpath_Http_HttpMessage {

    public function getHeaders();

    public function setHeaders(array $headers);

    public function hasBody();

    public function getBody();
}