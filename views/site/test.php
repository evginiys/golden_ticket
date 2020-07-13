<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><button class="btn btn-lg btn-success" id="btn">Get started with Yii</button></p>
    </div>

    <div class="body-content">
      <script>
          var socket = new WebSocket("ws://gt.local/ws");

          document.getElementById("btn").onclick = function() {
              socket.send(JSON.stringify({
                  handle: "online",
                  data : {
                      userId: "1",
                  }
              }));
          };

          socket.onmessage = function(event) {
              console.log(event);
          };
      </script>
    </div>
</div>
