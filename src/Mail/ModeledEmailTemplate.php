<?php
/*
 * Copyright 2016 Stormpath, Inc.
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

namespace Stormpath\Mail;

class ModeledEmailTemplate extends EmailTemplate
{
    const DEFAULT_MODEL = 'defaultModel';
    const LINK_BASE_URL = 'linkBaseUrl';

    /**
     * Gets the defaultModel property
     *
     * @return array
     */
    public function getDefaultModel()
    {
        return $this->getProperty(self::DEFAULT_MODEL);
    }

    /**
     * Sets the defaultModel property
     *
     * @param array $defaultModel The defaultModel of the object
     * @return self
     */
    public function setDefaultModel($defaultModel)
    {
        $this->setProperty(self::DEFAULT_MODEL, $defaultModel);

        return $this;
    }

    /**
     * Return the clickable URL that the user will receive inside the email.
     * This is just a convenience method for getting the `linkBaseUrl` out of
     * the `default model` array.
     *
     * @return null|string the URL the user will be taken to once they click on the URL received in the email.
     */
    public function getLinkBaseUrl()
    {
        $defaultModel = (array)$this->getDefaultModel();

        if(empty($defaultModel) || !key_exists(self::LINK_BASE_URL, $defaultModel)) {
            return null;
        }

        return $defaultModel[self::LINK_BASE_URL];
    }

    /**
     * Convenience method to specify the clickable url that the user will receive
     * inside the email. For Example, in the reset password workflow, this url
     * should point to the form where the user can insert their new password.
     *
     * @param string $linkBaseUrl the URL the user will be taken to once they click on the URL received in the email.
     * @return $this
     */
    public function setLinkBaseUrl($linkBaseUrl)
    {
        $defaultModel = $this->getDefaultModel();

        $defaultModel[self::LINK_BASE_URL] = $linkBaseUrl;

        $this->setDefaultModel($defaultModel);

        return $this;
    }

    /**
     * Save the model, Ultimately, this will call the parent save, but we want to
     * make sure the `linkBaseUrl` is set and not null first. This is required
     * by Stormpath to not be null when saving.
     *
     * @throws \InvalidArgumentException
     */
    public function save()
    {
        if(null === $this->getLinkBaseUrl()) {
            throw new \InvalidArgumentException('The defaultModel must contain the "linkBaseUrl" reserved property');
        }

        parent::save();

        return;
    }

}