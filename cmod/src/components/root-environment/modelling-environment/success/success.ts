import { Vue, Component, Prop, Emit } from "vue-property-decorator";
import WithRender from "./success.html";

@WithRender
@Component({
    name: "success"
})
export default class SuccessComponent extends Vue {
    @Prop(Boolean) show!: boolean;
    showQuestionnaireMessage: boolean = SHOW_QUESTIONNAIRE_MESSAGE;
    questionnaireUrl: string = QUESTIONNAIRE_URL.length <= 0 ? '#' : QUESTIONNAIRE_URL;

    @Emit('close')
    close() {}
}
