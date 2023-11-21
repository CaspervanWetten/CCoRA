import { Component, Vue, Emit } from "vue-property-decorator";
import WithRender from "./index.html?style=./index.scss";

@WithRender
@Component
export default class ConnectionStatusComponent extends Vue {
    status: ConnectionStatus = ConnectionStatus.ONLINE;

    created() {
        window.addEventListener("online", (event) => {
            this.status = ConnectionStatus.ONLINE;
            this.$emit("online");
        });
        window.addEventListener("offline", (event) => {
            this.status = ConnectionStatus.OFFLINE;
            this.$emit("offline");
        });
    }

    get isOnline() {
        return this.status === ConnectionStatus.ONLINE;
    }

    get isOffline() {
        return this.status === ConnectionStatus.OFFLINE;
    }
}

enum ConnectionStatus {
    ONLINE,
    OFFLINE
}

export {
    ConnectionStatus,
    ConnectionStatusComponent
}
