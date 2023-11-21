import { Vue, Component, Watch } from "vue-property-decorator";
import WithRender from "./editor.html?style=./editor.scss";

import { getModule } from "vuex-module-decorators";
import EditorSettingsModule from "src/store/editor-settings";
import DrawerSettingsModule from "src/store/drawer-settings";
import GraphDrawingSettingsModule from "src/store/graph-drawing-settings";
import ModellerModule from "src/store/modeller";
import PetrinetModule from "src/store/petrinet";
import FeedbackModule from "src/store/feedback";

import ContextMenuComponent from "./context-menu/context-menu";
import MessengerComponent from "./messenger/messenger";
import EditStateComponent from "./edit-state/edit-state";
import EditEdgeComponent from "./edit-edge/edit-edge";
import LoaderComponent from "src/components/common/loader/loader";

import Editor from "src/editor/editor";
import EditorOptions from "src/editor/editor-options";
import Difficulty from "src/editor/difficulty";
import DrawerOptions from "src/drawer/drawer-options";
import Petrinet from "src/system/petrinet/petrinet";
import Marking from "src/system/marking";
import Feedback from "src/feedback/feedback";
import { MarkingStringType } from "src/system/marking";

@WithRender
@Component({
    name: "editor",
    components: {
        "context-menu": ContextMenuComponent,
        "messenger": MessengerComponent,
        "edit-state": EditStateComponent,
        "edit-edge": EditEdgeComponent,
        "loader": LoaderComponent,
    }
})
export default class EditorComponent extends Vue {
    editor: Editor | null = null;
    showContextMenu: boolean = false;
    showEditState: boolean = false;
    showEditEdge: boolean = false;

    contextLeft: number = 0;
    contextTop: number = 0;
    contextX: number = 0;
    contextY: number = 0;

    addState() {
        if (this.editor) {
            let pos = this.editor.drawer.localCenter;
            let s = new Marking(this.editor.petrinet);
            this.editor.addState(s, pos);
        }
    }

    get feedbackButtonText() {
        let mod = getModule(FeedbackModule, this.$store);
        if (mod.isLoading) {
            return "Getting feedback...";
        }
        return "Request feedback";
    }

    get feedbackButtonDisabled() {
        return this.loadingFeedback;
    }

    get loadingFeedback() {
        let mod = getModule(FeedbackModule, this.$store);
        return mod.isLoading;
    }

    clearFeedback() {
        let mod = getModule(FeedbackModule, this.$store);
        mod.clear();
    }

    requestFeedback() {
        if (this.editor) {
            let mod = getModule(FeedbackModule, this.$store);
            mod.get(this.editor.graph);
        }
    }

    mounted(): void {
        let canvas = <HTMLCanvasElement><unknown>document.getElementById("editorCanvas");
        this.editor = new Editor(canvas, this.petrinet, this.editorSettings);
        this.editor.drawer.options = this.drawerSettings;
        this.editor.markingStyle = this.stringType;
    }

    openContextMenu(event): void {
        if (this.editor) {
            let box = this.editor.drawer.context.canvas.getBoundingClientRect();
            let drawer = this.editor.drawer;
            let point = drawer.globalToLocal(event);
            this.contextLeft = event.clientX - Math.round(box.left);
            this.contextTop = event.clientY - Math.round(box.top);
            this.contextX = point.x;
            this.contextY = point.y;
            this.showContextMenu = true;
        }
    }

    openEditMenu(event): void {
        if (this.editor && !event.ctrlKey) {
            let id = this.editor.selectionId;
            let graph = this.editor.graph;
            if (graph.hasState(id)) {
                this.showEditState = true;
            } else if (graph.hasEdge(id)) {
                this.showEditEdge = true;
            }
        }
    }

    closeContextMenu(): void {
        this.showContextMenu = false;
    }

    closeEditStateMenu(): void {
        this.showEditState = false;
    }

    closeEditEdgeMenu(): void {
        this.showEditEdge = false;
    }

    closeMenus(): void {
        this.closeContextMenu();
        this.closeEditStateMenu();
        this.closeEditEdgeMenu();
        if (this.editor) {
            let canvas = this.editor.drawer.context.canvas;
            canvas.focus();
        }
    }

    get showingMenu(): boolean {
        return this.showEditState || this.showEditEdge || this.showContextMenu;
    }

    toggleContext(): void {
        this.showContextMenu = !this.showContextMenu;
    }

    toggleStyle(): void {
        let mod = getModule(GraphDrawingSettingsModule, this.$store);
        if (this.stringType == MarkingStringType.FULL) {
            mod.setType(MarkingStringType.MINIMAL);
        } else {
            mod.setType(MarkingStringType.FULL);
        }
    }

    get hoverId(): number | null {
        if (this.editor) {
            return this.editor.hoverId;
        }
        return null;
    }

    get showFeedbackButtons(): boolean {
        let mod = getModule(EditorSettingsModule, this.$store);
        return mod.settings.difficulty === Difficulty.HARD;
    }

    get feedbackLoaderStyle() {
        let a = {
            "position": "absolute",
            "top": ".25rem",
            "left": ".25rem",
            "margin": "0",
            "padding": "0",
            "height": "1.5rem",
            "width": "1.5rem",
            "border-width": ".25rem",
        };
        return a;
    }

    // catch and get settings for the underlying Editor class
    get petrinet(): Petrinet {
        let mod = getModule(PetrinetModule, this.$store);
        return mod.petrinet;
    }

    @Watch('petrinet', { deep: false, immediate: false })
    onPetrinetChange(): void {
        if (this.editor) {
            this.editor.petrinet = this.petrinet;
        }
    }

    get feedback(): Feedback {
        let mod = getModule(FeedbackModule, this.$store);
        return mod.feedback;
    }

    @Watch('feedback', { deep: false, immediate: false })
    onFeedbackChange(): void {
        if (this.editor) {
            this.editor.feedback = this.feedback;
        }
    }

    get editorSettings(): EditorOptions {
        let mod = getModule(EditorSettingsModule, this.$store);
        return mod.settings;
    }

    @Watch('editorSettings', { deep: true, immediate: false })
    onEditorSettingsChange(): void {
        if (this.editor) {
            this.editor.options = this.editorSettings;
        }
    }

    get drawerSettings(): DrawerOptions {
        let mod = getModule(DrawerSettingsModule, this.$store);
        return mod.settings;
    }

    @Watch('drawerSettings', { deep: true, immediate: false })
    onDrawerSettingsChange(): void {
        if (this.editor) {
            this.editor.drawer.options = this.drawerSettings;
        }
    }

    get stringType(): MarkingStringType {
        let mod = getModule(GraphDrawingSettingsModule, this.$store);
        return mod.stringType;
    }

    @Watch('stringType', { deep: false, immediate: true })
    onStringTypeChange(): void {
        if (this.editor) {
            let type = this.stringType;
            this.editor.markingStyle = type;
        }
    }

    get isResizing(): boolean {
        let mod = getModule(ModellerModule, this.$store);
        return mod.resizing;
    }

    @Watch('isResizing', { deep: false, immediate: false })
    onResizeChange(): void {
        if (this.editor) {
            this.editor.drawer.resize();
        }
    }
}

