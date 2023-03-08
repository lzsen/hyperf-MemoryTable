<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Lzsen\MemoryTable\Listener;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Lzsen\MemoryTable\AbstractMemoryTable;
use Lzsen\MemoryTable\Annotation\MemoryTable;
use Psr\Container\ContainerInterface;

class BeforeMainServerStartListener implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            BeforeMainServerStart::class,
        ];
    }

    /**
     * @param BeforeMainServerStart $event
     */
    public function process(object $event): void
    {
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $collect = AnnotationCollector::getClassesByAnnotation(MemoryTable::class);
        foreach ($collect as $handler => $annotation) {
            $refClass = new \ReflectionClass($handler);
            $instance = $refClass->newInstance();
            if ($instance instanceof AbstractMemoryTable && $refClass->hasMethod('initialization')) {
                $method = $refClass->getmethod('initialization');
                $method->setAccessible(true);
                $method->invoke($instance);
                $logger->info(sprintf('MemoryTable %s created successfully', $handler));
            }
            $this->container->set($handler, $instance);
        }
    }
}
