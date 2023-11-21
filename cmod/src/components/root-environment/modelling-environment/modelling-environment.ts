import { Component, Vue, Watch } from "vue-property-decorator";
import WithRender from "./modelling-environment.html?style=./modelling-environment.scss";

import { getModule } from "vuex-module-decorators";
import FeedbackModule from "src/store/feedback";

import HeaderComponent from "./header/header";
import MenuComponent from "./menu/menu";
import MenuToggleComponent from "./menu-toggle/menu-toggle";
import ModellerComponent from "./modeller/modeller";
import TutorialComponent from "./tutorial/tutorial";
import SuccessComponent from "./success/success";

@WithRender
@Component({
    components: {
        "modeller": ModellerComponent,
        "modeller-header": HeaderComponent,
        "modeller-menu": MenuComponent,
        "modeller-menu-toggle": MenuToggleComponent,
        "modeller-tutorial": TutorialComponent,
        "modeller-success": SuccessComponent,
    },
})
export default class ModellingEnvironment extends Vue {
    menuOpen: boolean = false;
    showTutorial: boolean = true;
    hideSuccess: boolean = !SHOW_SUCCESS_MESSAGE;

    created() {
        window.addEventListener('keyup', (event) => {
            switch (event.keyCode) {
                case 72:
                    this.toggleTutorial();
                    break;
            }
        });
    }

    toggleMenu(): void {
        this.menuOpen = !this.menuOpen;
    }

    toggleTutorial() {
        this.showTutorial = !this.showTutorial;
    }

    toggleSuccess() {
        this.hideSuccess = true;
    }

    get showSuccess() {
        return this.feedback.correct && !this.hideSuccess;
    }

    get feedback() {
        let mod = getModule(FeedbackModule, this.$store);
        return mod.feedback;
    }
}
