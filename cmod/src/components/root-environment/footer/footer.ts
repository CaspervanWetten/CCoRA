import { Component, Vue } from "vue-property-decorator";
import WithRender from "./footer.html?style=./footer.scss";

@WithRender
@Component({
    name: "root-footer"
})
export default class FooterComponent extends Vue {
    sourceUrl:   string = SOURCE_URL && SOURCE_URL.length > 0 ? SOURCE_URL : "#";
    bugUrl:      string = BUG_URL && BUG_URL.length > 0 ? BUG_URL : "#";
    institution: string = INSTITUTION;
}
