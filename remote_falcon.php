<?
require_once('common.php');
include_once "/home/fpp/media/plugins/remote-falcon/baseurl.php";
include_once "/home/fpp/media/plugins/remote-falcon/version.php";
$baseUrl = getBaseUrl();
$pluginVersion = getVersion();
$rfSequencesUrl = $baseUrl . "/controlPanel/sequences";
$pluginName = basename(dirname(__FILE__));
$pluginConfigFile = $settings['configDirectory'] ."/plugin." .$pluginName;

$pluginEnabled = $pluginSettings['pluginEnabled'];
$pluginEnabled = $pluginEnabled == "true" ? true : false;
$autoRestartPlugin = $pluginSettings['autoRestartPlugin'];
$autoRestartPlugin = $autoRestartPlugin == "true" ? true : false;

$url = "http://127.0.0.1/api/plugin/remote-falcon/updates";
$options = array(
  'http' => array(
    'method'  => 'POST',
    'header'=>  "Content-Type: application/json; charset=UTF-8\r\n" .
                "Accept: application/json\r\n"
    )
);
$context = stream_context_create( $options );
$result = file_get_contents( $url, false, $context );
$response = json_decode( $result, true );
if ($response['updatesAvailable'] == 1) {
  $showUpdateDiv = "display:block";
}else{
  $showUpdateDiv = "display:none";
}

$remoteFalconState = "<h4 id=\"remoteFalconRunning\">Remote Falcon is currently running</h4>";
if($pluginEnabled == 0) {
  $remoteFalconState = "<h4 id=\"remoteFalconStopped\">Remote Falcon is currently stopped</h4>";
}

if (isset($_POST['restartRemoteFalcon'])) {
  WriteSettingToFile("pluginRestarting",urlencode("true"),$pluginName);
}
if (isset($_POST['stopRemoteFalcon'])) {
  $remoteFalconState = "<h4 id=\"remoteFalconStopped\">Remote Falcon is currently stopped</h4>";
  WriteSettingToFile("pluginEnabled",urlencode("false"),$pluginName);
}

$restartNotice = "";
if($autoRestartPlugin == 1) {
  $autoRestartPluginYes = "btn-primary";
  $autoRestartPluginNo = "btn-secondary";
  $restartNotice = "visibility: hidden;";
}else {
  $autoRestartPluginYes = "btn-secondary";
  $autoRestartPluginNo = "btn-primary";
  $restartNotice = "visibility: visible;";
}

?>

<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1"
    crossorigin="anonymous">
  <style>
    a {
      color: #D65A31;
    }
    #bodyWrapper {
      background-color: #20222e;
    }
    .pageContent {
      background-color: #171720;
    }
    .plugin-body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: rgb(238, 238, 238);
      background-color: rgb(0, 0, 0);
      font-size: 1rem;
      font-weight: 400;
      line-height: 1.5;
      padding-bottom: 2em;
      background-image: url("https://remotefalcon.com/brick-wall-background-with-juke.jpg");
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-position: top center;
      background-size: auto 100%;
    }
    .card {
      background-color: rgba(59, 69, 84, 0.7);
      border-radius: 0.5em;
      margin: 1em 1em 1em 1em;
      padding: 1em 1em 1em 1em;
    }
    .card-body {
      background-color: rgba(59, 69, 84, 0);
    }
    .card-subtitle {
      font-size: .9rem;
    }
    .setting-item {
      padding-bottom: 2em;
    }
    .input-group {
      padding-top: .5em;
    }
    .btn-primary {
      background-color: #D65A31;
      border-color: #D65A31;
    }
    .btn-primary:hover {
      background-color: #D65A31;
      border-color: #D65A31;
    }
    .btn-primary:focus {
      background-color: #D65A31;
      border-color: #D65A31;
    }
    .btn-danger {
      background-color: #A72525;
      border-color: #A72525;
    }
    .btn-danger:hover {
      background-color: #A72525;
      border-color: #A72525;
    }
    .btn-danger:focus {
      background-color: #A72525;
      border-color: #A72525;
    }
    .hvr-underline-from-center {
      display: inline-block;
      vertical-align: middle;
      -webkit-transform: perspective(1px) translateZ(0);
      transform: perspective(1px) translateZ(0);
      box-shadow: 0 0 1px rgba(0, 0, 0, 0);
      position: relative;
      overflow: hidden;
    }
    .hvr-underline-from-center:before {
      content: "";
      position: absolute;
      z-index: -1;
      left: 51%;
      right: 51%;
      bottom: 0;
      background: #FFF;
      height: 4px;
      -webkit-transition-property: left, right;
      transition-property: left, right;
      -webkit-transition-duration: 0.3s;
      transition-duration: 0.3s;
      -webkit-transition-timing-function: ease-out;
      transition-timing-function: ease-out;
    }
    .hvr-underline-from-center:hover:before, .hvr-underline-from-center:focus:before, .hvr-underline-from-center:active:before {
      left: 0;
      right: 0;
    }
		#remoteFalconRunning {
			color: #60F779;
		}
		#remoteFalconStopped {
			color: #A72525;
		}
		#update {
      padding-bottom: 1em;
      font-weight: bold;
			color: #A72525;
		}
    #env {
      color: #A72525;
    }
    #warning {
      font-weight: bold;
      color: #A72525;
    }
		#restartNotice {
			font-weight: bold;
      color: #D65A31;
      <? echo $restartNotice; ?>
		}

    .printSettingLabelCol {
      display: none
    }

    #remoteTokenRow,
    #remotePlaylistRow,
    #requestFetchTimeRow {
      padding-top: .5em;
    }
    #remoteToken,
    #remotePlaylist,
    #requestFetchTime {
      width: 100%
    }
  </style>
</head>
<body>
  <div class="container-fluid plugin-body">
    <div class="container-fluid" style="padding-top: 2em;">
      <div class="card">
        <div class="card-body"><div class="justify-content-md-center row" style="padding-bottom: 1em;">
          <div class="col-md-auto">
            <h1>Remote Falcon Plugin v<? echo $pluginVersion ?></h1>
          </div>
        </div>
        <div class="justify-content-md-center row" style="padding-bottom: 1em;">
          <div class="col-md-auto">
            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=FFKWA2CFP6JC6&currency_code=USD&source=url" target="_blank" rel="noopener noreferrer">
              <img style="margin-left: 1em;" alt="RF_Donate" src="https://remotefalcon.com/support-button-v2.png">
            </a>
          </div>
        </div>
        <div class="justify-content-md-center row" style="padding-bottom: 1em;">
          <div class="col-md-auto">
            <? echo $remoteFalconState; ?>
          </div>
        </div>
        <div style=<? echo "$showUpdateDiv"; ?>>
          <div id="update" class="justify-content-md-center row">
            <div class="col-md-auto">
              <h4 style="font-weight: bold;">An update is available!</h4>
            </div>
          </div>
        </div>
        <div class="justify-content-md-center row">
          <div class="col-md-auto">
            <h4 id="env"><? echo $baseUrl == "https://remotefalcon.me" ? "TEST" : "" ?></h4>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="card">
        <div class="card-body">
          <!-- Remote Token -->
          <div class="justify-content-md-center row setting-item">
            <div class="col-md-6">
							<div class="card-title h5">
								Remote Token <span id="restartNotice"> *</span>
							</div>
							<div class="mb-2 text-muted card-subtitle h6">
								Your Remote Token found on the Remote Falcon Control Panel
							</div>
						</div>
            <div class="col-md-6">
              <div class="input">
                <? PrintPluginSetting($pluginName, 'remoteToken'); ?>
              </div>
            </div>
          </div>
          <!-- Remote Playlist -->
          <div class="justify-content-md-center row setting-item">
            <div class="col-md-6">
              <div class="card-title h5">
                Remote Playlist <span id="restartNotice"> *</span>
              </div>
              <div class="mb-2 text-muted card-subtitle h6">
                This is the playlist that contains all the sequences to be controlled by your viewers
              </div>
            </div>
            <div class="col-md-6">
              <div class="input">
                <? PrintPluginSetting($pluginName, 'remotePlaylist', 'remotePlaylistUpdatedCallback'); ?>
                <script>
                  function remotePlaylistUpdatedCallback() {
                    var baseUrl = "<? echo $baseUrl ?>";
                    var playlists = [];
                    var remotePlaylist = pluginSettings['remotePlaylist'];
                    var remoteToken = pluginSettings['remoteToken'];
                    //var autoRestartPlugin = pluginSettings['autoRestartPlugin'];
                    //var pluginEnabled = pluginSettings['pluginEnabled'];
                    if(remoteToken.toString().length > 1 && remotePlaylist.toString().length > 1) {
                      $.ajax({
                        url: "/api/playlist/" + pluginSettings['remotePlaylist'],
                        type: 'GET',
                        async: false,
                        dataType: 'json',
                        success: function(data) {
                          playlists = data;
                        },
                        error: function() {
                          $.jGrowl('Unable to get data for playlist: ' + remotePlaylist, {themeState:'danger'});
                        }
                      });
                      if(playlists && playlists.mainPlaylist) {
                        var sequences = [];
                        var index = 1;
                        playlists.mainPlaylist.forEach(mainPlaylist => {
                          if(mainPlaylist.type === "both" || mainPlaylist.type === "sequence") {
                            sequences.push({
                              playlistName: mainPlaylist.sequenceName.replace(/\.[^/.]+$/, ""),
                              playlistDuration: mainPlaylist.duration,
                              playlistIndex: index
                            });
                          }else if(mainPlaylist.type === "media") {
                            sequences.push({
                              playlistName: mainPlaylist.mediaName.replace(/\.[^/.]+$/, ""),
                              playlistDuration: mainPlaylist.duration,
                              playlistIndex: index
                            });
                          }
                        });
                        var data = JSON.stringify({
                          playlists: sequences
                        });
                        console.log(data);
                        $.ajax({
                          headers: {
                            'remotetoken': remoteToken,
                          },
                          url: baseUrl + "/remotefalcon/api/syncPlaylists",
                          type: 'POST',
                          contentType: 'application/json; charset=UTF-8',
                          data: data,
                          async: false,
                          dataType: 'json',
                          success: function(data) {
                            $.jGrowl('Synced playlist!', {themeState:'success'});
                            // if(pluginEnabled && autoRestartPlugin) {

                            // }
                          },
                          error: function(err) {
                            $.jGrowl('Unable to sync playlist!', {themeState:'danger'});
                          }
                        });
                      }
                    }else {
                      $.jGrowl('Remote Token and Remote Playlist required!', {themeState:'danger'});
                    }
                    location.href = "plugin.php?plugin=" + <? echo $pluginName ?> + "&page=remote_falcon.php";
                  }
                </script>
              </div>
            </div>
          </div>
          <!-- Current Remote Playlist -->
          <div class="justify-content-md-center row setting-item" style="padding-top: .5em;">
            <div class="col-md-6">
              <div class="card-title h5">
                Current Remote Playlist
              </div>
              <div class="mb-2 text-muted card-subtitle h6">
                This is the current playlist synced with Remote Falcon (click to go to Sequences in your Control Panel)
              </div>
            </div>
            <div class="col-md-6">
              <h5><a href=<? echo "$rfSequencesUrl"; ?> target="_blank" rel="noopener noreferrer"><? echo $pluginSettings['remotePlaylist'] ?></a></h5>
            </div>
          </div>
          <!-- Request Fetch Time -->
          <div class="justify-content-md-center row setting-item">
            <div class="col-md-6">
							<div class="card-title h5">
								Request/Vote Fetch Time <span id="restartNotice"> *</span>
							</div>
							<div class="mb-2 text-muted card-subtitle h6">
								This sets when the plugin checks for the next request/vote (default is 10 seconds)
							</div>
						</div>
            <div class="col-md-6">
              <div class="input">
                <? PrintPluginSetting($pluginName, 'requestFetchTime'); ?>
              </div>
            </div>
          </div>
          <!-- Interrupt Schedule -->
          <div class="justify-content-md-center row setting-item">
            <div class="col-md-6">
              <div class="card-title h5">
                Interrupt Schedule <span id="restartNotice"> *</span>
              </div>
              <div class="mb-2 text-muted card-subtitle h6">
                Determines if a request or vote will interrupt the normal schedule
              </div>
            </div>
            <div class="col-md-6">
              <div class="input">
                <? PrintPluginSetting($pluginName, 'interruptSchedule'); ?>
              </div>
            </div>
          </div>
          <!-- Auto Restart Plugin -->
          <div class="justify-content-md-center row setting-item">
            <div class="col-md-6">
              <div class="card-title h5">
                Auto Restart Remote Falcon
              </div>
              <div class="mb-2 text-muted card-subtitle h6">
                Turning this on will automatically restart Remote Falcon when a plugin change is made
              </div>
            </div>
            <div class="col-md-6">
              <div class="input">
                <? PrintPluginSetting($pluginName, 'autoRestartPlugin', 'autoRestartPlugin'); ?>
                <script>
                  function autoRestartPlugin() {
                    location.href = "plugin.php?plugin=" + <? echo $pluginName ?> + "&page=remote_falcon.php";
                  }
                </script>
              </div>
            </div>
          </div>
          <!-- Restart Remote Falcon -->
          <div class="justify-content-md-center row setting-item">
            <div class="col-md-6">
              <div class="card-title h5">
                Restart Remote Falcon
              </div>
              <div class="mb-2 text-muted card-subtitle h6">
                This will restart the Remote Falcon plugin
              </div>
            </div>
            <div class="col-md-6">
              <form method="post">
                <button class="btn mr-md-3 hvr-underline-from-center btn-primary" id="restartRemoteFalcon" name="restartRemoteFalcon" type="submit">
                  Restart Remote Falcon
                </button>
              </form>
            </div>
          </div>
          <!-- Stop Remote Falcon -->
          <div class="justify-content-md-center row setting-item">
            <div class="col-md-6">
              <div class="card-title h5">
                Stop Remote Falcon
              </div>
              <div class="mb-2 text-muted card-subtitle h6">
                <span id="warning">WARNING! </span>This will immediately stop the Remote Falcon
                plugin and no requests/votes will be fetched!
              </div>
            </div>
            <div class="col-md-6">
            <form method="post">
                <button class="btn mr-md-3 hvr-underline-from-center btn-danger" id="stopRemoteFalcon" name="stopRemoteFalcon" type="submit">
                  Stop Remote Falcon
                </button>
              </form>
            </div>
          </div>
          <span id="restartNotice">* Requires Remote Falcon Restart</span>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

</body>
</html>