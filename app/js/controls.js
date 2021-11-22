var source;
source = new EventSource('/event-stream.php');
source.onmessage = function (stream) {
  document.getElementById("ascii-frame").textContent = stream.data;
}

function play(pointer) {
  source.close();
  source = new EventSource('/event-stream.php?src=' + streamtype + '&pointer=' + pointer);
  source.onmessage = function (stream) {
    document.getElementById("ascii-frame").textContent = stream.data;
    document.getElementById("pointer-id").textContent = stream.lastEventId;
  };
  audiosource = document.getElementById('soundtrack').getAttribute('src');
  if (streamtype == 'jedi') {
    document.getElementById("stars").classList.add('stars');
    setTimeout(function () {
      document.getElementById("twinkling").classList.add('twinkling');
    },
      30000);
    document.getElementById("death-star").classList.add('death-star');
    document.getElementById("no-star").classList.add('death-star');

    if (audiosource != "/media/sound/mos-eisley-kazoo-orchestra.mp3") {
      document.getElementById('soundtrack').setAttribute('src', '/media/sound/mos-eisley-kazoo-orchestra.mp3');
    }
    document.getElementById("credits").textContent = "* ASCII art credit: ASCII STAR WARS by Simon Jansen (http://www.asciimation.co.nz) \n" +
      '* Music credit: Star Wars theme by Mos Eisley Kazoo Orchestra ' +
      '';
  } else if (streamtype == 'asciistream' &&
    audiosource != "https://assets.mixkit.co/sfx/preview/mixkit-small-waves-harbor-rocks-1208.mp3"
  ) {
    document.getElementById('soundtrack').setAttribute('src', 'https://assets.mixkit.co/sfx/preview/mixkit-small-waves-harbor-rocks-1208.mp3');
  } else if (streamtype == 'ProDev' &&
    audiosource != "https://assets.mixkit.co/sfx/preview/mixkit-keyboard-typing-1386.mp3"
  ) {
    document.getElementById('soundtrack').setAttribute('src', 'https://assets.mixkit.co/sfx/preview/mixkit-keyboard-typing-1386.mp3');
  }
  document.getElementById('soundtrack').play();
  document.getElementById("stop-button").classList.remove('hidden');
  document.getElementById("play-button").classList.add('hidden');
}

function wait() {
  source.close();
  document.getElementById('soundtrack').pause();
  document.getElementById("play-button").classList.remove('hidden');
  document.getElementById("stop-button").classList.add('hidden');
  if (streamtype == 'jedi') {
    document.getElementById("stars").classList.remove('stars');
    document.getElementById("twinkling").classList.remove('twinkling');
    document.getElementById("death-star").classList.remove('death-star');
  }
}
var link;
link = document.getElementById("credits");
link.ondblclick = function () {
  window.open('http://www.asciimation.co.nz');
}
