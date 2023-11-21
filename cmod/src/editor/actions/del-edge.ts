import Editor from "src/editor/editor";

import Graph from "src/system/graph/graph";
import Edge  from "src/system/graph/edge";

import GraphDrawing from "src/drawing/graph-drawing"
import EdgeDrawing  from "src/drawing/edge-drawing";

import UndoableAction from "src/action/undoable-action";

export default class DeleteEdge implements UndoableAction {
    protected id:          number;
    protected editor:      Editor;
    protected edge:        Edge;
    protected edgeDrawing: EdgeDrawing;

    public constructor(editor: Editor, id: number) {
        this.editor = editor;
        this.id = id;
        let edge = this.editor.graph.getEdge(id);
        if ( edge !== null ) {
            this.edge = edge;
        } else {
            throw new Error(`Could not delete edge: invalid id: ${this.id}`);
        }
        this.edgeDrawing = this.editor.graphDrawing.getEdge(id);
    }

    public exec(): void {
        this.editor.graph.delEdge(this.id);
        this.editor.graphDrawing.delEdge(this.id);
    }

    public undo(): void {
        this.editor.graph.addEdge(this.edge, this.id);
        this.editor.graphDrawing.addEdge(this.id, this.edgeDrawing);
    }

    public redo(): void {
        this.exec();
    }
}
