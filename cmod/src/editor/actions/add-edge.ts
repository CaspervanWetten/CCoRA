import Editor from "src/editor/editor";

import Graph from "src/system/graph/graph";
import Edge  from "src/system/graph/edge";

import EdgeDrawing       from "src/drawing/edge-drawing";
import GraphDrawing      from "src/drawing/graph-drawing";
import LinearEdgeDrawing from "src/drawing/linear-edge-drawing";
import SelfLoopDrawing   from "src/drawing/self-loop-drawing";

import UndoableAction from "src/action/undoable-action";

export default class AddEdge implements UndoableAction {
    protected id:          number | null;
    protected edge:        Edge;
    protected edgeDrawing: EdgeDrawing | null;
    protected editor:      Editor;

    public constructor(editor: Editor, edge: Edge) {
        this.id = null;
        this.editor = editor;
        this.edge = edge;
        this.edgeDrawing = null;
    }

    public exec(): void {
        this.id = this.editor.graph.addEdge(this.edge);
        let drawing;
        if (this.edge.from == this.edge.to) {
            drawing = new SelfLoopDrawing(
                this.edge,
                this.editor.graphDrawing.getState(this.edge.from),
            );
        } else {
            drawing = new LinearEdgeDrawing(
                this.edge,
                this.editor.graphDrawing.getState(this.edge.from),
                this.editor.graphDrawing.getState(this.edge.to),
            );
        }
        this.editor.graphDrawing.addEdge(this.id, drawing);
        this.edgeDrawing = drawing;
    }

    public undo(): void {
        this.editor.graph.delEdge(this.id!);
        this.editor.graphDrawing.delEdge(this.id!);
    }

    public redo(): void {
        this.editor.graph.addEdge(this.edge, this.id);
        this.editor.graphDrawing.addEdge(this.id!, this.edgeDrawing!);
    }
}
