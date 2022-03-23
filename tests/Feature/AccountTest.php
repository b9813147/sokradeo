<?php

namespace Tests\Feature;

use App\Models\Tba;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Redis;
use Lcobucci\JWT\Parser;
use Tests\TestCase;

class AccountTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testID1ToID2()
    {

        $token = (new \Lcobucci\JWT\Builder())
            ->setIssuer('EVENT-WEB')
            ->setAudience('sokradeo')
            ->setSubject('0rech53tqlcjd')
            ->setExpiration(time() + 86400)
            ->set('accType', 'Client')
            ->set('accUser', '1')
            ->set('name',    '醍摩豆競賽網站')
            ->set('email',   null)
            ->sign(new \Lcobucci\JWT\Signer\Hmac\Sha256(), 'ZhknfxbWv9NTbqNT')
            ->getToken();
        dd((string)$token);

//        $payload = array(
//            "iss" => "example.org",
//            "aud" => "example.com",
//            "iat" => 1356999524,
//            "nbf" => 1357000000
//        );
        $token  = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJoYWJvb2tJZCI6IjA5MzQxNjEzMjIjNDEzMCIsInByb2ZpbGVQaWN0dXJlVXJsIjoiaHR0cHM6Ly9jb3Jlc3RvcmFnZXNlcnZpY2UuYmxvYi5jb3JlLndpbmRvd3MubmV0L2FjY291bnQvYXZhdGFyLzE1MjU2NTgzNzciLCJURUFNTW9kZWxJZCI6IjA5MzQxNjEzMjIjNDEzMCIsIm5hbWUiOiJBcmVzIiwibmJmIjoiMTUyMTYwOTYyOCIsInN0YXRpb24iOiJnbG9iYWwiLCJpc1N0dWRlbnQiOmZhbHNlLCJkb2N1bWVudElkIjoiMTUyNTY1ODM3NyIsImlkIjoiMTUyNTY1ODM3NyJ9.ul7Er2AK0tOswqEJ0rLy3dGthCOlN0X-2HQywQhb-aI';
//        $parser = new  Parser();
//       dd($parser->parse($token));

        $tokenParts   = explode(".", $token);
        $tokenHeader  = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader    = json_decode($tokenHeader);
        $jwtPayload   = json_decode($tokenPayload);
        dd($jwtHeader, $jwtPayload);


//        $jwt = JWT::encode($payload, $privateKey, 'RS256');
//        $decode = JWT::decode(,$payload,['HS256']);
//        dd($decode);

        $json_decode = collect(json_decode(file_get_contents(public_path(('learncenter_ies_tw.json')))));

        $json_decode->each(function ($q) {
            User::query()->where('habook', $q->tmid)->update(['habook' => 'id' . $q->tmid]);
            Tba::query()->where('habook_id', $q->tmid)->update(['habook_id' => 'id' . $q->tmid]);
        });

        $json_decode->each(function ($q) {
            User::query()->where('habook', $q->tmvid)->update(['habook' => $q->tmid]);
            Tba::query()->where('habook_id', $q->tmvid)->update(['habook_id' => $q->tmid]);
        });
    }
}
