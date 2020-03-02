<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/2
 * Time: 11:46
 */

namespace App\Common\Utils;

use Carbon\Carbon;
use DateTimeInterface;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT as FJWT;
use Illuminate\Support\Facades\Redis;

/**
 * Class JWT
 *
 * @package App\Common
 */
class JWT {

    private static $jwt;
    /** @var \Redis */
    private $redis;

    /**
     * @var array 载荷由如下键组成：
     *      sub - 主题，即用户 UID
     *      iss - 颁发者，项目名
     *      aud - 接收者，同 iss，由请求方提供
     *      iat - 颁发时间，不可晚于生效时间及过期时间
     *      nbf - 生效时间，不可早于颁发时间、晚于过期时间
     *      exp - 过期时间，不可早于颁发时间及生效时间
     */
    private $payload;

    /** @var string */
    private $priKey;

    /** @var string */
    private $pubKey;

    /** @var string */
    private $appKey;

    /**
     * JWT constructor.
     */
    public function __construct() {
        $this->payload = [
            'sub' => null,
            'iss' => 'AnDou',
            'aud' => null,
            'iat' => null,
            'nbf' => null,
            'exp' => null
        ];

        $this->priKey = config('rsa.private');
        $this->pubKey = config('rsa.public');
        $this->appKey = env('APP_KEY');

        $this->redis = Redis::connection();
        $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);
    }

    public static function getInstance() {
        return self::$jwt ?: self::$jwt = new self();
    }

    /**
     * @param string                              $subject
     * @param string                              $audience
     * @param Carbon|DateTimeInterface|int|string $expires
     *
     * @return bool|string
     */
    public function new(string $subject, string $audience = 'AnDou-API', $expires = null) {
        // get tokens in redis
        $tokenLen = $this->redis->llen("{$audience}:Token:User:{$subject}");
        $tokens = $this->redis->lrange("{$audience}:Token:User:{$subject}", 0, $tokenLen);

        // find expired token and delete
        foreach ($tokens as $key => $token) {
            $jwt = $this->decode($token);
            // object is available token, -1: expired; 0: exception
            if (!is_object($jwt)) {
                $this->redis->ltrim("{$audience}:Token:User:{$subject}", $key, 0);
            }
        }

        // encrypt subject with AES, mode CTR
        $sub = sodium_bin2hex(Crypt::AES(128, Crypt::hash($audience))->encrypt($subject));

        // set default expires
        if (!$expires) {
            $expires = Carbon::now()->addMonth();
        }

        // generate token
        $token = $this->generate($sub, $audience, $expires);

        if ($token) {
            // add new token to list
            $this->redis->lpush("{$audience}:Token:User:{$subject}", $token);
            // reset TTL to 30 days when add new token
            $this->redis->expire("{$audience}:Token:User:{$subject}", 2592000);
            return $token;
        }
        return false;
    }

    /**
     * @param string                              $subject
     * @param string                              $audience
     * @param Carbon|DateTimeInterface|int|string $expires
     *
     * @return bool|string
     */
    private function generate(string $subject, string $audience, $expires = null) {
        $this->payload['sub'] = $subject;
        $this->payload['aud'] = $audience;
        $this->payload['iat'] = $this->payload['nbf'] = time();

        if (is_numeric($expires)) {
            $strLen = strlen($expires);
            // timestamp
            if ($strLen === 10) {
                $expires = Carbon::createFromTimestamp($expires);
            } else if ($expires > 0 && $strLen < 10) {
                // seconds
                $expires = Carbon::now()->addSeconds($expires);
            } else {
                return false;
            }
        } else if (is_null($expires)) {
            $expires = Carbon::now()->addMonth();
        }

        if ($expires instanceof Carbon || $expires instanceof DateTimeInterface) {
            if (Carbon::now()->isAfter($expires)) {
                return false;
            }
        }

        $this->payload['exp'] = $expires->timestamp;
        return FJWT::encode($this->payload, $this->priKey, 'RS256');
    }

    /**
     * @param string $token
     *
     * @return int|object
     */
    public function decode(string $token) {
        try {
            return FJWT::decode($token, $this->pubKey, ['RS256']);
        } catch (ExpiredException $e) {
            return 0;
        } catch (Exception $e) {
            return -1;
        }
    }
}
