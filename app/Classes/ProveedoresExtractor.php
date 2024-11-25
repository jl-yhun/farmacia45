<?php

namespace App\Classes;

use Google\Auth\ApplicationDefaultCredentials;
use Google\Cloud\Functions\V2\FunctionServiceClient;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\Facades\Cache;

class ProveedoresExtractor implements IProveedoresExtractor
{
    public function extract($codigo): array
    {
        return [
            'levic' => $this->extractFromLevic($codigo)
        ];
    }

    private function tryGetFromCache($codigo)
    {
        $dataFromCache = Cache::get($codigo, null);

        return $dataFromCache;
    }

    private function extractFromLevic($codigo)
    {
        $fromCache = $this->tryGetFromCache($codigo);

        if ($fromCache)
            return $fromCache;
            
        $fnUrl = config('extractor.levic_fn_name');

        $token = $this->authenticate($fnUrl);

        $client = new GuzzleHttpClient();
        $response = $client->get($fnUrl . '/' . $codigo, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $precioWithDollar = json_decode($response->getBody());
        $precio = str_replace('$', '', $precioWithDollar->precio);

        $return = [
            'precio' => $precio,
            'available' => $precioWithDollar->available
        ];

        Cache::put($codigo, $return, 86400);
        return $return;
    }

    private function authenticate($cloudFunctionName)
    {
        if (config('app.env') == 'production') {
            putenv('GOOGLE_APPLICATION_CREDENTIALS=./application_default_credentials.json');
            $cred = ApplicationDefaultCredentials::getIdTokenCredentials(
                $cloudFunctionName
            );

            $token = $cred->fetchAuthToken();
            return $token['id_token'] ?? '';
        } else
            return '';
    }
}
