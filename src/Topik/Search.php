<?php 

namespace hendrowicaksono\TajukOnline\Topik;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class Search
{
    public function index($keyword = "Hello World")
    {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://httpbin.org',
            'verify' => false,
            // You can set any number of default request options.
            'timeout'  => 100.0,
        ]);
        #$response = $client->request('GET', 'https://tajukonline.perpusnas.go.id/DataAuthListSemuaRuas.aspx?keyword=perpustakaan&operand=1&tajuk=4');
        $response = $client->request('GET', 'https://tajukonline.perpusnas.go.id/DataAuthListSemuaRuas.aspx', [
            'query' => [
                'keyword' => $keyword,
                'operand' => '1',
                'tajuk' => '4'
            ]
        ]);
        $page_crawled = $response->getBody()->getContents();
        #echo ($page_crawled); die('page crawled');
        $crawler = new Crawler($page_crawled);
        $subCrawler = $crawler->filter("#ContentPlaceHolder1_dgData > tr > td");
        $data = array();
        foreach($subCrawler as $domElement) {
            #var_dump($domElement->nodeName);
            #echo trim(($domElement->nodeValue))."\n";
            $data[] = trim(($domElement->nodeValue));
        }
        #var_dump($data);
        #echo $keyword;
        unset($data[0]);
        unset($data[1]);
        unset($data[2]);
        unset($data[3]);
        unset($data[4]);
        #var_dump($data);

        $i = -1;
        $finres = array();
        foreach ($data as $k => $v) {
            if($k % 5 == 0) {
                #if ($i != 0) {
                    $i++;
                #}
                #echo 'No: '.$v."<br />";
                $finres[$i]['no'] = $v;
            }
            if($k % 5 == 1) {
                #echo 'Jumlah cantuman: '.$v."<br />";
                $finres[$i]['jumlah_cantuman'] = $v;
            }
            if($k % 5 == 2) {
                #echo 'Tajuk: '.$v."<br />";
                $finres[$i]['tajuk'] = $v;
            }
            if($k % 5 == 3) {
                #echo 'Klasifikasi: '.$v."<hr />";
                $finres[$i]['klasifikasi'] = trim($v);
            }
        }
        #var_dump($finres);
        preg_match_all("/^.*onclick\=\"OpenDetail.*$/misU", $page_crawled, $matches);
        #var_dump($response->getBody()->getContents());
        $i = 0;
        foreach ($matches[0] as $km => $vm) {
            $_tid = explode("OpenDetail('", $vm);
            $tid = trim(preg_replace("/\'\)\;\"/i", "", $_tid[1]));
            #echo ($tid)."\n";
            $finres[$i]['tid'] = $tid;
            $i++;
        }
        return $finres;
    

    }
}

