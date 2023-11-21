import Editor from "src/editor/editor";

import Graph        from "src/system/graph/graph";
import Edge         from "src/system/graph/edge";
import GraphDrawing from "src/drawing/graph-drawing";

import UndoableAction from "src/action/undoable-action";

export default class EditEdge implements UndoableAction {
    protected id:      number;
    protected oldEdge: Edge | null;
    protected newEdge: Edge;
    protected editor:  Editor;

    public constructor(editor: Editor, id: number, edge: Edge) {
        this.id = id;
        this.newEdge = edge;
        this.oldEdge = null;
        this.editor = editor;
    }

    public exec(): void {
        let edge = this.editor.graph.getEdge(this.id);
        if (edge === null) {
            throw new Error(`Could not edit edge: invalid id: ${this.id}`);
        }
        this.oldEdge = edge;
        this.editor.graph.delEdge(this.id);
        this.editor.graph.addEdge(this.newEdge, this.id);
        let edgeDrawing = this.editor.graphDrawing.getEdge(this.id);
        edgeDrawing.edge = this.newEdge;
    }

    public undo(): void {
        this.editor.graph.delEdge(this.id);
        this.editor.graph.addEdge(this.oldEdge, this.id);
        let drawing = this.editor.graphDrawing.getEdge(this.id);
        drawing.edge = this.oldEdge;
    }

    public redo(): void {
        this.exec();
    }
}
