import { Component, Vue, Prop } from "vue-property-decorator";
import WithRender from "./loader.html?style=./loader.scss";

@WithRender
@Component
export default class LoaderComponent extends Vue {
    classname: string = "loader";
}
