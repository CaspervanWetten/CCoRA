import { Component, Vue } from "vue-property-decorator";
import WithRender from "./root-environment.html?style=./root-environment.scss";

import InitEnvironment from "./init-environment/init-environment";
import ModellingEnvironment from "./modelling-environment/modelling-environment";
import FooterComponent from "./footer/footer";
import ConnectionStatusComponent from "./connection-status";

import { getModule } from "vuex-module-decorators";
import UserModule from "src/store/user";
import PetrinetModule from "src/store/petrinet";
import SessionModule from "src/store/session";

@WithRender
@Component({
    name: "root-environment",
    components: {
        "init-environment": InitEnvironment,
        "modelling-environment": ModellingEnvironment,
        "footer-component": FooterComponent,
        "connection-status": ConnectionStatusComponent
    }
})
export default class RootEnvironment extends Vue {
    get current() {
        let umod = getModule(UserModule, this.$store);
        let pmod = getModule(PetrinetModule, this.$store);
        let smod = getModule(SessionModule, this.$store);
        if (umod.id === null ||
            smod.id === null ||
            pmod.id === null ||
            pmod.petrinet === null) {
            return InitEnvironment;
        } else {
            return ModellingEnvironment;
        }
    }

    showConnectionStatus = false;

    online() {
        window.setTimeout(() => {
            this.showConnectionStatus = false;
        }, 1000);
    }

    offline() {
        this.showConnectionStatus = true;
    }
}
