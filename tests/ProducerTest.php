<?php

namespace MapIr\LaravelLogUsage\Tests;

use DateTimeImmutable;
use Kafka\Consumer;
use Kafka\Consumer\StopStrategy\Callback;
use Kafka\ConsumerConfig;
use Kafka\ProducerConfig;
use Orchestra\Testbench\TestCase;
use MapIr\LaravelLogUsage\LaravelLogUsageServiceProvider;

class ProducerTest extends TestCase
{
    private const MESSAGES_TO_SEND = 30;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $brokers;

    /**
     * @var string
     */
    private $topic;

    protected function getPackageProviders($app)
    {
        return [LaravelLogUsageServiceProvider::class];
    }
    protected function getEnvironmentSetUp($app)
    {
        $this->version  = getenv('BROKER_VERSION');
        $this->brokers  = getenv('KAFKA_HOST');
        $this->topic    = getenv('TOPIC');

        if (! $this->version || ! $this->brokers || ! $this->topic) {
            self::markTestSkipped(
                'Environment variables "KAFKA_VERSION", "KAFKA_TOPIC", and "KAFKA_BROKERS" must be provided'
            );
        }
    }
    protected function configureProducer()
    {
        $config = ProducerConfig::getInstance();
        $config->setMetadataBrokerList($this->brokers);
        $config->setBrokerVersion($this->version);
    }

    /**
     * @test
     *
     * @runInSeparateProcess
     */
    public function consumeProducedMessages(): void
    {
        $this->configureConsumer();

        $consumedMessages = 0;
        $executionEnd     = new DateTimeImmutable('+1 minute');

        $consumer = new Consumer(
            new Callback(
                function () use (&$consumedMessages, $executionEnd): bool {
                    return $consumedMessages >= self::MESSAGES_TO_SEND || new DateTimeImmutable() > $executionEnd;
                }
            )
        );

        $consumer->start(
            function (string $topic, int $partition, array $message) use (&$consumedMessages): void {
                self::assertSame($this->topic, $topic);
                self::assertLessThan(3, $partition);
                self::assertArrayHasKey('offset', $message);
                self::assertArrayHasKey('size', $message);
                self::assertArrayHasKey('message', $message);
                self::assertArrayHasKey('crc', $message['message']);
                self::assertArrayHasKey('magic', $message['message']);
                self::assertArrayHasKey('attr', $message['message']);
                self::assertArrayHasKey('key', $message['message']);
                self::assertArrayHasKey('value', $message['message']);
                self::assertContains('msg-', $message['message']['value']);
                if (version_compare($this->version, '0.10.0', '>=')) {
                    self::assertArrayHasKey('timestamp', $message['message']);
                    self::assertNotEquals(-1, $message['message']['timestamp']);
                }
                ++$consumedMessages;
            }
        );

        self::assertSame(self::MESSAGES_TO_SEND, $consumedMessages);
    }
    private function configureConsumer(): void
    {
        $config = ConsumerConfig::getInstance();
        $config->setMetadataBrokerList($this->brokers);
        $config->setBrokerVersion($this->version);
        $config->setGroupId('kafka-php-tests');
        $config->setOffsetReset('earliest');
        $config->setTopics([$this->topic]);
    }

    /**
     * @param int $amount
     * @return string[][]
     */
    public function createMessages(int $amount = self::MESSAGES_TO_SEND): array
    {
        $messages = [];

        for ($i = 0; $i < $amount; ++$i) {
            $messages[] = [
                'topic' => $this->topic,
                'value' => 'msg-' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
            ];
        }

        return $messages;
    }
}
