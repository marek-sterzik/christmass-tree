<?php

$queueFile = __DIR__ . "/queue.txt";

class Queue
{
    private $queueFile;

    public function __construct(string $queueFile)
    {
        $this->queueFile = $queueFile;
    }

    public function put(array $jsonData): self
    {
        file_put_contents($this->queueFile, json_encode($jsonData)."\n", FILE_APPEND | LOCK_EX);
        return $this;
    }

    public function read(): array
    {
        $records = [];
        $fd = @fopen($this->queueFile, "r+");
        if (!$fd) {
            return $records;
        }
        flock($fd, LOCK_EX);
        while ($line = fgets($fd)) {
            $record = @json_decode($line, true);
            if (is_array($record)) {
                $records[] = $record;
            }
        }
        fseek($fd, 0, SEEK_SET);
        ftruncate($fd, 0);

        flock($fd, LOCK_UN);
        fclose($fd);
        return $records;
    }

    public function readWithWaiting(): array
    {
        for ($i = 0; $i < 100; $i++) {
            $data = $this->read();
            if (!empty($data)) {
                return $data;
            }
            usleep(100000);
        }
    }
}

$queue = new Queue($queueFile);

$data = $_GET['write'] ?? null;

if (is_string($data)) {
    $data = @json_decode($data, true);
    if (is_array($data)) {
        $queue->put($data);
    }
}

if ($_GET['read'] ?? false) {
    $data = $queue->readWithWaiting();
} else {
    $data = "ok";
}

echo json_encode($data) . "\n";
