import { Component, Vue } from "vue-property-decorator"; 
import WithRender from "./header.html?style=./header.scss";

@WithRender
@Component({
    name: "modeller-header"
})
export default class HeaderComponent extends Vue {
    title: string = "CoRA"
}
