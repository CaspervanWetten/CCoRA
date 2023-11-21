import Edge from "src/system/graph/edge";

import { Vue, Component, Prop, Watch, Emit } from "vue-property-decorator";
import WithRender from "./edit-edge.html?style=./edit-edge.scss";

import { focus } from "vue-focus";

import { getModule } from "vuex-module-decorators";
import PetrinetModule from "src/store/petrinet";

import Editor from "src/editor/editor";
import StyleManager from "src/style-manager/style-manager";

import { CanvasRenderingContext2DUtils } from "src/utils/canvas-rendering-context-2d";

@WithRender
@Component({
    name: "edit-edge",
    directives: {
        "focus": focus
    }
})
export default class EditEdgeComponent extends Vue {
    @Prop(Editor) editor!: Editor;
    @Prop(Boolean) show!: boolean;

    label: string | null = null;
    labels: string[] | null = null;

    @Emit('close')
    confirm() {
        let id = this.id;
        let old = this.editor.graph.getEdge(id);
        let edge = new Edge(old.from, old.to, this.label);
        if (id !== null && this.label !== null) {
            this.editor.editEdge(id, edge);
        }
    }

    @Emit('close')
    cancel() {
        this.setLabel();
    }

    @Watch('show', { deep: false, immediate: false})
    onShowChange() {
        if (!this.show) {
            this.cancel();
        }
    }

    get id() {
        if (this.editor && this.editor.selectionId &&
            this.editor.graph.hasEdge(this.editor.selectionId)) {
            return this.editor.selectionId;
        }
        return null;
    }

    @Watch('id', {deep: false, immediate: true})
    onSelectionChange() {
        this.setLabel();
    }

    get petrinet() {
        let module = getModule(PetrinetModule, this.$store);
        return module.petrinet;
    }

    @Watch('petrinet', {deep: false, immediate: true})
    onPetrinetChange() {
        let petrinet = this.petrinet;
        let transitions = petrinet.transitions.toArray();
        this.labels = transitions;
    }

    protected setLabel() {
        let id = this.id;
        if (id !== null) {
            let graph = this.editor.graph;
            let edge = graph.getEdge(id);
            this.label = edge.label;
        }
    }

    get styleObject() {
        let id = this.id;
        if (id !== null) {
            let drawing = this.editor.graphDrawing.getEdge(id);
            if (!drawing) return {};
            let drawer = this.editor.drawer;
            let context = drawer.context;
            context.save();
            StyleManager.setEdgeStandardStyle(context);
            let position = drawer.localToGlobal(drawing.getLabelPosition(context));
            StyleManager.setEdgeTextStyle(context);
            let fontheight = CanvasRenderingContext2DUtils.getFontSize(context);
            context.restore();

            return {
                "left": position.x.toString() + "px",
                "top": (position.y + fontheight / 2).toString() + "px"
            };
        }
    }
}
