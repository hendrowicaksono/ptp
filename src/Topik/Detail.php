<?php 

namespace hendrowicaksono\TajukOnline\Topik;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class Detail
{
    public function index($tid)
    {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://httpbin.org',
            'verify' => false,
            // You can set any number of default request options.
            'timeout'  => 100.0,
        ]);
        #$response = $client->request('GET', 'https://tajukonline.perpusnas.go.id/AuthorityDetail.aspx?id=30459');
        $response = $client->request('GET', 'https://tajukonline.perpusnas.go.id/AuthorityDetailLengkap.aspx', [
            'query' => [
                'id' => $tid,
                'operand' => '1',
                'tajuk' => '4'
            ]
        ]);
        $page_crawled = $response->getBody()->getContents();
        #echo ($page_crawled); die('page crawled');
        $crawler = new Crawler($page_crawled);
        #$subCrawler = $crawler->filter("td > table > tr");
        $tajukCrawler = $crawler->filter("td > table > tr > td > span#lbSubjek");
        $data = array();
        $finres = array();
        #var_dump($tajukCrawler); die();
        #foreach($subCrawler as $domElement) {
        foreach($tajukCrawler as $domElement) {
            #var_dump($domElement->nodeName);
            #echo trim(($domElement->nodeValue))."\n";
            $data['tajuk'] = trim(($domElement->nodeValue));
            #var_dump($_td);
        }
        if (empty($data['tajuk'])) {
            $finres['status'] = 'noresult';
            $finres['message'] = 'no result found';
            #$finres['data'] = $data;
        } else {
            #echo "disini\n";
            $finres['status'] = 'success';
            $finres['message'] = 'some results found';
            #$finres['data'] = $data;
        }

        # LIHAT JUGA
        $lihatjugaCrawler = $crawler->filter("td > table > tr#trLihatJuga > td");
        foreach($lihatjugaCrawler as $domElement) {
            #var_dump($domElement->nodeName);
            #echo trim(($domElement->nodeValue))."\n";
            $data['lihat_juga'] = trim(($domElement->nodeValue));
            $data['lihat_juga'] = preg_replace("/1\./misU", "", $data['lihat_juga']);
            $data['lihat_juga'] = trim($data['lihat_juga']);
        }

        # IStILAH LUAS
        $istilahluasCrawler = $crawler->filter("tr#trIstilahLuas > td");
        $istilahluasCounter = 0;
        foreach($istilahluasCrawler as $domElement) {
            if ($istilahluasCounter > 1) {
                $_data['istilah_luas'] = trim(($domElement->nodeValue));
                $_data['istilah_luas'] = preg_replace("/\s+/", " ", $_data['istilah_luas']);
                $_data['istilah_luas'] = preg_replace("/[1-9]\./", "|", $_data['istilah_luas']);
                $_istilah_luas = explode ("|", $_data['istilah_luas']);
                foreach ($_istilah_luas as $kil => $vil) {
                    $vil = trim($vil);
                    if ($vil != '') {
                        $data['istilah_luas'][] = $vil;
                    }
                }
            }
            $istilahluasCounter++;
        }

        # IStILAH BERKAIT
        $istilahberkaitCrawler = $crawler->filter("tr#trIstilahBerkait > td");
        $istilahberkaitCounter = 0;
        foreach($istilahberkaitCrawler as $domElement) {
            if ($istilahberkaitCounter > 1) {
                $_data['istilah_berkait'] = trim(($domElement->nodeValue));
                $_data['istilah_berkait'] = preg_replace("/\s+/", " ", $_data['istilah_berkait']);
                $_data['istilah_berkait'] = preg_replace("/[1-9]\./", "|", $_data['istilah_berkait']);
                $_istilah_berkait = explode ("|", $_data['istilah_berkait']);
                foreach ($_istilah_berkait as $kib => $vib) {
                    $vib = trim($vib);
                    if ($vib != '') {
                        $data['istilah_berkait'][] = $vib;
                    }
                }
            }
            $istilahberkaitCounter++;
        }

        # IStILAH SEMPIT
        $istilahsempitCrawler = $crawler->filter("tr#trIstilahSempit > td");
        $istilahsempitCounter = 0;
        foreach($istilahsempitCrawler as $domElement) {
            if ($istilahsempitCounter > 1) {
                $_data['istilah_sempit'] = trim(($domElement->nodeValue));
                $_data['istilah_sempit'] = preg_replace("/\s+/", " ", $_data['istilah_sempit']);
                #$_data['istilah_sempit'] = preg_replace("/[1-9]\./", "|", $_data['istilah_sempit']);
                $_data['istilah_sempit'] = preg_replace("/[0-9]|[0-9][0-9]\./", "|", $_data['istilah_sempit']);
                $_data['istilah_sempit'] = preg_replace("/\./", "", $_data['istilah_sempit']);
                $_istilah_sempit = explode ("|", $_data['istilah_sempit']);
                foreach ($_istilah_sempit as $kis => $vis) {
                    $vis = trim($vis);
                    if ($vis != '') {
                        $data['istilah_sempit'][] = $vis;
                    }
                }
            }
            $istilahsempitCounter++;
        }



        $finres['data'] = $data;
        #var_dump($finres); die();

    
    
        return $finres;
    

    }
}

