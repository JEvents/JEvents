window.setTimeout(function () {

// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: https://codemirror.net/LICENSE

// Utility function that allows modes to be combined. The mode given
// as the base argument takes care of most of the normal mode
// functionality, but a second (typically simple) mode is used, which
// can override the style of text. Both modes get to parse all of the
// text, but when both assign a non-null style to a piece of code, the
// overlay wins, unless the combine argument was true and not overridden,
// or state.overlay.combineTokens was true, in which case the styles are
// combined.

    (function(mod) {
            mod(CodeMirror);
    })(function(CodeMirror) {
        "use strict";

        CodeMirror.overlayMode = function(base, overlay, combine) {
            return {
                startState: function() {
                    return {
                        base: CodeMirror.startState(base),
                        overlay: CodeMirror.startState(overlay),
                        basePos: 0, baseCur: null,
                        overlayPos: 0, overlayCur: null,
                        streamSeen: null
                    };
                },
                copyState: function(state) {
                    return {
                        base: CodeMirror.copyState(base, state.base),
                        overlay: CodeMirror.copyState(overlay, state.overlay),
                        basePos: state.basePos, baseCur: null,
                        overlayPos: state.overlayPos, overlayCur: null
                    };
                },

                token: function(stream, state) {
                    if (stream != state.streamSeen ||
                        Math.min(state.basePos, state.overlayPos) < stream.start) {
                        state.streamSeen = stream;
                        state.basePos = state.overlayPos = stream.start;
                    }

                    if (stream.start == state.basePos) {
                        state.baseCur = base.token(stream, state.base);
                        state.basePos = stream.pos;
                    }
                    if (stream.start == state.overlayPos) {
                        stream.pos = stream.start;
                        state.overlayCur = overlay.token(stream, state.overlay);
                        state.overlayPos = stream.pos;
                    }
                    stream.pos = Math.min(state.basePos, state.overlayPos);

                    // state.overlay.combineTokens always takes precedence over combine,
                    // unless set to null
                    if (state.overlayCur == null) return state.baseCur;
                    else if (state.baseCur != null &&
                        state.overlay.combineTokens ||
                        combine && state.overlay.combineTokens == null)
                        return state.baseCur + " " + state.overlayCur;
                    else return state.overlayCur;
                },

                indent: base.indent && function(state, textAfter, line) {
                    return base.indent(state.base, textAfter, line);
                },
                electricChars: base.electricChars,

                innerMode: function(state) { return {state: state.base, mode: base}; },

                blankLine: function(state) {
                    var baseToken, overlayToken;
                    if (base.blankLine) baseToken = base.blankLine(state.base);
                    if (overlay.blankLine) overlayToken = overlay.blankLine(state.overlay);

                    return overlayToken == null ?
                        baseToken :
                        (combine && baseToken != null ? baseToken + " " + overlayToken : overlayToken);
                }
            };
        };

    });

//if (typeof CodeMirror !== 'undefined') {
    CodeMirror.defineMode("mustache", function (config, parserConfig) {
        var mustacheOverlay = {
            startState: function () {
                return {
                    mustacheOpen: false,
                    mustacheOpenPos: 0,
                    mustacheClosePos: 0,
                    hashOpen1: false,
                    hashOpen2: false
                };
            },

            token: function (stream, state) {
                var ch;
                if (stream.match("{{")) {
                    state.mustacheOpenPos = stream.start;
                    while ((ch = stream.next()) != null)
                        if (ch == "}" && stream.next() == "}") {
                            stream.eat("}");
                            state.mustacheClosePos = stream.pos;
                            return "mustache";
                        }
                }
                while (stream.next() != null && !stream.match("{{", false)) {
                }
                return null;
            }
        };
        return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "text/html"), mustacheOverlay);

    });


    CodeMirror.defineMode("mustacheHash", function (config, parserConfig) {
        var mustacheHashOverlay = {
            startState: function () {
                return {
                    mustacheOpen: false,
                    mustacheOpenPos: 0,
                    hashOpen1: false,
                    hashOpen2: false
                };
            },

            token: function (stream, state) {
                var ch;
                // find opening {{ and throw away
                if (stream.match("{{", true)) {
                    state.mustacheOpen = true;
                    state.mustacheOpenPos = stream.start;
                    state.hashOpen1 = false;
                    return null;
                }
                if (state.mustacheOpen && !state.hashOpen1 && !state.hashOpen2) {
                    // advance to end of mustache or to the next #
                    while ((ch = stream.next()) != null && ch != "#" && !stream.match("}}", false)) {
                        return null;
                    }
                    // if its a mustache then this is not valid!
                    if (stream.match("}}")) {
                        state.mustacheOpen = false;
                        state.hashOpen1 = false;
                        state.hashOpen2 = false;
                        return null;
                    }
                    if (ch == "#") {
                        state.hashOpen1 = true;
                        return "mustache-hash-1";
                    }
                }

                if (state.hashOpen1) {
                    while ((ch = stream.next()) != null) {
                        var returnValue = "mustache-hash-1";
                        if (ch == "#") {
                            state.hashOpen1 = false;
                            state.hashOpen2 = true;
                            stream.backUp(1);
                            return returnValue;
                        } else if (ch == "}" && stream.next() == "}") {
                            state.mustacheOpen = false;
                            state.hashOpen1 = false;
                            state.hashOpen2 = false;
                            stream.backUp(2);
                            return returnValue;
                        }
                    }
                }

                if (state.hashOpen2) {
                    while ((ch = stream.next()) != null) {
                        var returnValue = "mustache-hash-2";
                        if (ch == "}" && stream.next() == "}") {
                            state.mustacheOpen = false;
                            state.hashOpen1 = false;
                            state.hashOpen2 = false;
                            stream.backUp(2);
                            return returnValue;
                        }
                    }
                }

                while (stream.next() != null && !stream.match("{{", false)) {
                    state.mustacheOpen = false;
                    state.hashOpen1 = false;
                    state.hashOpen2 = false;
                }
                return null;
            }
        };
        return CodeMirror.overlayMode(CodeMirror.getMode(config, "mustache"), mustacheHashOverlay);

    });

    /*
        CodeMirror.prototype.highlightFormatting = function () {

            //  console.log(this.getMode());
            //CodeMirror.overlayMode(this.getMode(), mustacheHashOverlay, true);

            //var tokens = this.getLineTokens(1);
            //console.log(tokens);
            // this.doc.markText({'line':2, 'ch':5}, {'line':3, 'ch':15}, {'inclusiveLeft':true, 'inclusiveright':true, 'className':'cm-mustache-hash '});

        }

     */
//}
}, 200);