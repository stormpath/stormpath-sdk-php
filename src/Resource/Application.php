<?php

namespace Stormpath\Resource;

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

use JWT;
use Stormpath\Authc\Api\ApiKeyEncryptionOptions;
use Stormpath\Authc\AuthenticationRequest;
use Stormpath\Authc\BasicAuthenticator;
use Stormpath\Authc\UsernamePasswordRequest;
use Stormpath\Client;
use Stormpath\Provider\ProviderAccountRequest;
use Stormpath\Exceptions\IdSite\InvalidCallbackUriException;
use Stormpath\Exceptions\IdSite\JWTUsedAlreadyException;
use Stormpath\Stormpath;
use Stormpath\Util\NonceStore;
use Stormpath\Util\UUID;

class Application extends InstanceResource implements Deletable
{
    const NAME                          = "name";
    const DESCRIPTION                   = "description";
    const STATUS                        = "status";
    const TENANT                        = "tenant";
    const ACCOUNTS                      = "accounts";
    const PASSWORD_RESET_TOKENS         = "passwordResetTokens";
    const DEFAULT_ACCOUNT_STORE_MAPPING = "defaultAccountStoreMapping";
    const DEFAULT_GROUP_STORE_MAPPING   = "defaultGroupStoreMapping";
    const GROUPS                        = "groups";
    const ACCOUNT_STORE_MAPPINGS        = "accountStoreMappings";
    const LOGIN_ATTEMPTS                = "loginAttempts";
    const OAUTH_POLICY                  = "oAuthPolicy";
    const CUSTOM_DATA                   = "customData";
    const AUTHORIZED_CALLBACK_URIS      = "authorizedCallbackUris";

    const PATH                          = "applications";

    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::APPLICATION, self::PATH, $options);
    }

    public static function instantiate($properties = null)
    {
        return Client::instantiate(Stormpath::APPLICATION, $properties);
    }

    public static function create($properties, array $options = array())
    {
        $application = $properties;

        if (!($application instanceof Application))
        {
            $application = self::instantiate($properties);
        }

        return Client::create('/'.self::PATH, $application, $options);
    }

    public function getName()
    {
        return $this->getProperty(self::NAME);
    }

    public function setName($name)
    {
        $this->setProperty(self::NAME, $name);
    }

    public function getDescription()
    {
        return $this->getProperty(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        $this->setProperty(self::DESCRIPTION, $description);
    }

    public function getStatus()
    {
        $value = $this->getProperty(self::STATUS);

        if ($value)
        {
            $value = strtoupper($value);
        }

        return $value;
    }

    public function setStatus($status)
    {
        $uprStatus = strtoupper($status);
        if (array_key_exists($uprStatus, Stormpath::$Statuses))
        {
            $this->setProperty(self::STATUS, Stormpath::$Statuses[$uprStatus]);
        }
    }

    /**
     * Array that defines the authorized URIs that the IdP can return your
     * user to. These should be URIs that you host yourself.
     *
     * @since 1.13.0
     * @param array $uris
     * @return self
     */
    public function setAuthorizedCallbackUris(array $uris = [])
    {
        $this->setProperty(self::AUTHORIZED_CALLBACK_URIS, $uris);
        return $this;
    }

    /**
     * Returns Array that defines the authorized URIs that the IdP can return
     * your user to. These should be URIs that you host yourself.
     *
     * @since 1.13.0
     * @return array
     */
    public function getAuthorizedCallbackUris()
    {
        return (array) $this->getProperty(self::AUTHORIZED_CALLBACK_URIS);
    }

    public function getTenant(array $options = array())
    {
        return $this->getResourceProperty(self::TENANT, Stormpath::TENANT, $options);
    }

    public function getAccounts(array $options = array())
    {
        return $this->getResourceProperty(self::ACCOUNTS, Stormpath::ACCOUNT_LIST, $options);
    }

    public function getDefaultAccountStoreMapping(array $options = array()) {

        return $this->getResourceProperty(self::DEFAULT_ACCOUNT_STORE_MAPPING, Stormpath::ACCOUNT_STORE_MAPPING, $options);
    }

    public function getDefaultGroupStoreMapping(array $options = array()) {

        return $this->getResourceProperty(self::DEFAULT_GROUP_STORE_MAPPING, Stormpath::ACCOUNT_STORE_MAPPING, $options);
    }

    public function getGroups(array $options = array()) {

        return $this->getResourceProperty(self::GROUPS, Stormpath::GROUP_LIST, $options);
    }

    public function getOauthPolicy(array $options = array())
    {
        return $this->getResourceProperty(self::OAUTH_POLICY, Stormpath::OAUTH_POLICY, $options);
    }

    public function getCustomData(array $options = array())
    {
        $customData =  $this->getResourceProperty(self::CUSTOM_DATA, Stormpath::CUSTOM_DATA, $options);

        if(!$customData) {
            $customData = new CustomData();
            $this->setProperty(self::CUSTOM_DATA, $customData);
        }

        return $customData;
    }

    public function getAccountStoreMappings(array $options = array()) {

        return $this->getResourceProperty(self::ACCOUNT_STORE_MAPPINGS, Stormpath::ACCOUNT_STORE_MAPPING_LIST, $options);
    }

    public function getLoginAttempts(array $options = array()) {

        return $this->getResourceProperty(self::LOGIN_ATTEMPTS, Stormpath::BASIC_LOGIN_ATTEMPT, $options);
    }

    public function createAccount(Account $account, array $options = array()) {

        return $this->getDataStore()->create($this->getHref() .'/'.Account::PATH, $account, Stormpath::ACCOUNT, $options);
    }

    public function createGroup(Group $group, array $options = array()) {

        return $this->getDataStore()->create($this->getHref() .'/'.Group::PATH, $group, Stormpath::GROUP, $options);
    }

    public function createAccountStoreMapping(AccountStoreMapping $accountStoreMapping, array $options = array()) {

        return AccountStoreMapping::_create($accountStoreMapping, $this, $this->dataStore, $options);
    }

    /**
     * Sends a password reset email for the specified account username or email address.  The email will contain
     * a password reset link that the user can click or copy into their browser address bar.
     * <p/>
     * This method merely sends the password reset email that contains the link and nothing else.  You will need to
     * handle the link requests and then reset the account's password as described in the
     * {@link verifyPasswordResetToken} PHPDoc.
     *
     * <p>It is possible to include an <code>AccountStore</code> in the <code>$options</code> array as a performance
     * enhancement if the application might be mapped to many (dozens, hundreds or thousands) of account stores.
     * This can be common in multi-tenant applications where each mapped
     * AccountStore represents a specific tenant or customer organization.  Specifying the AccountStore
     * in these scenarios bypasses the general email-only-based account search and performs a more-efficient direct
     * lookup directly against the specified AccountStore.  The AccountStore is usually discovered before calling this
     * method by inspecting a submitted tenant id or subdomain, e.g. http://ACCOUNT_STORE_NAME.foo.com </p>
     *
     * @param $accountUsernameOrEmail a username or email address of an Account that may login to the application.
     * @param $options options to pass to this request.
     * @return the account corresponding to the specified username or email address.
     * @see #verifyPasswordResetToken()
     */
    public function sendPasswordResetEmail($accountUsernameOrEmail, array $options = array(), $returnTokenResource = false)
    {
        $passwordResetToken = $this->createPasswordResetToken($accountUsernameOrEmail, $options);

        if ($returnTokenResource)
            return $passwordResetToken;

        return $passwordResetToken->getAccount();
    }

    /**
     * Verifies a password reset token in a user-clicked link within an email.
     * <p/>
     * <h2>Base Link Configuration</h2>
     * You need to define the <em>Base</em> link that will process HTTP requests when users click the link in the
     * email as part of your Application's Workflow Configuration within the Stormpath UI Console.  It must be a URL
     * served by your application's web servers.  For example:
     * <pre>
     * https://www.myApplication.com/passwordReset
     * </pre>
     * <h2>Runtime Link Processing</h2>
     * When an application user clicks on the link in the email at runtime, your web server needs to process the request
     * and look for an <i>spToken</i> request parameter.  You can then verify the <i>spToken</i>, and then finally
     * change the Account's password.
     * <p/>
     * Usage Example:
     * <p/>
     * Browser:
     * {@code GET https://www.myApplication/passwordReset?spToken=someTokenValueHere}
     * <p/>
     * Your code:
     * <pre>
     * $token = // get the spToken value from query string parameter
     *
     * $account = $application->verifyPasswordResetToken($token);
     *
     * //token has been verified - now set the new password with what the end-user submits:
     * $account->setPassword(user_submitted_new_password);
     * account->save();
     * </pre>
     *
     * @param $token the verification token, usually obtained as a request parameter by your application.
     * @param $options the options to pass to this request.
     * @return the Account matching the specified token.
     */
    // @codeCoverageIgnoreStart
    public function verifyPasswordResetToken($token, array $options = array())
    {
        $href = $this->getPasswordResetTokensHref();
        $href .= '/' .$token;

        $passwordResetProps = new \stdClass();

        $hrefName = self::HREF_PROP_NAME;

        $passwordResetProps->$hrefName = $href;

        $passwordResetToken = $this->getDataStore()->instantiate(Stormpath::PASSWORD_RESET_TOKEN, $passwordResetProps);

        return $passwordResetToken->getAccount($options);
    }
    // @codeCoverageIgnoreStart

    public function resetPassword($token, $password)
    {
        $href = $this->getPasswordResetTokensHref();
        $href .= '/' .$token;

        $passwordResetProps = new PasswordResetToken();
        $passwordResetProps->password = $password;

        $token = $this->getDataStore()->create($href, $passwordResetProps, Stormpath::PASSWORD_RESET_TOKEN);

        return $token->getAccount();
    }

    /**
     * Authenticates an account's submitted principals and credentials (e.g. username and password).  The account must
     * be in one of the Application's
     * <a href="http://docs.stormpath.com/rest/product-guide/#application-account-store-mappings">assigned Login Sources</a>.  If not
     * in an assigned login source, the authentication attempt will fail.
     * <h2>Example</h2>
     * Consider the following username/password-based example:
     * <p/>
     * <pre>
     * $request = new UsernamePasswordRequest($email, $submittedRawPlaintextPassword);
     * $account = $appToTest->authenticateAccount($request)->getAccount();
     * </pre>
     *
     * @param $request the authentication request representing an account's principals and credentials (e.g.
     *                username/password) used to verify their identity.
     * @param $options the options to pass to this request.
     * @return the result of the authentication. The authenticated account can be obtained from the
     *         <i>result</i>. {@link AuthenticationResult::getAccount()}.
     *
     * @throws ResourceError if the authentication attempt fails.
     */
    public function authenticateAccount(AuthenticationRequest $request, array $options = array())
    {
        $basicAuthenticator = new BasicAuthenticator($this->getDataStore());
        return $basicAuthenticator->authenticate($this->getHref(), $request, $options);
    }

    /**
     * @param string $username the username of the account to authenticate.
     * @param string $password the raw password to authenticate.
     * @param array $options the options to pass to this request.
     * @return the result of the authentication.
     *
     * @see #authenticateAccount()
     */
    public function authenticate($username, $password, array $options = array())
    {
        $request = new UsernamePasswordRequest($username, $password);
        return $this->authenticateAccount($request, $options);
    }


    /**
     * Generate the url for ID Site.
     *
     * @param array $options
     * @return string
     * @throws InvalidCallbackUriException
     */
    public function createIdSiteUrl(array $options = array())
    {
        if( ! isset( $options['callbackUri'] ) )
            throw new InvalidCallbackUriException('Please provide a \'callbackUri\' in the $options array.');

        $p = parse_url ( $this->href );
        $base = $p['scheme'] . '://' . $p['host'];

        $apiId = $this->getDataStore()->getApiKey()->getId();
        $apiSecret = $this->getDataStore()->getApiKey()->getSecret();
        
        $token = array(
            'jti'       => UUID::v4(),
            'iat'       => microtime(true),
            'iss'       => $apiId,  //API ID
            'sub'       => $this->href,
            'state'     => isset($options['state']) ? $options['state'] : '',
            'path'      => isset($options['path']) ? $options['path'] : '/',
            'cb_uri'    => $options['callbackUri']
        );

        if(isset($options['sp_token'])) {
            $token['sp_token'] = $options['sp_token'];
        }
        
        if(isset($options['organizationNameKey'])) {
            $token['onk'] = $options['organizationNameKey'];
        }

        if(isset($options['showOrganizationField'])) {
            $token['sof'] = true;
        }

        if(isset($options['useSubDomain'])) {
            $token['usd'] = true;
        }




        $jwt = JWT::encode($token, $apiSecret);

        $redirectUrl = $base . "/sso";

        if(isset($options['logout']))
            $redirectUrl .= "/logout";

        return $redirectUrl . "?jwtRequest=$jwt";

    }


    /**
     * Handle the response from Stormpath and return a parsed JWT.
     *
     * @param $responseUri
     * @return \StdClass
     * @throws JWTUsedAlreadyException
     */
    public function handleIdSiteCallback($responseUri)
    {

        $urlParse = parse_url ( $responseUri );

        parse_str($urlParse['query'], $params);
        $token = isset($params['jwtResponse']) ? $params['jwtResponse'] : '';
        $apiId = $this->getDataStore()->getApiKey()->getId();
        $apiSecret = $this->getDataStore()->getApiKey()->getSecret();

        $jwt = JWT::decode($token, $apiSecret, array('HS256'));

        if (isset($jwt->err)) {
            $error = new Error(json_decode($jwt->err));
            throw new ResourceError($error);
        }

        // Check to see if Nonce is already used
        $nonceStore = new NonceStore($this->getDataStore());
        $nonceUsed = $nonceStore->getNonce($jwt->irt);

        if($nonceUsed)
            throw new JWTUsedAlreadyException('The ID Site JWT has already been used.');

        $nonceStore->putNonce($jwt->irt);

        $return = new \StdClass();

        try {
            $account = $this->getDataStore()->getResource($jwt->sub, Stormpath::ACCOUNT);
        } catch (\Stormpath\Resource\ResourceError $re) {
            $account = null;
        }


        $return->account = $account;
        $return->state = $jwt->state;
        $return->isNew = $jwt->isNewSub;
        $return->status = $jwt->status;

        return $return;
    }



    public function delete() {

        $this->getDataStore()->delete($this);
    }

    private function createPasswordResetToken($accountUsernameOrEmail, array $options = array())
    {
        $href = $this->getPasswordResetTokensHref();

        $passwordResetToken = $this->getDataStore()->instantiate(Stormpath::PASSWORD_RESET_TOKEN);
        $passwordResetToken->email = $accountUsernameOrEmail;

        if (isset($options['accountStore']))
        {
            $accountStore = $options['accountStore'];
            if ($accountStore instanceof AccountStore)
            {
                $passwordResetToken->setAccountStore($accountStore);
            }
        }

        return $this->getDataStore()->create($href, $passwordResetToken, Stormpath::PASSWORD_RESET_TOKEN, $options);
    }

    public function getAccount(ProviderAccountRequest $request)
    {
        $providerData = $request->getProviderData();

        $providerAccountAccess = $this->getDataStore()->instantiate(Stormpath::PROVIDER_ACCOUNT_ACCESS);
        $providerAccountAccess->providerData = $providerData;

        return $this->getDataStore()->create($this->getHref().'/'.Account::PATH,
            $providerAccountAccess, Stormpath::PROVIDER_ACCOUNT_RESULT);
    }


    public function sendVerificationEmail($login, $options = array())
    {
        if ($login == null) {
            throw new \InvalidArgumentException('Login cannot be null');
        }

        $accountStore = null;
        if (isset($options['accountStore']))
        {
            $accountStore = $options['accountStore'];
            if ($accountStore instanceof AccountStore)
            {
                if ($accountStore != null && $accountStore->href == null) {
                    throw new \InvalidArgumentException("verificationEmailRequest's accountStore has been specified but its href is null.");
                }
            }
            else
            {
                throw new \InvalidArgumentException("The value for accountStore in the \$options array should be an instance of \\Stormpath\\Resource\\AccountStore");
            }
        }



        $verificationEmail = $this->getDataStore()->instantiate(Stormpath::VERIFICATION_EMAIL);
        $verificationEmail->login = $login;


        $this->getDataStore()->create($this->getHref() . '/' . VerificationEmail::PATH,
            $verificationEmail, Stormpath::VERIFICATION_EMAIL);
    }

    public function getApiKey($apiKeyId, $options = array())
    {
        $options['id'] = $apiKeyId;
        $apiKeyOptions = new ApiKeyEncryptionOptions($options);
        $options = array_merge($options, $apiKeyOptions->toArray());

        $apiKeyList = $this->getDataStore()->getResource($this->getHref() . '/' . ApiKey::PATH,
            Stormpath::API_KEY_LIST, $options);

        $iterator = $apiKeyList->iterator;

        $apiKey = $iterator->valid() ? $iterator->current() : null;
        if ($apiKey)
        {
            $apiKey->setApiKeyMetadata($apiKeyOptions);
        }

        return $apiKey;

    }

    // @codeCoverageIgnoreStart
    private function getPasswordResetTokensHref()
    {
        $passwordResetTokensRef = $this->getProperty(self::PASSWORD_RESET_TOKENS);

        if ($passwordResetTokensRef)
        {
            $hrefName = self::HREF_PROP_NAME;

            return $passwordResetTokensRef->$hrefName;
        }
    }
    // @codeCoverageIgnoreEnd
}
