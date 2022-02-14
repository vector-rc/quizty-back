<?php

namespace Quizty\Utils;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Validation\Constraint\ValidAt;

use DateTimeImmutable;
use DateTimeZone;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

final class JWT
{

    public static function config()
    {
        

        $private_key = InMemory::file(JWT_PRIVATE_KEY);
        $public_key = InMemory::file(JWT_PUBLIC_KEY);

        return Configuration::forAsymmetricSigner(new Signer\Rsa\Sha256(), $private_key, $public_key);
    }

    public static function sign($payload)
    {
        $config = self::config();
        $signer = $config->signer();
        return $signer->sign($payload, $config->signingKey());
    }

    public static function create( $claims = [], $headers = [],$expiresAt = INF)
    {
        $now   = new DateTimeImmutable();

        $config = self::config();
        $builder = $config->builder();
        if (!is_infinite($expiresAt)) $builder->expiresAt($now->modify('+' . $expiresAt . ' seconds'));

        if (count($claims) > 0) {
            foreach ($claims as $key => $value) {
                $builder->withClaim($key, $value);
            }
        }
        if (count($headers) > 0) {
            foreach ($headers as $key => $value) {
                $builder->withHeader($key, $value);
            }
        }
        $token = $builder->getToken($config->signer(), $config->signingKey());
        return $token->toString();
    }

    public static function parser($jwt)
    {
        $config = self::config();
        $token = $config->parser()->parse($jwt);
        if (! $token instanceof Plain) {
            throw new \RuntimeException('NOPE!');
        }
        return ['claims'=>$token->claims()->all(),'headers'=>$token->headers()->all()];
        //return $token;
    }
    public static function isExpired($jwt)
    {
        $now   = new DateTimeImmutable();
        $config = self::config();
        $token = $config->parser()->parse($jwt);
        return $token->isExpired($now);
    }

    public static function validate($jwt)
    {
        $config = self::config();
        $token = $config->parser()->parse($jwt);
        $config->setValidationConstraints(new SignedWith($config->signer(),$config->verificationKey()));
        $config->setValidationConstraints(new ValidAt(new SystemClock(new DateTimeZone('UTC'))));
        $constraints=$config->validationConstraints();
        return $config->validator()->validate($token, ...$constraints);
    }
}
