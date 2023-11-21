import Editor from "src/editor/editor";

import Graph from "src/system/graph/graph";
import GraphDrawing from "src/drawing/graph-drawing";

import UndoableAction from "src/action/undoable-action";

export default class SetInitial implements UndoableAction {
    protected id: number | null;
    protected editor: Editor;

    protected oldId = null;

    public constructor(editor: Editor, stateId: number | null) {
        this.id = stateId;
        this.editor = editor;
    }

    public exec(): void {
        this.oldId = this.editor.graph.initial;
        this.editor.graph.initial = this.id;
        this.editor.graphDrawing.initial = this.id;
    }

    public undo(): void {
        this.editor.graph.initial = this.oldId;
        this.editor.graphDrawing.initial = this.oldId;
    }

    public redo(): void {
        this.exec();
    }
}
