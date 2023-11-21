import { Vue, Component, Prop, Model, Emit, Watch } from "vue-property-decorator";
import WithRender from "./toggle.html?style=./toggle.scss";

import ToggleItem from "./toggle-item";

@WithRender
@Component({
    name: "toggle",
})
export default class ToggleComponent<T> extends Vue {
    @Model('change', { required: true })
    readonly selected!: T;
    @Prop({ required: true })
    readonly values!: Array<ToggleItem<T>>;

    val: T = this.selected;

    get value() {
        return this.val;
    }

    set value(val: T) {
        this.val = val;
        this.$emit('change', val);
    }

    isActive(val: T): boolean {
        return this.value == val;
    }

    @Watch('selected')
    onSelectedChange() {
        this.value = this.selected;
    }
}
