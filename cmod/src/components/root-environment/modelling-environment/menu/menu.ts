import { Prop, Component, Vue } from "vue-property-decorator";
import WithRender from "./menu.html?style=./menu.scss";

import { getModule } from "vuex-module-decorators";
import DrawerSettingsModule from "src/store/drawer-settings";
import GraphDrawingSettingsModule from "src/store/graph-drawing-settings";
import EditorSettingsModule from "src/store/editor-settings";
import Difficulty from "src/editor/difficulty";

import SwitchComponent from "src/components/common/switch/switch";
import ToggleComponent from "src/components/common/toggle/toggle";
import ToggleItem from "src/components/common/toggle/toggle-item";
import { MarkingStringType } from "src/system/marking";

@WithRender 
@Component({
    name: "modeller-menu",
    components: {
        "switcher": SwitchComponent,
        "toggler": ToggleComponent,
    }
})
export default class MenuComponent extends Vue {
    @Prop(Boolean)
    isOpen!: boolean;

    toggleSnap() {
        let mod = getModule(DrawerSettingsModule, this.$store);
        mod.toggleSnap();
    }

    toggleDraw() {
        let mod = getModule(DrawerSettingsModule, this.$store);
        mod.toggleDraw();
    }

    get snapToGrid(): boolean {
        let mod = getModule(DrawerSettingsModule, this.$store);
        return mod.settings.gridOptions.snapGrid;
    }

    get drawGrid(): boolean {
        let mod = getModule(DrawerSettingsModule, this.$store);
        return mod.settings.gridOptions.drawGrid;
    }

    get markingStringTypes(): Array<ToggleItem<MarkingStringType>> {
        return [
            { label: "minimal", value: MarkingStringType.MINIMAL },
            { label: "full",    value: MarkingStringType.FULL }
        ];
    }

    get markingStringType(): MarkingStringType {
        let mod = getModule(GraphDrawingSettingsModule, this.$store);
        return mod.stringType;
    }

    set markingStringType(val: MarkingStringType) {
        let mod = getModule(GraphDrawingSettingsModule, this.$store);
        mod.setType(val);
    }

    get difficulties(): Array<ToggleItem<Difficulty>> {
        return [
            { label: "easy", value: Difficulty.EASY },
            { label: "hard", value: Difficulty.HARD }
        ];
    }

    get difficulty(): Difficulty {
        let mod = getModule(EditorSettingsModule, this.$store);
        return mod.settings.difficulty;
    }

    set difficulty(d: Difficulty) {
        let mod = getModule(EditorSettingsModule, this.$store);
        mod.setDifficulty(d);
    }
}
