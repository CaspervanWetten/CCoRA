import Editor from "src/editor/editor";

import Graph from "src/system/graph/graph";
import State from "src/system/graph/state";
import Edge from "src/system/graph/edge";

import GraphDrawing from "src/drawing/graph-drawing";
import StateDrawing from "src/drawing/state-drawing";
import EdgeDrawing from "src/drawing/edge-drawing";

import UndoableAction from "src/action/undoable-action";
import HashTable from "src/hash-table/hash-table";

export default class DeleteState implements UndoableAction {
    protected id: number;
    protected state: State;
    protected stateDrawing: StateDrawing;
    protected initial: boolean;
    protected preset: HashTable<number, Edge> | null;
    protected postset: HashTable<number, Edge> | null;
    protected presetDrawings: HashTable<number, EdgeDrawing>;
    protected postsetDrawings: HashTable<number, EdgeDrawing>;
    protected editor: Editor;

    public constructor(
        editor: Editor,
        id: number
    ) {
        this.editor = editor;
        this.id = id;
        let state = this.editor.graph.getState(this.id);
        if (state !== null) {
            this.state = state;
        } else {
            throw Error(`Could not construct delete action for state with id: ${id}`);
        }
        this.stateDrawing = this.editor.graphDrawing.getState(this.id);
        this.initial = this.editor.graph.initial === this.id;
        // init variables for restoring edges upon undo
        this.preset = null;
        this.postset = null;
        this.presetDrawings = new HashTable<number, EdgeDrawing>();
        this.postsetDrawings = new HashTable<number, EdgeDrawing>();
    }

    public exec(): void {
        this.preset = this.editor.graph.preset(this.id);
        this.postset = this.editor.graph.postset(this.id);
        this.preset.each((id: number, edge: Edge) => {
            let drawing = this.editor.graphDrawing.getEdge(id);
            this.presetDrawings.set(id, drawing);
            // delete the edge from the graph and drawing
            this.editor.graph.delEdge(id);
            this.editor.graphDrawing.delEdge(id);
        });

        this.postset.each((id: number, edge: Edge) => {
            let drawing = this.editor.graphDrawing.getEdge(id);
            this.postsetDrawings.set(id, drawing);
            // delete the edge from the graph and drawing
            this.editor.graph.delEdge(id);
            this.editor.graphDrawing.delEdge(id);
        });
        if (this.initial) {
            this.editor.graph.initial = null;
            this.editor.graphDrawing.initial = null;
        }
        this.editor.graph.delState(this.id);
        this.editor.graphDrawing.delState(this.id);
    }

    public undo(): void {
        if (!this.preset || !this.postset) {
            throw new Error(`Could not restore edges: pre-set and/or post-set undefined`);
        }
        this.editor.graph.addState(this.state, this.id);
        this.editor.graphDrawing.addState(this.id, this.stateDrawing);
        this.preset.each((id: number, edge: Edge) => {
            this.editor.graph.addEdge(edge, id);
            this.editor.graphDrawing.addEdge(id, this.presetDrawings.get(id));
        });
        this.postset.each((id: number, edge: Edge) => {
            this.editor.graph.addEdge(edge, id);
            this.editor.graphDrawing.addEdge(id, this.postsetDrawings.get(id));
        });
        if (this.initial) {
            this.editor.graph.initial = this.id;
            this.editor.graphDrawing.initial = this.id;
        }
    }

    public redo(): void {
        this.exec();
    }
}
