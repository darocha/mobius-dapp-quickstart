<?php


namespace AppBundle\Mobius;


use ZuluCrypto\MobiusApi\Mobius;

class MobiusApiServiceFactory
{
    /**
     * @param $apiKey
     * @param $appUid
     * @return \ZuluCrypto\MobiusApi\Model\AppStore
     */
    public static function createAppApi($apiKey, $appUid)
    {
        $mobius = new Mobius($apiKey);

        return $mobius->getAppStore($appUid);
    }
}