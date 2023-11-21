import { Component, Emit, Prop, Vue } from "vue-property-decorator";
import WithRender from "./alert.html?style=./alert.scss";

@WithRender
@Component({
    name: "alert"
})
export default class AlertComponent extends Vue {
    @Prop(Boolean) show!: boolean;
    @Prop(Boolean) closeable!: boolean;

    @Emit('close-alert')
    close() {
    }
}
