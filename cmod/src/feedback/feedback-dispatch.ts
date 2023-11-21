import { getModule } from "vuex-module-decorators";

import FeedbackModule from "src/store/feedback";
import Graph from "src/system/graph/graph";

export default class FeedbackDispatch {
    public static get(graph: Graph): void {
        let mod = getModule(FeedbackModule);
        mod.get(graph);
    }

    public static clear(): void {
        let mod = getModule(FeedbackModule);
        mod.clear();
    }
}
