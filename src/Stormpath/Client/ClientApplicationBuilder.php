<?php

namespace Stormpath\Client;

use Stormpath\Client\ClientBuilder;
use Stormpath\Service\StormpathService;

class ClientApplicationBuilder
{

    const DOUBLE_SLASH = "//";

    private $clientBuilder;
    private $applicationHref;

    public function __construct($clientBuilder = null)
    {
        if (!$clientBuilder)
        {
            $clientBuilder = new ClientBuilder;

        } else
        {
            if (!($clientBuilder instanceof ClientBuilder))
            {
                throw new \InvalidArgumentException("'\$clientBuilder' must be an instance of " ."ClientBuilder when provided.");

            }
        }

        $this->clientBuilder = $clientBuilder;
    }

    /**
     * Sets the location of the YAML file to load containing the API Key (Id and secret) used by the
     * Client to communicate with the Stormpath REST API.
     * <p/>
     * You may load files from the filesystem, or URLs just specifying the file location.
     * <h3>File Contents</h3>
     * <p/>
     * When the file is loaded, the following name/value pairs are expected to be present by default:
     * <table>
     *     <tr>
     *         <th>Key</th>
     *         <th>Value</th>
     *     </tr>
     *     <tr>
     *         <td>apiKey.id</td>
     *         <td>An individual account's API Key ID</td>
     *     </tr>
     *     <tr>
     *         <td>apiKey.secret</td>
     *         <td>The API Key Secret (password) that verifies the paired API Key ID.</td>
     *     </tr>
     * </table>
     * <p/>
     * Assuming you were using these default property names, your {@code Services_Stormpath_Client_ClientApplicationBuilder}
     * usage might look like the following:
     * <pre>
     * $location = "/home/jsmith/.stormpath/apiKey.yml";
     *
     * $applicationHref = 'https://<b>apiKeyId:apiKeySecret@</b>api.stormpath.com/v1/applications/YOUR_APP_UID_HERE'
     *
     * $builder = new Services_Stormpath_Client_ClientApplicationBuilder;
     *
     * application = $builder
     *               ->setApplicationHref($applicationHref)
     *               ->setApiKeyFileLocation($location)
     *               ->build()
     *               ->getApplication();
     *
     * </pre>
     * <h3>Custom Property Names</h3>
     * If you want to control the property names used in the file, you may configure them via
     * {@link setApiKeyIdPropertyName} and
     * {@link setApiKeySecretPropertyName}.
     * <p/>
     * For example, if you had a {@code /home/jsmith/.stormpath/apiKey.yml} file with the following
     * name/value pairs:
     * <pre>
     * $myStormpathApiKeyId = 'foo'
     * $myStormpathApiKeySecret = 'mySuperSecretValue'
     * </pre>
     * Your {@code ClientBuilder} usage would look like the following:
     * <pre>
     * $location = "/home/jsmith/.stormpath/apiKey.yml";
     *
     * $builder = new Services_Stormpath_Client_ClientApplicationBuilder;
     * $application = $builder
     *                ->setApplicationHref($applicationHref)
     *                ->setApiKeyFileLocation($location)
     *                ->setApiKeyIdPropertyName($myStormpathApiKeyId)
     *                ->setApiKeySecretPropertyName($myStormpathApiKeySecret)
     *                ->build()
     *                ->getApplication();
     * </pre>
     *
     * @param $apiKeyFileLocation the file or url location of the API Key {@code .yml} file to load when
     *                 constructing the API Key to use for communicating with the Stormpath REST API.
     *
     * @return this Services_Stormpath_Client_ClientApplicationBuilder instance for method chaining.
     */
    public function setApiKeyFileLocation($apiKeyFileLocation)
    {
        $this->clientBuilder->setApiKeyFileLocation($apiKeyFileLocation);
        return $this;
    }

    /**
     * <p>
     * Sets the name used to query for the API Key ID from a YAML content.
     *
     * The <b>$apiKeyIdPropertyName</b> key can be a single name or a composed name, as deep as needed,
     * as long as it comes in the exact path order. If it's a single name, it must be specified as a string;
     * if it's a composed name, it must be specified in an array.
     * </p>
     * <code>
     * //Example 1: Having the file 'apiKey.yml' with the following content:
     *
     *
     *           apiKey.id: myStormpathApiKeyId
     *
     * //The method should be called as follows:
     *
     *           $builder->setApiKeyIdPropertyName('apiKey.id');
     *
     * //Example 2: Having the file 'apiKey.yml' with the following content:
     *
     *
     *           stormpath:
     *             apiKey:
     *             id: myStormpathApiKeyId
     *
     * //The method should be called as follows:
     *
     *           $keys = array('stormpath', 'apiKey', 'id');
     *           $builder->setApiKeyIdPropertyName($keys);
     *
     * </code>
     * @param string|array $apiKeyIdPropertyName the name used to query for the API Key ID from a YAML content.
     * @return this Services_Stormpath_Client_ClientApplicationBuilder instance for method chaining.
     */
    public function setApiKeyIdPropertyName($apiKeyIdPropertyName)
    {
        $this->clientBuilder->setApiKeyIdPropertyName($apiKeyIdPropertyName);
        return $this;
    }

    /**
     * <p>
     * Sets the name used to query for the API Key Secret from a YAML content.
     *
     * The <b>$apiKeySecretPropertyName</b> key can be a single name or a composed name, as deep as needed,
     * as long as it comes in the exact path order. If it's a single name, it must be specified as a string;
     * if it's a composed name, it must be specified in an array.
     * </p>
     * <code>
     * //Example 1: Having the file 'apiKey.yml' with the following content:
     *
     *
     *           apiKey.secret: myStormpathApiKeySecret
     *
     * //The method should be called as follows:
     *
     *           $builder->setApiKeySecretPropertyName('apiKey.secret');
     *
     * //Example 2: Having the file 'apiKey.yml' with the following content:
     *
     *
     *           stormpath:
     *             apiKey:
     *             secret: myStormpathApiKeySecret
     *
     * //The method should be called as follows:
     *
     *           $keys = array('stormpath', 'apiKey', 'secret');
     *           $builder->setApiKeySecretPropertyName($keys);
     *
     * </code>
     * @param string|array $apiKeySecretPropertyName the name used to query for the API Key Secret from a YAML content.
     * @return this Services_Stormpath_Client_ClientApplicationBuilder instance for method chaining.
     */
    public function setApiKeySecretPropertyName($apiKeySecretPropertyName)
    {
        $this->clientBuilder->setApiKeySecretPropertyName($apiKeySecretPropertyName);
        return $this;
    }

    /**
     * <p>
     * Allows usage of a YAML compliant string instead of loading a YAML file via
     * {@link setApiKeyFileLocation} configuration.
     * <p/>
     * The YAML contents and property name overrides function the same as described in the
     * {@link setApiKeyFileLocation} API Documentation.
     *
     * @param $apiKeyProperties the YAML string to use to load the API Key ID and Secret.
     *
     * @return this Services_Stormpath_Client_ClientApplicationBuilder instance for method chaining.
     *
     */
    public function setApiKeyProperties($apiKeyProperties)
    {
        $this->clientBuilder->setApiKeyProperties($apiKeyProperties);
        return $this;
    }

    /**
     * Sets the fully qualified Stormpath Application HREF (a URL) to use to acquire the Application instance when
     * {@link #build()} is called.  See the Class-level PHP Doc for usage scenarios.
     *
     * @param $applicationHref the fully qualified Stormpath Application HREF (a URL) to use to acquire the
     *                        Services_Stormpath_Resource_Application instance when {@link #build()} is called.
     * @return this Services_Stormpath_Client_ClientApplicationBuilder instance for method chaining.
     */
    public function setApplicationHref($applicationHref)
    {
        $this->applicationHref = $applicationHref;
        return $this;
    }

    public function build()
    {
        $href = is_string($this->applicationHref) ? trim($this->applicationHref) : null;

        if (!$href)
        {
            throw new \InvalidArgumentException("'\$applicationHref' property must be specified when using this builder implementation.");
        }

        $cleanedHref = $href;

        $atSignIndex = strpos($href, '@');

        if (is_int($atSignIndex))
        {
            $parts = $this->getHrefWithUserInfo($href, $atSignIndex);

            $cleanedHref = $parts[0] . $parts[2];

            $parts = explode(':', $parts[1], 2);

            $apiKeyProperties = $this->createApiKeyProperties($parts);

            $this->setApiKeyProperties($apiKeyProperties);

        } //otherwise an apiKey File/YAML/etc for the API Key is required

        $client = $this->buildClient();

        $application = $client->getDataStore()->getResource($cleanedHref, StormpathService::APPLICATION);

        return new ClientApplication($client, $application);
    }

    protected function buildClient()
    {
        return $this->clientBuilder->build();
    }

    protected function getHrefWithUserInfo($href, $atSignIndex)
    {
        if (!is_string($href))
        {
            throw new \InvalidArgumentException('$href must be a string');
        }

        if (!is_int($atSignIndex))
        {
            throw new \InvalidArgumentException('$atSignIndex must be an int');
        }

        $doubleSlashIndex = strpos($href, self::DOUBLE_SLASH);

        if ($doubleSlashIndex === false)
        {
            throw new \InvalidArgumentException('Invalid application href URL');
        }

        $doubleSlashIndex = $doubleSlashIndex + strlen(self::DOUBLE_SLASH);
        $parts[0] = substr($href, 0, $doubleSlashIndex); //up to and including the double slash
        $parts[1] = substr($href, $doubleSlashIndex, $atSignIndex - $doubleSlashIndex); //raw user info
        $parts[2] = substr($href, $atSignIndex + 1); //after the @ character

        return $parts;

    }

    protected function createApiKeyProperties($pair)
    {
        if (!is_array($pair))
        {
            throw new \InvalidArgumentException('$pair must be an array');
        }

        if (count($pair) != 2)
        {
            throw new \InvalidArgumentException('$applicationHref userInfo segment must consist' .
                ' of the following format: apiKeyId:apiKeySecret');
        }

        //creating YAML string:
        $properties = "--\napiKey.id: ".
                      urldecode($pair[0]).
                      "\napiKey.secret: ".
                      urldecode($pair[1]).
                      "\n\n";

        return $properties;
    }
}
