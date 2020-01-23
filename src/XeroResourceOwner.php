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

        return $self;
    }
}
