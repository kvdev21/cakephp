(function () {
    $(window).load(function () {
        /*
        Filter/Tag
         */

        Tag = Backbone.Model.extend({
            url:function () {
				
                return app.baseUrl + "tags/" + this.id + ".json";
            },
            defaults: {
                id: 0,
                title: ""
            }
        });

        TagCollection = Backbone.Collection.extend({
            model: Tag,
            initialize: function() {
                this.on("change", this.sort, this);
            },
            comparator: function(model) {
                return [model.get("title"), model.get("id")];
            }
        });

        Filter = Backbone.Model.extend({
            url: function () {
				console.log('filters output');                                                 
                return app.baseUrl + "filters/" + this.id + ".json";
            },
            defaults: {
                title: "",
                selected: false,
                all: false,
                allVideo: false,
                allAudio: false
            },
            initialize: function() {
                this.Tags = new TagCollection();
                this.Tags.parent = this;

                try {
                    if(tags = this.get("Tag"))
                        this.Tags.add(tags);
                } catch(e) {
                    // Do nothing
                }
            },
            getUrl: function() {
                var url = app.baseUrl + "tracks/index/ids-only:true";

                //var cnt = 0;
                this.Tags.each(function(item){
                    url += "/tagId:" + item.get("id");
                });
                url += ".json";

                return url;
            }
        });

        FilterButtonView = Backbone.View.extend({
            template: _.template($("#template-filter-button").html()),
            tagName: "div",
            className: "filter",
            initialize: function(options) {
                this.listenTo(this.model, "change", this.render);

                this.vent = options.vent;
            },
            render: function () {
                this.$el.html(this.template({
                    title: this.model.get("title")
                }));

                if(this.model.get("selected") == true) {
                    this.$el.addClass("on");
                } else {
                    this.$el.removeClass("on");
                }

                if(this.model.get("all") == true) {
                    this.$el.addClass("all");
                } else if(this.model.get("allVideo") == true) {
                    this.$el.addClass("all-video");
                } else if(this.model.get("allAudio") == true) {
                    this.$el.addClass("all-audio");
                } else {
                    this.$el.removeClass("all");
                    this.$el.removeClass("all-video");
                    this.$el.removeClass("all-audio");
                }

                return this;
            },
            events: {
                "click": "filter"
            },
            filter: function() {
                if(!this.model.get("selected")) {
                    this.vent.trigger("filter", this.model);
                }
			console.log(this.model.get('title'));                                                 

            }
        });

        FilterCollection = Backbone.Collection.extend({
            model: Filter,
            url: function() {
                return app.baseUrl + "filters/index.json";
            },
            parse: function(response) {
                return response.data;
            },
            comparator: function(model) {
                return [(model.get("id") == 0 ? 1 : 0), model.get("title").toLowerCase, model.get("id")];
            }
        });

        FiltersView = Backbone.View.extend({
            el: $("#filters"),
            filterCaching: true,
            filterCacheCnt: 0,
            cachedOnce: false,
            initialize: function(options) {
                var self = this;

                this.filterCaching = (options.filterCaching != null) ? options.filterCaching : this.filterCaching;

                this.collection = new FilterCollection();
                this.collection.fetch({
                    success: function() {
                        self.collection.sort();
						
                        self.collection.add({
                            title: "all video",
                            selected: true,
                            allVideo: true

                        });
                        self.collection.add({
                            title: "all audio",
                            selected: true,
                            allAudio: true
                        });
                        self.collection.add({
                            title: "view all",
                            selected: true,
                            all: true
                        });
                    }
                });

                this.collection.on("reset", this.render, this);
                this.collection.on("sort", this.render, this);

                this.vent = options.vent;
                _.bindAll(this, "filter");
                _.bindAll(this, "filterComplete");
                this.vent.bind("filter", this.filter);
                this.vent.bind("filterComplete", this.filterComplete);
            },
            render:function () {
                var self = this;

                this.$el.empty();
                this.collection.each(this.renderFilter, this);

                if(this.filterCaching == true && this.cachedOnce == false) {
                    this.$initDiv = $("div#update");
                    $(".filter", this.$el).each(function() {
                        self.filterCacheCnt++;
                        $(this).click();
                    });
                    this.cachedOnce = true;
                }
            },
            renderFilter: function(item) {
                var view = new FilterButtonView({
                    model: item,
                    vent: this.vent
                });

                $(this.$el).append(view.render().el);
            },
            filter: function(model) {
                this.collection.each(function(item) {
                    item.set({selected: false});
                });
                model.set({selected: true});

                this.vent.trigger("applyFilter", model);
                this.vent.trigger("startAutoResetTimeout");
            },
            filterComplete: function() {
                if(this.filterCacheCnt > 0) {
                    this.filterCacheCnt--;
                    if(this.filterCacheCnt == 0) {
                        this.filter(this.collection.at(this.collection.length - 1));
                        this.$initDiv.fadeOut();
                    }
                }
            }
        });

        /*
         Track
         */

        Genre = Backbone.Model.extend({
            url: function () {
                return app.baseUrl + "genres/" + this.id + ".json";
            },
            defaults: {
                id: 0,
                od_id: -1,
                title: "",
                selected: false
            }
        });

        Artist = Backbone.Model.extend({
            url:function () {
                return app.baseUrl + "artists/" + this.id + ".json";
            },
            defaults: {
                id: 0,
                name: ""
            }
        });

        Track = Backbone.Model.extend({
            url:function () {
                return app.baseUrl + "tracks/" + this.id + ".json";
            },
            defaults: {
                id: 0,
                artist_id: 0,
                track_name: "",
                bpm: 0,
                length: "00:00:00",
                file: "",
                year: 0,
                high: 0,
                uid: "",
                genre_1_id: 0,
                genre_2_id: 0,
                genre_3_id: 0,
                selected: false
            },
            initialize: function() {
                this.Artist = new Artist();
                this.Artist.parent = this;
                this.Genre1 = new Genre();
                this.Genre1.parent = this;
                this.Genre2 = new Genre();
                this.Genre2.parent = this;
                this.Genre3 = new Genre();
                this.Genre3.parent = this;

                try {
                    if(artist = this.get("TrackArtist"))
                        this.Artist.set(artist);
                    if(genre = this.get("Genre1"))
                        this.Genre1.set(genre);
                    if(genre = this.get("Genre2"))
                        this.Genre2.set(genre);
                    if(genre = this.get("Genre3"))
                        this.Genre3.set(genre);
                } catch(e) {
                    // Do nothing
                }
            }
        });

        PlaylistEntryTrack = Track.extend({
            defaults: {
                expires: null
            },
            initialize: function() {
                this.Track = new Track();
                this.Track.parent = this;

                try {
                    if(track = this.get("Track"))
                        this.Track.set(track);
                } catch(e) {
                    // Do nothing
                }
            },
            calculateExpiry: function(relativeDate) {
                var date = relativeDate || new Date();

                var split = this.Track.get("length").split(":");
                date.setHours(date.getHours() + parseInt(split[0]), date.getMinutes() + parseInt(split[1]), date.getSeconds() + parseInt(split[2]), date.getMilliseconds());

                this.set("expires", date);
            },
            expired: function() {
                return (this.get("expires") < new Date());
            }
        });

        PlaylistEntryCollection = Backbone.Collection.extend({
            model: PlaylistEntryTrack,
            initialize: function () {
                this.on("add", this.updateExpiryDates, this);
            },
            updateExpiryDates: function() {
                relativeDate = new Date();
                this.each(function(item) {
                    if(item.get("expires") == null) {
                        item.calculateExpiry(relativeDate);
                    }
                    relativeDate = new Date(item.get("expires"));
                });
            },
            filterOutExpired: function() {
                var filtered = this.filter(function(item) {
                    return (item.expired() == false);
                });

                //console.log(filtered);

                return this.reset(filtered);
            }
        });

        SelectionTrack = Track.extend({
            defaults: $.extend(true, {
                position: 0
            }, Track.defaults)
        });

        TrackView = Backbone.View.extend({
            tagName: "div",
            initialize: function () {
                this.listenTo(this.model, "change", this.render);
            },
            render:function () {
                this.$el.html(this.template({
                    artist: this.model.Artist.get("name"),
                    title: this.model.get("track_name")
                }));

                if (this.model.get("selected") == true) {
                    this.$el.addClass("added").removeClass("add");
                } else {
                    this.$el.addClass("add").removeClass("added");
                }

                if(this.model.get("file").split(".").pop() == "mpe") {
                    this.$el.addClass("video").removeClass("audio");
                } else {
                    this.$el.addClass("audio").removeClass("video");
                }

                return this;
            }
        });

        LibraryTrackView = TrackView.extend({
            template: _.template($("#template-library-track").html()),
            className: "track add",
            initialize: function(options) {
                LibraryTrackView.__super__.initialize.apply(this, options);
            }
        });

        SelectionTrackView = TrackView.extend({
            template: _.template($("#template-selection-track").html()),
            className: "track",
            initialize: function(options) {
                SelectionTrackView.__super__.initialize.apply(this, options);

                this.vent = options.vent;
            },
            events: {
               // "click .action.remove": "removeFromSelection"
                "click": "removeFromSelection"
            },
            removeFromSelection: function(e) {
                e.preventDefault();

                this.model.set("selected", false);

                this.vent.trigger("selectionLimitUnreached");
            }
        });

        TrackCollection = Backbone.Collection.extend({
            model: Track,
            url: function() {
                return app.baseUrl + "tracks/index.json";
            },
            parse: function(response) {
                return response.data;
            },
            initialize: function(models) {
                this.on("change", this.sort, this);

                this.filtered = new Backbone.Collection(models);
                this.on('reset', this.reFilter);
            },
            comparator: function(model) {
                return [model.Artist.get("name").replace(/^The\s+/, ""), model.get("track_name"), model.get("id")];
            },
            filterBy: function(params) {
                params = params || {};
                var filteredCollection = this.filter(function(item) {
                    if("ids" in params)
                        return _.contains(params.ids, item.id);
                    if("type" in params) {
                        if(params.type == "video")
                            return (item.get("file").split(".").pop() == "mpe");
                        else
                            return (item.get("file").split(".").pop() == "m3e");
                    } else
                        return true;
                });

                this.filtered.params = params;
                this.filtered.reset(filteredCollection);
            },
            reFilter: function() {
                this.filterBy(this.filtered.params);
            }
        });

        LibraryTrackCollection = TrackCollection.extend({
            initialize: function() {
                LibraryTrackCollection.__super__.initialize.apply(this, arguments);
            }
        });

        SelectionTrackCollection = TrackCollection.extend({
            initialize: function() {
                SelectionTrackCollection.__super__.initialize.apply(this, arguments);

                this.on("change:selected", this.trackSelected);
            },
            comparator: function(model) {
                return [model.get("position")];
            },
            addTrack: function(model) {
                if(this.length < 1) { //kamod
                    model.set("position", this.length);
                    this.add(model);
                }
            },
            removeTrack: function(model) {
                this.remove(model)
            },
            removeAllTracks: function() {
                this.each(function(item) {
                    item.set({"selected": false}, {silent: true});
                });

                this.reset();
            },
            trackSelected: function(model) {
                if(model.get("selected") == false) {
                    this.removeTrack(model);
                }
            }
        });

        TrackLibraryView = Backbone.View.extend({
            el: $("#tracklibrary"),
            filterCaching: true,
            cached: {},
            selectionLocked: false,
            locked: false,
            scrollLockTimeout: null,
            scrollLockLastStart: (new Date()).getTime(),
            scrolling: false,
            scrollSensitivity: 10,
            forceScrollTop: -1,
            scrollDiff: 0,
            initialize: function(options) {
                var self = this;

                this.filterCaching = (options.filterCaching != null) ? options.filterCaching : this.filterCaching;

                this.collection = new LibraryTrackCollection();

                this.collection.fetch({
                    success: function() {
                        self.collection.reFilter();
                        self.render();
                    }
                });

                this.vent = options.vent;

                _.bindAll(this, "applyFilter");
                _.bindAll(this, "render");
                _.bindAll(this, "lockSelection");
                _.bindAll(this, "unlockSelection");
                _.bindAll(this, "lock");
                _.bindAll(this, "unlock");
                this.vent.bind("applyFilter", this.applyFilter);
                this.vent.bind("selectionReset", this.render);
                this.vent.bind("selectionLimitReached", this.lockSelection);
                this.vent.bind("selectionLimitUnreached", this.unlockSelection);
                this.vent.bind("selectionLocked", this.lock);
                this.vent.bind("selectionUnlocked", this.unlock);

                /*setTimeout(function() {
                    self.iscroll = new iScroll("tracklibrary");
                }, 100);*/

                this.$el.niceScroll({
                    touchbehavior: true,
                    hwacceleration: true,
                    bouncescroll: true,
                    grabcursorenabled: false,
                    cursorcolor: "#20B8B3",
                    cursorborder: "1px solid #20B8B3",
                    cursoropacitymax: 0.6
                    /*onBeforeScrollStart: function(e) {
                        e.preventDefault();

                        setTimeout(function(e) {

                        });
                    }*/
                });

                /*this.$el.getNiceScroll()[0].cursor.mouseup(function(e){
                    alert('mouse up');// !! DONT GET THE ALERT !!
                });*/

                //this.$loadingSpinner = $("#options div.spinner");
            },
            render:function () {
                var self = this;

                this.$el.empty();

                var container = document.createDocumentFragment();

                this.collection.filtered.each(function(item) {
                    container.appendChild(self.renderTrack(item));
                });

                this.$el.append(container);

                // Try HTML method to speed things up possibly?
                // NOPE.
                /*var html = "";

                this.collection.filtered.each(function(item) {
                    html += self.renderTrack(item).html();
                });

                $("#tracks", this.$el).append(html);*/

            },
            renderTrack:function (item) {
                var view = new LibraryTrackView({
                    model: item
                });

                view.render();
                view.$el
                    .attr("data-id", item.get("id"));

                return view.el;
            },
            applyFilter: function(model) {
                var self = this;

                if(model.get("all") == true) {
                    this.collection.filterBy({});
                    this.render();

                    self.vent.trigger("filterComplete");

                    return this;
                } else if(model.get("allAudio") == true) {
                    this.collection.filterBy({
                        "type": "audio"
                    });
                    this.render();

                    self.vent.trigger("filterComplete");

                    return this;
                } else if(model.get("allVideo") == true) {
                    this.collection.filterBy({
                        "type": "video"
                    });
                    this.render();

                    self.vent.trigger("filterComplete");

                    return this;
                }

                var url = model.getUrl();

                if(this.filterCaching == true) {
                    if(typeof this.cached[url] != "undefined") {

                        this.collection.filterBy({
                            ids: this.cached[url]
                        });
                        this.render();

                        self.vent.trigger("filterComplete");

                        return this;
                    }
                }

                $.ajax({
                    url: url,
                    type: "get",
                    dataType: "json",
                    success: function(response) {
                        self.collection.filterBy({
                            ids: response.data
                        });
                        self.render();

                        if(self.filterCaching == true) {
                            self.cached[url] = response.data;
                        }
                    },
                    complete: function() {
                        self.vent.trigger("filterComplete");
                    }
                });

                return this;
            },
            events: {
                "mousedown .track": "activateTrack",
                "mouseup .track": "deactivateTrack",
                "click .track": "toggleSelection",/*
                "click .track .action.add": "addToSelection",
                "click .track .action.added": "removeFromSelection"*/
                "scroll": "scrollLock",
                "mouseup": "deactivateAllTracks"
            },
            toggleSelection: function(e) {
                e.preventDefault();

                if(this.locked == false && this.scrolling == false) {
                    if($(e.currentTarget).hasClass("added")) {
                        this.removeFromSelection(e);
                    } else {
                        if(this.selectionLocked == false)
                            this.addToSelection(e);
                    }
                }
            },
            activateTrack: function(e) {
                $(e.currentTarget).addClass("active");

                /*this.$el.bind("scroll", function(e) {
                    e.preventDefault();

                    e.target.scrollTop = 0;
                });*/

                this.forceScrollTop = this.$el.scrollTop();

                this.deactivateAllTracks();
            },
            deactivateTrack: function(e) {
                $(e.currentTarget).removeClass("active");

                this.forceScrollTop = -1;
                this.scrollDiff = 0;
            },
            deactivateAllTracks: function() {
                $(".track", this.$el).removeClass("active");
            },
            addToSelection:function (e) {
                e.preventDefault();

                var model = this.collection.get($(e.currentTarget).attr("data-id"));
                model.set("selected", true);

                this.vent.trigger("selectTrack", model);
            },
            removeFromSelection: function(e) {
                e.preventDefault();

                var model = this.collection.get($(e.currentTarget).attr("data-id"));
                model.set("selected", false);

                this.vent.trigger("unselectTrack", model);
            },
            lockSelection: function() {
                this.selectionLocked = true;
                this.$el.addClass("selection-locked");
            },
            unlockSelection: function() {
                this.selectionLocked = false;
                this.$el.removeClass("selection-locked");
            },
            lock: function() {
                this.locked = true;
                this.$el.addClass("locked");
            },
            unlock: function() {
                this.locked = false;
                this.$el.removeClass("locked");
            },
            scrollLock: function(e) {
                var self = this;

                e.preventDefault();

                //var top = this.$el.scrollTop();

                if(this.forceScrollTop > -1) {
                    this.scrollDiff = this.$el.scrollTop() - this.forceScrollTop;

                    if(this.scrollDiff > this.scrollSensitivity || this.scrollDiff < -this.scrollSensitivity) {
                        this.forceScrollTop = -1;
                        this.deactivateAllTracks();
                    } else {
                        this.$el.scrollTop(this.forceScrollTop);
                    }
                } else {
                    //clearTimeout(this.preScrollLockTimeout);
                    clearTimeout(this.scrollLockTimeout);

                    var now = (new Date()).getTime();
                    if(now - this.scrollLockLastStart > 500 && !this.scrolling) {
                        // SCROLL START

                        this.vent.trigger("startAutoResetTimeout");

                        //this.lock();
                        //this.preScrollLockTimeout = setTimeout(function(){
                            self.scrolling = true;
                            self.scrollLockLastStart = now;
                        //}, 100);
                    }

                    this.$el.bind("mouseup", function(){
                        self.scrollLockTimeout = setTimeout(function() {
                            if (self.scrolling) {
                                //self.unlock();
                                self.scrolling = false;
                                // test
                                //self.$el.scrollTop(self.$el.scrollTop);
                                //self.forceScrollTop = -1;

                                self.$el.unbind("mouseup").bind("mouseup", self.deactivateAllTracks);
                                self.forceScrollTop = self.$el.scrollTop();
                                self.scrollDiff = 0;
                            }
                        }, 50);
                    });

                    self.scrollLockTimeout = setTimeout(function() {
                        if (self.scrolling) {
                            //self.unlock();
                            self.scrolling = false;

                            self.forceScrollTop = self.$el.scrollTop();
                            self.scrollDiff = 0;
                        }
                    }, 400);
                }
            }
        });
        // TrackLibraryView = Backbone.View.extend({
            // el: $("#tracklibrary"),
            // filterCaching: true,
            // cached: {},
            // selectionLocked: false,
            // locked: false,
            // scrollLockTimeout: null,
            // scrollLockLastStart: (new Date()).getTime(),
            // scrolling: false,
            // scrollSensitivity: 10,
            // forceScrollTop: -1,
            // scrollDiff: 0,
            // initialize: function(options) {
                // var self = this;

                // this.filterCaching = (options.filterCaching != null) ? options.filterCaching : this.filterCaching;

                // this.collection = new LibraryTrackCollection();

                // this.collection.fetch({
                    // success: function() {
                        // self.collection.reFilter();
                        // self.render();
                    // }
                // });

                // this.vent = options.vent;

                // _.bindAll(this, "applyFilter");
                // _.bindAll(this, "render");
                // _.bindAll(this, "lockSelection");
                // _.bindAll(this, "unlockSelection");
                // _.bindAll(this, "lock");
                // _.bindAll(this, "unlock");
                // this.vent.bind("applyFilter", this.applyFilter);
                // this.vent.bind("selectionReset", this.render);
                // this.vent.bind("selectionLimitReached", this.lockSelection);
                // this.vent.bind("selectionLimitUnreached", this.unlockSelection);
                // this.vent.bind("selectionLocked", this.lock);
                // this.vent.bind("selectionUnlocked", this.unlock);

                // /*setTimeout(function() {
                    // self.iscroll = new iScroll("tracklibrary");
                // }, 100);*/

                // this.$el.niceScroll({
                    // touchbehavior: true,
                    // hwacceleration: true,
                    // bouncescroll: true,
                    // grabcursorenabled: false,
                    // cursorcolor: "#20B8B3",
                    // cursorborder: "1px solid #20B8B3",
                    // cursoropacitymax: 0.6
                    // /*onBeforeScrollStart: function(e) {
                        // e.preventDefault();

                        // setTimeout(function(e) {

                        // });
                    // }*/
                // });

                // /*this.$el.getNiceScroll()[0].cursor.mouseup(function(e){
                    // alert('mouse up');// !! DONT GET THE ALERT !!
                // });*/

                // //this.$loadingSpinner = $("#options div.spinner");
            // },
            // render:function () {
                // var self = this;
				// this.$el.empty();

				// var container = document.createElement('div');
				// container.className = 'track-grid';

				// var numRows = 6;
				// var numCols = 4;
				// var numTracks = this.collection.filtered.length;
				// var numEmptyCells = numRows * numCols - numTracks;

				// this.collection.filtered.each(function(item, index) {
				  // var trackView = self.renderTrack(item);
				  // trackView.className = 'track';
				  // trackView.setAttribute('data-id', item.get('id'));
				  // container.appendChild(trackView);
				// });

				// // Add empty cells if necessary
				// for (var i = 0; i < numEmptyCells; i++) {
				  // var emptyCell = document.createElement('div');
				  // emptyCell.className = 'empty-cell';
				  // container.appendChild(emptyCell);
				// }

				// this.$el.append(container);

            // },
            // renderTrack:function (item) {
                // var view = new LibraryTrackView({
                    // model: item
                // });

                // view.render();
                // view.$el
                    // .attr("data-id", item.get("id"));

                // return view.el;
            // },
            // applyFilter: function(model) {
                // var self = this;

                // if(model.get("all") == true) {
                    // this.collection.filterBy({});
                    // this.render();

                    // self.vent.trigger("filterComplete");

                    // return this;
                // } else if(model.get("allAudio") == true) {
                    // this.collection.filterBy({
                        // "type": "audio"
                    // });
                    // this.render();

                    // self.vent.trigger("filterComplete");

                    // return this;
                // } else if(model.get("allVideo") == true) {
                    // this.collection.filterBy({
                        // "type": "video"
                    // });
                    // this.render();

                    // self.vent.trigger("filterComplete");

                    // return this;
                // }

                // var url = model.getUrl();

                // if(this.filterCaching == true) {
                    // if(typeof this.cached[url] != "undefined") {

                        // this.collection.filterBy({
                            // ids: this.cached[url]
                        // });
                        // this.render();

                        // self.vent.trigger("filterComplete");

                        // return this;
                    // }
                // }

                // $.ajax({
                    // url: url,
                    // type: "get",
                    // dataType: "json",
                    // success: function(response) {
                        // self.collection.filterBy({
                            // ids: response.data
                        // });
                        // self.render();

                        // if(self.filterCaching == true) {
                            // self.cached[url] = response.data;
                        // }
                    // },
                    // complete: function() {
                        // self.vent.trigger("filterComplete");
                    // }
                // });

                // return this;
            // },
            // events: {
                // "mousedown .track": "activateTrack",
                // "mouseup .track": "deactivateTrack",
                // "click .track": "toggleSelection",/*
                // "click .track .action.add": "addToSelection",
                // "click .track .action.added": "removeFromSelection"*/
                // "scroll": "scrollLock",
                // "mouseup": "deactivateAllTracks"
            // },
            // toggleSelection: function(e) {
                // e.preventDefault();

                // if(this.locked == false && this.scrolling == false) {
                    // if($(e.currentTarget).hasClass("added")) {
                        // this.removeFromSelection(e);
                    // } else {
                        // if(this.selectionLocked == false)
                            // this.addToSelection(e);
                    // }
                // }
            // },
            // activateTrack: function(e) {
                // $(e.currentTarget).addClass("active");

                // /*this.$el.bind("scroll", function(e) {
                    // e.preventDefault();

                    // e.target.scrollTop = 0;
                // });*/

                // this.forceScrollTop = this.$el.scrollTop();

                // this.deactivateAllTracks();
            // },
            // deactivateTrack: function(e) {
                // $(e.currentTarget).removeClass("active");

                // this.forceScrollTop = -1;
                // this.scrollDiff = 0;
            // },
            // deactivateAllTracks: function() {
                // $(".track", this.$el).removeClass("active");
            // },
            // addToSelection:function (e) {
                // e.preventDefault();

                // var model = this.collection.get($(e.currentTarget).attr("data-id"));
                // model.set("selected", true);

                // this.vent.trigger("selectTrack", model);
            // },
            // removeFromSelection: function(e) {
                // e.preventDefault();

                // var model = this.collection.get($(e.currentTarget).attr("data-id"));
                // model.set("selected", false);

                // this.vent.trigger("unselectTrack", model);
            // },
            // lockSelection: function() {
                // this.selectionLocked = true;
                // this.$el.addClass("selection-locked");
            // },
            // unlockSelection: function() {
                // this.selectionLocked = false;
                // this.$el.removeClass("selection-locked");
            // },
            // lock: function() {
                // this.locked = true;
                // this.$el.addClass("locked");
            // },
            // unlock: function() {
                // this.locked = false;
                // this.$el.removeClass("locked");
            // },
            // scrollLock: function(e) {
                // var self = this;

                // e.preventDefault();

                // //var top = this.$el.scrollTop();

                // if(this.forceScrollTop > -1) {
                    // this.scrollDiff = this.$el.scrollTop() - this.forceScrollTop;

                    // if(this.scrollDiff > this.scrollSensitivity || this.scrollDiff < -this.scrollSensitivity) {
                        // this.forceScrollTop = -1;
                        // this.deactivateAllTracks();
                    // } else {
                        // this.$el.scrollTop(this.forceScrollTop);
                    // }
                // } else {
                    // //clearTimeout(this.preScrollLockTimeout);
                    // clearTimeout(this.scrollLockTimeout);

                    // var now = (new Date()).getTime();
                    // if(now - this.scrollLockLastStart > 500 && !this.scrolling) {
                        // // SCROLL START

                        // this.vent.trigger("startAutoResetTimeout");

                        // //this.lock();
                        // //this.preScrollLockTimeout = setTimeout(function(){
                            // self.scrolling = true;
                            // self.scrollLockLastStart = now;
                        // //}, 100);
                    // }

                    // this.$el.bind("mouseup", function(){
                        // self.scrollLockTimeout = setTimeout(function() {
                            // if (self.scrolling) {
                                // //self.unlock();
                                // self.scrolling = false;
                                // // test
                                // //self.$el.scrollTop(self.$el.scrollTop);
                                // //self.forceScrollTop = -1;

                                // self.$el.unbind("mouseup").bind("mouseup", self.deactivateAllTracks);
                                // self.forceScrollTop = self.$el.scrollTop();
                                // self.scrollDiff = 0;
                            // }
                        // }, 50);
                    // });

                    // self.scrollLockTimeout = setTimeout(function() {
                        // if (self.scrolling) {
                            // //self.unlock();
                            // self.scrolling = false;

                            // self.forceScrollTop = self.$el.scrollTop();
                            // self.scrollDiff = 0;
                        // }
                    // }, 400);
                // }
            // }
        // }); 
		
		/* grid formation */
		  // TrackLibraryView = Backbone.View.extend({
            // el: $("#tracklibrary"),
            // filterCaching: true,
            // cached: {},
            // selectionLocked: false,
            // locked: false,
            // scrollLockTimeout: null,
            // scrollLockLastStart: (new Date()).getTime(),
            // scrolling: false,
            // scrollSensitivity: 10,
            // forceScrollTop: -1,
            // scrollDiff: 0,
            // initialize: function(options) {
                // var self = this;

                // this.filterCaching = (options.filterCaching != null) ? options.filterCaching : this.filterCaching;

                // this.collection = new LibraryTrackCollection();

                // this.collection.fetch({
                    // success: function() {
                        // self.collection.reFilter();
                        // self.render();
                    // }
                // });

                // this.vent = options.vent;

                // _.bindAll(this, "applyFilter");
                // _.bindAll(this, "render");
                // _.bindAll(this, "lockSelection");
                // _.bindAll(this, "unlockSelection");
                // _.bindAll(this, "lock");
                // _.bindAll(this, "unlock");
                // this.vent.bind("applyFilter", this.applyFilter);
                // this.vent.bind("selectionReset", this.render);
                // this.vent.bind("selectionLimitReached", this.lockSelection);
                // this.vent.bind("selectionLimitUnreached", this.unlockSelection);
                // this.vent.bind("selectionLocked", this.lock);
                // this.vent.bind("selectionUnlocked", this.unlock);

                // /*setTimeout(function() {
                    // self.iscroll = new iScroll("tracklibrary");
                // }, 100);*/

                // this.$el.niceScroll({
                    // touchbehavior: true,
                    // hwacceleration: true,
                    // bouncescroll: true,
                    // grabcursorenabled: false,
                    // cursorcolor: "#20B8B3",
                    // cursorborder: "1px solid #20B8B3",
                    // cursoropacitymax: 0.6
                    // /*onBeforeScrollStart: function(e) {
                        // e.preventDefault();

                        // setTimeout(function(e) {

                        // });
                    // }*/
                // });

                // /*this.$el.getNiceScroll()[0].cursor.mouseup(function(e){
                    // alert('mouse up');// !! DONT GET THE ALERT !!
                // });*/

                // //this.$loadingSpinner = $("#options div.spinner");
            // },
            // render:function () {
                // var self = this;
				// this.$el.empty();

				// var container = document.createElement('div');
				// container.className = 'track-grid';

				// var numRows = 6;
				// var numCols = 4;
				// var numTracks = this.collection.filtered.length;
				// var numEmptyCells = numRows * numCols - numTracks;

				// this.collection.filtered.each(function(item, index) {
				  // var trackView = self.renderTrack(item);
				  // trackView.className = 'track';
				  // trackView.setAttribute('data-id', item.get('id'));
				  // container.appendChild(trackView);
				// });

				// // Add empty cells if necessary
				// for (var i = 0; i < numEmptyCells; i++) {
				  // var emptyCell = document.createElement('div');
				  // emptyCell.className = 'empty-cell';
				  // container.appendChild(emptyCell);
				// }

				// this.$el.append(container);

            // },
            // renderTrack:function (item) {
                // var view = new LibraryTrackView({
                    // model: item
                // });

                // view.render();
                // view.$el
                    // .attr("data-id", item.get("id"));

                // return view.el;
            // },
            // applyFilter: function(model) {
                // var self = this;

                // if(model.get("all") == true) {
                    // this.collection.filterBy({});
                    // this.render();

                    // self.vent.trigger("filterComplete");

                    // return this;
                // } else if(model.get("allAudio") == true) {
                    // this.collection.filterBy({
                        // "type": "audio"
                    // });
                    // this.render();

                    // self.vent.trigger("filterComplete");

                    // return this;
                // } else if(model.get("allVideo") == true) {
                    // this.collection.filterBy({
                        // "type": "video"
                    // });
                    // this.render();

                    // self.vent.trigger("filterComplete");

                    // return this;
                // }

                // var url = model.getUrl();

                // if(this.filterCaching == true) {
                    // if(typeof this.cached[url] != "undefined") {

                        // this.collection.filterBy({
                            // ids: this.cached[url]
                        // });
                        // this.render();

                        // self.vent.trigger("filterComplete");

                        // return this;
                    // }
                // }

                // $.ajax({
                    // url: url,
                    // type: "get",
                    // dataType: "json",
                    // success: function(response) {
                        // self.collection.filterBy({
                            // ids: response.data
                        // });
                        // self.render();

                        // if(self.filterCaching == true) {
                            // self.cached[url] = response.data;
                        // }
                    // },
                    // complete: function() {
                        // self.vent.trigger("filterComplete");
                    // }
                // });

                // return this;
            // },
            // events: {
                // "mousedown .track": "activateTrack",
                // "mouseup .track": "deactivateTrack",
                // "click .track": "toggleSelection",/*
                // "click .track .action.add": "addToSelection",
                // "click .track .action.added": "removeFromSelection"*/
                // "scroll": "scrollLock",
                // "mouseup": "deactivateAllTracks"
            // },
            // toggleSelection: function(e) {
                // e.preventDefault();

                // if(this.locked == false && this.scrolling == false) {
                    // if($(e.currentTarget).hasClass("added")) {
                        // this.removeFromSelection(e);
                    // } else {
                        // if(this.selectionLocked == false)
                            // this.addToSelection(e);
                    // }
                // }
            // },
            // activateTrack: function(e) {
                // $(e.currentTarget).addClass("active");

                // /*this.$el.bind("scroll", function(e) {
                    // e.preventDefault();

                    // e.target.scrollTop = 0;
                // });*/

                // this.forceScrollTop = this.$el.scrollTop();

                // this.deactivateAllTracks();
            // },
            // deactivateTrack: function(e) {
                // $(e.currentTarget).removeClass("active");

                // this.forceScrollTop = -1;
                // this.scrollDiff = 0;
            // },
            // deactivateAllTracks: function() {
                // $(".track", this.$el).removeClass("active");
            // },
            // addToSelection:function (e) {
                // e.preventDefault();

                // var model = this.collection.get($(e.currentTarget).attr("data-id"));
                // model.set("selected", true);

                // this.vent.trigger("selectTrack", model);
            // },
            // removeFromSelection: function(e) {
                // e.preventDefault();

                // var model = this.collection.get($(e.currentTarget).attr("data-id"));
                // model.set("selected", false);

                // this.vent.trigger("unselectTrack", model);
            // },
            // lockSelection: function() {
                // this.selectionLocked = true;
                // this.$el.addClass("selection-locked");
            // },
            // unlockSelection: function() {
                // this.selectionLocked = false;
                // this.$el.removeClass("selection-locked");
            // },
            // lock: function() {
                // this.locked = true;
                // this.$el.addClass("locked");
            // },
            // unlock: function() {
                // this.locked = false;
                // this.$el.removeClass("locked");
            // },
            // scrollLock: function(e) {
                // var self = this;

                // e.preventDefault();

                // //var top = this.$el.scrollTop();

                // if(this.forceScrollTop > -1) {
                    // this.scrollDiff = this.$el.scrollTop() - this.forceScrollTop;

                    // if(this.scrollDiff > this.scrollSensitivity || this.scrollDiff < -this.scrollSensitivity) {
                        // this.forceScrollTop = -1;
                        // this.deactivateAllTracks();
                    // } else {
                        // this.$el.scrollTop(this.forceScrollTop);
                    // }
                // } else {
                    // //clearTimeout(this.preScrollLockTimeout);
                    // clearTimeout(this.scrollLockTimeout);

                    // var now = (new Date()).getTime();
                    // if(now - this.scrollLockLastStart > 500 && !this.scrolling) {
                        // // SCROLL START

                        // this.vent.trigger("startAutoResetTimeout");

                        // //this.lock();
                        // //this.preScrollLockTimeout = setTimeout(function(){
                            // self.scrolling = true;
                            // self.scrollLockLastStart = now;
                        // //}, 100);
                    // }

                    // this.$el.bind("mouseup", function(){
                        // self.scrollLockTimeout = setTimeout(function() {
                            // if (self.scrolling) {
                                // //self.unlock();
                                // self.scrolling = false;
                                // // test
                                // //self.$el.scrollTop(self.$el.scrollTop);
                                // //self.forceScrollTop = -1;

                                // self.$el.unbind("mouseup").bind("mouseup", self.deactivateAllTracks);
                                // self.forceScrollTop = self.$el.scrollTop();
                                // self.scrollDiff = 0;
                            // }
                        // }, 50);
                    // });

                    // self.scrollLockTimeout = setTimeout(function() {
                        // if (self.scrolling) {
                            // //self.unlock();
                            // self.scrolling = false;

                            // self.forceScrollTop = self.$el.scrollTop();
                            // self.scrollDiff = 0;
                        // }
                    // }, 400);
                // }
            // }
        // });
     
/* pagination */
  // TrackLibraryView = Backbone.View.extend({
            // el: $("#tracklibrary"),
            // filterCaching: true,
            // cached: {},
            // selectionLocked: false,
            // locked: false,
            // scrollLockTimeout: null,
            // scrollLockLastStart: (new Date()).getTime(),
            // scrolling: false,
            // scrollSensitivity: 10,
            // forceScrollTop: -1,
            // scrollDiff: 0,
            // initialize: function(options) {
                // var self = this;

                // this.filterCaching = (options.filterCaching != null) ? options.filterCaching : this.filterCaching;

                // this.collection = new LibraryTrackCollection();

                // this.collection.fetch({
                    // success: function() {
                        // self.collection.reFilter();
                        // self.render();
                    // }
                // });

                // this.vent = options.vent;

                // _.bindAll(this, "applyFilter");
                // _.bindAll(this, "render");
                // _.bindAll(this, "lockSelection");
                // _.bindAll(this, "unlockSelection");
                // _.bindAll(this, "lock");
                // _.bindAll(this, "unlock");
                // this.vent.bind("applyFilter", this.applyFilter);
                // this.vent.bind("selectionReset", this.render);
                // this.vent.bind("selectionLimitReached", this.lockSelection);
                // this.vent.bind("selectionLimitUnreached", this.unlockSelection);
                // this.vent.bind("selectionLocked", this.lock);
                // this.vent.bind("selectionUnlocked", this.unlock);

                // /*setTimeout(function() {
                    // self.iscroll = new iScroll("tracklibrary");
                // }, 100);*/

                // this.$el.niceScroll({
                    // touchbehavior: true,
                    // hwacceleration: true,
                    // bouncescroll: true,
                    // grabcursorenabled: false,
                    // cursorcolor: "#20B8B3",
                    // cursorborder: "1px solid #20B8B3",
                    // cursoropacitymax: 0.6
                    // /*onBeforeScrollStart: function(e) {
                        // e.preventDefault();

                        // setTimeout(function(e) {

                        // });
                    // }*/
                // });

                // /*this.$el.getNiceScroll()[0].cursor.mouseup(function(e){
                    // alert('mouse up');// !! DONT GET THE ALERT !!
                // });*/

                // //this.$loadingSpinner = $("#options div.spinner");
            // },
            // render:function () {
                // var self = this;
				// this.$el.empty();

				// var container = document.createElement('div');
				// container.className = 'track-grid';

				// var numRows = 6;
				// var numCols = 4;
				// var numTracks = this.collection.filtered.length;
				// var numEmptyCells = numRows * numCols - numTracks;

				// this.collection.filtered.each(function(item, index) {
				  // var trackView = self.renderTrack(item);
				  // trackView.className = 'track';
				  // trackView.setAttribute('data-id', item.get('id'));
				  // container.appendChild(trackView);
				// });

				// // Add empty cells if necessary
				// for (var i = 0; i < numEmptyCells; i++) {
				  // var emptyCell = document.createElement('div');
				  // emptyCell.className = 'empty-cell';
				  // container.appendChild(emptyCell);
				// }

				// this.$el.append(container);

            // },
            // renderTrack:function (item) {
                // var view = new LibraryTrackView({
                    // model: item
                // });

                // view.render();
                // view.$el
                    // .attr("data-id", item.get("id"));

                // return view.el;
            // },
            // applyFilter: function(model) {
                // var self = this;

                // if(model.get("all") == true) {
                    // this.collection.filterBy({});
                    // this.render();

                    // self.vent.trigger("filterComplete");

                    // return this;
                // } else if(model.get("allAudio") == true) {
                    // this.collection.filterBy({
                        // "type": "audio"
                    // });
                    // this.render();

                    // self.vent.trigger("filterComplete");

                    // return this;
                // } else if(model.get("allVideo") == true) {
                    // this.collection.filterBy({
                        // "type": "video"
                    // });
                    // this.render();

                    // self.vent.trigger("filterComplete");

                    // return this;
                // }

                // var url = model.getUrl();

                // if(this.filterCaching == true) {
                    // if(typeof this.cached[url] != "undefined") {

                        // this.collection.filterBy({
                            // ids: this.cached[url]
                        // });
                        // this.render();

                        // self.vent.trigger("filterComplete");

                        // return this;
                    // }
                // }

                // $.ajax({
                    // url: url,
                    // type: "get",
                    // dataType: "json",
                    // success: function(response) {
                        // self.collection.filterBy({
                            // ids: response.data
                        // });
                        // self.render();

                        // if(self.filterCaching == true) {
                            // self.cached[url] = response.data;
                        // }
                    // },
                    // complete: function() {
                        // self.vent.trigger("filterComplete");
                    // }
                // });

                // return this;
            // },
            // events: {
                // "mousedown .track": "activateTrack",
                // "mouseup .track": "deactivateTrack",
                // "click .track": "toggleSelection",/*
                // "click .track .action.add": "addToSelection",
                // "click .track .action.added": "removeFromSelection"*/
                // "scroll": "scrollLock",
                // "mouseup": "deactivateAllTracks"
            // },
            // toggleSelection: function(e) {
                // e.preventDefault();

                // if(this.locked == false && this.scrolling == false) {
                    // if($(e.currentTarget).hasClass("added")) {
                        // this.removeFromSelection(e);
                    // } else {
                        // if(this.selectionLocked == false)
                            // this.addToSelection(e);
                    // }
                // }
            // },
            // activateTrack: function(e) {
                // $(e.currentTarget).addClass("active");

                // /*this.$el.bind("scroll", function(e) {
                    // e.preventDefault();

                    // e.target.scrollTop = 0;
                // });*/

                // this.forceScrollTop = this.$el.scrollTop();

                // this.deactivateAllTracks();
            // },
            // deactivateTrack: function(e) {
                // $(e.currentTarget).removeClass("active");

                // this.forceScrollTop = -1;
                // this.scrollDiff = 0;
            // },
            // deactivateAllTracks: function() {
                // $(".track", this.$el).removeClass("active");
            // },
            // addToSelection:function (e) {
                // e.preventDefault();

                // var model = this.collection.get($(e.currentTarget).attr("data-id"));
                // model.set("selected", true);

                // this.vent.trigger("selectTrack", model);
            // },
            // removeFromSelection: function(e) {
                // e.preventDefault();

                // var model = this.collection.get($(e.currentTarget).attr("data-id"));
                // model.set("selected", false);

                // this.vent.trigger("unselectTrack", model);
            // },
            // lockSelection: function() {
                // this.selectionLocked = true;
                // this.$el.addClass("selection-locked");
            // },
            // unlockSelection: function() {
                // this.selectionLocked = false;
                // this.$el.removeClass("selection-locked");
            // },
            // lock: function() {
                // this.locked = true;
                // this.$el.addClass("locked");
            // },
            // unlock: function() {
                // this.locked = false;
                // this.$el.removeClass("locked");
            // },
            // scrollLock: function(e) {
                // var self = this;

                // e.preventDefault();

                // //var top = this.$el.scrollTop();

                // if(this.forceScrollTop > -1) {
                    // this.scrollDiff = this.$el.scrollTop() - this.forceScrollTop;

                    // if(this.scrollDiff > this.scrollSensitivity || this.scrollDiff < -this.scrollSensitivity) {
                        // this.forceScrollTop = -1;
                        // this.deactivateAllTracks();
                    // } else {
                        // this.$el.scrollTop(this.forceScrollTop);
                    // }
                // } else {
                    // //clearTimeout(this.preScrollLockTimeout);
                    // clearTimeout(this.scrollLockTimeout);

                    // var now = (new Date()).getTime();
                    // if(now - this.scrollLockLastStart > 500 && !this.scrolling) {
                        // // SCROLL START

                        // this.vent.trigger("startAutoResetTimeout");

                        // //this.lock();
                        // //this.preScrollLockTimeout = setTimeout(function(){
                            // self.scrolling = true;
                            // self.scrollLockLastStart = now;
                        // //}, 100);
                    // }

                    // this.$el.bind("mouseup", function(){
                        // self.scrollLockTimeout = setTimeout(function() {
                            // if (self.scrolling) {
                                // //self.unlock();
                                // self.scrolling = false;
                                // // test
                                // //self.$el.scrollTop(self.$el.scrollTop);
                                // //self.forceScrollTop = -1;

                                // self.$el.unbind("mouseup").bind("mouseup", self.deactivateAllTracks);
                                // self.forceScrollTop = self.$el.scrollTop();
                                // self.scrollDiff = 0;
                            // }
                        // }, 50);
                    // });

                    // self.scrollLockTimeout = setTimeout(function() {
                        // if (self.scrolling) {
                            // //self.unlock();
                            // self.scrolling = false;

                            // self.forceScrollTop = self.$el.scrollTop();
                            // self.scrollDiff = 0;
                        // }
                    // }, 400);
                // }
            // }
        // });
     
		TrackSelectionView = Backbone.View.extend({
            el: $("#playlist-tracks"),
            $blankingPlate: $("#playlist-timeout"),
            $addToPlaylistButton: $("#add-to-playlist"),
            emptyContent: "",
            autoResetTimeout: null,
            initialize: function(options) {
                var self = this;

                this.emptyContent = this.$el.html();

                this.silvaPlaylistCollection = new PlaylistEntryCollection();

                this.collection = new SelectionTrackCollection();

                this.collection.on("add", this.render, this);
                this.collection.on("remove", this.render, this);
                this.collection.on("reset", this.selectionReset, this);

                $.get(app.baseUrl + "silva_playlist/enable.json");

                this.vent = options.vent;

                _.bindAll(this, "selectTrack");
                _.bindAll(this, "unselectTrack");
                _.bindAll(this, "startAutoResetTimeout");
                this.vent.bind("selectTrack", this.selectTrack);
                this.vent.bind("unselectTrack", this.unselectTrack);
                this.vent.bind("startAutoResetTimeout", this.startAutoResetTimeout);

                this.$addToPlaylistButton.click(function(e){
                    self.sendPlaylist(e);
                    $(this).removeClass("on");
                });

                this.$addToPlaylistButton.mousedown(function(e){
                    $(this).addClass("on");

                    $(this).bind("mousemove", function(e2) {
                        $(this).removeClass("on");

                        $(this).unbind("mousemove");
                    });
                });

                this.$addToPlaylistButton.mouseup(function(e){
                    $(this).removeClass("on");

                    $(this).unbind("mousemove");
                });
            },
            render:function () {
                this.$el.empty(); 
                if(this.collection.length > 0) {//kamod
                    this.$el.removeClass("empty");
                    this.collection.each(this.renderTrack, this);
                    //$("#sendplaylist-title, #send-playlist", this.$el).show();
                } else {
                    this.$el.addClass("empty");
                    this.$el.html(this.emptyContent);
                    //$("#sendplaylist-title, #send-playlist", this.$el).hide();
                }
            },
            renderTrack:function (item) {
                var view = new SelectionTrackView({
                    model: item,
                    vent: this.vent
                });

                this.$el.append(view.render().el);
            }/*,
            events: {
                "click #add-to-playlist": "sendPlaylist"
            }*/,
            sendPlaylist: function() {
                var self = this;

                // No tracks selected!
                if(this.collection.length < 1) //kamod
                    return false;

                this.silvaPlaylistCollection.filterOutExpired();
                var cnt = this.silvaPlaylistCollection.length;

                data = new Array();
                this.collection.each(function(item){
                    cnt++;

                    data.push({
                        uid: item.get("uid"),
                        position: cnt
                    });
                });

                //$("#thank-you", self.$el).html("adding tracks...");
                this.$blankingPlate.show();
                self.$el.addClass("locked");
                this.vent.trigger("selectionLocked");

                var tracksAdded = function(){
                    self.$el.removeClass("locked");
                    self.vent.trigger("selectionUnlocked");
                    self.vent.trigger("selectionLimitUnreached");
                    self.$blankingPlate.hide();
                };

                $.ajax({
                    url: app.baseUrl + "silva_playlist/add_tracks.json",
                    data: JSON.stringify(data),
                    type: "post",
                    complete: function() {
                        self.collection.each(function(item) {
                            var entry = new PlaylistEntryTrack();
                            entry.Track = item;

                            self.silvaPlaylistCollection.add(entry);
                        });

                        self.collection.removeAllTracks();

                        //$("#thank-you", self.$el).html("your tracks have been added to the playlist");
                        setTimeout(tracksAdded, 10000);
                    }
                });
            },
            selectTrack: function(model) {
                this.collection.addTrack(model);

                if(this.collection.length > 0) { //kamod
                    this.vent.trigger("selectionLimitReached");
                }

                this.startAutoResetTimeout();
            },
            unselectTrack: function(model) {
                this.collection.removeTrack(model);

                this.vent.trigger("selectionLimitUnreached");

                this.startAutoResetTimeout();
            },
            selectionReset: function() {
                this.vent.trigger("selectionReset");
                this.render();
            },
            startAutoResetTimeout: function() {
                var self = this;

                clearTimeout(this.autoResetTimeout);
                var removeAllTracks = function() {
                    self.collection.removeAllTracks();
					self.vent.trigger("selectionLimitUnreached");
                };
                this.autoResetTimeout = setTimeout(removeAllTracks, 20000);
            }
        });

        /* kamod
        Video Player
         */

          // // PlayerView = Backbone.View.extend({
            // el: $("#videoElement"),
            // vlc: null,
            // $trackInfoName: $("#track-info #trackname"),
            // $trackInfoArtist: $("#track-info #artist"),
            // updateCheckInterval: null,
            // currentClipStartTime: 0,
            // updateSeekStart: 0,
            // updateSeekInterval: null,
            // latencyMs: 0,
            // initialize: function(options) {
                // var self = this;

                // this.vent = options.vent;
                // this.currentPlayingTrack = options.currentPlayingTrack;

                // this.vlc = document.getElementById("vlc");
				// console.log(this.vlc);
                // this.vlc.controls = false;
                // this.vlc.video.aspectRatio = "750:425";

                // this.$el.parent().click(function(e) {
                    // //var filename = "1-2unlimited - get ready f_0.mpe";

                    // var filename = prompt("Enter a clip filename to play");
                    // if(filename != "" && filename != null) {
                        // self.playClip(filename);
                    // }
                // });

                // this.playCurrentClip();
                // this.startUpdateChecking();

                // _.bindAll(this, "endReached");
                // this.vent.bind("currentTrackEndReached", this.endReached);
            // },
            // startUpdateChecking: function() {
                // var self = this;

                // var checkForUpdateAgain = function() {
                    // self.checkForUpdate(true);
                // };

                // this.updateCheckInterval = setTimeout(checkForUpdateAgain, 1000);
            // },
            // checkForUpdate: function(startAgain) {
                // var self = this;

                // startAgain = (startAgain) ? startAgain : false;

                // $.ajax({
                    // url: app.baseUrl + "video/check_for_update.json",
                    // success: function(response) {
                        // if(response.data.update) {
                            // self.playCurrentClip();
                        // }
                    // },
                    // complete: function() {
                        // if(startAgain)
                            // self.startUpdateChecking();
                    // }
                // });
            // },
            // endReached: function() {
                // var self = this;

                // this.$trackInfoName.html("");
                // this.$trackInfoArtist.html("");

                // // Play holding clip
                // $.ajax({
                    // url: app.baseUrl + "video/decrypt.json",
                    // data: JSON.stringify({filename: ""}),
                    // type: "post",
                    // success: function(response) {
                        // self.vlc.playlist.items.clear();
                        // var id = self.vlc.playlist.add(response.data.mrl, null, new Array("no-audio", "input-fast-seek"));
                        // self.vlc.video.aspectRatio = "750:425";
                        // self.vlc.playlist.playItem(id);
                    // }
                // });
            // },
            // playCurrentClip: function() {
                // var self = this;

                // var start = (new Date()).getTime();
                // //console.log("START: " + start);
                // $.ajax({
                    // url: app.baseUrl + "video/get_current.json",
                    // success: function(response) {
                        // if(response.data.finished == true) {
                            // self.endReached();
                        // } else {
                            // self.currentPlayingTrack.set(response.data.status.Track);
                            // self.vent.trigger("playClip");

                            // self.$trackInfoName.html(response.data.status.Track.track_name);
                            // self.$trackInfoArtist.html(response.data.status.Track.TrackArtist.name);

                            // var end = (new Date()).getTime();
                            // //console.log("END: " + end);
                            // var offset = end - start;
                            // self.playClip(response.data.filename, (response.data.offset + offset));

                            // //console.log("playCurrentClip response offset: " + response.data.offset);
                            // //console.log("playCurrentClip local offset: " + offset)
                        // }
                    // }
                // });
            // },
            // getCurrentClipOffset: function(callback) {
                // //var start = (new Date()).getTime();

                // $.ajax({
                    // url: app.baseUrl + "video/get_current.json",
                    // success: function(response) {
                        // var now = (new Date()).getTime();
                        // var offset = now - response.data.start;
                        // if(callback)
                            // callback(offset, response.data.start);
                    // }
                // });
            // },
            // playClip: function(filename, offset) {
                // var self = this;

                // offset = (offset) ? offset : 0;

                // //console.log("playClip offset: " + offset);

                // //var start = (new Date()).getTime();

                // data = {
                    // filename: filename
                // };

                // $.ajax({
                    // url: app.baseUrl + "video/decrypt.json",
                    // data: JSON.stringify(data),
                    // type: "post",
                    // success: function(response) {
                        // /*self.unregisterEvent("MediaPlayerPlaying");
                         // self.unregisterEvent("MediaPlayerEndReached");*/

                        // //var url = app.url + "/files/decrypted-clips/" + encodeURIComponent(response.data.filename);

                        // //console.log(response.data.mrl);

                        // self.vlc.playlist.items.clear();
                        // var id = self.vlc.playlist.add(/*url*/response.data.mrl, null, new Array("no-audio", "input-fast-seek"));
                        // /*self.vlc.width = 750;
                        // self.vlc.height = 425;*/

                        // self.vlc.video.aspectRatio = "750:425";
                        // self.vlc.playlist.playItem(id);

                        // var updateSeek = function() {
                            // var now = (new Date()).getTime();
                            // self.vlc.video.aspectRatio = "750:425";
                            // self.vlc.input.time = (now - self.currentClipStartTime) + self.latencyMs;
                            // self.currentPlayingTrack.playHead(self.vlc.input.time);

                            // if((now - self.updateSeekStart) > 5000) {
                                // clearInterval(self.updateSeekInterval);
                            // }
                        // };

                        // var updatePlayHead = function() {
                            // var now = (new Date()).getTime();
                            // self.vlc.video.aspectRatio = "750:425";
                            // self.vlc.input.time = (now - self.currentClipStartTime) + self.latencyMs;
                            // self.currentPlayingTrack.playHead(self.vlc.input.time);
                        // };

                        // self.getCurrentClipOffset(function(offset, startTime) {
                            // self.currentClipStartTime = startTime;

                            // // Sync with Silva
                            // if(response.data.type == 'video') {
                                // self.latencyMs = 360;

                                // self.updateSeekStart = (new Date()).getTime();
                                // self.updateSeekInterval = setInterval(updateSeek, 1000);

                                // /*setTimeout(function() {
                                 // var now = (new Date()).getTime();
                                 // self.vlc.input.time = (now - self.currentClipStartTime) + latencyMs;
                                 // }, 5000);*/

                                // setTimeout(updatePlayHead, 10000);
                                // setTimeout(updatePlayHead, 15000);
                            // } else {
                                // self.latencyMs = 360;

                                // var now = (new Date()).getTime();
                                // var time = (now - self.currentClipStartTime) + self.latencyMs;

                                // self.currentPlayingTrack.playHead(time);
                            // }
                        // });

                        // /*self.registerEvent("MediaPlayerPlaying", function() {
                            // //self.show();
                            // //self.vlc.seek(seek, false);

                            // self.unregisterEvent("MediaPlayerPlaying");*/

                            // /*self.registerEvent("MediaPlayerEndReached", function() {
                                // //self.hide();
                                // self.unregisterEvent("MediaPlayerEndReached");
                            // });*/
                       // /* });*/
                    // }
                // });
            // },
            // hide: function() {
                // this.$el.hide();
            // },
            // show: function() {
                // this.$el.show();
            // },
            // splash: function() {
                // this.vlc.playlist.items.clear();
                // var id = this.vlc.playlist.add(app.url + "/img/Halloween-Night-Background.jpg");
                // this.vlc.playlist.playItem(id);
            // },
            // registerEvent: function(event, handler) {
                // if (this.vlc) {
                    // if (this.vlc.attachEvent) {
                        // // Microsoft
                        // this.vlc.attachEvent (event, handler);
                    // } else if (this.vlc.addEventListener) {
                        // // Mozilla: DOM level 2
                        // this.vlc.addEventListener (event, handler, false);
                    // } else {
                        // // DOM level 0
                        // this.vlc["on" + event] = handler;
                    // }
                // }
            // },
            // unregisterEvent: function(event, handler) {
                // if (this.vlc) {
                    // if (this.vlc.detachEvent) {
                        // // Microsoft
                        // this.vlc.detachEvent (event, handler);
                    // } else if (this.vlc.removeEventListener) {
                        // // Mozilla: DOM level 2
                        // this.vlc.removeEventListener (event, handler, false);
                    // } else {
                        // // DOM level 0
                        // this.vlc["on" + event] = null;
                    // }
                // }
            // }
        // });

         // WaveformView = TrackView.extend({
            // el: $("#waveform"),
            // $slider: $("#waveform .slider"),
            // updateSliderInterval: null,
            // zeroLeft: 0,
            // sliderWidth: 0,
            // enabled: false,
            // initialize: function(options) {
                // this.vent = options.vent;
                // this.currentPlayingTrack = options.currentPlayingTrack;

                // _.bindAll(this, "loadWaveform");
                // _.bindAll(this, "updatePlayHead");
                // _.bindAll(this, "startPlayHead");
                // _.bindAll(this, "stopPlayHead");
                // _.bindAll(this, "updateSliderPosition");
                // _.bindAll(this, "endReached");
                // this.vent.bind("playClip", this.loadWaveform);
                // this.vent.bind("updatePlayHead", this.updatePlayHead);
                // this.vent.bind("startPlayHead", this.startPlayHead);
                // this.vent.bind("stopPlayHead", this.stopPlayHead);
                // this.vent.bind("currentTrackEndReached", this.endReached);

                // this.updateCoords();

                // //this.updateSliderInterval = setInterval(this.updateSliderPosition, 100);
            // },
            // endReached: function() {
                // this.$slider.fadeOut();
            // },
            // loadWaveform: function(e) {
                // var self = this;

                // self.enabled = false;
                // self.$slider.fadeOut();
                // $.ajax({
                    // url: app.baseUrl + "tracks/waveform/" + this.currentPlayingTrack.get("id") + "/" + this.sliderWidth + ".json",
                    // success: function(response) {
                        // if(response.data.success) {
                            // var imgUrl = app.baseUrl + "img/waveforms/" + self.currentPlayingTrack.get("uid") + ".png";

                            // $('<img/>').attr("src", imgUrl).load(function() {
                                // $(this).remove();

                                // self.$slider.css("background-image", "url(" + imgUrl + ")");
                                // //self.$slider.css("background-image", "url(" + app.baseUrl + "img/waveforms/test.png)");

                                // self.updateCoords();
                                // self.$slider.fadeIn();

                                // self.enabled = true;
                            // });
                        // }
                    // }
                // });
            // },
            // updateCoords: function() {
                // this.zeroleft = this.$el.width() / 2;
                // this.sliderWidth = this.$slider.width();
            // },
            // updatePlayHead: function(e) {
                // this.updateSliderPosition();
            // },
            // startPlayHead: function(e) {
                // //this.$slider.show();
            // },
            // stopPlayHead: function(e) {
                // //this.$slider.hide();
            // },
            // updateSliderPosition: function() {
                // if(this.enabled) {
                    // var left = this.zeroleft - (this.sliderWidth * (this.currentPlayingTrack.percent / 100));

                    // //console.log(this.zeroleft + " - (" + this.sliderWidth + " * (" + this.currentPlayingTrack.percent + "/100)) = " + left);

                    // //var left = this.zeroleft - (this.sliderWidth * (this.currentPlayingTrack.predictedNextPercent / 100));

                    // this.$slider.css("left", left + "px");
                    // /*this.$slider.animate({
                        // "left": left + "px"
                    // }, this.currentPlayingTrack.playHeadUpdateRate, "linear");*/
                // }
           // }
        // }); 

        /*
         MAIN
         */

        var vent = _.extend({}, Backbone.Events);

        var trackSelectionView = new TrackSelectionView({vent: vent});
        var filtersView = new FiltersView({vent: vent, filterCaching: true});
        var trackLibraryView = new TrackLibraryView({vent: vent, filterCaching: true});

        CurrentPlayingTrack = Track.extend({
            pos: 0,
            len: 0,
            percent: 0,
            /*predictedNextPos: 0,
            predictedNextPercent: 0,*/
            playHeadUpdateRate: 40,
            playHeadInterval: null,
            initialize: function(options) {
                this.vent = options.vent;

                _.bindAll(this, "playHead");
                _.bindAll(this, "start");
                _.bindAll(this, "stop");
                _.bindAll(this, "updateLength");
                this.on("change:length", this.updateLength, this);
            },
            updateLength: function() {
                this.reset();

                //console.log("updateLength");
                var split = this.get("length").split(":");
                this.len = (parseInt(split[0]) * 3.6e+6) + (parseInt(split[1]) * 60000) + (parseInt(split[2]) * 1000);

                //console.log(this.get("length"));
                //console.log(this.len);
                this.start();
            },
            playHead: function(pos) {
                //console.log("PLAY HEAD", this.pos, this.len);

                if(pos != null) {
                    this.pos = parseInt(pos);
                    this.start();
                } else {
                    this.pos += this.playHeadUpdateRate;
                }

                this.percent = this.pos / this.len * 100;



                /*this.predictedNextPos = this.pos + this.playHeadUpdateRate;
                this.predictedNextPercent = this.predictedNextPos / this.len * 100;*/

                //console.log((this.pos / 1000) + " seconds / predicted in 1 second: " + (this.predictedNextPos / 1000));

                this.vent.trigger("updatePlayHead");

                //console.log(this.pos, this.len, this.percent);

                if(this.pos > this.len) {
                    //console.log(this.pos, this.len);
                    this.stop();
                    this.vent.trigger("currentTrackEndReached");
                    //this.reset();
                }
            },
            start: function() {
                //console.log("start");

                clearInterval(this.playHeadInterval);
                this.playHeadInterval = setInterval(this.playHead, this.playHeadUpdateRate);

                this.vent.trigger("startPlayHead");
            },
            stop: function() {
                //console.log("stop");

                clearInterval(this.playHeadInterval);

                this.vent.trigger("stopPlayHead");
            },
            reset: function() {
                this.pos = 0;
                this.len = 0;
                this.percent = 0;
                /*this.predictedNextPos = 0;
                this.predictedNextPercent = 0;*/
            }
        });
        var currentPlayingTrack = new CurrentPlayingTrack({vent: vent});
     //   var playerView = new PlayerView({vent: vent, currentPlayingTrack: currentPlayingTrack});
      //  var waveformView = new WaveformView({vent: vent, currentPlayingTrack: currentPlayingTrack});

        //$.fx.interval = 100;
    });
})();