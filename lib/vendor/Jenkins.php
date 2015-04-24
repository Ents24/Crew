<?php

class Jenkins
{
  private $jenkinsUrl;
  private $format;

  public function __construct($jenkinsUrl, $format = "json")
  {
    $this->jenkinsUrl = $jenkinsUrl;
    $this->format = $format;
  }

  public function findBuildForBranch($jobName, $branchName)
  {
    $builds = $this->getBuildsForJob($jobName);

    foreach($builds->builds as $build) {
      if(strpos($build->displayName, $branchName) === 0) {
        return $build;
      }
    }

    return null;
  }

  public function getBuildsForJob($jobName)
  {
    $endpoint = "/job/$jobName";
    $tree     = "builds[number,displayName,result,url]";

    return $this->makeRequest($endpoint, $tree);
  }

  private function makeRequest($endpoint, $tree = null)
  {
    $url = "{$this->jenkinsUrl}$endpoint/api/{$this->format}";

    if($tree) {
      $url = "$url?tree=$tree";
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);

    // make sure we got a real response
    if (strlen($response) == 0) {
      $errno = curl_errno($ch);
      $error = curl_error($ch);
      throw new Exception(-1, "CURL error: $errno - $error", $url);
    }

    // make sure we got a 200
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($code != 200) {
      throw new Exception($code, "HTTP status code: $code, response=$response", $url);
    }

    curl_close($ch);

    if($this->format == "json") {
      $response = json_decode($response);
    }

    return $response;
  }
}