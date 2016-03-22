<?php
/**
 * Class API
 *
 * This class is used to call all Cronalytics API Endpoints.
 * it is a simple class that is here to make sure everything is always in the correct format
 *
 * @package Cronalytics
 */
class CronalyticsAPI extends RestClient
{

    /**
     * @var string
     */
    private $API_URL = 'https://api.cronalytics.io';

    private $cronOptions = array();

    /**
     * API constructor.
     * @param array $options
     */
    public function __construct($options = array())  {
        $defaultOptions =  array(
            'x' => false,
            'vvv' => false
        );

        $this->cronOptions = array_merge($defaultOptions, $options);
        if ($this->cronOptions['x']) {
            $this->API_URL = 'http://172.17.0.1:8080';
        }

        $this->vvv('Cronalytics API constructed', $this->cronOptions);
        $this->vvv('default headers', $this->getDefaultHeaders());
        parent::__construct();
    }


    protected function vvv($message, $vars = 'random string that will never happen-29y3iuqhgifassf') {
        if ($this->cronOptions['vvv']) {
            echo "-- API: {$message}" . PHP_EOL;

            //to lazy to figure out a way to account for null, false vars that i will still want to show.
            if ($vars !== 'random string that will never happen-29y3iuqhgifassf') {
                var_dump($vars);
            }
        }
    }

    /**
     * @return array
     */
    protected function getDefaultHeaders() {
        return array(
            "Content-type" => "application/json",
            "Accept" => "text/plain",
        );
    }

    /**
     * Build Cron resource endpoint
     *
     * @param $cronHash
     * @return string
     */
    private function getCronUrl($cronHash = '') {
        $url = $this->API_URL . '/cron';
        if ( ! empty( $cronHash ) ) {
            $url .= "/{$cronHash}/";
        }

        return $url;
    }

    /**
     * Build Trigger resource endpoint
     *
     * @param $triggerHash
     * @return string
     */
    private function getTriggerUrl($triggerHash) {
        return $this->API_URL . '/trigger/' . $triggerHash . '/';
    }

    /**
     * Create a new 'Cron'
     *
     * @throws \Exception
     */
    public function addCron() {
        throw new \Exception('Not implemented');
    }

    public function addInterval($name, $email, $added_by, $interval, $interval_start, $interval_type) {
        $params = array(
            'type' => 'interval',
            'name' => $name,
            'email' => $email,
            'added_by' => $added_by,
            'interval' => $interval,
            'interval_start' => $interval_start,
            'interval_type' => $interval_type,
        );

        $response = $this->post($this->getCronUrl(), json_encode($params), $this->getDefaultHeaders());
        return $response->response;
    }

    /**
     * Given an existing Cron start a new trigger
     *
     * @param $cronHash
     * @param $startTime
     * @return mixed
     */
    public function startTrigger($cronHash, $startTime) {
        $this->vvv('Start trigger');
        $params  =  array(
            'start' => $startTime->format(DateTime::ISO8601),
        );
        $params = json_encode($params);
        $url = $this->getCronUrl($cronHash) . 'start';

        $this->vvv('url', $url);
        $this->vvv('params', $params);

        $response = $this->post($url, $params, $this->getDefaultHeaders());

        $this->vvv('response', $response->response);
        return json_decode($response->response, true);
    }

    /**
     * Given a started trigger, update that trigger with the end conditions
     *
     * @param $triggerHash
     * @param $endTime
     * @param null $result
     * @param null $isSuccess
     * @return mixed
     */
    public function endTrigger($triggerHash, $endTime, $result = null, $isSuccess = null) {
        $params = array(
            "end" => $endTime->format(DateTime::ISO8601),
            "result" => $result,
            "success" =>  $isSuccess,
        );
        $params = json_encode($params);
        $headers = $this->getDefaultHeaders();
        $headers['X-HTTP-Method-Override'] = 'PATCH';
        $url = $this->getTriggerUrl($triggerHash) . 'end';

        $this->vvv('Sending End trigger');
        $this->vvv('url', $url);
        $this->vvv('params', $params);
        $this->vvv('headers', $headers);

        $response = $this->execute($url, 'PATCH', $params, $headers);
        return $response;
    }

    /**
     * List all Triggers from a specific cron.
     *
     * This list can be filtered.
     *
     * @throws \Exception
     */
    public function listTriggers() {
        throw new \Exception('Not implemented');
    }


}