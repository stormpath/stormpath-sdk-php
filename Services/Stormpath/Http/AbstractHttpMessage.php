<?php

abstract class Services_Stormpath_Http_AbstractHttpMessage implements Services_Stormpath_Http_HttpMessage
{

    public function hasBody() {

        $body = getBody();
        return $body != null && strlen($body) != 0;

    }

}
