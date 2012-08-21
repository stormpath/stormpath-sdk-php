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

class Services_Stormpath_Resource_ResourceError extends RuntimeException
{
    private $error;

    public function __construct(Services_Stormpath_Resource_Error $error)
    {
        parent::__construct(($error->getMessage()) ? $error->getMessage() : '');
        $this->error = $error;
    }

    public function getStatus()
    {
        return $this->error ? $this->error->getStatus() : -1;
    }

    public function getErrorCode()
    {
        return $this->error ? $this->error->getCode() : -1;
    }

    public function getDeveloperMessage()
    {
        return $this->error ? $this->error->getDeveloperMessage() : null;
    }

    public function getMoreInfo()
    {
        return $this->error ? $this->error->getMoreInfo() : null;
    }
}
