<?php

namespace App\Services\Auth;

use Exception;
use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use App\Types\Auth\AccountType;

class ClientGuard extends TokenGuard
{
    private $headerParams = ['Authorization', 'Content-Type'];
    private $clientRepo;
    private $userRepo;
    
    public function __construct(UserProvider $provider, Request $request, ClientRepository $clientRepo, UserRepository $userRepo)
    {
        $this->inputKey   = null; // 不予使用
        $this->storageKey = null; // 不予使用
        $this->user       = null;
        $this->provider   = $provider;
        $this->request    = $request;
        $this->clientRepo = $clientRepo;
        $this->userRepo   = $userRepo;
    }
    
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (! is_null($this->user)) {
            return $this->user;
        }
        
        $this->validate($this->getHeaders());
        
        return $this->user;
    }
    
    /**
     * Get the token for the current request.
     *
     * @return string|null
     */
    public function getTokenForRequest()
    {
        return $this->request->header('Authorization', null);
    }
    
    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        /* token sample:
        $token = (new \Lcobucci\JWT\Builder())
            ->setIssuer('IES-CN')
            ->setAudience('sokradeo')
            ->setSubject('5a9cd4ab0a093')
            ->setExpiration(time() + 3600)
            ->set('accType', 'Habook')
            ->set('accUser', 'rice#5402')
            ->set('name',    'Rice')
            ->set('email',   'rice@habook.com.tw')
            ->sign(new \Lcobucci\JWT\Signer\Hmac\Sha256(), 'rrLbSS3SEbqGq14P')
            ->getToken();
        
        $token = (string) $token;
        */
        
        $token  = $this->request->bearerToken();
        $token  = (new \Lcobucci\JWT\Parser())->parse($token);
        $client = $this->clientRepo->getClient($token->getClaim('sub'));
        $valid  = $token->verify(new \Lcobucci\JWT\Signer\Hmac\Sha256(), $client->secret);
        if(! $valid) {
            return false;
        }
        
        if (Config::get('app.env') !== 'local') {
            try {
                if ($token->getClaim('exp') > (time() + 86400)) { // 限制有效過期時間區間
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        }
        
        $validator = new \Lcobucci\JWT\ValidationData();
        $validator->setIssuer($client->name);
        $validator->setAudience('sokradeo');
        $validator->setSubject($client->id);
        $valid = $token->validate($validator);
        if(! $valid) {
            return false;
        }
        
        $this->request->merge([
                'client' => $client,
        ]);
        
        $accType = $token->getClaim('accType');
        $accUser = $token->getClaim('accUser');
        $name    = $token->getClaim('name');
        $email   = $token->getClaim('email');
        
        if (! AccountType::check($accType)) {
            return false;
        }
        
        $credentials = [];
        if ($accType === AccountType::Client) {
            $credentials['client_id'  ] = $client->id;
            $credentials['client_user'] = $accUser;
        } else {
            $accCol = AccountType::getDbColNameByAccType($accType);
            $credentials[$accCol] = $accUser;
        }
        
        $this->user = $this->provider->retrieveByCredentials($credentials);
        
        // 註冊新增成員流程
        if (is_null($this->user)) {
            
            $accData = ['name' => $name, 'email' => $email];
            $this->user = ($accType === AccountType::Client)
                ? $this->userRepo->getUserOrCreateByClientAcc($client->id, $accUser, $accData)
                : $this->userRepo->getUserOrCreateByAcc($accType, $accUser, $accData);
            
        }
        
        return ! is_null($this->user);
    }
    
    private function getHeaders()
    {
        $headers = [];
        foreach ($this->headerParams as $param) {
            $headers[$param] = $this->request->header($param, null);
        }
        return $headers;
    }
    
}
