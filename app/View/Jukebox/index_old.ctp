<?php echo $this->Html->script('view/jukebox/index', array('inline' => false)); ?>
<script type="text/template" id="template-filter-button">
    <span class="title"><%= title %></span>
</script>
<script type="text/template" id="template-library-track">
    <div class="artist"><%= artist %></div>
    <div class="trackname"><%= title %></div>
    <div class="action add"></div>
</script>
<script type="text/template" id="template-selection-track">
    <div class="gradient track trackseltrack">
        <div class="artist"><%= artist %></div>
        <div class="trackname"><%= title %></div>
        <div class="action remove"></div>
    </div>
</script>
<div class="main">
    <div class="gradient" id="update">
        <div class="title" id="update-title">initialising...</div>
        <div class="spinner"></div>
    </div>
    <div class="gradient" id="options">
        <div id="filters-title" class="title">filters</div>
        <div id="filters">
        </div>
        <div id="tracklibrary-title" class="title">track library</div>
        <?php /*<div id="tracklibrary-container">*/ ?>
            <div id="tracklibrary">
                <?php /*<div id="tracks"></div>*/?>
            </div>
        <?php /*</div> */?>
        <?php /* <div class="spinner"></div> */ ?>
    </div>
    <div class="gradient" id="video">
        <div id="video-container">
            <div id="videoElement">
                <embed type="application/x-vlc-plugin" pluginspage="http://www.videolan.org"
                       width="970"
                       height="495"
                       id="vlc"
                       toolbar="no"
                       loop="false"
                       allowfullscreen="false">
                </embed>
            </div>
        </div>
    </div>
    <div class="gradient" id="playlist">
        <div id="yourselection-title" class="title">your selection</div>
        <div id="playlist-tracks">
        </div>
        <div id="sendplaylist-title" class="title">send to playlist</div>
        <div id="send-playlist"></div>
        <div id="jukebox-title" class="title">jukebox</div>
        <div id="thank-you" class="title"></div>
    </div>
</div>