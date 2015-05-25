<?php

namespace Stormpath\Resource;

/*
 * Copyright 2013 Stormpath, Inc.
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

use Stormpath\Authc\AuthenticationRequest;
use Stormpath\Authc\BasicAuthenticator;
use Stormpath\Authc\UsernamePasswordRequest;
use Stormpath\Client;
use Stormpath\Stormpath;

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
     * @param $accountUsernameOrEmail a username or email address of an Account that may login to the application.
     * @param $options options to pass to this request.
     * @return the account corresponding to the specified username or email address.
     * @see #verifyPasswordResetToken()
     */
    public function sendPasswordResetEmail($accountUsernameOrEmail, array $options = array())
    {
        $passwordResetToken = $this->createPasswordResetToken($accountUsernameOrEmail, $options);

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

    public function delete() {

        $this->getDataStore()->delete($this);
    }

    private function createPasswordResetToken($accountUsernameOrEmail, array $options = array())
    {
        $href = $this->getPasswordResetTokensHref();

        $passwordResetToken = $this->getDataStore()->instantiate(Stormpath::PASSWORD_RESET_TOKEN);
        $passwordResetToken->email = $accountUsernameOrEmail;

        return $this->getDataStore()->create($href, $passwordResetToken, Stormpath::PASSWORD_RESET_TOKEN, $options);
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
