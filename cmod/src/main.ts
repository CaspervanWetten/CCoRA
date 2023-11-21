import Vue from "vue";
import RootEnvironment from "src/components/root-environment/root-environment";

import Store from "./store/store";

class Main {
    public static main() {
        if (PRODUCTION) {
            Vue.config.silent = true;
            Vue.config.devtools = false;
            Vue.config.performance = false;
            Vue.config.productionTip = false;
        }

        new RootEnvironment({
            el: "#app",
            store: Store,
        });
    }
}

document.addEventListener("DOMContentLoaded", (event) => {
    Main.main();
});
