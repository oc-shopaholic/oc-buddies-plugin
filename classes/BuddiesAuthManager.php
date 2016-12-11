<?php namespace Lovata\Buddies\Classes;

use Kharanenka\Helper\Result;
use Session;
use Cookie;
use Lang;
use Lovata\Buddies\Models\Throttle;
use Lovata\Buddies\Models\User;
use October\Rain\Auth\Manager as AuthManager;

/**
 * Class BuddiesAuthManager
 * @package Lovata\Buddies\Classes
 */
class BuddiesAuthManager extends AuthManager
{
    protected static $instance;
    protected $sessionKey = 'buddies_user_auth';
    protected $userModel = 'Lovata\Buddies\Models\User';
    protected $groupModel = 'Lovata\Buddies\Models\UserGroup';
    protected $throttleModel = 'Lovata\Buddies\Models\Throttle';

    /**
     * Attempts to authenticate the given user according to the passed credentials.
     *
     * @param array $arCredentials The user login details
     * @param bool $bRemember Store a non-expire cookie for the user
     * 
     * @return void
     */
    public function authenticate(array $arCredentials, $bRemember = true)
    {
        $sLoginName = $this->createUserModel()->getLoginName();
        $sLoginCredentialKey = (isset($arCredentials[$sLoginName])) ? $sLoginName : 'email';

        //Check login filed
        if(empty($arCredentials[$sLoginCredentialKey])) {
            
            $arErrorData = [
                'message' => Lang::get('lovata.toolbox::lang.validation.required', ['attribute' => Lang::get('lovata.buddies::lang.field.'.$sLoginCredentialKey)]),
                'field' => $sLoginCredentialKey,
            ];
            
            Result::setFalse($arErrorData);
            return;
        }

        //Check password field
        if(empty($arCredentials['password'])) {
            
            $arErrorData = [
                'message' => Lang::get('lovata.toolbox::lang.validation.required', ['attribute' => Lang::get('lovata.buddies::lang.field.password')]),
                'field' => 'password',
            ];

            Result::setFalse($arErrorData);
            
            return;
        }

        /*
         * If the fallback 'login' was provided and did not match the necessary
         * login name, swap it over
         */
        if($sLoginCredentialKey !== $sLoginName) {
            $arCredentials[$sLoginName] = $arCredentials[$sLoginCredentialKey];
            unset($arCredentials[$sLoginCredentialKey]);
        }

        /*
         * If throttling is enabled, check they are not locked out first and foremost.
         */
        if($this->useThrottle) {
            /** @var Throttle $obThrottle */
            $obThrottle = $this->findThrottleByLogin($arCredentials[$sLoginName], $this->ipAddress);
            if(!empty($obThrottle)) {
                $obThrottle->check();
                if(!Result::flag()) {
                    return;
                }
            }
        }

        //Get user object
        $obUser = $this->findUserByCredentials($arCredentials);
        if(empty($obUser)) {
            if($this->useThrottle && !empty($obThrottle)) {
                $obThrottle->addLoginAttempt();
            }

            $arErrorData = [
                'message' => Lang::get('lovata.buddies::lang.message.e_login_not_correct'),
                'field' => $sLoginCredentialKey,
            ];
            
            Result::setFalse($arErrorData);
            return;
        }

        //Check user active flag
        if ($this->requireActivation && !$obUser->is_activated) {

            $arErrorData = [
                'message' => Lang::get('lovata.buddies::lang.message.e_user_not_active'),
                'field' => null,
            ];
            
            Result::setFalse($arErrorData);
            return;
        }

        if($this->useThrottle && !empty($obThrottle)) {
            $obThrottle->clearLoginAttempts();
        }

        $obUser->clearResetPassword();
        $this->login($obUser, $bRemember);

        Result::setTrue($this->user);
    }

    /**
     * Logs in the given user and sets properties
     * in the session.
     *
     * @param User $obUser
     * @param bool $bRemember
     */
    public function login($obUser, $bRemember = true)
    {
        $this->user = $obUser;

        //Create session/cookie data to persist the session
        $toPersist = [$obUser->getKey(), $obUser->getPersistCode()];
        Session::put($this->sessionKey, $toPersist);

        if ($bRemember) {
            Cookie::queue(Cookie::forever($this->sessionKey, $toPersist));
        }

        //Fire the 'afterLogin' event
        $obUser->afterLogin();
    }

    /**
     * Find a throttle record by login and ip address
     * @param string $sLoginName
     * @param string $ipAddress
     *
     * @return null|Throttle
     */
    public function findThrottleByLogin($sLoginName, $ipAddress)
    {
        $obUser = $this->findUserByLogin($sLoginName);
        if(empty($obUser)) {
            return null;
        }

        $iUserID = $obUser->getKey();
        return $this->findThrottleByUserId($iUserID, $ipAddress);
    }

    /**
     * Finds a user by the login value.
     * @param string $sLogin
     *
     * @return User|null
     */
    public function findUserByLogin($sLogin)
    {
        $obModel = $this->createUserModel();
        $obQuery = $obModel->newQuery();
        $this->extendUserQuery($obQuery);
        $obUser = $obQuery->where($obModel->getLoginName(), $sLogin)->first();
        return $obUser;
    }

    /**
     * Finds a user by the given credentials.
     * @param array $arCredentials
     *
     * @return null|User
     */
    public function findUserByCredentials(array $arCredentials)
    {
        $obModel = $this->createUserModel();
        $sLoginName = $obModel->getLoginName();

        if (!array_key_exists($sLoginName, $arCredentials)) {
            return null;
        }

        $obQuery = $obModel->newQuery();
        $this->extendUserQuery($obQuery);
        $arHashedAttributes = $obModel->getHashableAttributes();
        $arHashedCredentials = [];

        //Build query from given credentials
        foreach ($arCredentials as $sCredential => $sValue) {
            // All excepted the hashed attributes
            if (in_array($sCredential, $arHashedAttributes)) {
                $arHashedCredentials = array_merge($arHashedCredentials, [$sCredential => $sValue]);
            } else {
                $obQuery = $obQuery->where($sCredential, '=', $sValue);
            }
        }

        /** @var User $obUser */
        $obUser = $obQuery->first();
        if (empty($obUser)) {
            return null;
        }

        //Check the hashed credentials match
        foreach ($arHashedCredentials as $sCredential => $sValue) {

            if (!$obUser->checkHashValue($sCredential, $sValue)) {
                // Incorrect password
                if ($sCredential == 'password') {
                    return null;
                }

               return null;
            }
        }

        return $obUser;
    }


    /**
     * Registers a user by giving the required credentials
     * and an optional flag for whether to activate the user.
     *
     * @param array $arCredentials
     * @param $bActive
     * @return User
     */
    public function register(array $arCredentials, $bActive = false)
    {
        /** @var User $obUser */
        $obUser = $this->createUserModel();
        $obUser->fill($arCredentials);
        $obUser->save();

        // Prevents revalidation of the password field
        // on subsequent saves to this model object
        $obUser->password = null;

        return $this->user = $obUser;
    }
}