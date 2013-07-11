<?php

namespace Stormpath\Resource;

use Stormpath\Service\StormpathService;
use Stormpath\Authc\AuthenticationRequest;
use Stormpath\Authc\BasicAuthenticator;

class Application extends Instance
{
    const NAME                  = "name";
    const DESCRIPTION           = "description";
    const STATUS                = "status";
    const TENANT                = "tenant";
    const ACCOUNTS              = "accounts";
    const PASSWORD_RESET_TOKENS = "passwordResetTokens";
    
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
        
        if ($value) {
            $value = strtoupper($value);
        }
        
        return $value;
    }
    
    public function setStatus($status)
    {
        if (array_key_exists($status, StormpathService::$Statuses)) {
            $this->setProperty(self::STATUS, StormpathService::$Statuses[$status]);
        }
    }
    
    public function getTenant()
    {
        return $this->getResourceProperty(self::TENANT, StormpathService::TENANT);
    }
    
    public function getAccounts()
    {
        return $this->getResourceProperty(self::ACCOUNTS, StormpathService::ACCOUNT_LIST);
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
     * @return the account corresponding to the specified username or email address.
     * @see verifyPasswordResetToken
     */
    public function sendPasswordResetEmail($accountUsernameOrEmail)
    {
        $passwordResetToken = $this->createPasswordResetToken($accountUsernameOrEmail);
        
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
     * and look for an {@code spToken} request parameter.  You can then verify the {@code spToken}, and then finally
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
     * @return the Account matching the specified token.
     */
    public function verifyPasswordResetToken($token)
    {
        $passwordResetProps = new stdClass();
        
        $href = $this->getPasswordResetTokensHref();
        $href .= '/' .$token;
        $hrefName = self::HREF_PROP_NAME;
        $passwordResetProps->$hrefName = $href;
        
        $passwordResetToken = $this->getDataStore()->instantiate(StormpathService::PASSWORD_RESET_TOKEN, $passwordResetProps);
        
        return $passwordResetToken->getAccount();
    }
    
    /**
     * Authenticates an account's submitted principals and credentials (e.g. username and password).  The account must
     * be in one of the Application's
     * <a href="http://www.stormpath.com/docs/managing-applications-login-sources">assigned Login Sources</a>.  If not
     * in an assigned login source, the authentication attempt will fail.
     * <h2>Example</h2>
     * Consider the following username/password-based example:
     * <p/>
     * <pre>
     * $request = new Services_Stormpath_Authc_UsernamePasswordRequest($email, $submittedRawPlaintextPassword);
     * $account = $appToTest->authenticateAccount($request)->getAccount();
     * </pre>
     *
     * @param $request the authentication request representing an account's principals and credentials (e.g.
     *                username/password) used to verify their identity.
     * @return the result of the authentication.  The authenticated account can be obtained from
     *         {@code result.}{@link Services_Stormpath_Authc_AuthenticationResult::getAccount()}.
     *
     * @throws ResourceError if the authentication attempt fails.
     */
    public function authenticateAccount(AuthenticationRequest $request)
    {
        $basicAuthenticator = new BasicAuthenticator($this->getDataStore());
        
		return $basicAuthenticator->authenticate($this->getHref(), $request);
    }
    
    private  function createPasswordResetToken($accountUsernameOrEmail)
    {
        $href = $this->getPasswordResetTokensHref();
        
        $passwordResetProps = new stdClass();
        $passwordResetProps->email = $accountUsernameOrEmail;
        
        $passwordResetToken = $this->getDataStore()->instantiate(StormpathService::PASSWORD_RESET_TOKEN, $passwordResetProps);
        
        return $this->getDataStore()->create($href, $passwordResetToken, StormpathService::PASSWORD_RESET_TOKEN);
    }
    
    private function getPasswordResetTokensHref()
    {
        $passwordResetTokensRef = $this->getProperty(self::PASSWORD_RESET_TOKENS);
        
        if ($passwordResetTokensRef) {
            $hrefName = self::HREF_PROP_NAME;
            
            return $passwordResetTokensRef->$hrefName;
        }
    }
}