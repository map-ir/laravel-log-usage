<?php


namespace MapIr\LaravelLogUsage\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kafka\Producer;
use Kafka\ProducerConfig;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

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
            $config = ProducerConfig::getInstance(); // create new config kafka
            $config->setMetadataRefreshIntervalMs(config("logUsage.metadata_refresh_interval_ms"));
            $config->setMetadataBrokerList(config('logUsage.kafka_server'));
            $config->setBrokerVersion(config("logUsage.broker_version"));
            $config->setRequiredAck(config("logUsage.required_ack"));
            $config->setIsAsyn(config("logUsage.is_async"));
            $config->setProduceInterval(500);
            $producer =new Producer(
                function() use($logData) {
                    return [
                        [
                            'topic' => config('logUsage.topic'),
                            'value' => $logData ,
                            'key' => config('logUsage.key'),
                        ],
                    ];
                }
            );
            $logger= new Logger('log_usage');
            $logger->pushHandler(new SyslogHandler('log_usage'));
            $producer->setLogger($logger);
            $producer->success(function() {
                Log::info('Broker: successfully sent');
            });
            $producer->error(function() {
                Log::info('Broker: failed to send');
            });
            $producer->send(true);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $response;
        }

        return $response;
    }
}
