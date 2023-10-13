<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Psr\Cache\CacheItemInterface;





class VinylController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(HttpClientInterface $httpClient): Response
    {
        $response = $httpClient->request('GET', 'https://gist.githubusercontent.com/giorgiofellipe/7d9113a8129d641578c1/raw/31e14292ef2c2eb502f0ec2496753136fac1c947/music.json');
        $tracks = $response->toArray();
      //  dd($tracks);
        return $this->render('vinyl/homepage.html.twig', [
            'title' => 'PB & Jams',
            'tracks' => $tracks,
        ]);
    }

    #[Route('/browse/{slug}', name: 'app_browse')]
    public function browse(HttpClientInterface $httpClient , CacheInterface $cache , string $slug = null): Response
    {    
        dump($cache);
        $genre = $slug ? u(str_replace('-', ' ', $slug))->title(true) : null;
         $mixes = $cache->get('mixes_data',function(CacheItemInterface $cacheItem) use ($httpClient) {
            $cacheItem->expiresAfter(5);
            $response = $httpClient->request('GET', 'https://raw.githubusercontent.com/SymfonyCasts/vinyl-mixes/main/mixes.json');

           return  $response->toArray();

         });
       
   
        return $this->render('vinyl/browse.html.twig', [
            'genre' => $genre ,
            'mixes' => $mixes,
        ]);
       
    }

}
