<?php

/*
 * Copyright 2012 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
class Services_Stormpath_Http_DefaultResponse
    extends Services_Stormpath_Http_AbstractHttpMessage
    implements Services_Stormpath_Http_Response {

    private $httpStatus;
    private $headers;
    private $body;
    private $contentType;
    private $contentLength;

    public function __construct($httpStatus, $contentType, $body, $contentLength)
    {
        $this->body = $body;
        $this->contentLength = $contentLength;
        $this->contentType = $contentType;
        $this->headers = array();
        $this->httpStatus = $httpStatus;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getContentLength()
    {
        return $this->contentLength;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function isError()
    {
        return $this->isServerError() || $this->isClientError();
    }

    public function  isServerError()
    {
        $status = $this->getHttpStatus();
        return ($status >= 500 && $status < 600);
    }

    public function isClientError()
    {
        $status = $this->getHttpStatus();
        return ($status >= 400 && $status < 500);
    }
}