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

/**
 * A ClientApplication is a simple wrapper around a {@link Services_Stormpath_Client_Client} and
 * {@link Services_Stormpath_Resource_Application} instance, returned from
 * the {@code Services_Stormpath_Client_ClientApplicationBuilder}.
 * {@link Services_Stormpath_Client_ClientApplicationBuilder#build()}
 * method.
 *
 * @since 0.2.0
 */
class Services_Stormpath_Client_ClientApplication
{
    private $client;
    private $application;

    public function __construct(Services_Stormpath_Client_Client $client,
                                Services_Stormpath_Resource_Application $application)
    {
        $this->client = $client;
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function getClient()
    {
        return $this->client;
    }
}
