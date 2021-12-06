<?php


namespace MapIr\LaravelLogUsage\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RdKafka\Conf;
use RdKafka\Producer;
use RdKafka\Exception;
class LogUsageMiddleware
{
    /**
     * Handle an incoming request.
     *
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $userData = json_decode($response->getContent(), true);
        $requestBody = $request->all(); // get all data request
        //create log data from request
        $logData = json_encode([
            'response_time' => date('Y-m-d H:i:s'),
            'api_path' => $request->path(),
            'full_url' => $request->getUri(),
            'response_status' => $response->getStatusCode(),
            'ip' => $request->ip(),
            'port' => $request->getPort(),
            'user-agent' => $request->header('user-agent'),
            'response' => $userData,
            'request_headers' => $request->header(),
            'response_headers' => $response->headers,
            'request_method' => $request->getMethod(),
            'request_body_params' => $requestBody
        ], JSON_UNESCAPED_UNICODE);

        try {
            $conf = new Conf();
            $conf->set('metadata.broker.list', config('logUsage.kafka_server'));
            $producer = new Producer($conf);
            $topic = $producer->newTopic(config('logUsage.topic'));
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, $logData);
            $producer->poll(0);
            $result = $producer->flush(config("logUsage.metadata_refresh_interval_ms"));
            if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
                Log::error('Was unable to flush, messages might be lost!');

            } else{
                Log::info('Broker: successfully sent');
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return $response;
        }
        return $response;
    }
}
