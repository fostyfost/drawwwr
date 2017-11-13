/**
 * @param action
 * @returns {jQuery}
 */
jQuery.fn.preloader = function (action) {
    if (actions[action] && typeof actions[action] === 'function') {
        actions[action].apply(this);
    }

    return this;
};

(function (jQuery) {
    /**
     * @type {{start: start, stop: stop}}
     */
    var actions = {
        start: function () {
            this.$preloaderInner = jQuery('<div></div>')
                .addClass('loader-inner')
                .addClass('ball-scale-multiple')
                .loaders();

            this.$preloader = jQuery('<div></div>')
                .addClass('preloader')
                .append(this.$preloaderInner);

            this.$preloaderOverlay = jQuery('<div id="js__preloader"></div>')
                .addClass('preloader-overlay')
                .append(this.$preloader);

            this.append(this.$preloaderOverlay);
        },

        stop: function () {
            this.find('.preloader-overlay').remove();
        }
    };

    /**
     * @param action
     * @returns {jQuery}
     */
    jQuery.fn.preloader = function (action) {
        actions[action].apply(this);

        return this;
    };
}(jQuery));

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(['jquery'], function (a0) {
            return (factory(a0));
        });
    } else if (typeof exports === 'object') {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(this, function (jQuery) {
    (function () {
        'use strict';

        var Drawwwr = function () {
            this.init();

            return this;
        };

        Drawwwr.prototype.init = function () {
            /**
             * @param options
             * @returns {Brush}
             * @constructor
             */
            var Brush = function (options) {
                var _ = this;

                _.Foreground = '#000000';
                _.Thickness = 10;
                _.Icon = 'fa fa-paint-brush';
                _.LastPaint = null;
                _.Multitouch = false;
                _.Opacity = 1;

                /**
                 * @constructor
                 */
                _.Start = function () {
                    Artboard.Layer.Context.globalCompositeOperation = 'source-over';
                    Artboard.Layer.Context.strokeStyle = _.Foreground;
                    Artboard.Layer.Context.lineJoin = Artboard.Layer.Context.lineCap = 'round';

                    _.LastPaint = null;
                };

                /**
                 * @constructor
                 */
                _.Stop = function () {
                    _.LastPaint = null;
                };

                /**
                 * @param e
                 * @constructor
                 */
                _.Draw = function (e) {
                    if (_.LastPaint) {
                        Artboard.Layer.Context.globalAlpha = _.Opacity;
                        Artboard.Layer.Context.beginPath();
                        Artboard.Layer.Context.lineWidth = e.brushPressure * _.Thickness;
                        Artboard.Layer.Context.moveTo(_.LastPaint[0], _.LastPaint[1]);
                        Artboard.Layer.Context.lineTo(e.brushX, e.brushY);
                        Artboard.Layer.Context.stroke();
                        Artboard.Layer.Context.globalAlpha = 1;
                    }

                    _.LastPaint = [e.brushX, e.brushY, e.brushPressure];
                };

                /**
                 * @constructor
                 */
                _.Pick = function () {
                    Artboard.Brush = _;

                    Artboard.ColorPickerLabel.css('background-color', _.Foreground);
                    Artboard.ThicknessLabel.html(_.Thickness);
                };

                for (var i in options) {
                    if (options.hasOwnProperty(i)) {
                        _[i] = options[i];
                    }
                }

                if (_.Init) {
                    _.Init.call(_);
                }

                return _;
            };

            /**
             * @returns {Layer}
             * @constructor
             */
            var Layer = function () {
                var _ = this,
                    lang = {
                        layer: 'Layer',
                        newName: 'Enter a new name for layer',
                        deleteConfirmation: 'Are you sure you want to delete'
                    };

                if (window.drawwwrData !== undefined) {
                    lang.layer = window.drawwwrData.layer !== undefined
                        ? window.drawwwrData.layer
                        : 'Layer';

                    lang.newName = window.drawwwrData.newName !== undefined
                        ? window.drawwwrData.newName
                        : 'Enter a new name for layer';

                    lang.deleteConfirmation = window.drawwwrData.deleteConfirmation !== undefined
                        ? window.drawwwrData.deleteConfirmation
                        : 'Are you sure you want to delete';
                }

                _.Active = false;
                _.Hidden = false;

                _.Canvas = jQuery('<canvas>')
                    .addClass('layer')
                    .data('Layer', _)
                    .prependTo('.artboards');

                _.Context = _.Canvas[0].getContext('2d');

                _.Drawing = false;
                _.Erasing = false;

                _.Index = -1;

                Object.defineProperty(_, 'Name', {
                    get: function () {
                        return _.MenuItem.find('> span').text().trim();
                    },
                    set: function () {
                        _.MenuItem.find('> span').html(arguments[0]);
                    }
                });

                /**
                 * @param width
                 * @param height
                 * @returns {Layer}
                 * @constructor
                 */
                _.Resize = function (width, height) {
                    _.Canvas
                        .attr({
                            width: (width !== undefined) ? width : _.Canvas.parent().width(),
                            height: (height !== undefined) ? height : _.Canvas.parent().height()
                        });

                    return _;
                };

                _.MenuItem = jQuery('<div>')
                    .addClass('layer')
                    .prependTo('.sidebar .controls.layers > ul')
                    .data('Layer', _)
                    .on('click', function () {
                        _.Focus();

                        return false;
                    })
                    .on('dblclick', function () {
                        var newname = prompt(lang.newName + ' ' + (_.Index + 1) + ':', _.Name);

                        if (newname && newname !== _.Name) {
                            _.Name = newname;
                        }

                        return false;
                    });

                jQuery('<span>')
                    .text(lang.layer + ' ' + (_.Index + 1))
                    .appendTo(_.MenuItem);

                _.HideButton = jQuery('<a></a>')
                    .attr({
                        href: '#hide'
                    })
                    .addClass('hide-layer')
                    .appendTo(_.MenuItem)
                    .on('mouseenter mouseleave', function (e) {
                        jQuery('html')[e.altKey ? 'addClass' : 'removeClass']('alt');
                    })
                    .on('click', function (e) {
                        if (e.altKey || e.button === 5 || e.button === '5') {
                            if (confirm(lang.deleteConfirmation + ' ' + _.Name + '?')) {
                                _.Canvas.remove();
                                _.MenuItem.remove();

                                var layers = [];

                                for (var i in Artboard.Layers.List) {
                                    if (Artboard.Layers.List[i] !== _) {
                                        layers.push(Artboard.Layers.List[i]);
                                    }
                                }

                                Artboard.Layers.List = layers;
                            }

                            jQuery('html').removeClass('alt');
                        } else {
                            _.Hidden = !_.Hidden;

                            _.MenuItem[_.Hidden ? 'addClass' : 'removeClass']('hidden');
                            _.Canvas[_.Hidden ? 'addClass' : 'removeClass']('hidden');
                        }

                        return false;
                    });

                /**
                 * @returns {Layer}
                 * @constructor
                 */
                _.Focus = function () {
                    jQuery('.layer.active').removeClass('active');

                    for (var i in Artboard.Layers.List) {
                        Artboard.Layers.List[i].Active = false;
                    }

                    Artboard.Layer = _;

                    _.Active = true;

                    _.Canvas.addClass('active');
                    _.MenuItem.addClass('active');

                    return _;
                };

                /**
                 * @param e
                 * @returns {{brushX: number, brushY: number, brushPressure: number}}
                 * @constructor
                 */
                _.GetBrushCoords = function (e) {
                    var offset = _.Context.canvas.getBoundingClientRect(),
                        x = e.pageX - offset.left,
                        y = e.pageY - offset.top,
                        z = 1;

                    if (e['touches'] && e['touches'][0] && typeof e['touches'][0]['force'] !== 'undefined') {
                        z = e['touches'][0]['force'];
                    } else {
                        z = ((e['pressure'] !== undefined)
                            ? e['pressure']
                            : ((e['mozPressure'] !== undefined) ? e['mozPressure'] : 1));
                    }

                    return ({
                        brushX: x,
                        brushY: y,
                        brushPressure: z
                    });
                };

                /**
                 * @param e
                 * @constructor
                 */
                _.Draw = function (e) {
                    if (!_.Drawing) {
                        return;
                    }

                    var brush = _.GetBrushCoords(e);

                    e.brushX = brush.brushX;
                    e.brushY = brush.brushY;
                    e.brushPressure = brush.brushPressure;

                    (_.Erasing ? Artboard.Brushes.Eraser : Artboard.Brush).Draw(e);
                };

                /**
                 * @param e
                 * @constructor
                 */
                _.DrawStart = function (e) {
                    var brush = _.GetBrushCoords(e);

                    e.brushX = brush.brushX;
                    e.brushY = brush.brushY;
                    e.brushPressure = brush.brushPressure;

                    _.Erasing = (e.button === 5 || e.button === '5' || e.altKey);

                    (_.Erasing ? Artboard.Brushes.Eraser : Artboard.Brush).Start(e);

                    _.Drawing = true;

                    jQuery('html').addClass('drawing');
                };

                /**
                 * @param e
                 * @constructor
                 */
                _.DrawStop = function (e) {
                    _.Drawing = false;

                    jQuery('html').removeClass('drawing');

                    var brush = _.GetBrushCoords(e);

                    e.brushX = brush.brushX;
                    e.brushY = brush.brushY;
                    e.brushPressure = brush.brushPressure;

                    (_.Erasing ? Artboard.Brushes.Eraser : Artboard.Brush).Stop(e);

                    _.Erasing = false;
                };

                return _;
            };

            var Artboard = new (function () {
                var _ = this,
                    lang = {
                        layer: 'Layer',
                        def: 'Default',
                        pencil: 'Pencil',
                        eraser: 'Eraser'
                    };

                if (window.drawwwrData !== undefined) {
                    lang.layer = window.drawwwrData.layer !== undefined
                        ? window.drawwwrData.layer
                        : 'Layer';

                    lang.def = window.drawwwrData.def !== undefined
                        ? window.drawwwrData.def
                        : 'Default';

                    lang.pencil = window.drawwwrData.pencil !== undefined
                        ? window.drawwwrData.pencil
                        : 'Pencil';

                    lang.eraser = window.drawwwrData.eraser !== undefined
                        ? window.drawwwrData.eraser
                        : 'Eraser';
                }

                Object.defineProperty(_, 'Background', {
                    /**
                     * @returns {*|jQuery}
                     */
                    get: function () {
                        return jQuery('.artboards').css('background-color');
                    },
                    /**
                     * @returns {*|jQuery}
                     */
                    set: function () {
                        return jQuery('.artboards').css('background-color', arguments[0]);
                    }
                });

                _.Layer = null;

                _.Layers = {
                    List: [],
                    /**
                     * @param callback
                     * @returns {Layer}
                     * @constructor
                     */
                    Add: function (callback) {
                        var layer = new Layer();

                        layer.Index = _.Layers.List.push(layer) - 1;
                        layer.MenuItem.find('> span').html(lang.layer + ' ' + (layer.Index + 1));

                        layer.Resize();

                        for (var i in _.Layers.List) {
                            _.Layers.List[i].Canvas
                                .prependTo('.artboards')
                                .css('z-index', i);
                        }

                        if (callback) {
                            callback.call(_, layer);
                        }

                        return layer;
                    },
                    /**
                     * @param index
                     * @returns {*}
                     * @constructor
                     */
                    Focus: function (index) {
                        if (_.Layers.List[index]) {
                            return _.Layers.List[index].Focus();
                        }

                        return null;
                    }
                };

                _.Direction = 0;
                _.LastPaint = null;

                _.Brushes = {
                    Default: new Brush({
                        title: lang.def
                    }),
                    Pencil: new Brush({
                        title: lang.pencil,
                        Icon: 'fa fa-pencil',
                        Opacity: .75,
                        Thickness: 1,
                        /**
                         * @member {string} Foreground
                         * @constructor
                         */
                        Start: function () {
                            _.Layer.Context.globalCompositeOperation = 'source-over';
                            _.Layer.Context.strokeStyle = this.Foreground;
                            _.Layer.Context.lineJoin = 'round';
                            _.Layer.Context.lineCap = 'butt';

                            this.LastPaint = null;
                        }
                    }),
                    Shape: new Brush({
                        Icon: false,
                        Background: 'rgba(255, 0, 0, .25)',
                        Foreground: '#000000',
                        Thickness: 5,
                        Points: [],
                        /**
                         * @returns {boolean}
                         * @constructor
                         */
                        Start: function () {
                            return false;
                        },
                        /**
                         * @returns {boolean}
                         * @constructor
                         */
                        Stop: function () {
                            return false;
                        },
                        /**
                         * @returns {boolean}
                         * @constructor
                         */
                        Draw: function () {
                            return false;
                        }
                    }),
                    Eraser: new Brush({
                        title: lang.eraser,
                        Icon: 'fa fa-eraser',
                        Thickness: 20,
                        /**
                         * @constructor
                         */
                        Start: function () {
                            _.Layer.Context.globalCompositeOperation = 'destination-out';
                            _.Layer.Context.strokeStyle = '#000';
                            _.Layer.Context.lineJoin = _.Layer.Context.lineCap = 'round';

                            this.LastPaint = null;
                        },
                        /**
                         * @constructor
                         */
                        Stop: function () {
                            _.Erasing = false;

                            this.LastPaint = null;
                        },
                        /**
                         * @param e
                         * @constructor
                         */
                        Draw: function (e) {
                            if (this.LastPaint) {
                                _.Layer.Context.beginPath();
                                _.Layer.Context.lineWidth = e.brushPressure * this.Thickness;
                                _.Layer.Context.moveTo(this.LastPaint[0], this.LastPaint[1]);
                                _.Layer.Context.lineTo(e.brushX, e.brushY);
                                _.Layer.Context.stroke();
                            }

                            this.LastPaint = [e.brushX, e.brushY, e.brushPressure];
                        }
                    })
                };

                _.Brush = _.Brushes.Default;

                /**
                 * @returns {boolean}
                 * @private
                 */
                function _onBeforeUnload () {
                    return true;
                }

                /**
                 * @param file
                 * @returns {boolean}
                 * @private
                 */
                function _prepareToImport (file) {
                    if (typeof file === 'object' && file.tagName && file.tagName.toLowerCase() === 'input') {
                        var input = file;

                        for (var i in input.files) {
                            if (input.files.hasOwnProperty(i)) {
                                _.Import(input.files[i]);
                            }
                        }

                        input.value = '';

                        return false;
                    }

                    return true;
                }

                /**
                 * @param file
                 * @returns {*}
                 * @constructor
                 */
                _.Import = function (file) {
                    var preparationResult = _prepareToImport(file);

                    if (!preparationResult) {
                        return false;
                    }

                    if (file && file instanceof File) {
                        var filetype = file.name.replace(/^(.*)\.(.*?)$/, '$2').toLowerCase();

                        switch (filetype) {
                            case 'png':
                            case 'jpg':
                            case 'jpeg':
                            case 'gif':
                            case 'bmp':
                                _.Layers.Add(function (layer) {
                                    layer.Name = file.name.replace(/^(.*)\.(.*?)$/, '$1');

                                    var reader = new FileReader();

                                    reader.onload = function (r) {
                                        var img = new Image();

                                        img.onload = function () {
                                            layer.Context.drawImage(this, 0, 0);
                                        };

                                        img.src = r.target.result;
                                    };

                                    reader.readAsDataURL(file);
                                });

                                break;
                            case 'psd':
                                _.Layers.Add(function (layer) {
                                    layer.Name = file.name.replace(/^(.*)\.(.*?)$/, '$1');

                                    var PSD = require('psd');

                                    PSD.fromDroppedFile(file).then(function (psd) {
                                        var img = new Image();

                                        img.onload = function () {
                                            layer.Context.drawImage(this, 0, 0);
                                        };

                                        img.src = psd.image.toBase64();
                                    });
                                });

                                break;
                            case 'h5i':
                                var reader = new FileReader();

                                reader.onload = function (r) {
                                    try {
                                        var data = jQuery.parseJSON(r.target.result);

                                        for (var i in data.layers) {
                                            var layerData = data.layers[i],
                                                newLayer = _.Layers.Add(),
                                                img = new Image();

                                            newLayer.Name = layerData.name;

                                            img.onload = _.drawImageOnLoad(newLayer);

                                            img.src = layerData.image;
                                        }
                                    } catch (e) {
                                        console.error(e);
                                    }
                                };

                                reader.readAsText(file);

                                break;
                        }
                    }

                    return _;
                };

                /**
                 * @param layer
                 * @returns {Function}
                 */
                _.drawImageOnLoad = function (layer) {
                    return function () {
                        layer.Context.drawImage(this, 0, 0);
                    };
                };

                /**
                 * @returns {Element}
                 * @private
                 */
                function _getCvs () {
                    var cvs = document.createElement('canvas'),
                        ctx = cvs.getContext('2d'),
                        listProp;

                    for (listProp in _.Layers.List) {
                        if (_.Layers.List.hasOwnProperty(listProp)) {
                            if (_.Layers.List[listProp].Context.canvas.width > cvs.width) {
                                cvs.width = _.Layers.List[listProp].Context.canvas.width;
                            }

                            if (_.Layers.List[listProp].Context.canvas.height > cvs.height) {
                                cvs.height = _.Layers.List[listProp].Context.canvas.height;
                            }
                        }
                    }

                    ctx.fillStyle = _.Background;

                    ctx.fillRect(0, 0, cvs.width, cvs.height);

                    for (listProp in _.Layers.List) {
                        if (_.Layers.List.hasOwnProperty(listProp) && !_.Layers.List[listProp].Hidden) {
                            ctx.drawImage(_.Layers.List[listProp].Context.canvas, 0, 0);
                        }
                    }

                    return cvs;
                }

                /**
                 * @returns {Artboard}
                 * @constructor
                 */
                _.ExportPng = function () {
                    var cvs = _getCvs();

                    cvs.toBlob(function (blob) {
                        saveAs(blob, 'image.png');
                    });

                    return _;
                };

                /**
                 * @returns {string}
                 * @private
                 */
                function _getJsonString () {
                    if (!window.JSON) {
                        return '';
                    }

                    var data = {
                        layers: []
                    };

                    for (var i in _.Layers.List) {
                        var layer = _.Layers.List[i];

                        data.layers.push({
                            name: layer.Name,
                            image: layer.Context.canvas.toDataURL('image/png')
                        });
                    }

                    return JSON.stringify(data);
                }

                /**
                 * @returns {*}
                 * @constructor
                 */
                _.ExportH5i = function () {
                    var data = _getJsonString();

                    saveAs(new Blob([data], {
                        type: 'text/plain;charset=utf-8'
                    }), 'image.h5i');

                    return _;
                };

                /**
                 * @constructor
                 */
                _.Save = function () {
                    var $body = jQuery('body'),
                        incorrectPasswordLength = '',
                        saveButtonText = 'Save',
                        cancelButtonText = 'Close',
                        $dialog,
                        $errorMessage,
                        $successMessage,
                        $form,
                        formData = '',
                        $password = jQuery('#password'),
                        passwordValue = '',
                        $tips = jQuery('#validateTips'),
                        $errorMessageText = jQuery('#errorMessageText'),
                        $successMessageText = jQuery('#successMessageText'),
                        cvs,
                        encrypted,
                        valid;

                    /**
                     * @param t
                     * @private
                     */
                    function _updateTips (t) {
                        $tips
                            .text(t)
                            .addClass('ui-state-highlight');

                        setTimeout(function () {
                            $tips.removeClass('ui-state-highlight', 1500);
                        }, 500);
                    }

                    /**
                     * @param o
                     * @param min
                     * @param max
                     * @returns {boolean}
                     * @private
                     */
                    function _checkPasswordLength (o, min, max) {
                        if (o.val().length > max || o.val().length < min) {
                            o.addClass('ui-state-error');

                            if (incorrectPasswordLength === '' || incorrectPasswordLength === undefined) {
                                incorrectPasswordLength = 'Length must be between ' + min + ' and ' + max;
                            }

                            _updateTips(incorrectPasswordLength);

                            return false;
                        }

                        return true;
                    }

                    /**
                     * @private
                     */
                    function _setFileSizeLocalError () {
                        switch (true) {
                            case window.drawwwrData === undefined:
                                $errorMessageText.text('The file is too large');
                                break;

                            case window.drawwwrData.fileSizeError === undefined:
                                $errorMessageText.text('The file is too large');
                                break;

                            default:
                                $errorMessageText.text(window.drawwwrData.fileSizeError);
                        }
                    }

                    /**
                     * @private
                     */
                    function _setUnexpectedError () {
                        switch (true) {
                            case window.drawwwrData === undefined:
                                $errorMessageText.text('Unexpected error');
                                break;

                            case window.drawwwrData.unexpectedError === undefined:
                                $errorMessageText.text('Unexpected error');
                                break;

                            default:
                                $errorMessageText.text(window.drawwwrData.unexpectedError);
                        }
                    }

                    /**
                     * @private
                     */
                    function _setSuccessMessage () {
                        switch (true) {
                            case window.drawwwrData === undefined:
                                $successMessageText.text('Success');
                                break;

                            case window.drawwwrData.successMessage === undefined:
                                $successMessageText.text('Success');
                                break;

                            default:
                                $successMessageText.text(window.drawwwrData.successMessage);
                        }
                    }

                    /**
                     * @returns {boolean}
                     * @private
                     */
                    function _checkAndSave () {
                        $password.removeClass('ui-state-error');

                        valid = _checkPasswordLength($password, 5, 16);

                        if (!valid) {
                            return false;
                        }

                        $('#hiddenSubmit').click();

                        passwordValue = $password.val();

                        $dialog.dialog('close');

                        $body.preloader('start');

                        try {
                            cvs = _getCvs();

                            cvs.toBlob(function (blobImage) {
                                var fileImageSize = Math.ceil(blobImage.size / 1024);

                                if (fileImageSize > 1024) {
                                    _setFileSizeLocalError();

                                    $errorMessage.dialog('open');
                                } else {
                                    var jsonString = _getJsonString(),
                                        blobH5i,
                                        fileH5iSize;

                                    encrypted = CryptoJS.AES.encrypt(jsonString, passwordValue);

                                    encrypted = encrypted.toString();

                                    blobH5i = new Blob([encrypted], {
                                        type: 'application/octet-stream'
                                    });

                                    fileH5iSize = Math.ceil(blobH5i.size / 1024);

                                    if (fileH5iSize > 1024) {
                                        _setFileSizeLocalError();

                                        $errorMessage.dialog('open');

                                        return false;
                                    }

                                    _save();
                                }
                            });
                        } catch (error) {
                            setTimeout(function () {
                                $body.preloader('stop');
                            }, 1000);

                            console.error('Catch', error);
                        }
                    }

                    /**
                     * @param data
                     * @param textStatus
                     * @param jqXHR
                     * @private
                     */
                    function _success (data, textStatus, jqXHR) {
                        switch (true) {
                            case data.status === undefined:
                                _setUnexpectedError();

                                $errorMessage.dialog('open');

                                break;

                            case data.status === 'error':
                                if (data.text === undefined) {
                                    _setUnexpectedError();
                                } else {
                                    $errorMessageText.text(data.text);
                                }

                                $errorMessage.dialog('open');

                                break;

                            case data.status === 'success':
                                if (data.text === undefined) {
                                    _setSuccessMessage();
                                } else {
                                    $successMessageText.text(data.text);
                                }

                                $successMessage.dialog('open');

                                break;
                        }

                        console.log({
                            'data': data,
                            'textStatus': textStatus,
                            'jqXHR': jqXHR
                        });
                    }

                    /**
                     * @param jqXHR
                     * @param textStatus
                     * @param errorThrown
                     * @private
                     */
                    function _error (jqXHR, textStatus, errorThrown) {
                        _setUnexpectedError();

                        $errorMessage.dialog('open');

                        console.error({
                            'jqXHR': jqXHR,
                            'textStatus': textStatus,
                            'errorThrown': errorThrown
                        });
                    }

                    /**
                     * @param jqXHR
                     * @param textStatus
                     * @private
                     */
                    function _complete (jqXHR, textStatus) {
                        setTimeout(function () {
                            $body.preloader('stop');
                        }, 1000);

                        console.log({
                            'jqXHR': jqXHR,
                            'textStatus': textStatus
                        });
                    }

                    /**
                     * @returns {boolean}
                     * @private
                     */
                    function _save () {
                        if (valid) {
                            setTimeout(function () {
                                try {
                                    formData = $form.serializeArray();

                                    formData.push({
                                        name: 'IMAGE_FILE',
                                        value: cvs.toDataURL('image/png')
                                    });

                                    formData.push({
                                        name: 'H5I_FILE',
                                        value: encrypted
                                    });

                                    jQuery.ajax({
                                        type: 'POST',
                                        data: formData,
                                        dataType: 'json',
                                        url: window.drawwwrData.ajaxPath,
                                        success: _success,
                                        error: _error,
                                        complete: _complete
                                    });
                                } catch (error) {
                                    setTimeout(function () {
                                        $body.preloader('stop');
                                    }, 1000);

                                    console.error('Catch', error);
                                }
                            }, 100);
                        }

                        return valid;
                    }

                    if (window.drawwwrData !== undefined) {
                        if (window.drawwwrData.incorrectPasswordLength !== undefined) {
                            incorrectPasswordLength = window.drawwwrData.incorrectPasswordLength;
                        }

                        if (window.drawwwrData.saveButtonText !== undefined) {
                            saveButtonText = window.drawwwrData.saveButtonText;
                        }

                        if (window.drawwwrData.cancelButtonText !== undefined) {
                            cancelButtonText = window.drawwwrData.cancelButtonText;
                        }
                    }

                    $form = jQuery('#creationForm')
                        .on('submit', function (event) {
                            event.preventDefault();
                        });

                    $dialog = jQuery('#dialogForm').dialog({
                        dialogClass: 'no-close',
                        autoOpen: false,
                        width: 350,
                        modal: true,
                        buttons: [
                            {
                                text: saveButtonText,
                                click: function () {
                                    _checkAndSave();
                                }
                            },
                            {
                                text: cancelButtonText,
                                click: function () {
                                    $dialog.dialog('close');
                                }
                            }
                        ],
                        close: function () {
                            $form.trigger('reset');

                            $password.removeClass('ui-state-error');
                        }
                    });

                    $errorMessage = jQuery('#dialogError').dialog({
                        dialogClass: 'no-close ui-state-error',
                        autoOpen: false,
                        width: 350,
                        modal: true,
                        buttons: [
                            {
                                text: 'OK',
                                click: function () {
                                    $errorMessage.dialog('close');
                                }
                            }
                        ]
                    });

                    $successMessage = jQuery('#dialogSuccess').dialog({
                        dialogClass: 'no-close',
                        autoOpen: false,
                        width: 350,
                        modal: true,
                        buttons: [
                            {
                                text: 'OK',
                                click: function () {
                                    $successMessage.dialog('close');
                                }
                            }
                        ],
                        close: function () {
                            jQuery(window).off('beforeunload', _onBeforeUnload);

                            document.location.href = '/';
                        }
                    });

                    $dialog.dialog('open');
                };

                /**
                 * @param width
                 * @param height
                 * @returns {Artboard}
                 * @constructor
                 */
                _.Resize = function (width, height) {
                    for (var i in _.Layers.List) {
                        _.Layers.List[i].Resize(width, height);
                    }

                    return _;
                };

                _.Resize();

                jQuery(document)
                    .on('mouseenter mouseleave mousemove', function (e) {
                        jQuery('html')[e.altKey ? 'addClass' : 'removeClass']('alt');
                    })
                    .on('mouseup mouseleave blur', function (e) {
                        for (var i in _.Layers.List) {
                            _.Layers.List[i].DrawStop.call(_.Layers.List[i].Canvas[0], e.originalEvent);
                        }
                    })
                    .on('dragenter dragover', function (e) {
                        e.preventDefault();

                        return false;
                    })
                    .on('drop', function (e) {
                        for (var i in e.originalEvent['dataTransfer'].files) {
                            if (e.originalEvent['dataTransfer'].files.hasOwnProperty(i)) {
                                _.Import(e.originalEvent['dataTransfer'].files[i]);
                            }
                        }

                        return false;
                    });

                jQuery(window)
                    .on('keydown keyup', function (e) {
                        jQuery('html')[e.altKey ? 'addClass' : 'removeClass']('alt');
                    })
                    .on('resize', function () {
                        jQuery('.controls-section.top').css({
                            height: jQuery(window).height() - jQuery('.sidebar .controls-section.bottom').height()
                        });
                    })
                    .on('beforeunload', _onBeforeUnload)
                    .trigger('resize');

                jQuery(function () {
                    if (jQuery('.color-picker').length) {
                        _.ColorPickerLabel = jQuery('.color-picker .current-color');

                        _.ColorPickerCanvas = jQuery('<canvas>')
                            .appendTo('.color-picker')
                            .on(
                                ('ontouchstart' in document.documentElement)
                                    ? 'touchstart'
                                    : 'mousedown',
                                function () {
                                    jQuery(this).data('Picking', true);

                                    return false;
                                }
                            )
                            .on(
                                ('ontouchstart' in document.documentElement)
                                    ? 'touchend touchleave touchcancel'
                                    : 'mouseup mouseleave',
                                function () {
                                    jQuery(this).data('Picking', false);

                                    return false;
                                }
                            )
                            .on(
                                ('ontouchstart' in document.documentElement)
                                    ? 'touchmove'
                                    : 'mousemove',
                                function (e) {
                                    if (jQuery(this).data('Picking')) {
                                        var off = jQuery(this).offset(),
                                            x = (e.pageX || e.originalEvent.pageX) - off.left,
                                            y = (e.pageY || e.originalEvent.pageY) - off.top,
                                            pixel = _.ColorPicker.getImageData(x, y, 1, 1),
                                            color = 'rgb(' +
                                                pixel.data[0] +
                                                ',' +
                                                pixel.data[1] +
                                                ',' +
                                                pixel.data[2] +
                                                ')';

                                        _.ColorPickerLabel.css('background-color', color);

                                        for (var i in _.Brushes) {
                                            if (_.Brushes.hasOwnProperty(i)) {
                                                _.Brushes[i].Foreground = color;
                                            }
                                        }
                                    }

                                    return false;
                                }
                            );

                        _.ColorPicker = _.ColorPickerCanvas[0].getContext('2d');

                        var img = new Image();

                        img.onload = function () {
                            var width = _.ColorPickerCanvas.parent().width();

                            _.ColorPickerCanvas
                                .attr('width', width)
                                .attr('height', width);

                            _.ColorPicker.drawImage(this, 0, 0, this.width, this.height, 0, 0, width, width);
                        };

                        img.src = typeof window['colorPickerImageSrc'] !== 'undefined'
                            ? window['colorPickerImageSrc']
                            : '';
                    }

                    _.ThicknessLabel = jQuery('.controls-section .thickness .current-thickness');

                    _.ThicknessSlider = jQuery('.controls-section .thickness input[name="thickness"]')
                        .on('input', function () {
                            _.ThicknessLabel.html(this.value);
                            _.Brush.Thickness = parseInt(this.value);
                        });

                    if (window['PointerEvent']) {
                        jQuery('.artboards')
                            .on('pointerup', 'canvas.layer.active', function (e) {
                                var layer = jQuery(this).data('Layer');

                                if (!layer.Active || layer.Hidden || e.originalEvent['pointerType'] === 'touch') {
                                    return false;
                                }

                                layer.DrawStop.call(this, e.originalEvent);

                                return false;
                            })
                            .on('pointerdown', 'canvas.layer.active', function (e) {
                                var layer = jQuery(this).data('Layer');

                                if (!layer.Active || layer.Hidden || e.originalEvent['pointerType'] === 'touch') {
                                    return false;
                                }

                                layer.DrawStart.call(this, e.originalEvent);

                                return false;
                            })
                            .on('pointermove', 'canvas.layer.active', function (e) {
                                var layer = jQuery(this).data('Layer');

                                if (!layer.Active || layer.Hidden || e.originalEvent['pointerType'] === 'touch') {
                                    return false;
                                }

                                layer.Draw.call(this, e.originalEvent);

                                return false;
                            });
                    } else if ('ontouchstart' in document.documentElement) {
                        jQuery('.artboards')
                            .on('touchend touchleave touchcancel', 'canvas.layer.active', function (e) {
                                var layer = jQuery(this).data('Layer');

                                if (!layer.Active || layer.Hidden) {
                                    return false;
                                }

                                layer.DrawStop.call(this, e.originalEvent);

                                return false;
                            })
                            .on('touchstart', 'canvas.layer.active', function (e) {
                                var layer = jQuery(this).data('Layer');

                                if (!layer.Active || layer.Hidden) {
                                    return false;
                                }

                                layer.DrawStart.call(this, e.originalEvent);

                                return false;
                            })
                            .on('touchmove', 'canvas.layer.active', function (e) {
                                var layer = jQuery(this).data('Layer');

                                if (!layer.Active || layer.Hidden) {
                                    return false;
                                }

                                layer.Draw.call(this, e.originalEvent);

                                return false;
                            });
                    } else {
                        jQuery('.artboards')
                            .on('mousedown', 'canvas.layer.active', function (e) {
                                var layer = jQuery(this).data('Layer');

                                if (!layer.Active || layer.Hidden || e.originalEvent['pointerType'] === 'touch') {
                                    return false;
                                }

                                layer.DrawStart.call(this, e.originalEvent);

                                return false;
                            })
                            .on('mousemove', 'canvas.layer.active', function (e) {
                                var layer = jQuery(this).data('Layer');

                                if (!layer.Active || layer.Hidden || e.originalEvent['pointerType'] === 'touch') {
                                    return false;
                                }

                                layer.Draw.call(this, e.originalEvent);

                                return false;
                            });
                    }

                    jQuery('.sidebar .controls-section .layers > ul').sortable({
                        axis: 'y',
                        container: 'parent',
                        revert: 250,
                        stop: function () {
                            var layers = [];

                            jQuery(jQuery(this).closest('ul').find('> .layer').get().reverse()).each(function () {
                                var layer = jQuery(this).data('Layer');

                                layer.Index = layers.push(layer) - 1;
                            });

                            _.Layers.List = layers;

                            for (var i in _.Layers.List) {
                                _.Layers.List[i].Canvas
                                    .prependTo('.artboards')
                                    .css('z-index', i);
                            }
                        }
                    });

                    for (var i in _.Brushes) {
                        if (_.Brushes.hasOwnProperty(i) && _.Brushes[i].Icon) {
                            jQuery('<a>')
                                .attr({
                                    href: '#brush',
                                    title: _.Brushes[i].title
                                })
                                .addClass((_.Brushes[i].Icon) + ((i === 'Default') ? ' active' : ''))
                                .data('brush', i)
                                .appendTo('.brushes')
                                .on('click', function () {
                                    jQuery(this).addClass('active').siblings('.active').removeClass('active');

                                    _.Brush = _.Brushes[jQuery(this).data('brush')];

                                    return false;
                                });
                        }
                    }

                    _.Layers.Add().Focus();

                    jQuery('#drawwwrAdd').on('click', function (event) {
                        event.preventDefault();

                        _.Layers.Add().Focus();

                        return false;
                    });

                    jQuery('#drawwwrExportPng').on('click', function (event) {
                        event.preventDefault();

                        _.ExportPng();

                        return false;
                    });

                    jQuery('#drawwwrExportH5i').on('click', function (event) {
                        event.preventDefault();

                        _.ExportH5i();

                        return false;
                    });

                    jQuery('#drawwwrImport').on('change', function (event) {
                        event.preventDefault();

                        _.Import(this);

                        return false;
                    });

                    jQuery('#drawwwrSave').on('click', function (event) {
                        event.preventDefault();

                        _.Save(this);

                        return false;
                    });
                });

                return _;
            });

        };

        jQuery.fn.drawwwr = function () {
            return new Drawwwr();
        };
    })();
}));

jQuery(function () {
    jQuery('body').drawwwr();
});

/**
 * @returns {boolean}
 */
function goBack () {
    if (
        window.history.length > 1
        && document.referrer !== window.location.href
        && document.referrer.split('/')[2] === window.location.host
    ) {
        window.history.back();
    } else {
        window.location.href = '/';
    }

    return false;
}