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
 * Internal DataStore used for implementation purposes only.  Not intended to be called by SDK end users!
 * <p/>
 * <b>WARNING: This API CAN CHANGE AT ANY TIME, WITHOUT NOTICE.  DO NOT DEPEND ON IT.</b>
 *
 */

interface Services_Stormpath_DataStore_InternalDataStore
    extends Services_Stormpath_DataStore_DataStore
{

    public function create($parentHref,
                           Services_Stormpath_Resource_Resource $resource,
                           $returnType);

    public function save(Services_Stormpath_Resource_Resource $resource,
                         $returnType = null);

    public function delete(Services_Stormpath_Resource_Resource $resource);

}