<?php
$config = parse_ini_file('./config.ini');
?>
<!DOCTYPE html>

<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0" />
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        <link href="http://cdn.quilt.janrain.com/2.1.4/quilt.css" media="all" rel="stylesheet" type="text/css" />
        <style type="text/css">
          .contentBoxWhiteShadow .btn_large {margin-top: 10px}
          .contentBoxDarkBlueTexture {border-radius: 0; padding-top: 50px; padding-bottom: 50px;}
          .contentBoxDarkBlueTexture h1 {padding-bottom: 25px;}
          .col_10 h2 {padding-top: 0;}
          .centered-col {float: none;margin: 0 auto; display: block;}
        </style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>        <script type="text/javascript">

            // set up the navigation
            $(document).ready(function() {

                // on sign out, tell the widget to destroy the session
                $('#signOut').click(function() {

                    if (confirm("Really sign out?")) {
                        janrain.events.onCaptureSessionEnded.addHandler(function(result){
                            window.location.reload();
                        });
                        janrain.capture.ui.endCaptureSession();
                    }

                    return false;
                });
            });

            function janrainCaptureWidgetOnLoad() {
                janrain.settings.capture.flowName = 'signIn';
                janrain.settings.language = 'en';
                janrain.settings.capture.stylesheets = ['demo.css'];
                janrainReturnExperience();
                janrain.capture.ui.start();
                janrain.events.onCaptureRegistrationSuccess.addHandler(function(result){
                    document.getElementById("launchDemo").style.display = 'none';
                });
                janrain.events.onCaptureLoginSuccess.addHandler(function(result){
                    janrain.capture.ui.modal.close();
                    document.getElementById("launchDemo").style.display = 'none';
                    window.location.reload();
                });
                janrain.events.onCaptureSessionFound.addHandler(function(result){
                   document.getElementById("launchDemo").style.display = 'none';
                });
                janrain.events.onCaptureSessionNotFound.addHandler(function(result){
                   document.getElementById("launchDemo").style.display = 'block';
                });
                janrain.events.onCaptureSessionEnded.addHandler(function(result){
                    window.location.reload();
                });
                janrain.events.onCaptureSessionFound.addHandler(function(result){
                    $("#loggedInNavigation").show();
                    $("#loggedOutNavigation").hide();
                });

                janrain.events.onCaptureSessionNotFound.addHandler(function(result){
                    $("#loggedInNavigation").hide();
                    $("#loggedOutNavigation").show();
                });

                janrain.events.onCaptureBackplaneReady.addHandler(function(){
                  var handleMsg = function(msg){
                    if (msg.type == 'identity/login'){
                      $("#loggedInNavigation").hide();
                      $("#loggedOutNavigation").show();
                      janrain.capture.ui.modal.close();
                      $.ajax({
                        type: 'GET',
                        url: 'getmessage.php?bus=' + janrain.settings.capture.backplaneBusName
                          + '&messageUrl=' + msg.messageURL,
                        success: function(res) {
                          var div = $('<div>').addClass('cenetered-col col10').append($('<pre>').html(JSON.stringify(res, null, 4)));
                          $('#mainContent').append(div);
                          $('#bpResults').show();
                        }
                      });
                    }
                  }
                  Backplane.subscribe(handleMsg);
                  var messages = Backplane.getCachedMessages();
                  for (var i in messages) {
                    handleMsg(messages[i]);
                  }

                });
            }
        </script>
    </head>
    <body class="janrain-font">
        <div class="global_nav">
            <div class="wrapper">
                <ul id="loggedInNavigation" class="nav-list" style="display: none;">
                  <li><a href="index.php">Home</a></li>
                  <li><a href="replay.php">Replay</a></li>
                  <li><a href="#" id="signOut">Sign Out</a></li>
                </ul>
                <ul id="loggedOutNavigation" class="nav-list">
                  <li><a href="index.php">Home</a></li>
                  <li><a href="replay.php">Replay</a></li>
                  <li><a href="#" class="capture_modal_open" id="signInLink">Sign In</a></li>
                </ul>
            </div>
        </div>
        <div class="contentBoxDarkBlueTexture">
            <div class="wrapper">
                <h1 class="lrg centerText">Welcome to the Janrain Demo Site.</h1>
            </div>
        </div>
        <div class="wrapper">
            <div id="launchDemo" class="section" style="display: none;">
                <div class="grid-block">
                    <div class="centered-col col2">
                        <div class="contentBoxWhiteShadow">
                            <div class="grid_block">
                                <div class="col_2">
                                    <span class="janrain-icon-gears janrain-icon-48 janrain-icon-green-bg"></span>
                                </div>
                                <div class="col_10">
                                    <h2>Demo Capture Widget</h2>
                                    See examples of the sign in, create account and edit profile flow
                                </div>
                            </div>
                            <div class="rightText">
                                <a href="#" class="capture_modal_open primary btn_large btn">Launch Demo <span class="step janrain-icon-forward janrain-icon-16"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="bpResults" class="section" style="display:none;">
                <div class="grid-block">
                    <div class="centered-col col10">
                        <div class="contentBoxWhiteShadow">
                            <div class="grid_block" id="mainContent">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          <script src="http://pse.janrain.com/customer_dev/<?php echo $config['pse_app_id']; ?>/scripts/index.js" id="janrainCaptureDevScript"></script>
          <script>
            janrain.settings.capture.backplane = true;
            janrain.settings.capture.backplaneBusName = '<?php echo $config['bp_bus']; ?>';
            janrain.settings.capture.backplaneVersion = 2;
            janrain.settings.capture.backplaneBlock = 20;
          </script>
    </body>
</html>

