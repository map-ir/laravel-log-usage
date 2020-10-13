<?php

/*
 * Config log usage map-ir
 */
return [
    "topic" => env("TOPIC","log-usage-undefined"),

    //Kafka Broker server list
    "kafka_server" => env("KAFKA_HOST","localhost:9202"),

    //User supplied broker version
    "broker_version" => env("BROKER_VERSION","1.0.0"),

    /**
     * Topic metadata refresh interval in milliseconds.
     * The metadata is automatically refreshed on error and connect.
     * Use -1 to disable the intervened refresh
     */
    "metadata_refresh_interval_ms" => env("KAFKA_MRIM",10000),

    /**
     *This field indicates how many acknowledgements the leader broker must receive from ISR brokers before responding to the request:
     * 0=Broker does not send any response/ack to client,
     * 1=Only the leader broker will need to ack the message,
     * -1 or all=broker will block until message is committed by all in sync replicas (ISRs)
     * or broker's in.sync.replicas setting before sending response.
     */
    "required_ack" => env("REQUIRED_ACK",1),

    //Whether to use asynchronous production messages
    "is_async" => env("IS_ASYNC",false),
    // key for kafka producer
    "key" => env("KAFKA_KEY","Map-ir")
];
