import {MatchDecorator, Decoration, EditorView, ViewPlugin, WidgetType} from "@codemirror/view"
import { JoomlaEditor, JoomlaEditorDecorator } from 'editor-api';
import { keymap, createFromTextarea, EditorState } from 'codemirror';

window.setTimeout(function () {

  let editor = JoomlaEditor.get('value');
  console.log(editor);

  function _optionalChain(ops) {
    let lastAccessLHS = undefined;
    let value = ops[0];
    let i = 1;
    while (i < ops.length) {
      const op = ops[i];
      const fn = ops[i + 1];
      i += 2;
      if ((op === 'optionalAccess' || op === 'optionalCall') && value == null) {
        return undefined;
      }
      if (op === 'access' || op === 'optionalAccess') {
        lastAccessLHS = value;
        value = fn(value);
      } else if (op === 'call' || op === 'optionalCall') {
        value = fn((...args) => value.call(lastAccessLHS, ...args));
        lastAccessLHS = undefined;
      }
    }
    return value;
  }//!placeholderMatcher

  const placeholderMatcher = new MatchDecorator({
    regexp: /\[\[(\w+)\]\]/g,
    decoration: match => Decoration.replace({
      widget: new PlaceholderWidget(match[1]),
    })
  });

  //!placeholderPlugin

  const placeholders = ViewPlugin.fromClass(class {

    constructor(view) {
      this.placeholders = placeholderMatcher.createDeco(view);
    }

    update(update) {
      this.placeholders = placeholderMatcher.updateDeco(update, this.placeholders);
    }
  }, {
    decorations: instance => instance.placeholders,
    provide: plugin => EditorView.atomicRanges.of(view => {
      return _optionalChain([view, 'access', _ => _.plugin, 'call', _2 => _2(plugin), 'optionalAccess', _3 => _3.placeholders]) || Decoration.none
    })
  });

  //!placeholderWidget

  class PlaceholderWidget extends WidgetType {
    constructor(name) {
      super();
      this.name = name;
    }

    eq(other) {
      return this.name == other.name
    }

    toDOM() {
      let elt = document.createElement("span");
      elt.style.cssText = `
      border: 1px solid blue;
      border-radius: 4px;
      padding: 0 3px;
      background: lightblue;`;
      elt.textContent = this.name;
      return elt
    }

    ignoreEvent() {
      return false
    }
  }

  //!placeholderCreate


  new EditorView({
    doc: "Dear [[name]],\nYour [[item]] is on its way. Please see [[order]] for details.\n",
    extensions: [placeholders],
    parent: document.querySelector("#editor-placeholder")
  });

}, 1000);
