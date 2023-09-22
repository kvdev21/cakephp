<?php echo $this->Html->script('view/jukebox/index', array('inline' => false)); ?>

<script type="text/template" id="template-filter-button">
    <div class="filtername"><%= title %></div>
</script>
<script type="text/template" id="template-library-track">
    <div class="icon"></div>
    <div class="trackname"><%= title %></div>
    <div class="artist"><%= artist %></div>
</script>
<script type="text/template" id="template-selection-track">
    <div class="icon"></div>
    <div class="trackname"><%= title %></div>
    <div class="artist"><%= artist %></div>
</script>
<div class="main">
    <div class="gradient" id="update">
        <div class="title" id="update-title">Loading interface...</div>
    </div>
    <!--   <div id="video-container">
        <div id="videoElement">
            <embed type="application/x-vlc-plugin" pluginspage="http://www.videolan.org"
                   width="750"
                   height="425"
                   id="vlc"
                   toolbar="no"
                   loop="true"
                   allowfullscreen="false"
                   version="VideoLAN.VLCPlugin.2"
                   windowless="false"
                   mute="true">
            </embed>
        </div>
    </div> -->
    <div id="track-info">
        <div id="trackname"></div>
        <div id="artist"></div>
    </div>
     <!-- <div id="waveform">
        <div class="slider"></div>
    </div>
    <div id="waveform-front"></div> -->
    <div id="add-to-playlist"></div>
    <div id="playlist-tracks" class="empty">
        browse the track library</br>
        and add your favourite tunes</br>
        to enjoy whilst you workout
    </div>
    <div id="tracklibrary">
    </div>
    <div id="alphabet">
        <div class="letter">A</div>
        <div class="letter">B</div>
        <div class="letter">C</div>
        <div class="letter">D</div>
        <div class="letter">E</div>
        <div class="letter on">F</div>
        <div class="letter">G</div>
        <div class="letter">H</div>
        <div class="letter">I</div>
        <div class="letter">J</div>
        <div class="letter">K</div>
        <div class="letter">L</div>
        <div class="letter">M</div>
        <div class="letter">N</div>
        <div class="letter">O</div>
        <div class="letter">P</div>
        <div class="letter">Q</div>
        <div class="letter">R</div>
        <div class="letter">S</div>
        <div class="letter">T</div>
        <div class="letter">U</div>
        <div class="letter">V</div>
        <div class="letter">W</div>
        <div class="letter">X</div>
        <div class="letter">Y</div>
        <div class="letter">Z</div>
    </div>
    <div id="filters">
    </div>
</div>
<div id="playlist-timeout">

</div>