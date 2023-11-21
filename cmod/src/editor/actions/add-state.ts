import Editor from "src/editor/editor";

import Graph from "src/system/graph/graph";
import State from "src/system/graph/state";

import GraphDrawing from "src/drawing/graph-drawing"
import StateDrawing from "src/drawing/state-drawing";

import Vector2D from "src/vector/vector2d";
import StyleManager from "src/style-manager/style-manager";

import UndoableAction from "src/action/undoable-action";

export default class AddState implements UndoableAction {
    protected id: number | null;
    protected editor: Editor;
    protected state: State;
    protected stateDrawing: StateDrawing | null;
    protected position: Vector2D | null;

    public constructor(
        editor: Editor,
        state: State,
        pos: Vector2D | null
    ) {
        this.id           = null;
        this.state        = state;
        this.stateDrawing = null;
        this.editor       = editor;
        this.position     = pos;
    }

    public exec(): void {
        this.id = this.editor.graph.addState(this.state);
        this.stateDrawing = new StateDrawing(
            this.state,
            this.editor.graphDrawing.options.markingStyle,
            this.position
        );
        let context = this.editor.drawer.context;
        context.save();
        StyleManager.setStateStandardStyle(context);
        let width = this.stateDrawing.getWidth(context);
        let height = this.stateDrawing.getHeight(context);
        this.stateDrawing.position = new Vector2D(
            this.position.x - width  / 2,
            this.position.y - height / 2
        );
        context.restore();
        this.editor.graphDrawing.addState(this.id, this.stateDrawing);
    }

    public undo(): void {
        this.editor.graph.delState(this.id!);
        this.editor.graphDrawing.delState(this.id!);
    }

    public redo(): void {
        this.editor.graph.addState(this.state, this.id);
        this.editor.graphDrawing.addState(
            this.id!,
            this.stateDrawing!
        );
    }
}

