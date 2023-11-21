import { Component, Vue } from "vue-property-decorator";
import WithRender from "./init-environment.html?style=./init-environment.scss";

import WelcomeDialogComponent from "./welcome/welcome";
import UploadDialogComponent from "./upload/upload";
import LoaderComponent from "src/components/common/loader/loader";

import { getModule } from "vuex-module-decorators";
import UserModule from "src/store/user";
import PetrinetModule from "src/store/petrinet";
import SessionModule from "src/store/session";

@WithRender
@Component({
    name: "manager-dialog",
    components: {
        "welcome": WelcomeDialogComponent,
        "upload": UploadDialogComponent,
        "loader": LoaderComponent
    }
})
export default class InitEnvironment extends Vue {
    current() {
        let umod = getModule(UserModule, this.$store);
        let pmod = getModule(PetrinetModule, this.$store);
        let smod = getModule(SessionModule, this.$store);

        if (umod.isLoading || pmod.isLoading || smod.isLoading) {
            return LoaderComponent;
        }
        else if (umod.id !== null) {
            return UploadDialogComponent;
        }
        return WelcomeDialogComponent;
    }

    get isLoading(): boolean {
        let mod = getModule(UserModule, this.$store);
        return mod.isLoading;
    }
}
