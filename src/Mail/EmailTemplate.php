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

use Stormpath\Resource\InstanceResource;

abstract class EmailTemplate extends InstanceResource
{
    const NAME                  = "name";
    const SUBJECT               = "subject";
    const FROM_NAME             = "fromName";
    const TEXT_BODY             = "textBody";
    const HTML_BODY             = "htmlBody";
    const MIME_TYPE             = "mimeType";
    const DESCRIPTION           = "description";
    const FROM_EMAIL_ADDRESS    = "fromEmailAddress";

    const PATH = "emailTemplates";

    /**
     * Gets the href property
     *
     * @return
     */
    public function getHref()
    {
        return $this->getProperty(self::HREF_PROP_NAME);
    }

    /**
     * Gets the name property
     *
     * @return string
     */
    public function getName()
    {
        return $this->getProperty(self::NAME);
    }

    /**
     * Sets the name property
     *
     * @param string $name The name of the object
     * @return self
     */
    public function setName($name)
    {
        $this->setProperty(self::NAME, $name);

        return $this;
    }

    /**
     * Gets the description property
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getProperty(self::DESCRIPTION);
    }

    /**
     * Sets the description property
     *
     * @param string $description The description of the object
     * @return self
     */
    public function setDescription($description)
    {
        $this->setProperty(self::DESCRIPTION, $description);

        return $this;
    }

    /**
     * Gets the fromName property
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->getProperty(self::FROM_NAME);
    }

    /**
     * Sets the fromName property
     *
     * @param string $fromName The fromName of the object
     * @return self
     */
    public function setFromName($fromName)
    {
        $this->setProperty(self::FROM_NAME, $fromName);

        return $this;
    }

    /**
     * Gets the fromEmailAddress property
     *
     * @return string
     */
    public function getFromEmailAddress()
    {
        return $this->getProperty(self::FROM_EMAIL_ADDRESS);
    }

    /**
     * Sets the fromEmailAddress property
     *
     * @param string $fromEmailAddress The fromEmailAddress of the object
     * @return self
     */
    public function setFromEmailAddress($fromEmailAddress)
    {
        $this->setProperty(self::FROM_EMAIL_ADDRESS, $fromEmailAddress);

        return $this;
    }

    /**
     * Gets the subject property
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->getProperty(self::SUBJECT);
    }

    /**
     * Sets the subject property
     *
     * @param string $subject The subject of the object
     * @return self
     */
    public function setSubject($subject)
    {
        $this->setProperty(self::SUBJECT, $subject);

        return $this;
    }

    /**
     * Gets the textBody property
     *
     * @return string
     */
    public function getTextBody()
    {
        return $this->getProperty(self::TEXT_BODY);
    }

    /**
     * Sets the textBody property
     *
     * @param string $textBody The textBody of the object
     * @return self
     */
    public function setTextBody($textBody)
    {
        $this->setProperty(self::TEXT_BODY, $textBody);

        return $this;
    }

    /**
     * Gets the htmlBody property
     *
     * @return string
     */
    public function getHtmlBody()
    {
        return $this->getProperty(self::HTML_BODY);
    }

    /**
     * Sets the htmlBody property
     *
     * @param string $htmlBody The htmlBody of the object
     * @return self
     */
    public function setHtmlBody($htmlBody)
    {
        $this->setProperty(self::HTML_BODY, $htmlBody);

        return $this;
    }

    /**
     * Gets the mimeType property
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->getProperty(self::MIME_TYPE);
    }

    /**
     * Sets the mimeType property
     *
     * @param string $mimeType The mimeType of the object
     * @return self
     */
    public function setMimeType($mimeType)
    {
        $this->setProperty(self::MIME_TYPE, $mimeType);

        return $this;
    }

}