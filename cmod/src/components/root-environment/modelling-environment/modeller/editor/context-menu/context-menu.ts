import { Vue, Component, Prop, Emit } from "vue-property-decorator";
import WithRender from "./context-menu.html?style=./context-menu.scss";

import Editor from "src/editor/editor";
import Marking from "src/system/marking";
import Vector2D from "src/vector/vector2d";

@WithRender
@Component({
    name: "context-menu"
})
export default class ContextMenuComponent extends Vue {
    @Prop(Editor) editor!: Editor;
    @Prop(Number) left!: number;
    @Prop(Number) top!: number;
    @Prop(Number) x!: number;
    @Prop(Number) y!: number;

    get styleObject() {
        return {
            left: this.left.toString() + "px",
            top: this.top.toString() + "px",
        };
    }

    @Emit('close')
    addState() {
        if (this.editor !== null) {
            let position = new Vector2D(this.x, this.y);
            let state = new Marking(this.editor.petrinet);
            this.editor.addState(state, position);
        }
    }

    @Emit('close')
    setInitial() {
        if (this.stateSelected) {
            this.editor.setInitial(this.id);
        }
    }

    @Emit('close')
    deleteElement() {
        if (this.stateSelected) {
            this.editor.delState(this.id);
        } else if (this.edgeSelected) {
            this.editor.delEdge(this.id);
        }
    }

    editElement() {
        this.$emit("close");
        if (this.stateSelected) {
            this.$emit('edit-state', event);
        } else if (this.edgeSelected) {
            this.$emit('edit-edge', event);
        }
    }

    @Emit('close')
    flipEdge() {
        if (this.linearEdge) {
            this.editor.flipEdge(this.id);
        }
    }

    get id() {
        if (this.editor && this.editor.selectionId) {
            return this.editor.selectionId;
        }
        return null;
    }

    get stateSelected() {
        return this.id && this.editor.graph.hasState(this.id);
    }

    get edgeSelected() {
        return this.id && this.editor.graph.hasEdge(this.id);
    }

    get linearEdge() {
        if (this.edgeSelected) {
            let edge = this.editor.graph.getEdge(this.id);
            if (edge !== null) {
                return edge.from !== edge.to;
            }
        }
        return false;
    }
}
