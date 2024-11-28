<?php
class Platonus {
    public $baseUrl;
    private $endpoints;
    public $authToken;
    public $sid;
    private $headers;

    public function __construct() {
        $this->baseUrl = 'https://platonus.arsu.kz/rest';
        $this->endpoints = [
            'login' => '/api/login',
            'logout' => '/api/logout',
            'journal' => '/api/journal',
            'subjects' => '/mobile/journal/records',
        ];
        $this->headers = ["Content-Type" => "application/json"];
    }

    private function request($endpoint, $method = 'GET', $data = []) {
        $url = $this->baseUrl . $endpoint;
        $this->setAuthHeaders();

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->formatHeaders(),
            CURLOPT_POSTFIELDS => !empty($data) ? json_encode($data) : null,
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('CURL Error: ' . curl_error($ch));
        }

        $this->lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function setAuthHeaders() {
        if ($this->authToken) {
            $this->headers['token'] = $this->authToken;
        }
        if ($this->sid) {
            $this->headers['sid'] = $this->sid;
        }
    }

    private function formatHeaders() {
        return array_map(fn($key, $value) => "$key: $value", array_keys($this->headers), $this->headers);
    }

    public function getLastHttpCode() {
        return $this->lastHttpCode;
    }

    public function login($login, $password) {
        $data = [
            "authForDeductedStudentsAndGraduates" => "false",
            "login" => $login,
            "password" => $password,
        ];

        $response = $this->request($this->endpoints['login'], 'POST', $data);

        if (empty($response['login_status']) || $response['login_status'] !== 'success') {
            throw new Exception('Login failed: ' . json_encode($response));
        }

        $this->authToken = $response['auth_token'];
        $this->sid = $response['sid'];

        return true;
    }
    
    public function getAuthToken() {
        return $this->authToken;
    }

    public function getSid() {
        return $this->sid;
    }
    
    public function setSid($sid) {
        $this->sid = $sid;
    }
    
    public function setAuthToken($authToken) {
        $this->authToken = $authToken;
    }

    public function getJournal($year, $semester, $language = 'ru') {
        return $this->request($this->endpoints['journal'] . "/$year/$semester/$language");
    }

    public function getSubjects($year, $semester) {
        return $this->request($this->endpoints['subjects'] . "/$year/$semester");
    }

    public function getSubject($year, $semester, $subjectID) {
        return $this->request($this->endpoints['subjects'] . "/$year/$semester?subjectID=$subjectID");
    }


    public function logout() {
        $this->request($this->endpoints['logout'], 'POST');
        $this->authToken = null;
        $this->sid = null;

        return true;
    }
}