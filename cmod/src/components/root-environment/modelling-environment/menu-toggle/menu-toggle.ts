import { Prop, Emit, Component, Vue } from "vue-property-decorator";
import WithRender from "./menu-toggle.html?style=./menu-toggle.scss";

@WithRender
@Component({
    name: "modeller-menu-toggle",
})
export default class MenuToggleComponent extends Vue {
    @Prop(Boolean) 
    isOpen!: boolean
    
    @Emit("menu-toggle")
    menuToggle() { }
}
