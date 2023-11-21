import Editor from "src/editor/editor";

import Graph from "src/system/graph/graph"
import State from "src/system/graph/state";

import GraphDrawing from "src/drawing/graph-drawing";

import UndoableAction from "src/action/undoable-action";

export default class EditState implements UndoableAction {

    protected id:       number;
    protected oldState: State | null;
    protected newState: State;
    protected editor:   Editor;

    public constructor(
        editor: Editor,
        id: number,
        state: State
    ) {
        this.editor = editor;
        this.id = id;
        this.newState = state;
        this.oldState = null;
    }

    public exec(): void {
        this.oldState = this.editor.graph.getState(this.id);
        this.editor.graph.delState(this.id);
        this.editor.graph.addState(this.newState, this.id);
        let drawing = this.editor.graphDrawing.getState(this.id);
        drawing.state = this.newState;
    }

    public undo(): void {
        this.editor.graph.delState(this.id);
        this.editor.graph.addState(this.oldState!, this.id);
        let drawing = this.editor.graphDrawing.getState(this.id);
        drawing.state = this.oldState!;
    }

    public redo(): void {
        this.exec();
    }
}
