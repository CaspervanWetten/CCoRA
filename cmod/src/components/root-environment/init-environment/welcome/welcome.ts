import { Component, Vue } from "vue-property-decorator";
import WithRender from "./welcome.html";

import AlertComponent from "src/components/common/messages/alert/alert";

import { getModule } from "vuex-module-decorators";
import UserModule from "src/store/user";

@WithRender
@Component({
    name: "welcome",
    components: {
        "alert": AlertComponent
    }
})
export default class WelcomeDialogComponent extends Vue {
    username: string = "";
    hide: boolean = false;

    get error(): string {
        let mod = getModule(UserModule, this.$store);
        return mod.error;
    }

    get showAlert(): boolean {
        let mod = getModule(UserModule, this.$store);
        return mod.error.length > 0 && !this.hide;
    }

    send() {
        let mod = getModule(UserModule, this.$store);
        this.hide = false;
        mod.register(this.username);
    }

    closeAlert() {
        this.hide = true;
    }
}
