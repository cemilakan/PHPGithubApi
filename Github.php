<?php

class Github
{
    private $BASE_URL = "https://api.github.com";
    private $TOKEN = "de00e3038e2a041df9954d5a55fdcce369b9f32c";
    private $curl;

    public function __construct() {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 1);
        curl_setopt($this->curl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
    }

    private function call($action = null, $data = [], $type = 'GET')
    {
        $url = $this->BASE_URL.$action;
        $header = ['Content-Type: application/json; charset=utf-8', 'Accept: application/vnd.github.v3+json'];
        array_push($header,'Authorization: token '.$this->TOKEN);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($this->curl);
        $httpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);
        return ['data' => json_decode($result), 'code' => $httpcode];
    }

    public function getOrg($name)
    {
        return $this->call("/users/".$name);
    }

    public function listRepos($name, $type = "user")
    {
        return $this->call('/'.$type.'/'.$name.'/repos');
    }

    public function addRepo($name, $data, $type = "user")
    {
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));
        return $this->call('/'.$type.'/'.$name.'/repos');
    }

    public function getRepo($owner, $repo)
    {
        return $this->call('/repos/'.$owner.'/'.$repo);
    }

    public function updateRepo($owner, $repo, $data)
    {
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));
        return $this->call('/repos/'.$owner.'/'.$repo);
    }

    public function deleteRepo($owner, $repo)
    {
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->call('/repos/'.$owner.'/'.$repo);
    }

    public function listRepoTags($owner, $repo)
    {
        return $this->call('/repos/'.$owner.'/'.$repo.'/tags');
    }

    public function getUser($username)
    {
        return $this->call("/users/".$username);
    }

}
// Sample
$github = new Github();
$user = $github->getUser("cemilakan");

echo json_encode($user["data"]);
