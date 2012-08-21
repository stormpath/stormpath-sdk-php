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
interface Services_Stormpath_DataStore_DataStore
{
    /**
     * Instantiates and returns a new instance of the specified Resource type name.  The instance is merely instantiated
     * and is not saved/synchronized with the server in any way.
     * <p/>
     * This method effectively replaces the {@code new} keyword that would have been used otherwise if the concrete
     * implementation was known (Resource implementation classes are intentionally not exposed to SDK end-users).
     *
     * @param $className the Resource class name (as a String) to instantiate. This can be the fully qualified name or the
     * simple name of the Resource (which is also the simple name of the .php file).
     * @param $properties the optional Properties of the Resource to instantiate.
     *
     * @return a new instance of the specified Resource.
     */
    public function instantiate($className, stdClass $properties = null);

    /**
     * Looks up (retrieves) the resource at the specified {@code href} URL and returns the resource as an instance
     * of the specified {@code class} name.
     * <p/>
     * The {@code $className} argument must represent the name of an interface that is a sub-interface of
     * {@code Resource}, for example {@code 'Services_Stormpath_Resource_Account'},
     * {@code 'Services_Stormpath_Resource_Directory'}, etc.
     *
     * @param href  the resource URL of the resource to retrieve
     * @param class the {@code Resource} sub-interface to instantiate. This can be the fully qualified name or the
     * simple name of the Resource (which is also the simple name of the .php file).
     * @return an instance of the specified class based on the data returned from the specified {@code href} URL.
     */
    public function  getResource($href, $className);

}