import Editor from "src/editor/editor";

import Graph from "src/system/graph/graph";
import Edge from "src/system/graph/edge";

import GraphDrawing from "src/drawing/graph-drawing";
import LinearEdgeDrawing from "src/drawing/linear-edge-drawing";

import UndoableAction from "src/action/undoable-action";

export default class FlipEdge implements UndoableAction {
    protected editor: Editor;
    protected id: number;
    protected edge: Edge;

    public constructor(editor: Editor, id: number) {
        this.editor = editor;
        this.id = id;
        this.edge = null;
    }

    public exec(): void {
        if (!this.edge) {
            let edge = this.editor.graph.getEdge(this.id);
            this.edge = edge;
        }
        if (this.edge === null) {
            throw new Error(`Could not flip edge: invalid id: ${this.id}`);
        }
        let edge = this.edge;
        if (edge.from === edge.to) {
            throw new Error(`Could not flip edge: cannot flip self-loop`);
        }
        let newEdge = new Edge(edge.to, edge.from, edge.label);
        let sDrawing = this.editor.graphDrawing.getState(edge.from);
        let tDrawing = this.editor.graphDrawing.getState(edge.to);
        let drawing = new LinearEdgeDrawing(newEdge, tDrawing, sDrawing);
        this.editor.graph.delEdge(this.id);
        this.editor.graph.addEdge(newEdge, this.id);
        this.editor.graphDrawing.delEdge(this.id);
        this.editor.graphDrawing.addEdge(this.id, drawing);
    }

    public undo(): void {
        let sDrawing = this.editor.graphDrawing.getState(this.edge.from);
        let tDrawing = this.editor.graphDrawing.getState(this.edge.to);
        let drawing = new LinearEdgeDrawing(this.edge, sDrawing, tDrawing);
        this.editor.graph.delEdge(this.id);
        this.editor.graph.addEdge(this.edge, this.id);
        this.editor.graphDrawing.delEdge(this.id);
        this.editor.graphDrawing.addEdge(this.id, drawing);
    }

    public redo(): void {
        this.exec();
    }
}
