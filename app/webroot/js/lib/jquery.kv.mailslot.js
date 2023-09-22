/**
 * Mailslot classes
 * 
 * Allows mailslots to be sent to KV applications with specific options
 * Note: Requires jQuery to be loaded first
 */
(function($) {
    /**
     * Mailslot class
     */
    function Mailslot(opt) {
        // Private properties
        var locked = false;
        var ajaxRequests = {
            requests: new Array(),
            cancelAll: function() {
                $(this.ajaxRequests.requests).each(function(index, item){
                    item.abort();
                });
            },
            add: function(request) {
                this.requests.push(request);
            }
        };
        var params = {};
        var paramsString = "";
        
        // Public properties
        this.options = {
            ip: "127.0.0.1",
            port: "3002",
            URL: "/mailslot",
            prefix: "http://",
            lockDelay: 0,
            disableOnLockSelectors: {},
            proxyURL: ""
        };
        $.extend(this.options, opt||{});

        // Privileged methods
        this.send = function(functionName, command, theParams) {
            var sendOptions = {
                functionName: "",
                command: "",
                ip: this.options.ip,
                port: this.options.port,
                ignoreDelay: false
            };
            
            if(locked === true) return false;
            
            if(typeof functionName === "object") {
                sendOptions = $.extend(sendOptions, functionName||{});
            } else {
                sendOptions.functionName = functionName;
                sendOptions.command = command;
                sendOptions.params = theParams;
            }
            
            if(typeof sendOptions.params === "object") {
                $.extend(params, sendOptions.params||{});
            }

            paramsString = "";
            $.each(params, function(index, item){
                if(typeof item === "string") {
                    paramsString += "&" + item;
                }
            });

            var urlString = this.options.prefix + sendOptions.ip + ":" + sendOptions.port + this.options.URL + "?FunctionName=" + sendOptions.functionName + "&Command=" + sendOptions.command + paramsString;

            if(this.options.proxyURL != "")
                urlString = this.options.proxyURL + escape(urlString);

            ajaxRequests.add($.ajax({
                url: urlString,
                cache: false
            }));

            if(this.options.lockDelay > 0 && sendOptions.ignoreDelay !== true) {
                this.lock();
                var self = this;
                setTimeout(function(){
                    self.unlock();
                }, this.options.lockDelay);
            }
            
            return true;
        }

        this.lock = function() {
            locked = true;

            if(typeof this.options.disableOnLockSelectors === "object") {
                $.each(this.options.disableOnLockSelectors, function(index, item){
                    if(typeof item === "string") {
                        $(item).addClass("mailslot-disabled");
                    }
                });
            }
        }

        this.unlock = function() {
            locked = false;

            if(typeof this.options.disableOnLockSelectors === "object") {
                $.each(this.options.disableOnLockSelectors, function(index, item){
                    if(typeof item === "string") {
                        $(item).removeClass("mailslot-disabled");
                    }
                });
            }
        }
        
        this.set = function(option, value) {
            this.options[option] = value;
        }
    }
    
    /**
     * MailslotSilva class
     */
    function MailslotSilva(opt) {
        var options = {
            port: 11500,
            lockDelay: 500
        };
        $.extend(options, opt||{});
        
        Mailslot.call(this, options);
    };
    
    MailslotSilva.prototype.constructor = MailslotSilva;
    
    MailslotSilva.prototype.playClip = function(uid, theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        return this.send("SemiAutomatic", "PlayNow", ["PlayImmediately=YES", "UID=" + uid, "Channel=" + channel]);
    }
    
    MailslotSilva.prototype.playMarketingClip = function(fileName, duration) {
        return this.send("Marketing", "PlayNow", ["ClipFilename=" + fileName, "Duration=" + duration]);
    }
    
    MailslotSilva.prototype.resetMarketing = function() {
        return this.send("Marketing", "PlayNow");
    }
    
    MailslotSilva.prototype.setVolume = function(level, theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        return this.send({
            functionName: "PlayerControl",
            command: "SetVolume",
            params: ["Value=" + level, "Channel=" + channel],
            ignoreDelay: false
        }); // Example of using object as options argument
    }
    
    MailslotSilva.prototype.skip = function(value) {
        return this.send("PlayerControl", "Skip", ["VALUE=" + value]);
    }
    
    /**
     * MailslotMusicScheduler class
     */
    function MailslotMusicScheduler(opt) {
        var options = {
            port: 3002
        };
        $.extend(options, opt||{});
        
        Mailslot.call(this, options);
    };
    
    MailslotMusicScheduler.prototype.constructor = MailslotMusicScheduler;
    
    MailslotMusicScheduler.prototype.enablePriorityPlaylist = function(theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        return this.send("PriorityPlaylist", "Enable", ["CHANNEL=" + channel]);
    }
    
    MailslotMusicScheduler.prototype.disablePriorityPlaylist = function(theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        return this.send("PriorityPlaylist", "Disable", ["CHANNEL=" + channel]);
    }
    
    MailslotMusicScheduler.prototype.playlistReconstruct = function(theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        return this.send("PriorityPlaylist", "Reconstruct", ["CHANNEL=" + channel]);
    }
    
    MailslotMusicScheduler.prototype.playlistAdd = function(uid, theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        return this.send("PriorityPlaylist", "Add", ["UID=" + uid, "CHANNEL=" + channel]);
    }
    
    MailslotMusicScheduler.prototype.playlistInsert = function(uid, thePosition, theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        var position = (thePosition == null) ? 1 : thePosition;
        return this.send("PriorityPlaylist", "Insert", ["UID=" + uid, "POSITION=" + position, "CHANNEL=" + channel]);
    }
    
    MailslotMusicScheduler.prototype.playlistDelete = function(uid, theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        return this.send("PriorityPlaylist", "Delete", ["UID=" + uid, "CHANNEL=" + channel]);
    }
    
    MailslotMusicScheduler.prototype.playlistClear = function(theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        return this.send("PriorityPlaylist", "Clear", ["CHANNEL=" + channel]);
    }
    
    MailslotMusicScheduler.prototype.playlistMoveTo = function(uid, thePosition, theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        var position = (thePosition == null) ? 1 : thePosition;
        return this.send("PriorityPlaylist", "Move", ["UID=" + uid, "POSITION=" + position, "CHANNEL=" + channel]);
    }
    
    MailslotMusicScheduler.prototype.playlistMoveUp = function(uid, theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        return this.send("PriorityPlaylist", "MoveUp", ["UID=" + uid, "CHANNEL=" + channel]);
    }
    
    MailslotMusicScheduler.prototype.playlistMoveDown = function(uid, theChannel) {
        var channel = (theChannel == null) ? "Channel1" : theChannel;
        return this.send("PriorityPlaylist", "MoveDown", ["UID=" + uid, "CHANNEL=" + channel]);
    }
    
    /**
     * MailslotCaptureKv class
     */
    function MailslotCaptureKv(opt) {
        var options = {
            port: 3002,
            lockDelay: 500
        };
        $.extend(options, opt||{});
        
        Mailslot.call(this, options);
    };
    
    MailslotCaptureKv.prototype.constructor = MailslotCaptureKv;
    
    MailslotCaptureKv.prototype.newSession = function(name) {
        return this.send("VIDEO-CAPTURE", "New-Session", ["NAME=" + name]);
    }
    
    MailslotCaptureKv.prototype.startRecording = function(sessionName) {
        this.send({
            functionName: "VIDEO-CAPTURE",
            command: "New-Session",
            params: ["NAME=" + sessionName],
            ignoreDelay: true
        });
        
        var self = this;
        setTimeout(function(){
            self.send("VIDEO-CAPTURE", "START");
        }, self.options.lockDelay);
    }
    
    MailslotCaptureKv.prototype.restartRecording = function() {
        return this.send("VIDEO-CAPTURE", "RESTART");
    }
    
    MailslotCaptureKv.prototype.stopRecording = function() {
        return this.send("VIDEO-CAPTURE", "STOP");
    }
    
    // Add classes to the jQuery namespace
    $.mailslot = Mailslot;
    $.mailslotSilva = MailslotSilva;
    $.mailslotMusicScheduler = MailslotMusicScheduler;
    $.mailslotCaptureKv = MailslotCaptureKv;
})(jQuery);