<?php
$asciifile = './ascii/play.ascii';
$frame_id = 0;

init($asciifile, $frame_id);
$stream = new EventStreamer($asciifile, $frame_id);
$stream->sendHeaders();
$stream->startStreaming();
$stream->endStream();


function init(&$asciifile, &$frame_id)
{
  if (isset($_GET['pointer'])) {
    $frame_id = intval($_GET['pointer']);
  }
  if (isset($_GET['src'])) {
    switch (true) {
      case strval($_GET['src']) == 'asciistream':
        $asciifile = './ascii/asciistream.ascii';
        break;
      case strval($_GET['src']) == 'jedi':
        $asciifile = './ascii/jedi.ascii';
        break;
      case strval($_GET['src']) == 'ProDev':
        $asciifile = './ascii/prodev.ascii';
        break;
      default:
        $asciifile = './ascii/play.ascii';
    }
  }
}



class EventStreamer
{

  const FRAME_SIZE = 13;
  const DELAY = 250;
  private $file_stream;
  private int $start_pointer;

  function __construct(string $file, int $pointer = 0)
  {
    $this->file_stream = fopen($file, 'r');
    $this->start_pointer = $pointer;
  }

  function sendHeaders()
  { header('X-Accel-Buffering: no');
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
  }

  function endStream()
  {
    ob_end_clean();
  }

  function startStreaming()
  {
    ob_start();
    if ($this->file_stream) {
      $frame_id = 0;
      if ($this->start_pointer > 0) {
        $frame_catcher = 0;
        while ($frame_catcher < ($this->start_pointer * (self::FRAME_SIZE+1))) {
          fgets($this->file_stream);
          $frame_catcher++;
        }
        $frame_id = $this->start_pointer;
      }
      $speed = self::DELAY;
      while ($line = fgets($this->file_stream)) {
        $text_speed = intval($line);
        if ($text_speed > 1) {
          $speed = 2 * self::DELAY * $text_speed;
        }
        $data = "";
        echo "retry:" . self::DELAY . PHP_EOL;
        echo "id: $frame_id" . PHP_EOL;
        for ($frame_pos = 0; $frame_pos < self::FRAME_SIZE; $frame_pos++) {
          echo 'data: ' . fgets($this->file_stream);
        }
        echo PHP_EOL;
        ob_flush();
        flush();

        if ($text_speed > 1) {
          $speed = self::DELAY * 100 * $text_speed;
        }
        usleep($speed);

        //  sendData($frame_id, $data, $speed);
        $frame_id++;
      }
    }
  }

  /**
   * Constructs the SSE data format and flushes that data to the client.
   *
   * @param string $id id of this connection.
   * @param string $data Line of text that should be transmitted.
   * @param string $speed refresh rate speed.
   */
  private function sendData($id, $data, $speed)
  {
    echo "retry:" . $speed . PHP_EOL;
    echo "id: $id" . PHP_EOL;
    echo $data;
    echo PHP_EOL;
    ob_flush();
    flush();
  }
}
