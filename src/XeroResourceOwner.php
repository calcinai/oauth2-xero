<?php
/**
 * @package    oauth2-xero
 * @author     Michael Calcinai <michael@calcin.ai>
 */

namespace Calcinai\OAuth2\Client;

use Firebase\JWT\JWT;

class XeroResourceOwner
{
    /**
     * Xero user's UUID
     *
     * @var string
     */
    public $xero_userid;

    /**
     * @var string
     */
    public $preferred_username;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $given_name;

    /**
     * @var string
     */
    public $family_name;

    /**
     * @var string the unique user ID, different to Xero user ID
     */
    public $sub;

    /**
     * @var int details expirey time
     */
    public $exp;

    /**
     * @var int the time the authorisation was first granted for this token
     */
    public $auth_time;

    /**
     * @var int token issued at time; last authentication flow time
     */
    public $iat;

    /**
     * @var string
     */
    public $aud;

    /**
     * @param $token
     * @return static
     */
    public static function fromJWT($token)
    {
        list($header, $body, $crypto) = explode('.', $token);

        //This needs to be done manually as we don't get a signed JWT
        $decoded = JWT::jsonDecode(JWT::urlsafeB64Decode($body));

        $self = new static();

        $self->xero_userid = $decoded->xero_userid;
        $self->preferred_username = $decoded->preferred_username;
        $self->email = $decoded->email;
        $self->given_name = $decoded->given_name;
        $self->family_name = $decoded->family_name;
        $self->sub = $decoded->sub;
        $self->exp = $decoded->exp;
        $self->auth_time = $decoded->auth_time;
        $self->iat = $decoded->iat;
        $self->aud = $decoded->aud;

        return $self;
    }

    /**
     * Provide camelCase access to the underlying properties.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        $snakeCase = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

        if (property_exists($this, $snakeCase)) {
            return $this->$snakeCase;
        }
    }
}
