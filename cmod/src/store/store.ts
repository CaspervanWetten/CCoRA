import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

import UserModule from "./user";
import PetrinetModule from "./petrinet";
import SessionModule from "./session";
import FeedbackModule from "./feedback";
import ModellerModule from "./modeller";
import EditorSettingsModule from "./editor-settings";
import DrawerSettingsModule from "./drawer-settings";
import GraphDrawingSettingsModule from "./graph-drawing-settings";

const Store = new Vuex.Store({
    modules: {
        UserModule,
        PetrinetModule,
        SessionModule,
        FeedbackModule,
        ModellerModule,
        EditorSettingsModule,
        DrawerSettingsModule,
        GraphDrawingSettingsModule,
    }
});

export default Store;
